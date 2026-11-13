<?php
/**
 * Mapa con clic: no carga Google Maps (ni sus cookies) hasta que la persona pulsa "Ver mapa".
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_q   = impakto_dato( 'latitud' ) && impakto_dato( 'longitud' ) ? impakto_dato( 'latitud' ) . ',' . impakto_dato( 'longitud' ) : 'Impakto Marketing, ' . impakto_dato( 'direccion' ) . ', ' . impakto_dato( 'ciudad' ) . ', ' . impakto_dato( 'region' );
$impakto_src = 'https://maps.google.com/maps?q=' . rawurlencode( $impakto_q ) . '&z=16&output=embed';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'mapa' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-mapa="<?php echo esc_url( $impakto_src ); ?>">
	<?php if ( $attributes['imagen'] ) : ?>
		<?php echo wp_get_attachment_image( $attributes['imagen'], 'full', false, array( 'class' => 'mapa__imagen', 'loading' => 'lazy', 'alt' => '' ) ); ?>
	<?php else : ?>
		<div class="mapa__imagen marcador-imagen" style="--ratio: 16 / 7" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="mapa__capa">
		<p><?php echo esc_html( impakto_dato( 'direccion' ) . ', ' . impakto_dato( 'ciudad' ) ); ?></p>
		<button type="button" class="boton"><?php echo impakto_icono( 'ubicacion' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Ver mapa', 'impakto' ); ?></span></button>
		<p class="is-style-nota"><?php esc_html_e( 'Al abrir el mapa se carga Google Maps, que puede usar cookies.', 'impakto' ); ?></p>
	</div>
</div>
