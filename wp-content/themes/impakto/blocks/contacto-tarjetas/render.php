<?php
/**
 * Tarjetas de contacto con datos de Impakto → Ajustes y clics medibles.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_tel    = impakto_dato( 'telefono' );
$impakto_correo = impakto_dato( 'correo_publico' );
$impakto_dir    = impakto_dato( 'direccion' ) . ( impakto_dato( 'referencia' ) ? ' (' . impakto_dato( 'referencia' ) . ')' : '' ) . ', ' . impakto_dato( 'ciudad' ) . ', ' . impakto_dato( 'region' );
$impakto_hor    = impakto_dato( 'horario' );
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'contacto-tarjetas' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<li class="contacto-tarjeta contacto-tarjeta--destacada">
		<?php echo impakto_icono( 'whatsapp', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'WhatsApp', 'impakto' ); ?></h2>
		<p><?php echo esc_html( $impakto_tel ); ?></p>
		<?php echo impakto_boton_whatsapp( array( 'texto' => __( 'Escribir ahora', 'impakto' ), 'ubicacion' => 'contacto', 'clase' => 'boton boton--sm' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</li>
	<li class="contacto-tarjeta">
		<?php echo impakto_icono( 'telefono', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'Teléfono', 'impakto' ); ?></h2>
		<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $impakto_tel ) ); ?>" data-evento="click_telefono" data-ubicacion="contacto"><?php echo esc_html( $impakto_tel ); ?></a></p>
	</li>
	<?php if ( $impakto_correo ) : ?>
		<li class="contacto-tarjeta">
			<?php echo impakto_icono( 'correo', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'Correo', 'impakto' ); ?></h2>
			<p><a href="mailto:<?php echo esc_attr( antispambot( $impakto_correo ) ); ?>" data-evento="click_email" data-ubicacion="contacto"><?php echo esc_html( antispambot( $impakto_correo ) ); ?></a></p>
		</li>
	<?php endif; ?>
	<li class="contacto-tarjeta">
		<?php echo impakto_icono( 'ubicacion', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'Dirección', 'impakto' ); ?></h2>
		<p><?php echo esc_html( $impakto_dir ); ?></p>
		<?php if ( impakto_dato( 'mapa_url' ) ) : ?>
			<a class="boton boton--enlace" href="<?php echo esc_url( impakto_dato( 'mapa_url' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Cómo llegar', 'impakto' ); ?></a>
		<?php endif; ?>
	</li>
	<li class="contacto-tarjeta">
		<?php echo impakto_icono( 'reloj', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'Horario', 'impakto' ); ?></h2>
		<p><?php echo esc_html( $impakto_hor ? $impakto_hor . ' · ' . impakto_dato( 'disponibilidad' ) : impakto_dato( 'disponibilidad' ) ); ?></p>
	</li>
	<li class="contacto-tarjeta">
		<?php echo impakto_icono( 'instagram', 'icono--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<h2 class="contacto-tarjeta__titulo"><?php esc_html_e( 'Redes', 'impakto' ); ?></h2>
		<ul class="redes redes--claras">
			<?php foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'vimeo' => 'Vimeo' ) as $impakto_red => $impakto_nombre ) : ?>
				<?php if ( impakto_dato( $impakto_red ) ) : ?>
					<li><a href="<?php echo esc_url( impakto_dato( $impakto_red ) ); ?>" target="_blank" rel="noopener me"><?php echo impakto_icono( $impakto_red ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php echo esc_html( $impakto_nombre ); ?></span></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</li>
</ul>
