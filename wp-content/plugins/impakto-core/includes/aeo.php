<?php
/**
 * AEO (BRIEF §13): /llms.txt generado desde los ajustes y robots.txt con los rastreadores de IA.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sirve /llms.txt.
 */
add_action(
	'parse_request',
	static function () {
		$ruta = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$base = rtrim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $base . '/llms.txt' !== $ruta ) {
			return;
		}
		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo impakto_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- texto plano.
		exit;
	},
	0
);

/**
 * Contenido de /llms.txt (Markdown).
 *
 * @return string
 */
function impakto_llms_txt() {
	$a   = impakto_ajustes();
	$l   = array();
	$l[] = '# ' . $a['marca'] . ' — ' . $a['lema'];
	$l[] = '';
	$l[] = '> Agencia de marketing de Manizales (Caldas, Colombia) que acompaña todo el proceso comercial: investigación, marca y estrategia, comunicación audiovisual, marketing digital, generación de clientes potenciales y ventas. Tiene estudio audiovisual y sala de podcast propios, y alquila equipos audiovisuales.';
	$l[] = '';
	$l[] = '## Empresa';
	$l[] = '- Razón social: ' . $a['razon_social'] . ' (NIT ' . $a['nit'] . ')';
	$l[] = '- Dirección: ' . $a['direccion'] . ( $a['referencia'] ? ' (' . $a['referencia'] . ')' : '' ) . ', ' . $a['ciudad'] . ', ' . $a['region'] . ', Colombia';
	$l[] = '- WhatsApp y teléfono: ' . $a['telefono'] . ' (https://wa.me/' . impakto_whatsapp_numero() . ')';
	if ( $a['correo_publico'] ) {
		$l[] = '- Correo: ' . $a['correo_publico'];
	}
	if ( $a['horario'] ) {
		$l[] = '- Horario de atención: ' . $a['horario'];
	}
	$l[] = '- ' . $a['disponibilidad'];
	$l[] = '- Áreas atendidas: Manizales, Caldas, Eje Cafetero, Colombia y clientes en el exterior.';
	$l[] = '- Sitio: ' . home_url( '/' );
	$redes = array_filter( array( $a['instagram'], $a['facebook'], $a['linkedin'], $a['vimeo'] ) );
	if ( $redes ) {
		$l[] = '- Redes: ' . implode( ' · ', $redes );
	}
	$l[] = '';

	$l[] = '## Espacios y alquiler (precios en COP con IVA incluido)';
	$t   = impakto_tarifas();
	foreach ( $t['estudio']['espacios'] ?? array() as $e ) {
		$l[] = '- Estudio audiovisual, ' . $e['nombre'] . ' (' . $e['descripcion'] . '): 1 hora ' . impakto_cop( $e['bloques'][1] ) . ' · 4 horas ' . impakto_cop( $e['bloques'][4] ) . ' · 8 horas ' . impakto_cop( $e['bloques'][8] ) . '. ' . home_url( '/estudio/' );
	}
	foreach ( $t['podcast']['paquetes'] ?? array() as $p ) {
		$l[] = '- Sala de podcast (24 m², insonorizada), ' . $p['nombre'] . ': ' . impakto_cop( $p['precio_hora'] ) . ' por hora. ' . home_url( '/sala-de-podcast/' );
	}
	$cam = impakto_equipo_precio_minimo( 'camaras' );
	if ( $cam ) {
		$l[] = '- Alquiler de equipos audiovisuales (cámaras Sony, lentes, drones DJI con piloto, iluminación Nanlite, micrófonos, estabilizadores y más): cámaras desde ' . impakto_cop( $cam ) . ' por jornada. ' . home_url( '/alquiler-de-equipos/' );
	}
	$l[] = '- Depósito de seguridad reembolsable: ' . impakto_cop( (int) ( $t['deposito_reembolsable'] ?? 0 ) ) . '. Términos: ' . home_url( '/terminos-de-alquiler/' );
	$l[] = '';

	$l[]       = '## Servicios';
	$servicios = get_posts(
		array(
			'post_type'      => 'servicio',
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	foreach ( $servicios as $s ) {
		$l[] = '- [' . get_the_title( $s ) . '](' . get_permalink( $s ) . '): ' . wp_strip_all_tags( get_the_excerpt( $s ) );
	}
	$l[] = '';

	$l[]       = '## Casos y portafolio';
	$proyectos = get_posts(
		array(
			'post_type'      => 'proyecto',
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	foreach ( $proyectos as $p ) {
		$l[] = '- [' . get_the_title( $p ) . '](' . get_permalink( $p ) . '): ' . wp_strip_all_tags( get_the_excerpt( $p ) );
	}
	$l[] = '';

	$l[] = '## Artículos';
	foreach ( get_posts( array( 'posts_per_page' => 20 ) ) as $p ) {
		$l[] = '- [' . get_the_title( $p ) . '](' . get_permalink( $p ) . ')';
	}
	$l[] = '';
	$l[] = '## Páginas';
	foreach ( array( 'nosotros' => 'Nosotros', 'contacto' => 'Contacto', 'portafolio' => 'Portafolio', 'blog' => 'Blog', 'politica-de-tratamiento-de-datos' => 'Política de tratamiento de datos' ) as $slug => $nombre ) {
		$l[] = '- [' . $nombre . '](' . home_url( '/' . $slug . '/' ) . ')';
	}
	return implode( "\n", $l ) . "\n";
}

/**
 * robots.txt: permite a buscadores y rastreadores de IA, bloquea /wp-admin/ (salvo admin-ajax) e indica el sitemap.
 * Si el sitio no es público (staging), se respeta el bloqueo de WordPress.
 */
add_filter(
	'robots_txt',
	static function ( $salida, $publico ) {
		if ( ! $publico ) {
			return $salida;
		}
		$ruta  = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$bots  = array( 'Googlebot', 'Bingbot', 'GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-SearchBot', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended' );
		$lines = array( 'User-agent: *', 'Disallow: ' . $ruta . 'wp-admin/', 'Allow: ' . $ruta . 'wp-admin/admin-ajax.php', '' );
		foreach ( $bots as $bot ) {
			$lines[] = 'User-agent: ' . $bot;
			$lines[] = 'Allow: /';
			$lines[] = 'Disallow: ' . $ruta . 'wp-admin/';
			$lines[] = 'Allow: ' . $ruta . 'wp-admin/admin-ajax.php';
			$lines[] = '';
		}
		$lines[] = 'Sitemap: ' . home_url( '/sitemap_index.xml' );
		$lines[] = '';
		$lines[] = '# Resumen para asistentes de IA: ' . home_url( '/llms.txt' );
		return implode( "\n", $lines ) . "\n";
	},
	9999,
	2
);
