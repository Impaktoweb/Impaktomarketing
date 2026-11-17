<?php
/**
 * Instalación del sitio: configuración de WordPress y Rank Math, tarifas y contenido inicial.
 * Se usa desde WP-CLI (wp impakto instalar / configurar) y desde Impakto → Instalación.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Configura SEO y ajustes del sitio como en el entorno local.
 *
 * @return string[] Avisos.
 */
function impakto_configurar_sitio() {
	$avisos = array();
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
		$avisos[] = 'Rank Math no está activo: instálalo y vuelve a ejecutar la configuración.';
	}
	flush_rewrite_rules();
	return $avisos;
}

/**
 * Instalación completa: tarifas + contenido inicial + configuración.
 * No sobrescribe contenido existente (salvo que $forzar sea true).
 *
 * @param bool $forzar Reemplazar el contenido sembrado.
 * @return string[] Resumen por paso.
 */
function impakto_instalar_sitio( $forzar = false ) {
	require_once IMPAKTO_CORE_PATH . 'includes/semillas.php';
	require_once IMPAKTO_CORE_PATH . 'includes/semillas-servicios.php';
	$resumen = array();
	if ( ! impakto_tarifas() ) {
		$r         = impakto_importar_tarifas( (string) file_get_contents( impakto_tarifas_json_defecto() ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$resumen[] = is_wp_error( $r ) ? 'Tarifas: ' . $r->get_error_message() : sprintf( 'Tarifas importadas (%d equipos).', $r['equipos_creados'] );
	} else {
		// Las tarifas guardadas no se tocan, salvo la marca de IVA, que sigue al archivo del plugin.
		$archivo = json_decode( (string) file_get_contents( impakto_tarifas_json_defecto() ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$guardadas = get_option( 'impakto_tarifas' );
		if ( is_array( $guardadas ) && is_array( $archivo ) && isset( $archivo['iva_incluido'] ) && ( $guardadas['iva_incluido'] ?? null ) !== $archivo['iva_incluido'] ) {
			$guardadas['iva_incluido'] = (bool) $archivo['iva_incluido'];
			update_option( 'impakto_tarifas', $guardadas );
		}
		$resumen[] = 'Tarifas: ya existían, no se tocaron (IVA no incluido).';
	}
	foreach ( array( 'articulos', 'contenido-articulos', 'servicios', 'contenido-servicios', 'proyectos', 'paginas', 'legales', 'menus' ) as $tipo ) {
		$resumen[] = $tipo . ': ' . call_user_func( 'impakto_sembrar_' . str_replace( '-', '_', $tipo ), $forzar );
	}
	foreach ( impakto_configurar_sitio() as $aviso ) {
		$resumen[] = 'Aviso: ' . $aviso;
	}
	$resumen[] = 'Configuración del sitio y de Rank Math aplicada.';
	return $resumen;
}

/**
 * Impakto → Instalación: el mismo proceso desde el administrador (sin SSH).
 */
add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'impakto', __( 'Instalación', 'impakto-core' ), __( 'Instalación', 'impakto-core' ), 'manage_options', 'impakto-instalacion', 'impakto_pantalla_instalacion' );
	},
	12
);

/**
 * Pantalla de instalación.
 */
function impakto_pantalla_instalacion() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Impakto → Instalación', 'impakto-core' ) . '</h1>';
	if ( isset( $_POST['impakto_instalar'] ) && check_admin_referer( 'impakto_instalar' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$resumen = impakto_instalar_sitio( ! empty( $_POST['forzar'] ) );
		echo '<div class="notice notice-success"><p><strong>' . esc_html__( 'Instalación terminada.', 'impakto-core' ) . '</strong></p><ul style="list-style:disc;padding-left:20px">';
		foreach ( $resumen as $linea ) {
			echo '<li>' . esc_html( $linea ) . '</li>';
		}
		echo '</ul></div>';
	}
	$rank = defined( 'RANK_MATH_VERSION' );
	echo '<p>' . esc_html__( 'Carga el contenido inicial (páginas, servicios, portafolio, artículos, equipos, legales y menús), importa las tarifas y aplica la configuración del sitio y de Rank Math. Es seguro repetirlo: no duplica ni sobrescribe lo que ya existe.', 'impakto-core' ) . '</p>';
	if ( ! $rank ) {
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Instala y activa Rank Math SEO antes de continuar para que la configuración de SEO quede completa.', 'impakto-core' ) . '</p></div>';
	}
	echo '<form method="post">';
	wp_nonce_field( 'impakto_instalar' );
	echo '<p><label><input type="checkbox" name="forzar" value="1"> ' . esc_html__( 'Reemplazar el contenido sembrado si ya existe (borra cambios hechos a mano en esas páginas)', 'impakto-core' ) . '</label></p>';
	submit_button( __( 'Instalar contenido y configuración', 'impakto-core' ), 'primary', 'impakto_instalar' );
	echo '</form>';
	impakto_seccion_vaciar_pruebas();
	echo '</div>';
}

/**
 * Vaciar las solicitudes de prueba y reiniciar el consecutivo. Solo aparece mientras el sitio
 * está bloqueado para buscadores (staging), para que no se use por error en el sitio publicado.
 */
function impakto_seccion_vaciar_pruebas() {
	if ( '0' !== (string) get_option( 'blog_public' ) ) {
		return;
	}
	global $wpdb;
	$tabla = impakto_tabla_solicitudes();
	if ( isset( $_POST['impakto_vaciar'] ) && check_admin_referer( 'impakto_vaciar' ) && ! empty( $_POST['confirmo'] ) ) {
		$borradas = (int) $wpdb->query( "DELETE FROM {$tabla}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$contadores = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( 'impakto_consecutivo_' ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( $contadores as $opcion ) {
			delete_option( $opcion );
		}
		/* translators: %d: número de solicitudes. */
		echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Se borraron %d solicitudes y el consecutivo vuelve a empezar en 0001.', 'impakto-core' ), $borradas ) ) . '</p></div>';
	}
	$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabla}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	echo '<hr><h2>' . esc_html__( 'Antes de publicar: borrar solicitudes de prueba', 'impakto-core' ) . '</h2>';
	/* translators: %d: número de solicitudes. */
	echo '<p>' . esc_html( sprintf( __( 'Hay %d solicitudes registradas. Este botón las borra todas y reinicia el consecutivo de cotizaciones (IMP-AAAA-0001). Solo está disponible mientras el sitio está oculto a los buscadores.', 'impakto-core' ), $total ) ) . '</p>';
	echo '<form method="post">';
	wp_nonce_field( 'impakto_vaciar' );
	echo '<p><label><input type="checkbox" name="confirmo" value="1" required> ' . esc_html__( 'Confirmo que todas las solicitudes son de prueba', 'impakto-core' ) . '</label></p>';
	submit_button( __( 'Borrar solicitudes de prueba', 'impakto-core' ), 'delete', 'impakto_vaciar' );
	echo '</form>';
}
