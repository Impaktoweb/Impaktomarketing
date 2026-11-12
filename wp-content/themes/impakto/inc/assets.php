<?php
/**
 * Carga de CSS y JS. CSS propio con variables; JS vanilla en módulos, sin jQuery.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hojas de estilo globales, en orden.
 *
 * @return array<string,string> handle => ruta relativa.
 */
function impakto_hojas_globales() {
	return array(
		'impakto-tokens'     => 'assets/css/tokens.css',
		'impakto-base'       => 'assets/css/base.css',
		'impakto-botones'    => 'assets/css/components/botones.css',
		'impakto-secciones'  => 'assets/css/components/secciones.css',
		'impakto-elementos'  => 'assets/css/components/elementos.css',
		'impakto-tarjetas'   => 'assets/css/components/tarjetas.css',
		'impakto-header'     => 'assets/css/components/header.css',
		'impakto-menu-movil' => 'assets/css/components/menu-movil.css',
		'impakto-footer'     => 'assets/css/components/footer.css',
	);
}

/**
 * Versión de un archivo según su fecha de modificación (rompe caché al cambiar).
 *
 * @param string $relativa Ruta relativa al tema.
 * @return string
 */
function impakto_version_archivo( $relativa ) {
	$ruta = IMPAKTO_TEMA_PATH . '/' . $relativa;
	return file_exists( $ruta ) ? (string) filemtime( $ruta ) : IMPAKTO_TEMA_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		$anterior = array();
		foreach ( impakto_hojas_globales() as $handle => $ruta ) {
			wp_enqueue_style( $handle, IMPAKTO_TEMA_URL . '/' . $ruta, $anterior, impakto_version_archivo( $ruta ) );
			$anterior = array( $handle );
		}
		if ( is_page( 'guia-de-estilo' ) ) {
			wp_enqueue_style( 'impakto-guia', IMPAKTO_TEMA_URL . '/assets/css/guia.css', $anterior, impakto_version_archivo( 'assets/css/guia.css' ) );
		}

		foreach ( array( 'menu', 'reveal' ) as $modulo ) {
			$ruta = 'assets/js/' . $modulo . '.js';
			wp_enqueue_script_module( 'impakto-' . $modulo, IMPAKTO_TEMA_URL . '/' . $ruta, array(), impakto_version_archivo( $ruta ) );
		}

		// Estilos del bloque FAQ de Rank Math: usamos los del tema.
		wp_dequeue_style( 'rank-math-faq-block' );
	},
	20
);

/**
 * Precarga de las fuentes que pinta el primer pantallazo (400 y 600).
 */
add_action(
	'wp_head',
	static function () {
		foreach ( array( '400', '600' ) as $peso ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( IMPAKTO_TEMA_URL . '/assets/fonts/montserrat-' . $peso . '.woff2' )
			);
		}
	},
	1
);

/**
 * Quita lo que no se usa en el frontend.
 */
add_action(
	'init',
	static function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}
);
