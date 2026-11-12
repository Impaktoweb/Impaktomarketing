<?php
/**
 * Taxonomías: categoria-portafolio y categoria-equipo.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra las taxonomías. Se registran antes que los CPT para que la regla
 * /portafolio/categoria/{slug}/ tenga prioridad sobre /portafolio/{slug}/.
 */
function impakto_registrar_taxonomias() {
	register_taxonomy(
		'categoria-portafolio',
		array( 'proyecto' ),
		array(
			'labels'            => array(
				'name'          => __( 'Categorías del portafolio', 'impakto-core' ),
				'singular_name' => __( 'Categoría del portafolio', 'impakto-core' ),
				'menu_name'     => __( 'Categorías', 'impakto-core' ),
				'all_items'     => __( 'Todas las categorías', 'impakto-core' ),
				'edit_item'     => __( 'Editar categoría', 'impakto-core' ),
				'add_new_item'  => __( 'Añadir categoría', 'impakto-core' ),
				'search_items'  => __( 'Buscar categorías', 'impakto-core' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'       => 'portafolio/categoria',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'categoria-equipo',
		array( 'equipo' ),
		array(
			'labels'            => array(
				'name'          => __( 'Categorías de equipos', 'impakto-core' ),
				'singular_name' => __( 'Categoría de equipos', 'impakto-core' ),
				'menu_name'     => __( 'Categorías', 'impakto-core' ),
				'edit_item'     => __( 'Editar categoría', 'impakto-core' ),
				'add_new_item'  => __( 'Añadir categoría', 'impakto-core' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'impakto_registrar_taxonomias', 9 );

/**
 * Categorías del portafolio (BRIEF §9), con slugs estables para las redirecciones.
 *
 * @return array<string,string> slug => nombre.
 */
function impakto_categorias_portafolio() {
	return array(
		'naming'                     => 'Naming',
		'branding'                   => 'Branding y Brand Essence',
		'diseno-grafico'             => 'Diseño gráfico y material publicitario',
		'comunicacion-digital-redes' => 'Comunicación digital y redes',
		'produccion-audiovisual'     => 'Producción audiovisual',
		'fotografia'                 => 'Fotografía',
		'pauta-digital'              => 'Pauta digital',
		'sitio-web-seo'              => 'Sitio web y SEO',
		'crm-ventas-digitales'       => 'CRM y ventas digitales',
	);
}
