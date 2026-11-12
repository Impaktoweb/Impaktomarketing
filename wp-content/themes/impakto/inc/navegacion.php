<?php
/**
 * Datos de navegación: menús con respaldo, servicios agrupados y estado "actual".
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menús por defecto (si no hay uno asignado en Apariencia → Menús).
 *
 * @return array<string,array<int,array{0:string,1:string}>> ubicación => [ [título, ruta] ].
 */
function impakto_menus_defecto() {
	return array(
		'principal'        => array(
			array( __( 'Inicio', 'impakto' ), '/' ),
			array( __( 'Servicios', 'impakto' ), '/servicios/' ),
			array( __( 'Estudio', 'impakto' ), '/estudio/' ),
			array( __( 'Sala de podcast', 'impakto' ), '/sala-de-podcast/' ),
			array( __( 'Alquiler de equipos', 'impakto' ), '/alquiler-de-equipos/' ),
			array( __( 'Portafolio', 'impakto' ), '/portafolio/' ),
			array( __( 'Nosotros', 'impakto' ), '/nosotros/' ),
			array( __( 'Blog', 'impakto' ), '/blog/' ),
			array( __( 'Contacto', 'impakto' ), '/contacto/' ),
		),
		'footer-espacios'  => array(
			array( __( 'Estudio audiovisual', 'impakto' ), '/estudio/' ),
			array( __( 'Sala de podcast', 'impakto' ), '/sala-de-podcast/' ),
			array( __( 'Alquiler de equipos', 'impakto' ), '/alquiler-de-equipos/' ),
			array( __( 'Términos de alquiler', 'impakto' ), '/terminos-de-alquiler/' ),
		),
		'footer-impakto'   => array(
			array( __( 'Nosotros', 'impakto' ), '/nosotros/' ),
			array( __( 'Portafolio', 'impakto' ), '/portafolio/' ),
			array( __( 'Marketing inmobiliario', 'impakto' ), '/marketing-inmobiliario/' ),
			array( __( 'Blog', 'impakto' ), '/blog/' ),
			array( __( 'Contacto', 'impakto' ), '/contacto/' ),
		),
		'footer-legal'     => array(
			array( __( 'Términos de alquiler', 'impakto' ), '/terminos-de-alquiler/' ),
			array( __( 'Cancelaciones y daños', 'impakto' ), '/politica-de-cancelaciones-y-danos/' ),
			array( __( 'Tratamiento de datos', 'impakto' ), '/politica-de-tratamiento-de-datos/' ),
			array( __( 'Política de cookies', 'impakto' ), '/politica-de-cookies/' ),
			array( __( 'Términos de uso', 'impakto' ), '/terminos-de-uso/' ),
		),
		'footer-servicios' => array(),
	);
}

/**
 * Elementos de un menú: el asignado en el administrador o el de respaldo.
 *
 * @param string $ubicacion Ubicación registrada.
 * @return array<int,array{titulo:string,url:string,actual:bool}>
 */
function impakto_items_menu( $ubicacion ) {
	$items      = array();
	$ubicadas   = get_nav_menu_locations();
	$menu_items = ! empty( $ubicadas[ $ubicacion ] ) ? wp_get_nav_menu_items( $ubicadas[ $ubicacion ] ) : false;

	if ( $menu_items ) {
		foreach ( $menu_items as $item ) {
			if ( (int) $item->menu_item_parent ) {
				continue;
			}
			$items[] = array(
				'titulo' => $item->title,
				'url'    => $item->url,
				'actual' => impakto_es_actual( $item->url ),
			);
		}
		return $items;
	}

	if ( 'footer-servicios' === $ubicacion ) {
		foreach ( impakto_servicios_publicados() as $servicio ) {
			$items[] = array(
				'titulo' => get_the_title( $servicio ),
				'url'    => get_permalink( $servicio ),
				'actual' => false,
			);
		}
		return $items;
	}

	foreach ( impakto_menus_defecto()[ $ubicacion ] ?? array() as list( $titulo, $ruta ) ) {
		$url     = home_url( $ruta );
		$items[] = array(
			'titulo' => $titulo,
			'url'    => $url,
			'actual' => impakto_es_actual( $url ),
		);
	}
	return $items;
}

/**
 * ¿La URL corresponde a la página que se está viendo (o a su sección)?
 *
 * @param string $url URL.
 * @return bool
 */
function impakto_es_actual( $url ) {
	$ruta   = trailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	$actual = trailingslashit( (string) wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) );
	if ( '/' === $ruta ) {
		return is_front_page();
	}
	if ( str_starts_with( $actual, $ruta ) ) {
		return true;
	}
	return ( '/servicios/' === $ruta && is_singular( 'servicio' ) )
		|| ( '/blog/' === $ruta && ( is_singular( 'post' ) || is_category() || is_author() ) );
}

/**
 * Servicios publicados, en el orden del administrador.
 *
 * @return WP_Post[]
 */
function impakto_servicios_publicados() {
	static $servicios = null;
	if ( null === $servicios ) {
		$servicios = post_type_exists( 'servicio' ) ? get_posts(
			array(
				'post_type'      => 'servicio',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		) : array();
	}
	return $servicios;
}

/**
 * Servicios agrupados por bloque del proceso comercial (para el mega menú).
 *
 * @return array<string,array{titulo:string,servicios:WP_Post[]}>
 */
function impakto_servicios_agrupados() {
	$grupos = array();
	$nombres = function_exists( 'impakto_bloques_proceso' ) ? impakto_bloques_proceso() : array();
	foreach ( $nombres as $clave => $titulo ) {
		$grupos[ $clave ] = array(
			'titulo'    => $titulo,
			'servicios' => array(),
		);
	}
	foreach ( impakto_servicios_publicados() as $servicio ) {
		$bloque = get_post_meta( $servicio->ID, '_impakto_bloque', true );
		if ( isset( $grupos[ $bloque ] ) ) {
			$grupos[ $bloque ]['servicios'][] = $servicio;
		}
	}
	return array_filter( $grupos, static fn( $g ) => ! empty( $g['servicios'] ) );
}
