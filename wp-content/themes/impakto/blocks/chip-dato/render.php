<?php
/**
 * Chip de dato: etiqueta oscura | valor claro.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_clase = 'chip-dato' . ( $attributes['grande'] ? ' chip-dato--grande' : '' );
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => $impakto_clase ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<span class="chip-dato__etiqueta"><?php echo esc_html( $attributes['etiqueta'] ); ?></span>
	<span class="chip-dato__valor"><?php echo wp_kses( $attributes['valor'], array( 'strong' => array() ) ); ?></span>
</p>
