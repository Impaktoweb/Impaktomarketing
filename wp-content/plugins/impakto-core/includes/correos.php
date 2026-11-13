<?php
/**
 * Correos: notificación interna a CEO y KAM y cotización HTML al cliente.
 * El transporte lo hace FluentSMTP (en local, Studio captura los correos).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Destinatarios internos (CEO + KAM). Si faltan, el correo del administrador.
 *
 * @return string[]
 */
function impakto_destinatarios_internos() {
	$correos = array_filter( array( impakto_ajuste( 'correo_ceo' ), impakto_ajuste( 'correo_kam' ) ), 'is_email' );
	return $correos ? array_values( $correos ) : array( get_option( 'admin_email' ) );
}

/**
 * Renderiza una plantilla de correo.
 *
 * @param string              $nombre Archivo en templates/emails/.
 * @param array<string,mixed> $datos  Variables.
 * @return string
 */
function impakto_plantilla_correo( $nombre, $datos ) {
	$datos['logo'] = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() . '/assets/img/logo-impakto-blanco.png' : '';
	ob_start();
	include IMPAKTO_CORE_PATH . 'templates/emails/' . $nombre . '.php';
	return (string) ob_get_clean();
}

/**
 * Envía un correo HTML.
 *
 * @param string|string[] $para     Destinatarios.
 * @param string          $asunto   Asunto.
 * @param string          $html     Cuerpo.
 * @param string          $responder Correo para "Responder a".
 * @return bool
 */
function impakto_enviar_html( $para, $asunto, $html, $responder = '' ) {
	$cabeceras = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( $responder && is_email( $responder ) ) {
		$cabeceras[] = 'Reply-To: ' . $responder;
	}
	return wp_mail( $para, $asunto, $html, $cabeceras );
}

/**
 * Nombres legibles de los tipos de solicitud.
 *
 * @param string $tipo Tipo.
 * @return string
 */
function impakto_nombre_tipo( $tipo ) {
	$nombres = array(
		'estudio'  => 'Estudio',
		'podcast'  => 'Sala de podcast',
		'equipos'  => 'Equipos',
		'contacto' => 'Contacto',
	);
	return $nombres[ $tipo ] ?? ucfirst( $tipo );
}
