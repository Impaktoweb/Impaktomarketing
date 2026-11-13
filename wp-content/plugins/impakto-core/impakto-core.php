<?php
/**
 * Plugin Name:       Impakto Core
 * Description:       Datos y lógica del sitio de Impakto Marketing: servicios, portafolio, equipos, tarifas, cotizaciones y solicitudes.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      8.1
 * Author:            Impakto Marketing S.A.S.
 * Text Domain:       impakto-core
 * Domain Path:       /languages
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

define( 'IMPAKTO_CORE_VERSION', '0.1.0' );
define( 'IMPAKTO_CORE_FILE', __FILE__ );
define( 'IMPAKTO_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'IMPAKTO_CORE_URL', plugin_dir_url( __FILE__ ) );

$impakto_core_includes = array(
	'helpers',
	'calculo',
	'taxonomias',
	'cpt-servicio',
	'cpt-proyecto',
	'cpt-equipo',
	'meta-campos',
	'ajustes',
	'ajustes-tarifas',
	'migracion-tarifas',
	'leads-db',
	'cotizacion',
	'antispam',
	'correos',
	'rest-api',
	'rest-solicitudes',
	'roles',
);
foreach ( $impakto_core_includes as $impakto_core_file ) {
	require_once IMPAKTO_CORE_PATH . 'includes/' . $impakto_core_file . '.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once IMPAKTO_CORE_PATH . 'includes/cli.php';
}

/**
 * Base de las URL de autor: /blog/autor/{slug}/.
 */
add_action(
	'init',
	static function () {
		global $wp_rewrite;
		// El "front" de los enlaces ya es /blog/ (estructura /blog/%postname%/).
		$wp_rewrite->author_base = 'autor';
	},
	5
);

register_activation_hook(
	__FILE__,
	static function () {
		impakto_registrar_taxonomias();
		impakto_registrar_cpt_servicio();
		impakto_registrar_cpt_proyecto();
		impakto_registrar_cpt_equipo();
		impakto_roles_activar();
		impakto_crear_tabla_solicitudes();
		if ( false === get_option( 'impakto_ajustes' ) ) {
			add_option( 'impakto_ajustes', impakto_ajustes_defecto() );
		}
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	static function () {
		flush_rewrite_rules();
	}
);
