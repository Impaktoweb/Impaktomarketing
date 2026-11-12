<?php
/**
 * Íconos de línea (1,5 px, currentColor) en SVG en línea
 * (funcionan igual en el sitio y dentro del iframe del editor).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trazos de cada ícono (viewBox 0 0 24 24).
 *
 * @return array<string,string>
 */
function impakto_iconos_trazos() {
	return array(
		'flecha-diagonal' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'flecha'          => '<path d="M4 12h16M14 6l6 6-6 6"/>',
		'menu'            => '<path d="M4 8h16M4 16h16"/>',
		'cerrar'          => '<path d="M6 6l12 12M18 6 6 18"/>',
		'chevron'         => '<path d="m6 9 6 6 6-6"/>',
		'mas'             => '<path d="M12 5v14M5 12h14"/>',
		'menos'           => '<path d="M5 12h14"/>',
		'check'           => '<path d="m5 12 4 4L19 7"/>',
		'whatsapp'        => '<path d="M20.5 11.6a8.5 8.5 0 0 1-12.4 7.6L3.5 20.5l1.3-4.4a8.5 8.5 0 1 1 15.7-4.5Z"/><path d="M9 8.2c.3-.5.7-.5 1-.5.3 0 .6.6.9 1.4.2.5-.4 1-.6 1.3.5 1.3 1.6 2.4 2.9 2.9.3-.2.8-.8 1.3-.6.8.3 1.4.6 1.4.9 0 .3 0 .7-.5 1-.6.4-1.4.6-2.3.3a8 8 0 0 1-4.3-4.3c-.3-.9-.1-1.7.2-2.4Z"/>',
		'telefono'        => '<path d="M5 4h3l2 5-2.5 1.5a11 11 0 0 0 6 6L15 14l5 2v3a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
		'correo'          => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>',
		'ubicacion'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'reloj'           => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'instagram'       => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
		'facebook'        => '<path d="M14 8.5h3V5h-3a4 4 0 0 0-4 4v2.5H7.5V15H10v6h3.5v-6H16l.7-3.5h-3.2V9a.5.5 0 0 1 .5-.5Z"/>',
		'linkedin'        => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10.5V17M8 7.5v.01M12 17v-6.5M12 13.5a2.5 2.5 0 0 1 5 0V17"/>',
		'vimeo'           => '<path d="M3 8.6 4 10s2-1.5 2.6-.8c.7.7 2.6 8 3.3 9.4.6 1.2 2.3 2.7 4.1-.2C15.9 15.5 21.6 9 20.9 5.6 20.2 2.3 16 3.4 14.5 6.6c2.4-1.4 3.6.6 2.4 2.8-1.2 2.3-2.4 3.8-3 3.8-.5 0-1-1.5-1.7-4.3C11.5 6.1 11.5 2 8.7 2.6 6.8 3 3 8.6 3 8.6Z"/>',
		'camara'          => '<rect x="3" y="7" width="13" height="10" rx="2"/><path d="m16 10.5 5-3v9l-5-3"/>',
		'microfono'       => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5.5 11a6.5 6.5 0 0 0 13 0M12 17.5V21M8.5 21h7"/>',
		'sinfin'          => '<path d="M4 4v9a7 7 0 0 0 7 7h9M4 4h16"/>',
		'luz'             => '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3Z"/>',
		'play'            => '<path d="M8 5v14l11-7Z"/>',
		'calendario'      => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
	);
}

/**
 * Marcado de un ícono.
 *
 * @param string $nombre Id del ícono.
 * @param string $clase  Clases extra.
 * @param string $titulo Texto accesible (vacío = decorativo).
 * @return string
 */
function impakto_icono( $nombre, $clase = '', $titulo = '' ) {
	$accesible = $titulo ? ' role="img" aria-label="' . esc_attr( $titulo ) . '"' : ' aria-hidden="true" focusable="false"';
	$trazos = impakto_iconos_trazos();
	if ( ! isset( $trazos[ $nombre ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icono %1$s" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"%2$s>%3$s</svg>',
		esc_attr( trim( $clase ) ),
		$accesible,
		$trazos[ $nombre ] // SVG estático del tema.
	);
}
