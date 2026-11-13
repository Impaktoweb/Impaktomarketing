<?php
/**
 * Comandos WP-CLI: studio wp impakto <comando>.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Herramientas de Impakto.
 */
class Impakto_CLI {

	/**
	 * Importa las tarifas desde un JSON (por defecto, data/tarifas.json del plugin).
	 *
	 * ## OPTIONS
	 *
	 * [--archivo=<ruta>]
	 * : Ruta del JSON.
	 *
	 * [--actualizar]
	 * : Sobrescribe el precio de los equipos que ya existen.
	 *
	 * @subcommand importar-tarifas
	 * @param array $args  Posicionales.
	 * @param array $assoc Opciones.
	 */
	public function importar_tarifas( $args, $assoc ) {
		$ruta = $assoc['archivo'] ?? impakto_tarifas_json_defecto();
		if ( ! is_readable( $ruta ) ) {
			WP_CLI::error( "No se puede leer $ruta" );
		}
		$resultado = impakto_importar_tarifas( file_get_contents( $ruta ), isset( $assoc['actualizar'] ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( is_wp_error( $resultado ) ) {
			WP_CLI::error( $resultado->get_error_message() );
		}
		WP_CLI::success( sprintf( 'Tarifas importadas. Equipos creados: %d · actualizados: %d', $resultado['equipos_creados'], $resultado['equipos_actualizados'] ) );
	}

	/**
	 * Verifica los cálculos de la sección 7.2 del BRIEF.
	 *
	 * @subcommand probar-calculo
	 */
	public function probar_calculo() {
		$casos = array(
			array( 'Medio estudio 5 h', impakto_precio_estudio( 'medio-estudio', 5 ), 470000 ),
			array( 'Medio estudio 6 h', impakto_precio_estudio( 'medio-estudio', 6 ), 590000 ),
			array( 'Medio estudio 7 h', impakto_precio_estudio( 'medio-estudio', 7 ), 700000 ),
			array( 'Estudio completo 8 h', impakto_precio_estudio( 'estudio-completo', 8 ), 1200000 ),
			array( 'Estudio completo 3 h', impakto_precio_estudio( 'estudio-completo', 3 ), 600000 ),
			array( 'Podcast 2c+2m × 3 h', impakto_precio_podcast( '2c-2m', 3 ), 600000 ),
		);
		$fallos = 0;
		foreach ( $casos as list( $nombre, $obtenido, $esperado ) ) {
			$ok = $obtenido === $esperado;
			$fallos += $ok ? 0 : 1;
			WP_CLI::log( sprintf( '%s %-24s %14s (esperado %s)', $ok ? '✔' : '✘', $nombre, impakto_cop( $obtenido ), impakto_cop( $esperado ) ) );
		}
		$fallos ? WP_CLI::error( "$fallos casos fallaron" ) : WP_CLI::success( 'Todos los cálculos coinciden.' );
	}

	/**
	 * Siembra el contenido inicial (servicios, proyectos, artículos, páginas y menús).
	 * No sobrescribe lo que ya existe, salvo con --forzar.
	 *
	 * ## OPTIONS
	 *
	 * [--forzar]
	 * : Reemplaza el contenido de las entradas sembradas.
	 *
	 * [--solo=<tipo>]
	 * : articulos|servicios|contenido-servicios|proyectos|paginas|menus
	 *
	 * @param array $args  Posicionales.
	 * @param array $assoc Opciones.
	 */
	public function sembrar( $args, $assoc ) {
		require_once IMPAKTO_CORE_PATH . 'includes/semillas.php';
		require_once IMPAKTO_CORE_PATH . 'includes/semillas-servicios.php';
		$solo   = $assoc['solo'] ?? '';
		$forzar = isset( $assoc['forzar'] );
		foreach ( array( 'articulos', 'contenido-articulos', 'servicios', 'contenido-servicios', 'proyectos', 'paginas', 'legales', 'menus' ) as $tipo ) {
			if ( $solo && $solo !== $tipo ) {
				continue;
			}
			$resultado = call_user_func( 'impakto_sembrar_' . str_replace( '-', '_', $tipo ), $forzar );
			WP_CLI::log( sprintf( '%-10s %s', $tipo, $resultado ) );
		}
		flush_rewrite_rules();
		WP_CLI::success( 'Contenido sembrado.' );
	}

	/**
	 * Configura SEO y ajustes del sitio como en el entorno local: enlaces permanentes, zona horaria,
	 * Rank Math (títulos, sitemap, breadcrumbs, módulos y tablas), imagen OG por defecto y favicon.
	 *
	 * @subcommand configurar
	 */
	public function configurar() {
		global $wp_rewrite;
		update_option( 'timezone_string', 'America/Bogota' );
		update_option( 'start_of_week', 1 );
		update_option( 'blogname', 'Impakto Marketing' );
		update_option( 'blogdescription', 'The Marketing Factory' );
		$wp_rewrite->set_permalink_structure( '/blog/%postname%/' );
		$wp_rewrite->set_category_base( 'blog/categoria' );
		$wp_rewrite->set_tag_base( 'blog/etiqueta' );

		// Imagen para compartir y favicon desde el tema (se importan una sola vez).
		$importar = static function ( $archivo, $titulo, $alt ) {
			$existente = get_posts( array( 'post_type' => 'attachment', 'title' => $titulo, 'posts_per_page' => 1, 'fields' => 'ids' ) );
			if ( $existente ) {
				return (int) $existente[0];
			}
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$tmp = wp_tempnam( basename( $archivo ) );
			copy( get_template_directory() . '/assets/img/' . $archivo, $tmp );
			$id = media_handle_sideload( array( 'name' => $archivo, 'tmp_name' => $tmp ), 0, $titulo );
			if ( is_wp_error( $id ) ) {
				return 0;
			}
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
			return (int) $id;
		};
		$og   = $importar( 'og-default.jpg', 'Impakto Marketing — The Marketing Factory', 'Logo de Impakto Marketing sobre fondo grafito: The Marketing Factory' );
		$icon = $importar( 'icono-512.png', 'Ícono del sitio Impakto', '' );
		if ( $icon ) {
			update_option( 'site_icon', $icon );
		}

		if ( defined( 'RANK_MATH_VERSION' ) ) {
			$g = array_merge(
				(array) get_option( 'rank-math-options-general', array() ),
				array(
					'breadcrumbs'                => 'on',
					'breadcrumbs_separator'      => '/',
					'breadcrumbs_home'           => 'on',
					'breadcrumbs_home_label'     => 'Inicio',
					'breadcrumbs_archive_format' => '%s',
					'breadcrumbs_search_format'  => 'Resultados para %s',
					'breadcrumbs_404_label'      => 'Página no encontrada',
					'breadcrumbs_blog_page'      => 'on',
					'404_monitor_mode'           => 'simple',
					'redirections_header_code'   => '301',
				)
			);
			update_option( 'rank-math-options-general', $g );
			$t = array_merge(
				(array) get_option( 'rank-math-options-titles', array() ),
				array(
					'title_separator'                      => '|',
					'knowledgegraph_type'                  => 'company',
					'knowledgegraph_name'                  => 'Impakto Marketing',
					'website_name'                         => 'Impakto Marketing',
					'website_alternate_name'               => 'The Marketing Factory',
					'knowledgegraph_logo'                  => get_template_directory_uri() . '/assets/img/logo-impakto-color-1684.png',
					'open_graph_image'                     => $og ? wp_get_attachment_url( $og ) : '',
					'open_graph_image_id'                  => $og,
					'twitter_card_type'                    => 'summary_large_image',
					'url_author_base'                      => 'autor',
					'author_custom_robots'                 => 'on',
					'author_robots'                        => array( 'noindex' ),
					'pt_page_title'                        => '%title% %sep% %sitename%',
					'pt_page_default_rich_snippet'         => 'off',
					'pt_post_title'                        => '%title% %sep% %sitename%',
					'pt_servicio_title'                    => '%title% %sep% %sitename%',
					'pt_servicio_default_rich_snippet'     => 'off',
					'pt_proyecto_title'                    => '%title% %sep% %sitename%',
					'pt_proyecto_description'              => '%excerpt%',
					'pt_proyecto_default_rich_snippet'     => 'off',
					'pt_proyecto_archive_title'            => 'Portafolio de branding, audiovisual y marketing | Impakto',
					'pt_proyecto_archive_description'      => 'Naming, branding, producción audiovisual, pauta y ventas digitales para constructoras, marcas de consumo e industria. Mira nuestros casos de éxito.',
					'tax_category_title'                   => '%term%: artículos de marketing %sep% Impakto',
					'tax_categoria-portafolio_title'       => 'Portafolio de %term% %sep% Impakto Marketing',
					'tax_categoria-portafolio_description' => 'Proyectos de %term% de Impakto Marketing: marcas, campañas y resultados medibles para empresas de Manizales, Colombia y el exterior.',
					'noindex_empty_taxonomies'             => 'on',
					'noindex_search'                       => 'on',
				)
			);
			update_option( 'rank-math-options-titles', $t );
			$excluir = array_filter( array_map( static fn( $s ) => get_page_by_path( $s ) ? get_page_by_path( $s )->ID : 0, array( 'guia-de-estilo', 'gracias' ) ) );
			$sm      = array_merge(
				(array) get_option( 'rank-math-options-sitemap', array() ),
				array(
					'authors_sitemap'                  => 'off',
					'tax_categoria-portafolio_sitemap' => 'on',
					'tax_category_sitemap'             => 'on',
					'pt_product_sitemap'               => 'off',
					'include_featured_image'           => 'on',
					'exclude_posts'                    => implode( ',', $excluir ),
				)
			);
			update_option( 'rank-math-options-sitemap', $sm );
			update_option( 'rank_math_modules', array( 'link-counter', 'seo-analysis', 'sitemap', 'rich-snippet', 'redirections', '404-monitor' ) );
			update_option( 'rank_math_wizard_completed', true );
			update_option( 'rank_math_registration_skip', true );
			if ( method_exists( '\\RankMath\\Installer', 'create_tables' ) ) {
				\RankMath\Installer::create_tables( array( 'redirections', '404-monitor' ) );
			}
		} else {
			WP_CLI::warning( 'Rank Math no está activo: instálalo y vuelve a ejecutar este comando.' );
		}
		flush_rewrite_rules();
		WP_CLI::success( 'Sitio configurado.' );
	}

	/**
	 * Instalación completa en un WordPress nuevo: configurar + importar tarifas + sembrar contenido.
	 *
	 * @subcommand instalar
	 */
	public function instalar() {
		$this->importar_tarifas( array(), array() );
		$this->sembrar( array(), array() );
		$this->configurar();
	}
}

WP_CLI::add_command( 'impakto', 'Impakto_CLI' );
