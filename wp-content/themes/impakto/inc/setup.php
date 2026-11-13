<?php
/**
 * Soportes del tema, menús, estilos de bloque y clases del body.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'impakto', IMPAKTO_TEMA_PATH . '/languages' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		remove_theme_support( 'core-block-patterns' );

		add_editor_style(
			array(
				'assets/css/tokens.css',
				'assets/css/base.css',
				'assets/css/components/botones.css',
				'assets/css/components/secciones.css',
				'assets/css/components/elementos.css',
				'assets/css/components/tarjetas.css',
				'assets/css/editor.css',
			)
		);

		// Menús editables en Apariencia → Menús.
		register_nav_menus(
			array(
				'principal'        => __( 'Menú principal', 'impakto' ),
				'footer-servicios' => __( 'Footer: Servicios', 'impakto' ),
				'footer-espacios'  => __( 'Footer: Espacios y equipos', 'impakto' ),
				'footer-impakto'   => __( 'Footer: Impakto', 'impakto' ),
				'footer-legal'     => __( 'Footer: Legal', 'impakto' ),
			)
		);

		// Tamaños de imagen de DISENOS-Y-MEDIDAS.md.
		add_image_size( 'impakto-tarjeta', 800, 1000, true );  // 4:5.
		add_image_size( 'impakto-espacio', 1200, 1500, true ); // 4:5.
		add_image_size( 'impakto-proyecto', 1600, 1200, true ); // 4:3.
		add_image_size( 'impakto-destacado', 1600, 2000, true ); // 4:5.
		add_image_size( 'impakto-articulo', 1600, 900, true );  // 16:9.
		add_image_size( 'impakto-hero', 1920, 1080, true );    // 16:9.
	}
);

/**
 * Estilos de bloque (variantes de bloques nativos con la línea gráfica).
 */
add_action(
	'init',
	static function () {
		$estilos = array(
			'core/group'     => array(
				'seccion-clara'   => __( 'Sección clara', 'impakto' ),
				'seccion-gris'    => __( 'Sección gris', 'impakto' ),
				'seccion-blanca'  => __( 'Sección blanca', 'impakto' ),
				'seccion-oscura'  => __( 'Sección oscura', 'impakto' ),
				'seccion-grafito' => __( 'Sección grafito', 'impakto' ),
				'seccion-naranja' => __( 'Sección naranja', 'impakto' ),
				'seccion-cta'     => __( 'CTA final (grafito con curvas)', 'impakto' ),
				'hero-curvas'     => __( 'Hero claro con curvas', 'impakto' ),
				'hero-oscuro'     => __( 'Hero oscuro (foto o video de fondo)', 'impakto' ),
			),
			'core/paragraph' => array(
				'kicker' => __( 'Etiqueta (kicker)', 'impakto' ),
				'bajada' => __( 'Bajada', 'impakto' ),
				'nota'   => __( 'Nota pequeña', 'impakto' ),
				'respuesta-rapida' => __( 'Respuesta rápida (AEO)', 'impakto' ),
			),
			'core/heading'   => array( 'display' => __( 'Display de sección', 'impakto' ) ),
			'core/list'      => array(
				'flecha'         => __( 'Lista con flecha ↗', 'impakto' ),
				'linea-tiempo'   => __( 'Línea de tiempo 01 ——●', 'impakto' ),
				'barra-vertical' => __( 'Numerada con barra 01 |', 'impakto' ),
				'pills'          => __( 'Pills', 'impakto' ),
				'caracteristicas' => __( 'Características con ícono ✓', 'impakto' ),
			),
			'core/button'    => array( 'flecha' => __( 'Enlace con flecha →', 'impakto' ) ),
			'core/table'     => array( 'tarifas' => __( 'Tabla de tarifas', 'impakto' ) ),
		);
		foreach ( $estilos as $bloque => $variantes ) {
			foreach ( $variantes as $nombre => $etiqueta ) {
				register_block_style(
					$bloque,
					array(
						'name'  => $nombre,
						'label' => $etiqueta,
					)
				);
			}
		}

		// Categoría de patrones propia.
		register_block_pattern_category( 'impakto', array( 'label' => __( 'Impakto', 'impakto' ) ) );
		register_block_pattern_category( 'impakto-inicio', array( 'label' => __( 'Impakto · Inicio', 'impakto' ) ) );
	}
);

/**
 * Clases del body: hero oscuro bajo el header y barra inferior móvil.
 */
add_filter(
	'body_class',
	static function ( $clases ) {
		$clases[] = 'tiene-barra-movil';
		if ( impakto_tiene_hero_oscuro() ) {
			$clases[] = 'tiene-hero-oscuro';
		}
		return $clases;
	}
);

/**
 * ¿La página abre con un hero oscuro (el header arranca transparente)?
 *
 * @return bool
 */
function impakto_tiene_hero_oscuro() {
	$oscuro = is_front_page()
		|| is_page( array( 'estudio', 'sala-de-podcast', 'alquiler-de-equipos' ) )
		|| is_singular( array( 'servicio', 'proyecto' ) );
	return (bool) apply_filters( 'impakto_hero_oscuro', $oscuro );
}

/**
 * Clase "js" en <html> lo antes posible, para animaciones que solo aplican con JS.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>document.documentElement.classList.add('js');</script>\n";
	},
	0
);
