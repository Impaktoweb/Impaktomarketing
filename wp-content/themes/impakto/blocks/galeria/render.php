<?php
/**
 * Galería: imágenes de la biblioteca con lightbox, o marcadores mientras llegan las fotos.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_ids = array_filter( array_map( 'absint', explode( ',', (string) $attributes['ids'] ) ) );
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'galeria', 'tabindex' => '0', 'aria-label' => __( 'Galería: desliza para ver más', 'impakto' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $impakto_ids ) : ?>
		<?php foreach ( $impakto_ids as $impakto_id ) : ?>
			<li><a href="<?php echo esc_url( wp_get_attachment_image_url( $impakto_id, 'full' ) ); ?>" data-lightbox><?php echo wp_get_attachment_image( $impakto_id, 'large', false, array( 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 33vw, 80vw' ) ); ?></a></li>
		<?php endforeach; ?>
	<?php else : ?>
		<?php for ( $impakto_i = 0; $impakto_i < max( 1, (int) $attributes['marcadores'] ); $impakto_i++ ) : ?>
			<li><div class="marcador-imagen" style="--ratio: 3 / 2" role="img" aria-label="<?php echo esc_attr( $attributes['etiqueta'] ); ?>"><span><?php echo esc_html( $attributes['etiqueta'] ); ?></span></div></li>
		<?php endfor; ?>
	<?php endif; ?>
</ul>
