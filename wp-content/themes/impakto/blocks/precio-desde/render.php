<?php
/**
 * Chip "Desde $120.000 COP por hora + IVA" con el valor de Impakto → Tarifas.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_precio = impakto_tema_desde( $attributes['tipo'] );
if ( ! $impakto_precio ) {
	return;
}
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => 'chip-dato chip-dato--grande' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<span class="chip-dato__etiqueta"><?php esc_html_e( 'Desde', 'impakto' ); ?></span>
	<span class="chip-dato__valor"><?php echo esc_html( impakto_tema_cop( $impakto_precio ) ); ?>&nbsp;<small><?php esc_html_e( 'por hora + IVA', 'impakto' ); ?></small></span>
</p>
