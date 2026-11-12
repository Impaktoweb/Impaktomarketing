<?php
/**
 * Importa data/tarifas.json: tarifas → opción impakto_tarifas; equipos → CPT equipo.
 * Es idempotente: vuelve a correr sin duplicar (busca cada equipo por su id/slug).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ruta del JSON incluido en el plugin.
 *
 * @return string
 */
function impakto_tarifas_json_defecto() {
	return IMPAKTO_CORE_PATH . 'data/tarifas.json';
}

/**
 * Importa tarifas desde un JSON.
 *
 * @param string $json            Contenido JSON.
 * @param bool   $actualizar_equipos Sobrescribe precio de equipos existentes.
 * @return array{ok:bool,mensaje:string,equipos_creados:int,equipos_actualizados:int}|WP_Error
 */
function impakto_importar_tarifas( $json, $actualizar_equipos = false ) {
	$datos = json_decode( $json, true );
	if ( ! is_array( $datos ) || empty( $datos['estudio'] ) || empty( $datos['podcast'] ) ) {
		return new WP_Error( 'impakto_json', __( 'El archivo no tiene el formato de tarifas esperado.', 'impakto-core' ) );
	}

	$equipos = $datos['equipos'] ?? array();
	unset( $datos['equipos'], $datos['_nota'] );
	$datos['equipos_unidad'] = $equipos['unidad_precio'] ?? 'jornada';
	update_option( 'impakto_tarifas', $datos, false );

	$creados      = 0;
	$actualizados = 0;
	$orden_cat    = 0;
	foreach ( $equipos['categorias'] ?? array() as $categoria ) {
		$termino = term_exists( $categoria['id'], 'categoria-equipo' );
		if ( ! $termino ) {
			$termino = wp_insert_term( $categoria['nombre'], 'categoria-equipo', array( 'slug' => $categoria['id'] ) );
		}
		if ( is_wp_error( $termino ) ) {
			continue;
		}
		$term_id = (int) $termino['term_id'];
		update_term_meta( $term_id, '_impakto_orden', ++$orden_cat );

		$orden = 0;
		foreach ( $categoria['items'] as $item ) {
			$resultado = impakto_guardar_equipo( $item, $term_id, ++$orden, $actualizar_equipos );
			'creado' === $resultado ? $creados++ : ( 'actualizado' === $resultado ? $actualizados++ : null );
		}
	}

	// Equipos sin precio: se crean como borrador para completarlos luego. [CONFIRMAR precios].
	foreach ( $equipos['pendientes_de_precio'] ?? array() as $nombre ) {
		if ( str_contains( $nombre, '???' ) ) {
			continue;
		}
		$item = array(
			'id'     => sanitize_title( $nombre ),
			'nombre' => $nombre,
			'precio' => 0,
		);
		if ( 'creado' === impakto_guardar_equipo( $item, 0, 999, false, 'draft' ) ) {
			++$creados;
		}
	}

	delete_transient( 'impakto_tarifas_rest' );

	return array(
		'ok'                   => true,
		'mensaje'              => __( 'Tarifas importadas.', 'impakto-core' ),
		'equipos_creados'      => $creados,
		'equipos_actualizados' => $actualizados,
	);
}

/**
 * Crea o actualiza un equipo.
 *
 * @param array<string,mixed> $item       Datos del equipo.
 * @param int                 $term_id    Categoría (0 = sin categoría).
 * @param int                 $orden      Orden.
 * @param bool                $actualizar Sobrescribir precio si existe.
 * @param string              $estado     Estado de la entrada nueva.
 * @return string creado|actualizado|igual
 */
function impakto_guardar_equipo( $item, $term_id, $orden, $actualizar, $estado = 'publish' ) {
	$existente = get_page_by_path( $item['id'], OBJECT, 'equipo' );
	if ( $existente && ! $actualizar ) {
		return 'igual';
	}
	$post_id = $existente ? $existente->ID : wp_insert_post(
		array(
			'post_type'   => 'equipo',
			'post_status' => $estado,
			'post_title'  => $item['nombre'],
			'post_name'   => $item['id'],
			'menu_order'  => $orden,
		)
	);
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 'igual';
	}
	update_post_meta( $post_id, '_impakto_precio', (int) ( $item['precio'] ?? 0 ) );
	update_post_meta( $post_id, '_impakto_unidad', 'jornada' );
	update_post_meta( $post_id, '_impakto_requiere_operador', ! empty( $item['requiere_operador'] ) );
	if ( $term_id ) {
		wp_set_object_terms( $post_id, array( $term_id ), 'categoria-equipo' );
	}
	return $existente ? 'actualizado' : 'creado';
}
