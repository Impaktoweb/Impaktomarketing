<?php
/**
 * Tema Impakto: arranque.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

define( 'IMPAKTO_TEMA_VERSION', '0.1.0' );
define( 'IMPAKTO_TEMA_PATH', get_template_directory() );
define( 'IMPAKTO_TEMA_URL', get_template_directory_uri() );

foreach ( array( 'datos', 'navegacion', 'setup', 'iconos', 'whatsapp', 'assets', 'bloques', 'seo-helpers', 'portafolio', 'blog' ) as $impakto_archivo ) {
	require_once IMPAKTO_TEMA_PATH . '/inc/' . $impakto_archivo . '.php';
}
