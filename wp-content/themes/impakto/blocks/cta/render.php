<?php
/**
 * Botón de acción: WhatsApp (mensaje precargado + medición) o "Cotiza tu espacio".
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_variantes = array(
	'primario'   => 'boton',
	'secundario' => 'boton boton--secundario',
	'enlace'     => 'boton boton--enlace',
);
$impakto_clase = $impakto_variantes[ $attributes['variante'] ] ?? 'boton';
$impakto_attrs = get_block_wrapper_attributes();

echo '<div ' . $impakto_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
if ( 'proyectos' === $attributes['tipo'] ) {
	// Portafolio filtrado por la categoría vinculada al servicio actual.
	$impakto_cat = is_singular( 'servicio' ) ? get_post_meta( get_the_ID(), '_impakto_categoria_portafolio', true ) : '';
	printf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $impakto_clase ),
		esc_url( home_url( $impakto_cat ? '/portafolio/categoria/' . $impakto_cat . '/' : '/portafolio/' ) ),
		esc_html( $attributes['texto'] ? $attributes['texto'] : __( 'Ver proyectos', 'impakto' ) )
	);
} elseif ( 'cotizar' === $attributes['tipo'] ) {
	echo impakto_boton_cotizar( $attributes['texto'], $impakto_clase ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} else {
	$impakto_args = array(
		'mensaje'   => $attributes['mensaje'],
		'ubicacion' => $attributes['ubicacion'] ? $attributes['ubicacion'] : 'cta',
		'clase'     => $impakto_clase,
		'icono'     => 'enlace' !== $attributes['variante'],
	);
	if ( $attributes['texto'] ) {
		$impakto_args['texto'] = $attributes['texto'];
	}
	echo impakto_boton_whatsapp( $impakto_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
echo '</div>';
