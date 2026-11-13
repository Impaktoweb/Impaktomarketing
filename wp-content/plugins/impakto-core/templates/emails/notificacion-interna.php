<?php
/**
 * Correo interno a CEO y KAM con la solicitud completa.
 *
 * @var array<string,mixed> $datos consecutivo, tipo, accion, cot, contacto, sel, pagina, utm.
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

$cot      = $datos['cot'];
$contacto = $datos['contacto'];
ob_start();
?>
<p style="margin:0 0 4px;color:#B35600;font-size:12px;letter-spacing:2px;text-transform:uppercase;"><?php echo esc_html( 'Nueva cotización · ' . impakto_nombre_tipo( $datos['tipo'] ) ); ?></p>
<h1 style="margin:0 0 16px;font-size:24px;color:#111110;"><?php echo esc_html( $datos['consecutivo'] ); ?></h1>
<p style="margin:0 0 16px;"><?php echo esc_html( 'whatsapp' === $datos['accion'] ? 'El cliente eligió validar disponibilidad por WhatsApp.' : 'El cliente pidió la cotización por correo.' ); ?></p>
<table role="presentation" cellspacing="0" cellpadding="0" style="font-size:14px;margin:0 0 8px;">
	<tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Nombre</td><td><strong><?php echo esc_html( $contacto['nombre'] ); ?></strong></td></tr>
	<tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Celular</td><td><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D+/', '', $contacto['celular'] ) ); ?>"><?php echo esc_html( $contacto['celular'] ); ?></a></td></tr>
	<?php if ( ! empty( $contacto['correo'] ) ) : ?><tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Correo</td><td><?php echo esc_html( $contacto['correo'] ); ?></td></tr><?php endif; ?>
	<?php $fecha = impakto_fecha_legible( $datos['sel']['fecha'] ?? '', $datos['sel']['hora'] ?? '' ); ?>
	<?php if ( $fecha ) : ?><tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Fecha</td><td><?php echo esc_html( $fecha ); ?></td></tr><?php endif; ?>
	<tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Página</td><td><?php echo esc_html( $datos['pagina'] ); ?></td></tr>
	<?php if ( ! empty( $datos['utm'] ) ) : ?><tr><td style="padding:2px 12px 2px 0;color:#5E5E5C;">Origen</td><td><?php echo esc_html( implode( ' · ', array_map( static fn( $k, $v ) => "$k=$v", array_keys( $datos['utm'] ), $datos['utm'] ) ) ); ?></td></tr><?php endif; ?>
</table>
<?php include __DIR__ . '/tabla-cotizacion.php'; ?>
<?php
$datos['contenido'] = ob_get_clean();
$datos['titulo']    = $datos['consecutivo'];
include __DIR__ . '/base.php';
