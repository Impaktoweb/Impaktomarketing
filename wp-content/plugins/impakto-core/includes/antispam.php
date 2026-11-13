<?php
/**
 * Antispam de formularios y cotizadores (BRIEF §7.3): honeypot, tiempo mínimo de 3 s,
 * máximo 5 envíos por IP por hora (IP guardada como hash) y nonce de WordPress.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valida un envío.
 *
 * @param WP_REST_Request $req Petición.
 * @return true|WP_Error
 */
function impakto_validar_antispam( $req ) {
	if ( ! wp_verify_nonce( (string) $req->get_header( 'x_wp_nonce' ), 'wp_rest' ) ) {
		return new WP_Error( 'impakto_nonce', __( 'La página caducó. Recárgala e inténtalo de nuevo.', 'impakto-core' ), array( 'status' => 403 ) );
	}
	if ( '' !== trim( (string) $req->get_param( 'sitio_web' ) ) ) { // Honeypot.
		return new WP_Error( 'impakto_spam', __( 'No pudimos enviar tu solicitud.', 'impakto-core' ), array( 'status' => 400 ) );
	}
	$segundos = absint( $req->get_param( 'tiempo' ) );
	if ( $segundos < 3 ) {
		return new WP_Error( 'impakto_rapido', __( 'Revisa tus datos y vuelve a enviar.', 'impakto-core' ), array( 'status' => 400 ) );
	}
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$clave = 'impakto_rl_' . md5( wp_salt() . $ip );
	$conteo = (int) get_transient( $clave );
	if ( $conteo >= 5 ) {
		return new WP_Error( 'impakto_limite', __( 'Recibimos varias solicitudes desde tu conexión. Escríbenos por WhatsApp y te atendemos de inmediato.', 'impakto-core' ), array( 'status' => 429 ) );
	}
	set_transient( $clave, $conteo + 1, HOUR_IN_SECONDS );
	return true;
}
