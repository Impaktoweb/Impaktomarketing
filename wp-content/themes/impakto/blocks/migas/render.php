<?php
/**
 * Breadcrumbs visibles (BRIEF §13). El schema BreadcrumbList lo emite Rank Math.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( is_front_page() || is_404() || ! function_exists( 'rank_math_get_breadcrumbs' ) ) {
	return;
}
$impakto_html = rank_math_get_breadcrumbs(
	array(
		'wrap_before' => '<nav class="migas" aria-label="' . esc_attr__( 'Migas de pan', 'impakto' ) . '"><div class="contenedor"><p>',
		'wrap_after'  => '</p></div></nav>',
	)
);
echo wp_kses_post( $impakto_html );
