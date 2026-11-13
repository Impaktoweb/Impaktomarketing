<?php
/**
 * Precios desde por categoría de equipos (por jornada).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'impakto_equipo_precio_minimo' ) ) {
	return;
}
$impakto_cats = array(
	'camaras'     => __( 'Cámaras', 'impakto' ),
	'lentes'      => __( 'Lentes', 'impakto' ),
	'iluminacion' => __( 'Iluminación', 'impakto' ),
	'microfonos'  => __( 'Micrófonos', 'impakto' ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'fila-chips' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $impakto_cats as $impakto_slug => $impakto_nombre ) : ?>
		<?php $impakto_min = impakto_equipo_precio_minimo( $impakto_slug ); ?>
		<?php if ( $impakto_min ) : ?>
			<p class="chip-dato"><span class="chip-dato__etiqueta"><?php echo esc_html( $impakto_nombre ); ?></span><span class="chip-dato__valor"><?php echo esc_html( sprintf( /* translators: %s: precio. */ __( 'desde %s', 'impakto' ), impakto_tema_cop( $impakto_min, false ) ) ); ?></span></p>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<p class="is-style-nota chips-equipos__nota"><?php esc_html_e( 'Precios por jornada, con IVA incluido.', 'impakto' ); ?></p>
