<?php
/**
 * Enlaces de WhatsApp: una sola función genera todos (BRIEF §12).
 * Los atributos data-evento los lee tracking.js (Fase 5) para click_whatsapp / Contact.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'whatsapp_link' ) ) {
	/**
	 * URL de WhatsApp con mensaje precargado.
	 *
	 * @param string $mensaje   Mensaje (vacío = el de la página actual).
	 * @param string $ubicacion header|flotante|servicio|cta|contacto|… (se usa en la analítica).
	 * @return string
	 */
	function whatsapp_link( $mensaje = '', $ubicacion = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- firma del BRIEF.
		$numero  = function_exists( 'impakto_whatsapp_numero' ) ? impakto_whatsapp_numero() : impakto_dato( 'whatsapp', '573125525901' );
		$mensaje = '' !== $mensaje ? $mensaje : impakto_contexto_pagina()['wa_mensaje'];
		return 'https://wa.me/' . rawurlencode( $numero ) . '?text=' . rawurlencode( $mensaje );
	}
}

/**
 * Botón de WhatsApp completo, con los datos para la medición.
 *
 * @param array<string,string> $args texto, mensaje, ubicacion, servicio, clase, icono (bool).
 * @return string
 */
function impakto_boton_whatsapp( $args = array() ) {
	$contexto = impakto_contexto_pagina();
	$args     = wp_parse_args(
		$args,
		array(
			'texto'     => __( 'Hablemos por WhatsApp', 'impakto' ),
			'mensaje'   => '',
			'ubicacion' => 'cta',
			'servicio'  => $contexto['servicio'],
			'clase'     => 'boton',
			'icono'     => true,
		)
	);
	return sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="noopener" data-evento="click_whatsapp" data-ubicacion="%3$s" data-servicio="%4$s">%5$s<span>%6$s</span><span class="screen-reader-text"> %7$s</span></a>',
		esc_attr( $args['clase'] ),
		esc_url( whatsapp_link( $args['mensaje'], $args['ubicacion'] ) ),
		esc_attr( $args['ubicacion'] ),
		esc_attr( $args['servicio'] ),
		$args['icono'] ? impakto_icono( 'whatsapp' ) : '',
		esc_html( $args['texto'] ),
		esc_html__( '(abre WhatsApp en una pestaña nueva)', 'impakto' )
	);
}

/**
 * Botón que abre el selector "Cotiza tu espacio".
 *
 * @param string $texto Texto.
 * @param string $clase Clases.
 * @return string
 */
function impakto_boton_cotizar( $texto = '', $clase = 'boton' ) {
	return sprintf(
		'<button type="button" class="%1$s" data-abre-selector aria-haspopup="dialog" aria-controls="selector-espacio">%2$s</button>',
		esc_attr( $clase ),
		esc_html( $texto ? $texto : __( 'Cotiza tu espacio', 'impakto' ) )
	);
}
