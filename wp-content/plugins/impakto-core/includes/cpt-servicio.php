<?php
/**
 * Tipo de contenido: servicio (/servicios/{slug}/).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra el CPT servicio. El índice /servicios/ es una página normal.
 */
function impakto_registrar_cpt_servicio() {
	register_post_type(
		'servicio',
		array(
			'labels'        => array(
				'name'               => __( 'Servicios', 'impakto-core' ),
				'singular_name'      => __( 'Servicio', 'impakto-core' ),
				'add_new'            => __( 'Añadir servicio', 'impakto-core' ),
				'add_new_item'       => __( 'Añadir servicio', 'impakto-core' ),
				'edit_item'          => __( 'Editar servicio', 'impakto-core' ),
				'new_item'           => __( 'Nuevo servicio', 'impakto-core' ),
				'view_item'          => __( 'Ver servicio', 'impakto-core' ),
				'all_items'          => __( 'Todos los servicios', 'impakto-core' ),
				'search_items'       => __( 'Buscar servicios', 'impakto-core' ),
				'not_found'          => __( 'No hay servicios.', 'impakto-core' ),
				'not_found_in_trash' => __( 'No hay servicios en la papelera.', 'impakto-core' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'hierarchical'  => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-megaphone',
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
			'rewrite'       => array(
				'slug'       => 'servicios',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'impakto_registrar_cpt_servicio', 10 );
