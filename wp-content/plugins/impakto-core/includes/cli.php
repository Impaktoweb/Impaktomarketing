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
		foreach ( impakto_configurar_sitio() as $aviso ) {
			WP_CLI::warning( $aviso );
		}
		WP_CLI::success( 'Sitio configurado.' );
	}

	/**
	 * Instalación completa en un WordPress nuevo: configurar + importar tarifas + sembrar contenido.
	 *
	 * @subcommand instalar
	 */
	public function instalar() {
		foreach ( impakto_instalar_sitio() as $linea ) {
			WP_CLI::log( $linea );
		}
		WP_CLI::success( 'Instalación completa.' );
	}
}

WP_CLI::add_command( 'impakto', 'Impakto_CLI' );
