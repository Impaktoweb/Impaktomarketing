<?php
/**
 * Tipo de contenido: proyecto (/portafolio/ y /portafolio/{slug}/).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra el CPT proyecto.
 */
function impakto_registrar_cpt_proyecto() {
	register_post_type(
		'proyecto',
		array(
			'labels'        => array(
				'name'               => __( 'Proyectos', 'impakto-core' ),
				'singular_name'      => __( 'Proyecto', 'impakto-core' ),
				'menu_name'          => __( 'Portafolio', 'impakto-core' ),
				'add_new'            => __( 'Añadir proyecto', 'impakto-core' ),
				'add_new_item'       => __( 'Añadir proyecto', 'impakto-core' ),
				'edit_item'          => __( 'Editar proyecto', 'impakto-core' ),
				'new_item'           => __( 'Nuevo proyecto', 'impakto-core' ),
				'view_item'          => __( 'Ver proyecto', 'impakto-core' ),
				'all_items'          => __( 'Todos los proyectos', 'impakto-core' ),
				'search_items'       => __( 'Buscar proyectos', 'impakto-core' ),
				'not_found'          => __( 'No hay proyectos.', 'impakto-core' ),
				'not_found_in_trash' => __( 'No hay proyectos en la papelera.', 'impakto-core' ),
			),
			'public'        => true,
			'has_archive'   => 'portafolio',
			'hierarchical'  => false,
			'menu_position' => 22,
			'menu_icon'     => 'dashicons-portfolio',
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields', 'revisions' ),
			'taxonomies'    => array( 'categoria-portafolio' ),
			'rewrite'       => array(
				'slug'       => 'portafolio',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'impakto_registrar_cpt_proyecto', 10 );
