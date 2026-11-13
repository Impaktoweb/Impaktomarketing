<?php
/**
 * Redirecciones 301 desde el sitio actual (BRIEF §14). Se resuelven en código para que
 * funcionen desde el primer día; las nuevas se crean en Rank Math → Redirecciones.
 *
 * | Origen                                              | Destino                                   |
 * | /portafolio-item/{slug}/ y /portfolio-item/{slug}/  | /portafolio/{slug}/ o /portafolio/         |
 * | /portafolio-categoria/{slug}/                       | /portafolio/categoria/{slug}/              |
 * | /portfolio/                                         | /portafolio/                               |
 * | /user-interface-graphic-design/, /hello-world/      | /blog/                                     |
 * | /category/{slug}/                                   | /blog/                                     |
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Destino de una ruta antigua (o '' si no aplica).
 *
 * @param string $ruta Ruta relativa sin la base del sitio, p. ej. "portfolio-item/xien".
 * @return string URL de destino.
 */
function impakto_destino_redireccion( $ruta ) {
	$ruta = trim( $ruta, '/' );
	if ( preg_match( '#^(?:portafolio|portfolio)-item/([^/]+)$#', $ruta, $m ) ) {
		$slug     = sanitize_title( $m[1] );
		$proyecto = get_page_by_path( $slug, OBJECT, 'proyecto' );
		return $proyecto && 'publish' === $proyecto->post_status ? get_permalink( $proyecto ) : home_url( '/portafolio/' );
	}
	if ( preg_match( '#^(?:portafolio|portfolio)-categoria/([^/]+)$#', $ruta, $m ) ) {
		$term = get_term_by( 'slug', sanitize_title( $m[1] ), 'categoria-portafolio' );
		return $term ? get_term_link( $term ) : home_url( '/portafolio/' );
	}
	if ( in_array( $ruta, array( 'portfolio', 'portafolio-item', 'portfolio-item' ), true ) ) {
		return home_url( '/portafolio/' );
	}
	if ( in_array( $ruta, array( 'user-interface-graphic-design', 'hello-world' ), true ) || preg_match( '#^category/[^/]+(?:/page/\d+)?$#', $ruta ) ) {
		return home_url( '/blog/' );
	}
	return '';
}

add_action(
	'template_redirect',
	static function () {
		if ( ! is_404() ) {
			return;
		}
		$ruta = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$ruta = substr( $ruta, strlen( $base ) );
		$dest = impakto_destino_redireccion( (string) $ruta );
		if ( $dest ) {
			wp_safe_redirect( $dest, 301, 'Impakto' );
			exit;
		}
	},
	1
);
