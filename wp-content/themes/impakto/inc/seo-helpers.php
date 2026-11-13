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

/**
 * Breadcrumbs: "Inicio / Servicios / Naming" (los servicios no tienen archivo propio).
 */
add_filter(
	'rank_math/frontend/breadcrumb/items',
	static function ( $items ) {
		if ( is_singular( 'servicio' ) && count( $items ) >= 2 ) {
			$pagina = get_page_by_path( 'servicios' );
			if ( $pagina ) {
				array_splice( $items, 1, 0, array( array( get_the_title( $pagina ), get_permalink( $pagina ) ) ) );
			}
		}
		// Portafolio: "Inicio / Portafolio / …" en archivo, categorías y proyectos.
		if ( is_singular( 'proyecto' ) || is_tax( 'categoria-portafolio' ) || is_post_type_archive( 'proyecto' ) ) {
			$archivo = array( __( 'Portafolio', 'impakto' ), get_post_type_archive_link( 'proyecto' ) );
			if ( is_post_type_archive( 'proyecto' ) ) {
				return array( $items[0], array( __( 'Portafolio', 'impakto' ), '' ) );
			}
			$ultimo = end( $items );
			return array( $items[0], $archivo, $ultimo );
		}
		return $items;
	}
);

/**
 * Títulos: si con el sufijo " | Impakto Marketing" pasan de 60 caracteres, se usa " | Impakto".
 */
add_filter(
	'rank_math/frontend/title',
	static function ( $titulo ) {
		if ( mb_strlen( $titulo ) > 60 && str_ends_with( $titulo, ' | Impakto Marketing' ) ) {
			$titulo = mb_substr( $titulo, 0, -mb_strlen( ' | Impakto Marketing' ) ) . ' | Impakto';
		}
		return $titulo;
	}
);

/**
 * Descripciones de proyectos sin descripción propia: resumen + contexto, máximo ~155 caracteres.
 */
add_filter(
	'rank_math/frontend/description',
	static function ( $desc ) {
		if ( ! is_singular( 'proyecto' ) || get_post_meta( get_queried_object_id(), 'rank_math_description', true ) ) {
			return $desc;
		}
		$id     = get_queried_object_id();
		$cats   = get_the_terms( $id, 'categoria-portafolio' );
		$nombre = $cats && ! is_wp_error( $cats ) ? mb_strtolower( $cats[0]->name ) : 'marketing';
		$base   = rtrim( wp_strip_all_tags( get_the_excerpt( $id ) ), '. ' ) . '.';
		$extra  = sprintf( ' Proyecto de %s de Impakto Marketing, agencia de Manizales.', $nombre );
		$texto  = mb_strlen( $base . $extra ) <= 158 ? $base . $extra : $base;
		return mb_strlen( $texto ) > 158 ? mb_substr( $texto, 0, 155 ) . '…' : $texto;
	}
);
