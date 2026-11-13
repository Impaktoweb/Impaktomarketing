<?php
/**
 * Ayudas de SEO del frontend (Fase 1: noindex de páginas internas).
 * Schema, breadcrumbs y Rank Math se completan en la Fase 6.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Páginas que nunca se indexan.
 *
 * @return string[] Slugs.
 */
function impakto_paginas_noindex() {
	return array( 'guia-de-estilo', 'gracias' );
}

add_filter(
	'wp_robots',
	static function ( $robots ) {
		if ( is_page( impakto_paginas_noindex() ) || get_query_var( 'caso' ) ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
			unset( $robots['max-image-preview'] );
		}
		return $robots;
	}
);

// Rank Math: respeta el noindex (la exclusión del sitemap se configura en la Fase 6).
add_filter(
	'rank_math/frontend/robots',
	static function ( $robots ) {
		if ( is_page( impakto_paginas_noindex() ) || get_query_var( 'caso' ) ) {
			$robots['index']  = 'noindex';
			$robots['follow'] = 'nofollow';
		}
		return $robots;
	}
);
