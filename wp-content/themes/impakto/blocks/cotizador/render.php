<?php
/**
 * Cotizador: contenedor + datos en JSON (tarifas de Impakto → Tarifas y catálogo de Equipos).
 * La interfaz la arma assets/js/cotizador.js; sin JavaScript se ofrece WhatsApp.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'impakto_tarifas_publicas' ) ) {
	return;
}

$impakto_tipo    = $attributes['tipo'];
$impakto_datos   = impakto_tarifas_publicas();
$impakto_catalog = $impakto_datos['equipos']['categorias'] ?? array();

// Podcast: solo equipos adicionales que tienen sentido en la sala (BRIEF §6.3, sala-de-podcast.md).
if ( 'podcast' === $impakto_tipo ) {
	$impakto_catalog = array_values( array_filter( $impakto_catalog, static fn( $c ) => in_array( $c['id'], array( 'teleprompters', 'microfonos', 'iluminacion' ), true ) ) );
}

$impakto_config = array(
	'tipo'        => $impakto_tipo,
	'rest'        => esc_url_raw( rest_url( 'impakto/v1/solicitudes' ) ),
	'nonce'       => wp_create_nonce( 'wp_rest' ),
	'whatsapp'    => $impakto_datos['whatsapp'] ?? '',
	'deposito'    => (int) ( $impakto_datos['deposito_reembolsable'] ?? 0 ),
	'terminos'    => home_url( '/terminos-de-alquiler/' ),
	'privacidad'  => home_url( '/politica-de-tratamiento-de-datos/' ),
	'estudio'     => $impakto_datos['estudio'] ?? array(),
	'podcast'     => $impakto_datos['podcast'] ?? array(),
	'adicionales' => array_values( array_filter( $impakto_datos['adicionales'] ?? array(), static fn( $a ) => in_array( $impakto_tipo, $a['aplica'], true ) && 'edicion-podcast' !== $a['id'] ) ),
	'catalogo'    => $impakto_catalog,
	// Kits sugeridos (alquiler-de-equipos.md §4). Precio = suma de sus equipos. [CONFIRMAR precios de kit].
	'kits'        => 'equipos' === $impakto_tipo ? array(
		array(
			'nombre' => 'Kit entrevista',
			'items'  => array( 'sony-a7-iv', 'sony-fe-85-gm', 'dji-mic', 'nanlite-fc120c', 'heavyduty-smallrig' ),
		),
		array(
			'nombre' => 'Kit redes sociales',
			'items'  => array( 'sony-zv-e10-ii', 'hollyland-lark-m2', 'lumecube-studio-panel', 'teleprompter-smallrig' ),
		),
		array(
			'nombre' => 'Kit cine',
			'items'  => array( 'sony-a7-iv', 'dji-ronin-rs4', 'nanlite-fc500', 'lantern-120', 'feelworld-7', 'c-stand-jirafa' ),
		),
	) : array(),
);
$impakto_wa = function_exists( 'whatsapp_link' ) ? whatsapp_link( '', 'cotizador' ) : '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'cotizador cotizador--' . $impakto_tipo, 'data-cotizador' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<script type="application/json" class="cotizador__datos"><?php echo wp_json_encode( $impakto_config, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
	<div class="cotizador__cargando" aria-hidden="true"></div>
	<noscript>
		<p><?php esc_html_e( 'Para usar el cotizador activa JavaScript, o escríbenos y armamos tu cotización por WhatsApp.', 'impakto' ); ?></p>
		<p><a class="boton" href="<?php echo esc_url( $impakto_wa ); ?>"><?php esc_html_e( 'Cotizar por WhatsApp', 'impakto' ); ?></a></p>
	</noscript>
</div>
