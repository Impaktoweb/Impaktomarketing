<?php
/**
 * POST /wp-json/impakto/v1/contacto — formulario de la página Contacto (BRIEF §8).
 * Guarda la solicitud, avisa a CEO y KAM y devuelve la URL de /gracias/.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'impakto/v1',
			'/contacto',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => '__return_true',
				'callback'            => 'impakto_rest_contacto',
			)
		);
	}
);

/**
 * Procesa el formulario de contacto.
 *
 * @param WP_REST_Request $req Petición.
 * @return WP_REST_Response|WP_Error
 */
function impakto_rest_contacto( WP_REST_Request $req ) {
	$valida = impakto_validar_antispam( $req );
	if ( is_wp_error( $valida ) ) {
		return $valida;
	}
	$datos = array(
		'nombre'   => sanitize_text_field( (string) $req['nombre'] ),
		'empresa'  => sanitize_text_field( (string) $req['empresa'] ),
		'correo'   => sanitize_email( (string) $req['correo'] ),
		'celular'  => preg_replace( '/[^\d+ ]/', '', sanitize_text_field( (string) $req['celular'] ) ),
		'servicio' => sanitize_text_field( (string) $req['servicio'] ),
		'mensaje'  => sanitize_textarea_field( (string) $req['mensaje'] ),
	);
	$errores = array();
	if ( mb_strlen( $datos['nombre'] ) < 2 ) {
		$errores['nombre'] = __( 'Escribe tu nombre.', 'impakto-core' );
	}
	if ( ! is_email( $datos['correo'] ) ) {
		$errores['correo'] = __( 'Escribe un correo válido.', 'impakto-core' );
	}
	if ( strlen( preg_replace( '/\D+/', '', $datos['celular'] ) ) < 7 ) {
		$errores['celular'] = __( 'Escribe un celular válido.', 'impakto-core' );
	}
	if ( ! $req['acepta'] ) {
		$errores['acepta'] = __( 'Debes aceptar la política de tratamiento de datos.', 'impakto-core' );
	}
	if ( $errores ) {
		return new WP_Error( 'impakto_campos', implode( ' ', $errores ), array( 'status' => 400, 'campos' => $errores ) );
	}

	$utm = array();
	foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid', 'entrada' ) as $k ) {
		if ( ! empty( $req['utm'][ $k ] ) ) {
			$utm[ $k ] = sanitize_text_field( $req['utm'][ $k ] );
		}
	}
	$pagina = esc_url_raw( (string) $req['pagina'] );
	impakto_guardar_solicitud(
		array(
			'tipo'   => 'contacto',
			'pagina' => $pagina,
			'utm'    => $utm,
			'datos'  => $datos,
		)
	);
	impakto_enviar_html(
		impakto_destinatarios_internos(),
		sprintf( '[Impakto] Nuevo contacto – %s – %s', $datos['servicio'] ? $datos['servicio'] : 'General', $datos['nombre'] ),
		impakto_plantilla_correo(
			'contacto-interna',
			array(
				'datos'  => $datos,
				'pagina' => $pagina,
				'utm'    => $utm,
			)
		),
		$datos['correo']
	);
	return rest_ensure_response(
		array(
			'ok'      => true,
			'gracias' => home_url( '/gracias/' ),
		)
	);
}
