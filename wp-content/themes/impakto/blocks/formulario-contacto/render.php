<?php
/**
 * Formulario de contacto (nombre*, empresa, correo*, celular*, servicio, mensaje, aceptación*).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_opciones = array_merge(
	function_exists( 'impakto_servicios_publicados' ) ? wp_list_pluck( impakto_servicios_publicados(), 'post_title' ) : array(),
	array( __( 'Alquiler del estudio', 'impakto' ), __( 'Sala de podcast', 'impakto' ), __( 'Alquiler de equipos', 'impakto' ), __( 'Otro', 'impakto' ) )
);
$impakto_cfg = array(
	'rest'  => esc_url_raw( rest_url( 'impakto/v1/contacto' ) ),
	'nonce' => wp_create_nonce( 'wp_rest' ),
);
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => 'formulario' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-formulario-contacto data-config="<?php echo esc_attr( wp_json_encode( $impakto_cfg ) ); ?>" novalidate>
	<div class="formulario__fila">
		<div class="campo"><label class="campo__etiqueta" for="fc-nombre"><?php esc_html_e( 'Nombre', 'impakto' ); ?> <span aria-hidden="true">*</span></label><input class="campo__input" id="fc-nombre" name="nombre" autocomplete="name" required aria-describedby="fc-nombre-error"><p class="campo__error" id="fc-nombre-error" hidden></p></div>
		<div class="campo"><label class="campo__etiqueta" for="fc-empresa"><?php esc_html_e( 'Empresa', 'impakto' ); ?></label><input class="campo__input" id="fc-empresa" name="empresa" autocomplete="organization"></div>
	</div>
	<div class="formulario__fila">
		<div class="campo"><label class="campo__etiqueta" for="fc-correo"><?php esc_html_e( 'Correo', 'impakto' ); ?> <span aria-hidden="true">*</span></label><input class="campo__input" id="fc-correo" name="correo" type="email" autocomplete="email" required aria-describedby="fc-correo-error"><p class="campo__error" id="fc-correo-error" hidden></p></div>
		<div class="campo"><label class="campo__etiqueta" for="fc-celular"><?php esc_html_e( 'Celular', 'impakto' ); ?> <span aria-hidden="true">*</span></label><input class="campo__input" id="fc-celular" name="celular" type="tel" inputmode="tel" autocomplete="tel" required aria-describedby="fc-celular-error"><p class="campo__error" id="fc-celular-error" hidden></p></div>
	</div>
	<div class="campo"><label class="campo__etiqueta" for="fc-servicio"><?php esc_html_e( 'Servicio de interés', 'impakto' ); ?></label>
		<select class="campo__input" id="fc-servicio" name="servicio"><option value=""><?php esc_html_e( 'Elige una opción', 'impakto' ); ?></option>
			<?php foreach ( $impakto_opciones as $impakto_o ) : ?>
				<option><?php echo esc_html( $impakto_o ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="campo"><label class="campo__etiqueta" for="fc-mensaje"><?php esc_html_e( 'Mensaje', 'impakto' ); ?></label><textarea class="campo__input" id="fc-mensaje" name="mensaje" rows="5"></textarea></div>
	<div class="campo campo--oculto" aria-hidden="true"><label for="fc-web">Sitio web</label><input id="fc-web" name="sitio_web" tabindex="-1" autocomplete="off"></div>
	<label class="casilla"><input type="checkbox" name="acepta" required aria-describedby="fc-acepta-error"><span><?php echo wp_kses( sprintf( /* translators: %s: URL política. */ __( 'Acepto la <a href="%s" target="_blank" rel="noopener">política de tratamiento de datos</a>.', 'impakto' ), esc_url( home_url( '/politica-de-tratamiento-de-datos/' ) ) ), array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) ) ); ?> <span aria-hidden="true">*</span></span></label>
	<p class="campo__error" id="fc-acepta-error" hidden></p>
	<p class="formulario__error" role="alert" hidden></p>
	<button type="submit" class="boton"><?php esc_html_e( 'Enviar', 'impakto' ); ?></button>
	<p class="is-style-nota"><?php esc_html_e( '* Campos obligatorios. Te respondemos por WhatsApp o por correo.', 'impakto' ); ?></p>
</form>
