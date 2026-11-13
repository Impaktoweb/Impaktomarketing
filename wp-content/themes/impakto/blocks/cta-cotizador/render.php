<?php
/**
 * CTA contextual dentro de un artículo o página: cotizador o WhatsApp.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_destinos = array(
	'estudio'  => array( '/estudio/#cotizador', __( 'Elige formato, horas y equipos, y conoce tu valor al instante.', 'impakto' ), __( 'Cotizar estudio', 'impakto' ) ),
	'podcast'  => array( '/sala-de-podcast/#cotizador', __( 'Arma tu cotización en 1 minuto y valida disponibilidad por WhatsApp.', 'impakto' ), __( 'Cotizar sala de podcast', 'impakto' ) ),
	'equipos'  => array( '/alquiler-de-equipos/#cotizador', __( 'Arma tu kit y valida disponibilidad por WhatsApp.', 'impakto' ), __( 'Ver catálogo', 'impakto' ) ),
	'whatsapp' => array( '', __( 'Cuéntanos tu objetivo y te decimos qué tiene más impacto para tu negocio hoy.', 'impakto' ), __( 'Hablemos por WhatsApp', 'impakto' ) ),
);
list( $impakto_url, $impakto_texto, $impakto_boton ) = $impakto_destinos[ $attributes['destino'] ] ?? $impakto_destinos['estudio'];
$impakto_texto = $attributes['texto'] ? $attributes['texto'] : $impakto_texto;
$impakto_boton = $attributes['boton'] ? $attributes['boton'] : $impakto_boton;
?>
<aside <?php echo get_block_wrapper_attributes( array( 'class' => 'cta-banda es-oscuro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="cta-banda__texto"><?php echo esc_html( $impakto_texto ); ?></p>
	<?php
	if ( 'whatsapp' === $attributes['destino'] ) {
		echo impakto_boton_whatsapp( array( 'texto' => $impakto_boton, 'ubicacion' => 'articulo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		printf( '<a class="boton" href="%s">%s</a>', esc_url( home_url( $impakto_url ) ), esc_html( $impakto_boton ) );
	}
	?>
</aside>
