<?php
/**
 * Acceso a los datos de Impakto Core con valores de respaldo:
 * el tema sigue funcionando aunque el plugin esté desactivado.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ajuste de Impakto → Ajustes.
 *
 * @param string $clave   Clave.
 * @param string $defecto Respaldo.
 * @return string
 */
function impakto_dato( $clave, $defecto = '' ) {
	if ( function_exists( 'impakto_ajuste' ) ) {
		return (string) impakto_ajuste( $clave, $defecto );
	}
	$respaldo = array(
		'marca'        => 'Impakto Marketing',
		'lema'         => 'The Marketing Factory',
		'razon_social' => 'Impakto Marketing S.A.S.',
		'nit'          => '901.856.272-9',
		'telefono'     => '+57 312 552 5901',
		'whatsapp'     => '573125525901',
		'ciudad'       => 'Manizales',
		'region'       => 'Caldas',
	);
	return $respaldo[ $clave ] ?? $defecto;
}

/**
 * Formato COP.
 *
 * @param int  $valor  Valor.
 * @param bool $moneda Con " COP".
 * @return string
 */
function impakto_tema_cop( $valor, $moneda = true ) {
	return function_exists( 'impakto_cop' ) ? impakto_cop( $valor, $moneda ) : '$' . number_format( (float) $valor, 0, ',', '.' ) . ( $moneda ? ' COP' : '' );
}

/**
 * Precio "desde" de estudio, podcast o equipos (0 si no hay datos).
 *
 * @param string $tipo estudio|podcast|equipos.
 * @return int
 */
function impakto_tema_desde( $tipo ) {
	return function_exists( 'impakto_precio_desde' ) ? (int) impakto_precio_desde( $tipo ) : 0;
}

/**
 * Contexto de la página actual: mensaje de WhatsApp y CTA de la barra móvil.
 * Filtrable con "impakto_contexto_pagina".
 *
 * @return array{wa_mensaje:string,servicio:string,cta_texto:string,cta_url:string,cta_selector:bool}
 */
function impakto_contexto_pagina() {
	static $contexto = null;
	if ( null !== $contexto ) {
		return $contexto;
	}
	$contexto = array(
		'wa_mensaje'   => __( 'Hola Impakto 👋 Quiero hablar sobre mi proyecto.', 'impakto' ),
		'servicio'     => '',
		'cta_texto'    => __( 'Cotiza tu espacio', 'impakto' ),
		'cta_url'      => '',
		'cta_selector' => true,
	);

	$espacios = array(
		'estudio'             => __( 'Hola Impakto 👋 Quiero información sobre el alquiler del estudio.', 'impakto' ),
		'sala-de-podcast'     => __( 'Hola Impakto 👋 Quiero información sobre la sala de podcast.', 'impakto' ),
		'alquiler-de-equipos' => __( 'Hola Impakto 👋 Quiero información sobre el alquiler de equipos.', 'impakto' ),
	);

	if ( is_page( array_keys( $espacios ) ) ) {
		$slug     = get_post_field( 'post_name', get_queried_object_id() );
		$contexto = array_merge(
			$contexto,
			array(
				'wa_mensaje'   => $espacios[ $slug ],
				'servicio'     => $slug,
				'cta_texto'    => __( 'Cotizar', 'impakto' ),
				'cta_url'      => '#cotizador',
				'cta_selector' => false,
			)
		);
	} elseif ( is_singular( 'servicio' ) ) {
		$id       = get_queried_object_id();
		$mensaje  = get_post_meta( $id, '_impakto_whatsapp_mensaje', true );
		$cat      = get_post_meta( $id, '_impakto_categoria_portafolio', true );
		$contexto = array_merge(
			$contexto,
			array(
				/* translators: %s: nombre del servicio. */
				'wa_mensaje'   => $mensaje ? $mensaje : sprintf( __( 'Hola Impakto, quiero información sobre %s.', 'impakto' ), get_the_title( $id ) ),
				'servicio'     => get_post_field( 'post_name', $id ),
				'cta_texto'    => __( 'Ver proyectos', 'impakto' ),
				'cta_url'      => $cat ? home_url( '/portafolio/categoria/' . $cat . '/' ) : home_url( '/portafolio/' ),
				'cta_selector' => false,
			)
		);
	}

	if ( is_singular( 'proyecto' ) ) {
		$terminos = get_the_terms( get_queried_object_id(), 'categoria-portafolio' );
		$servicio = $terminos && ! is_wp_error( $terminos ) ? $terminos[0]->name : '';
		$contexto = array_merge(
			$contexto,
			array(
				/* translators: %s: servicio. */
				'wa_mensaje'   => $servicio ? sprintf( __( 'Hola Impakto, quiero información sobre %s.', 'impakto' ), $servicio ) : $contexto['wa_mensaje'],
				'servicio'     => $terminos && ! is_wp_error( $terminos ) ? $terminos[0]->slug : '',
				'cta_texto'    => __( 'Ver más proyectos', 'impakto' ),
				'cta_url'      => get_post_type_archive_link( 'proyecto' ),
				'cta_selector' => false,
			)
		);
	}

	$contexto = apply_filters( 'impakto_contexto_pagina', $contexto );
	return $contexto;
}

/**
 * [impakto_correo prefijo="" respaldo=""]: correo público de Impakto → Ajustes (o el texto de respaldo).
 */
add_shortcode(
	'impakto_correo',
	static function ( $atts ) {
		$atts   = shortcode_atts(
			array(
				'prefijo'  => '',
				'respaldo' => '',
			),
			$atts
		);
		$correo = impakto_dato( 'correo_publico' );
		if ( ! $correo ) {
			return esc_html( $atts['respaldo'] );
		}
		return esc_html( $atts['prefijo'] ) . '<a href="mailto:' . esc_attr( antispambot( $correo ) ) . '" data-evento="click_email" data-ubicacion="legal">' . esc_html( antispambot( $correo ) ) . '</a>';
	}
);

/**
 * [impakto_fecha_publicacion]: fecha de publicación de la página actual.
 */
add_shortcode(
	'impakto_fecha_publicacion',
	static fn() => esc_html( get_the_date( get_option( 'date_format' ) ) )
);

/**
 * [impakto_fecha_actualizacion]: "Última actualización: 13 de noviembre de 2026".
 */
add_shortcode(
	'impakto_fecha_actualizacion',
	static fn() => '<p class="legal__fecha">' . esc_html__( 'Última actualización:', 'impakto' ) . ' <time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date( get_option( 'date_format' ) ) ) . '</time></p>'
);
