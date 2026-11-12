<?php
/**
 * Footer: logo blanco + lema, columnas, NAP, redes y línea legal.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_columnas = array(
	'footer-servicios' => __( 'Servicios', 'impakto' ),
	'footer-espacios'  => __( 'Espacios y equipos', 'impakto' ),
	'footer-impakto'   => __( 'Impakto', 'impakto' ),
	'footer-legal'     => __( 'Legal', 'impakto' ),
);
$impakto_redes    = array(
	'instagram' => 'Instagram',
	'facebook'  => 'Facebook',
	'linkedin'  => 'LinkedIn',
	'vimeo'     => 'Vimeo',
);
$impakto_tel      = impakto_dato( 'telefono' );
$impakto_correo   = impakto_dato( 'correo_publico' );
$impakto_horario  = impakto_dato( 'horario' );
$impakto_mapa     = impakto_dato( 'mapa_url' );
?>
<footer class="sitio-footer es-oscuro">
	<div class="contenedor">
		<div class="sitio-footer__marca">
			<div>
				<a class="sitio-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( IMPAKTO_TEMA_URL . '/assets/img/logo-impakto-blanco.png' ); ?>" width="160" height="50" alt="<?php echo esc_attr( impakto_dato( 'marca' ) ); ?>" loading="lazy">
				</a>
				<p class="sitio-footer__lema">The <strong>Marketing</strong> <span>Factory</span></p>
			</div>
			<div class="fila-botones">
				<?php
				echo impakto_boton_whatsapp( array( 'ubicacion' => 'footer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo impakto_boton_cotizar( '', 'boton boton--secundario' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>

		<div class="sitio-footer__columnas">
			<div class="sitio-footer__col sitio-footer__nap">
				<h2><?php echo esc_html( impakto_dato( 'razon_social' ) ); ?></h2>
				<address>
					<p><?php echo impakto_icono( 'ubicacion' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php
						echo esc_html( impakto_dato( 'direccion' ) . ( impakto_dato( 'referencia' ) ? ' (' . impakto_dato( 'referencia' ) . ')' : '' ) );
						?>
						<br><span class="sangria"><?php echo esc_html( impakto_dato( 'ciudad' ) . ', ' . impakto_dato( 'region' ) . ', Colombia' ); ?></span>
					</p>
					<p><?php echo impakto_icono( 'telefono' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $impakto_tel ) ); ?>" data-evento="click_telefono" data-ubicacion="footer"><?php echo esc_html( $impakto_tel ); ?></a></p>
					<?php if ( $impakto_correo ) : ?>
						<p><?php echo impakto_icono( 'correo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( antispambot( $impakto_correo ) ); ?>" data-evento="click_email" data-ubicacion="footer"><?php echo esc_html( antispambot( $impakto_correo ) ); ?></a></p>
					<?php endif; ?>
					<p><?php echo impakto_icono( 'reloj' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $impakto_horario ? $impakto_horario . ' · ' . impakto_dato( 'disponibilidad' ) : impakto_dato( 'disponibilidad' ) ); ?></p>
				</address>
				<?php if ( $impakto_mapa ) : ?>
					<a class="boton boton--enlace" href="<?php echo esc_url( $impakto_mapa ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Cómo llegar', 'impakto' ); ?></a>
				<?php endif; ?>
			</div>

			<?php foreach ( $impakto_columnas as $impakto_ubicacion => $impakto_titulo ) : ?>
				<?php $impakto_items = impakto_items_menu( $impakto_ubicacion ); ?>
				<?php if ( $impakto_items ) : ?>
					<nav class="sitio-footer__col" aria-label="<?php echo esc_attr( $impakto_titulo ); ?>">
						<h2><?php echo esc_html( $impakto_titulo ); ?></h2>
						<ul>
							<?php foreach ( $impakto_items as $impakto_item ) : ?>
								<li><a href="<?php echo esc_url( $impakto_item['url'] ); ?>"><?php echo esc_html( $impakto_item['titulo'] ); ?></a></li>
							<?php endforeach; ?>
							<?php if ( 'footer-legal' === $impakto_ubicacion ) : ?>
								<li><button type="button" data-abre-cookies><?php esc_html_e( 'Preferencias de cookies', 'impakto' ); ?></button></li>
							<?php endif; ?>
						</ul>
					</nav>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<div class="sitio-footer__legal">
			<p>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . impakto_dato( 'razon_social' ) . ' · NIT ' . impakto_dato( 'nit' ) ); ?> · <span class="sitio-footer__hashtag"><?php echo esc_html( impakto_dato( 'hashtag', '#Creaciónqueimpakta' ) ); ?></span></p>
			<ul class="redes">
				<?php foreach ( $impakto_redes as $impakto_red => $impakto_nombre ) : ?>
					<?php $impakto_url = impakto_dato( $impakto_red ); ?>
					<?php if ( $impakto_url ) : ?>
						<li><a href="<?php echo esc_url( $impakto_url ); ?>" target="_blank" rel="noopener me"><?php echo impakto_icono( $impakto_red ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php echo esc_html( $impakto_nombre ); ?></span></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>
