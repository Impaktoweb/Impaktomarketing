<?php
/**
 * Bloques propios (dinámicos, sin paso de compilación) y formato "Resaltado naranja".
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lista de carpetas de bloques del tema.
 *
 * @return string[]
 */
function impakto_carpetas_bloques() {
	return array_filter( glob( IMPAKTO_TEMA_PATH . '/blocks/*', GLOB_ONLYDIR ), static fn( $dir ) => file_exists( $dir . '/block.json' ) );
}

add_action(
	'init',
	static function () {
		wp_register_script(
			'impakto-bloques-editor',
			IMPAKTO_TEMA_URL . '/assets/js/editor.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-rich-text', 'wp-i18n', 'wp-data' ),
			impakto_version_archivo( 'assets/js/editor.js' ),
			true
		);

		$controles = array();
		foreach ( impakto_carpetas_bloques() as $dir ) {
			$tipo = register_block_type( $dir );
			if ( ! $tipo ) {
				continue;
			}
			$json = json_decode( (string) file_get_contents( $dir . '/block.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$controles[ $tipo->name ] = array(
				'etiquetas' => $json['impakto']['etiquetas'] ?? array(),
				'opciones'  => $json['impakto']['opciones'] ?? array(),
				'multilinea' => $json['impakto']['multilinea'] ?? array(),
			);
		}
		wp_add_inline_script( 'impakto-bloques-editor', 'window.impaktoBloques = ' . wp_json_encode( $controles ) . ';', 'before' );
	}
);

/**
 * Categoría "Impakto" en el insertor de bloques.
 */
add_filter(
	'block_categories_all',
	static function ( $categorias ) {
		array_unshift(
			$categorias,
			array(
				'slug'  => 'impakto',
				'title' => __( 'Impakto', 'impakto' ),
			)
		);
		return $categorias;
	}
);

add_action(
	'enqueue_block_editor_assets',
	static function () {
		wp_enqueue_script( 'impakto-bloques-editor' );
	}
);
