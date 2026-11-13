<?php
/**
 * POST /wp-json/impakto/v1/solicitudes — envíos del cotizador (BRIEF §7.3 y §8).
 * 1) valida antispam, 2) recalcula la cotización, 3) guarda la solicitud con su consecutivo,
 * 4) avisa a CEO y KAM, 5) si se pidió, envía la cotización al cliente y 6) devuelve el enlace de WhatsApp.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'impakto/v1',
			'/solicitudes',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'permission_callback' => '__return_true',
				'callback'            => 'impakto_rest_crear_solicitud',
				'args'                => array(
					'tipo'      => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'estudio', 'podcast', 'equipos' ),
					),
					'accion'    => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'whatsapp', 'email' ),
					),
					'seleccion' => array(
						'required' => true,
						'type'     => 'object',
					),
					'contacto'  => array(
						'required' => true,
						'type'     => 'object',
					),
					'acepta'    => array(
						'required' => true,
						'type'     => 'boolean',
					),
				),
			)
		);
	}
);

/**
 * Crea la solicitud.
 *
 * @param WP_REST_Request $req Petición.
 * @return WP_REST_Response|WP_Error
 */
function impakto_rest_crear_solicitud( WP_REST_Request $req ) {
	$valida = impakto_validar_antispam( $req );
	if ( is_wp_error( $valida ) ) {
		return $valida;
	}

	$tipo     = $req['tipo'];
	$accion   = $req['accion'];
	$c        = (array) $req['contacto'];
	$contacto = array(
		'nombre'  => sanitize_text_field( $c['nombre'] ?? '' ),
		'celular' => preg_replace( '/[^\d+ ]/', '', sanitize_text_field( $c['celular'] ?? '' ) ),
		'correo'  => sanitize_email( $c['correo'] ?? '' ),
	);
	if ( ! $req['acepta'] ) {
		return new WP_Error( 'impakto_acepta', __( 'Debes aceptar los términos de alquiler y la política de datos.', 'impakto-core' ), array( 'status' => 400 ) );
	}
	if ( mb_strlen( $contacto['nombre'] ) < 2 || strlen( preg_replace( '/\D+/', '', $contacto['celular'] ) ) < 7 ) {
		return new WP_Error( 'impakto_contacto', __( 'Escribe tu nombre y un celular válido.', 'impakto-core' ), array( 'status' => 400 ) );
	}
	if ( 'email' === $accion && ! is_email( $contacto['correo'] ) ) {
		return new WP_Error( 'impakto_correo', __( 'Escribe un correo válido para enviarte la cotización.', 'impakto-core' ), array( 'status' => 400 ) );
	}

	$s   = (array) $req['seleccion'];
	$sel = array(
		'espacio'     => sanitize_key( $s['espacio'] ?? '' ),
		'paquete'     => sanitize_key( $s['paquete'] ?? '' ),
		'horas'       => absint( $s['horas'] ?? 1 ),
		'dias'        => absint( $s['dias'] ?? 1 ),
		'fecha'       => preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) ( $s['fecha'] ?? '' ) ) ? $s['fecha'] : '',
		'hora'        => preg_match( '/^\d{1,2}:\d{2}$/', (string) ( $s['hora'] ?? '' ) ) ? $s['hora'] : '',
		'edicion'     => sanitize_key( $s['edicion'] ?? '' ),
		'equipos'     => array_map( 'absint', (array) ( $s['equipos'] ?? array() ) ),
		'adicionales' => array(
			'maquillaje' => ! empty( $s['adicionales']['maquillaje'] ),
			'productor'  => absint( $s['adicionales']['productor'] ?? 0 ),
		),
	);

	$cot = impakto_cotizar( $tipo, $sel );
	if ( is_wp_error( $cot ) ) {
		$cot->add_data( array( 'status' => 400 ) );
		return $cot;
	}

	$utm = array();
	foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid', 'entrada' ) as $k ) {
		if ( ! empty( $req['utm'][ $k ] ) ) {
			$utm[ $k ] = sanitize_text_field( $req['utm'][ $k ] );
		}
	}
	$pagina      = esc_url_raw( (string) $req['pagina'] );
	$consecutivo = impakto_siguiente_consecutivo();
	$mensaje     = impakto_mensaje_whatsapp( $cot, $consecutivo, $sel, $contacto );
	$wa_url      = 'https://wa.me/' . impakto_whatsapp_numero() . '?text=' . rawurlencode( $mensaje );

	impakto_guardar_solicitud(
		array(
			'tipo'        => 'cotizacion_' . $accion,
			'consecutivo' => $consecutivo,
			'pagina'      => $pagina,
			'utm'         => $utm,
			'datos'       => $contacto,
			'detalle'     => array(
				'servicio'  => $tipo,
				'seleccion' => $sel,
				'cotizacion' => $cot,
			),
			'total'       => $cot['total'],
		)
	);

	$datos = array(
		'consecutivo'  => $consecutivo,
		'tipo'         => $tipo,
		'accion'       => $accion,
		'cot'          => $cot,
		'contacto'     => $contacto,
		'sel'          => $sel,
		'pagina'       => $pagina,
		'utm'          => $utm,
		'whatsapp_url' => $wa_url,
		'deposito'     => (int) ( impakto_tarifas()['deposito_reembolsable'] ?? 200000 ),
	);

	// Aviso interno: [Impakto] Nueva cotización Estudio – $1.490.000 – Nombre.
	impakto_enviar_html(
		impakto_destinatarios_internos(),
		sprintf( '[Impakto] Nueva cotización %s – %s – %s', impakto_nombre_tipo( $tipo ), impakto_cop( $cot['total'], false ), $contacto['nombre'] ),
		impakto_plantilla_correo( 'notificacion-interna', $datos ),
		$contacto['correo']
	);
	$enviado_cliente = false;
	if ( 'email' === $accion ) {
		$enviado_cliente = impakto_enviar_html(
			$contacto['correo'],
			sprintf( 'Tu cotización %s · Impakto Marketing', $consecutivo ),
			impakto_plantilla_correo( 'cotizacion-cliente', $datos ),
			impakto_ajuste( 'correo_publico' )
		);
	}

	return rest_ensure_response(
		array(
			'ok'           => true,
			'consecutivo'  => $consecutivo,
			'total'        => $cot['total'],
			'whatsapp_url' => $wa_url,
			'correo'       => $enviado_cliente,
		)
	);
}
