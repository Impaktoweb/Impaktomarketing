<?php
/**
 * Registro de solicitudes: tabla {prefix}impakto_solicitudes y consecutivo IMP-AAAA-NNNN.
 * La pantalla Impakto → Solicitudes llega en la Fase 5.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

define( 'IMPAKTO_DB_VERSION', '1' );

/**
 * Nombre de la tabla.
 *
 * @return string
 */
function impakto_tabla_solicitudes() {
	global $wpdb;
	return $wpdb->prefix . 'impakto_solicitudes';
}

/**
 * Crea o actualiza la tabla.
 */
function impakto_crear_tabla_solicitudes() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$tabla   = impakto_tabla_solicitudes();
	$charset = $wpdb->get_charset_collate();
	dbDelta(
		"CREATE TABLE $tabla (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			creado datetime NOT NULL,
			tipo varchar(40) NOT NULL,
			consecutivo varchar(20) NOT NULL DEFAULT '',
			pagina varchar(255) NOT NULL DEFAULT '',
			utm longtext NULL,
			datos longtext NULL,
			detalle longtext NULL,
			total bigint(20) unsigned NOT NULL DEFAULT 0,
			estado varchar(20) NOT NULL DEFAULT 'nueva',
			PRIMARY KEY  (id),
			KEY tipo (tipo),
			KEY estado (estado),
			KEY creado (creado)
		) $charset;"
	);
	update_option( 'impakto_db_version', IMPAKTO_DB_VERSION, false );
}

add_action(
	'plugins_loaded',
	static function () {
		if ( get_option( 'impakto_db_version' ) !== IMPAKTO_DB_VERSION ) {
			impakto_crear_tabla_solicitudes();
		}
	}
);

/**
 * Siguiente consecutivo del año: IMP-2026-0042.
 *
 * @return string
 */
function impakto_siguiente_consecutivo() {
	global $wpdb;
	$anio   = wp_date( 'Y' );
	$opcion = 'impakto_consecutivo_' . $anio;
	add_option( $opcion, 0, '', false );
	// Incremento atómico en la base de datos para evitar duplicados entre envíos simultáneos.
	$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = option_value + 1 WHERE option_name = %s", $opcion ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	wp_cache_delete( $opcion, 'options' );
	wp_cache_delete( 'alloptions', 'options' );
	$numero = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $opcion ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	return sprintf( 'IMP-%s-%04d', $anio, $numero );
}

/**
 * Guarda una solicitud.
 *
 * @param array<string,mixed> $fila tipo, consecutivo, pagina, utm, datos, detalle, total.
 * @return int Id.
 */
function impakto_guardar_solicitud( $fila ) {
	global $wpdb;
	$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		impakto_tabla_solicitudes(),
		array(
			'creado'      => current_time( 'mysql' ),
			'tipo'        => $fila['tipo'],
			'consecutivo' => $fila['consecutivo'] ?? '',
			'pagina'      => $fila['pagina'] ?? '',
			'utm'         => wp_json_encode( $fila['utm'] ?? array() ),
			'datos'       => wp_json_encode( $fila['datos'] ?? array(), JSON_UNESCAPED_UNICODE ),
			'detalle'     => wp_json_encode( $fila['detalle'] ?? array(), JSON_UNESCAPED_UNICODE ),
			'total'       => (int) ( $fila['total'] ?? 0 ),
			'estado'      => 'nueva',
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
	);
	return (int) $wpdb->insert_id;
}
