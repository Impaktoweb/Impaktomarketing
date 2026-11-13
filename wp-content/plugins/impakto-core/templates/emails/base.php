<?php
/**
 * Marco HTML de los correos (600 px, tablas, estilos en línea).
 *
 * @var array<string,mixed> $datos titulo, contenido, logo.
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html lang="es-CO">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title><?php echo esc_html( $datos['titulo'] ); ?></title></head>
<body style="margin:0;padding:0;background:#F1F1EF;font-family:Montserrat,Arial,Helvetica,sans-serif;color:#373734;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F1F1EF;"><tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#FFFFFF;border-radius:12px;overflow:hidden;">
	<tr><td style="background:#111110;padding:28px 32px;">
		<?php if ( $datos['logo'] ) : ?><img src="<?php echo esc_url( $datos['logo'] ); ?>" width="140" alt="Impakto Marketing" style="display:block;border:0;"><?php endif; ?>
		<p style="margin:12px 0 0;color:#FF8000;font-size:11px;letter-spacing:3px;text-transform:uppercase;">The Marketing Factory</p>
	</td></tr>
	<tr><td style="padding:32px;font-size:15px;line-height:1.6;">
		<?php echo $datos['contenido']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- armado y escapado en cada plantilla. ?>
	</td></tr>
	<tr><td style="padding:20px 32px;background:#F7F7F5;font-size:12px;line-height:1.6;color:#5E5E5C;">
		<?php echo esc_html( impakto_ajuste( 'razon_social' ) . ' · NIT ' . impakto_ajuste( 'nit' ) ); ?><br>
		<?php echo esc_html( impakto_ajuste( 'direccion' ) . ', ' . impakto_ajuste( 'ciudad' ) . ', ' . impakto_ajuste( 'region' ) . ' · ' . impakto_ajuste( 'telefono' ) ); ?>
	</td></tr>
</table>
</td></tr></table>
</body>
</html>
