<?php
/**
 * Cotización al cliente: resumen, condiciones clave, enlace a términos y botón de WhatsApp.
 *
 * @var array<string,mixed> $datos consecutivo, cot, contacto, whatsapp_url, deposito.
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

$cot = $datos['cot'];
ob_start();
?>
<p style="margin:0 0 4px;color:#B35600;font-size:12px;letter-spacing:2px;text-transform:uppercase;">Tu cotización · <?php echo esc_html( $datos['consecutivo'] ); ?></p>
<h1 style="margin:0 0 16px;font-size:24px;line-height:1.25;color:#111110;">Hola, <?php echo esc_html( $datos['contacto']['nombre'] ); ?></h1>
<p style="margin:0 0 8px;">Este es el resumen de tu cotización. Es una estimación: todo el servicio se puede personalizar según los equipos y el tiempo que necesites. Confirmamos disponibilidad y te sugerimos lo que haga falta.</p>
<?php include __DIR__ . '/tabla-cotizacion.php'; ?>
<h2 style="margin:24px 0 8px;font-size:16px;color:#111110;">Condiciones clave</h2>
<ul style="margin:0 0 16px;padding-left:18px;color:#373734;font-size:14px;">
	<li>Reserva con al menos un día de anticipación; confirmas con el anticipo indicado en los términos de alquiler.</li>
	<li>Depósito de seguridad reembolsable de <?php echo esc_html( impakto_cop( $datos['deposito'] ) ); ?>.</li>
	<li>Puedes reprogramar sin penalidad hasta 24 horas antes. Tienes 30 minutos de tolerancia de llegada.</li>
	<li>Los valores no incluyen IVA.</li>
</ul>
<p style="margin:0 0 24px;font-size:14px;"><a href="<?php echo esc_url( home_url( '/terminos-de-alquiler/' ) ); ?>" style="color:#B35600;">Lee los términos de alquiler completos</a></p>
<table role="presentation" cellspacing="0" cellpadding="0"><tr><td style="border-radius:999px;background:#FF8000;">
	<a href="<?php echo esc_attr( impakto_url_whatsapp_segura( $datos['whatsapp_url'] ) ); ?>" style="display:inline-block;padding:14px 28px;color:#111110;font-weight:bold;text-decoration:none;font-size:15px;">Validar disponibilidad por WhatsApp</a>
</td></tr></table>
<?php
$datos['contenido'] = ob_get_clean();
$datos['titulo']    = 'Tu cotización ' . $datos['consecutivo'];
include __DIR__ . '/base.php';
