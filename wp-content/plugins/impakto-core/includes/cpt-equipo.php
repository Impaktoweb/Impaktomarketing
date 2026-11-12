<?php
/**
 * Tipo de contenido: equipo (catálogo de alquiler). No tiene página propia:
 * se muestra en /alquiler-de-equipos/ y en los cotizadores.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra el CPT equipo.
 */
function impakto_registrar_cpt_equipo() {
	register_post_type(
		'equipo',
		array(
			'labels'              => array(
				'name'               => __( 'Equipos', 'impakto-core' ),
				'singular_name'      => __( 'Equipo', 'impakto-core' ),
				'add_new'            => __( 'Añadir equipo', 'impakto-core' ),
				'add_new_item'       => __( 'Añadir equipo', 'impakto-core' ),
				'edit_item'          => __( 'Editar equipo', 'impakto-core' ),
				'all_items'          => __( 'Todos los equipos', 'impakto-core' ),
				'search_items'       => __( 'Buscar equipos', 'impakto-core' ),
				'not_found'          => __( 'No hay equipos.', 'impakto-core' ),
				'not_found_in_trash' => __( 'No hay equipos en la papelera.', 'impakto-core' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_position'       => 23,
			'menu_icon'           => 'dashicons-camera',
			'supports'            => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'taxonomies'          => array( 'categoria-equipo' ),
			'rewrite'             => false,
		)
	);
}
add_action( 'init', 'impakto_registrar_cpt_equipo', 10 );

/**
 * Columnas de precio en el listado de equipos.
 */
add_filter(
	'manage_equipo_posts_columns',
	static function ( $columnas ) {
		$columnas['impakto_precio']   = __( 'Precio', 'impakto-core' );
		$columnas['impakto_operador'] = __( 'Operador', 'impakto-core' );
		return $columnas;
	}
);
add_action(
	'manage_equipo_posts_custom_column',
	static function ( $columna, $post_id ) {
		if ( 'impakto_precio' === $columna ) {
			$precio = (int) get_post_meta( $post_id, '_impakto_precio', true );
			echo $precio ? esc_html( impakto_cop( $precio ) ) : '<em>' . esc_html__( 'Consultar', 'impakto-core' ) . '</em>';
		}
		if ( 'impakto_operador' === $columna ) {
			echo get_post_meta( $post_id, '_impakto_requiere_operador', true ) ? esc_html__( 'Sí', 'impakto-core' ) : '—';
		}
	},
	10,
	2
);
