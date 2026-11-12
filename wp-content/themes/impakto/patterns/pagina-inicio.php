<?php
/**
 * Title: Página de Inicio completa
 * Slug: impakto/pagina-inicio
 * Categories: impakto-inicio
 * Post Types: page
 * Inserter: no
 *
 * Une las secciones de Inicio en el orden del BRIEF §6.1. Se usa para sembrar la página.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

foreach ( array( 'hero', 'servicios', 'propuesta', 'cifras', 'espacios', 'portafolio', 'por-que', 'blog', 'faq', 'cta' ) as $impakto_seccion ) {
	include __DIR__ . '/inicio-' . $impakto_seccion . '.php';
	echo "\n\n";
}
