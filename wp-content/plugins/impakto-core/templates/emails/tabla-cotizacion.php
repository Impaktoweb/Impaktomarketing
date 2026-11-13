<?php
/**
 * Tabla de líneas de una cotización (se incluye desde otras plantillas).
 *
 * @var array<string,mixed> $cot Cotización.
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;
?>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:16px 0;">
	<?php foreach ( $cot['lineas'] as $linea ) : ?>
		<tr>
			<td style="padding:10px 0;border-bottom:1px solid #CFCFCB;"><strong><?php echo esc_html( $linea['concepto'] ); ?></strong><br><span style="color:#5E5E5C;font-size:13px;"><?php echo esc_html( $linea['detalle'] ); ?></span></td>
			<td align="right" style="padding:10px 0;border-bottom:1px solid #CFCFCB;white-space:nowrap;"><?php echo esc_html( impakto_cop( $linea['valor'], false ) ); ?></td>
		</tr>
	<?php endforeach; ?>
	<tr>
		<td style="padding:14px 0;font-size:17px;"><strong>Total estimado</strong> <span style="color:#5E5E5C;font-size:13px;">(IVA incluido)</span></td>
		<td align="right" style="padding:14px 0;font-size:20px;color:#B35600;white-space:nowrap;"><strong><?php echo esc_html( impakto_cop( $cot['total'] ) ); ?></strong></td>
	</tr>
</table>
<?php if ( ! empty( $cot['notas'] ) ) : ?>
	<p style="margin:0 0 16px;font-size:13px;color:#5E5E5C;"><?php echo esc_html( implode( ' · ', $cot['notas'] ) ); ?></p>
<?php endif; ?>
