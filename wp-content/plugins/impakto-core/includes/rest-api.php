<?php
/**
 * REST API: GET /wp-json/impakto/v1/tarifas.
 * (POST /solicitudes llega en la Fase 3 con el cotizador.)
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Catálogo de equipos agrupado por categoría.
 *
 * @param bool $todos Incluir borradores (para exportar).
 * @return array{unidad_precio:string,categorias:array<int,array<string,mixed>>}
 */
function impakto_equipos_catalogo( $todos = false ) {
	$terminos = get_terms(
		array(
			'taxonomy'   => 'categoria-equipo',
			'hide_empty' => false,
		)
	);
	$terminos = is_wp_error( $terminos ) ? array() : $terminos;
	usort( $terminos, static fn( $a, $b ) => (int) get_term_meta( $a->term_id, '_impakto_orden', true ) <=> (int) get_term_meta( $b->term_id, '_impakto_orden', true ) );

	$categorias = array();
	foreach ( $terminos as $termino ) {
		$posts = get_posts(
			array(
				'post_type'      => 'equipo',
				'post_status'    => $todos ? array( 'publish', 'draft' ) : 'publish',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'categoria-equipo',
						'terms'    => $termino->term_id,
					),
				),
			)
		);
		$items = array();
		foreach ( $posts as $post ) {
			$miniatura = get_the_post_thumbnail_url( $post, 'thumbnail' );
			$items[]   = array(
				'id'                => $post->post_name,
				'nombre'            => get_the_title( $post ),
				'precio'            => (int) get_post_meta( $post->ID, '_impakto_precio', true ),
				'unidad'            => get_post_meta( $post->ID, '_impakto_unidad', true ) ? get_post_meta( $post->ID, '_impakto_unidad', true ) : 'jornada',
				'requiere_operador' => (bool) get_post_meta( $post->ID, '_impakto_requiere_operador', true ),
				'specs'             => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $post->ID, '_impakto_specs', true ) ) ) ) ),
				'foto'              => $miniatura ? $miniatura : '',
			);
		}
		if ( $items ) {
			$categorias[] = array(
				'id'     => $termino->slug,
				'nombre' => $termino->name,
				'items'  => $items,
			);
		}
	}
	return array(
		'unidad_precio' => impakto_tarifas()['equipos_unidad'] ?? 'jornada',
		'categorias'    => $categorias,
	);
}

/**
 * Datos públicos para los cotizadores, en caché hasta el próximo cambio.
 *
 * @return array<string,mixed>
 */
function impakto_tarifas_publicas() {
	$cache = get_transient( 'impakto_tarifas_rest' );
	if ( is_array( $cache ) ) {
		return $cache;
	}
	$datos             = impakto_tarifas();
	$datos['equipos']  = impakto_equipos_catalogo();
	$datos['whatsapp'] = impakto_whatsapp_numero();
	unset( $datos['estudio']['confirmar'], $datos['podcast']['confirmar'] );
	set_transient( 'impakto_tarifas_rest', $datos, DAY_IN_SECONDS );
	return $datos;
}

// Invalida la caché cuando cambian tarifas o equipos.
add_action( 'update_option_impakto_tarifas', static fn() => delete_transient( 'impakto_tarifas_rest' ) );
add_action( 'update_option_impakto_ajustes', static fn() => delete_transient( 'impakto_tarifas_rest' ) );
add_action( 'save_post_equipo', static fn() => delete_transient( 'impakto_tarifas_rest' ) );
add_action( 'set_object_terms', static fn() => delete_transient( 'impakto_tarifas_rest' ) );

add_action(
	'rest_api_init',
	static function () {
		register_rest_route(
			'impakto/v1',
			'/tarifas',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => static function () {
					$respuesta = rest_ensure_response( impakto_tarifas_publicas() );
					$respuesta->header( 'Cache-Control', 'public, max-age=300' );
					return $respuesta;
				},
			)
		);
	}
);

/**
 * Los ajustes y las tarifas se imprimen en todas las páginas (NAP, WhatsApp, IDs de analítica,
 * precios): al guardarlos se purga la caché de páginas de LiteSpeed para que se vean al instante.
 */
$impakto_purgar_cache = static function () {
	do_action( 'litespeed_purge_all' );
};
add_action( 'update_option_impakto_tarifas', $impakto_purgar_cache );
add_action( 'update_option_impakto_ajustes', $impakto_purgar_cache );
add_action( 'add_option_impakto_ajustes', $impakto_purgar_cache );
