<?php
/**
 * Impakto → Solicitudes (BRIEF §8): lista con filtros por tipo, fecha y estado, detalle,
 * cambio de estado y exportación a CSV. Solo Administrador y Editor (capacidad impakto_solicitudes).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Estados de una solicitud.
 *
 * @return array<string,string>
 */
function impakto_estados_solicitud() {
	return array(
		'nueva'      => __( 'Nueva', 'impakto-core' ),
		'contactada' => __( 'Contactada', 'impakto-core' ),
		'reservada'  => __( 'Reservada', 'impakto-core' ),
		'descartada' => __( 'Descartada', 'impakto-core' ),
	);
}

/**
 * Tipos de solicitud.
 *
 * @return array<string,string>
 */
function impakto_tipos_solicitud() {
	return array(
		'cotizacion_whatsapp' => __( 'Cotización por WhatsApp', 'impakto-core' ),
		'cotizacion_email'    => __( 'Cotización por correo', 'impakto-core' ),
		'contacto'            => __( 'Formulario de contacto', 'impakto-core' ),
	);
}

/**
 * Condiciones SQL a partir de los filtros de la URL.
 *
 * @return string WHERE (preparado).
 */
function impakto_where_solicitudes() {
	global $wpdb;
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- filtros de lectura.
	$where = array( '1=1' );
	$tipo  = isset( $_GET['tipo'] ) ? sanitize_key( $_GET['tipo'] ) : '';
	$est   = isset( $_GET['estado'] ) ? sanitize_key( $_GET['estado'] ) : '';
	$desde = isset( $_GET['desde'] ) ? sanitize_text_field( wp_unslash( $_GET['desde'] ) ) : '';
	$hasta = isset( $_GET['hasta'] ) ? sanitize_text_field( wp_unslash( $_GET['hasta'] ) ) : '';
	$q     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	// phpcs:enable
	if ( isset( impakto_tipos_solicitud()[ $tipo ] ) ) {
		$where[] = $wpdb->prepare( 'tipo = %s', $tipo );
	}
	if ( isset( impakto_estados_solicitud()[ $est ] ) ) {
		$where[] = $wpdb->prepare( 'estado = %s', $est );
	}
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $desde ) ) {
		$where[] = $wpdb->prepare( 'creado >= %s', $desde . ' 00:00:00' );
	}
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $hasta ) ) {
		$where[] = $wpdb->prepare( 'creado <= %s', $hasta . ' 23:59:59' );
	}
	if ( '' !== $q ) {
		$like    = '%' . $wpdb->esc_like( $q ) . '%';
		$where[] = $wpdb->prepare( '(datos LIKE %s OR consecutivo LIKE %s)', $like, $like );
	}
	return implode( ' AND ', $where );
}

/**
 * Tabla de solicitudes.
 */
class Impakto_Tabla_Solicitudes extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'solicitud',
				'plural'   => 'solicitudes',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Columnas.
	 *
	 * @return array<string,string>
	 */
	public function get_columns() {
		return array(
			'creado'      => __( 'Fecha', 'impakto-core' ),
			'consecutivo' => __( 'Consecutivo', 'impakto-core' ),
			'tipo'        => __( 'Tipo', 'impakto-core' ),
			'contacto'    => __( 'Contacto', 'impakto-core' ),
			'detalle'     => __( 'Servicio', 'impakto-core' ),
			'total'       => __( 'Total', 'impakto-core' ),
			'origen'      => __( 'Origen', 'impakto-core' ),
			'estado'      => __( 'Estado', 'impakto-core' ),
		);
	}

	/**
	 * Carga los datos.
	 */
	public function prepare_items() {
		global $wpdb;
		$tabla   = impakto_tabla_solicitudes();
		$where   = impakto_where_solicitudes();
		$por_pag = 25;
		$pagina  = $this->get_pagenum();
		$total   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tabla WHERE $where" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where se arma con prepare().
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tabla WHERE $where ORDER BY creado DESC LIMIT %d OFFSET %d", $por_pag, ( $pagina - 1 ) * $por_pag ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->_column_headers = array( $this->get_columns(), array(), array() );
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $por_pag,
			)
		);
	}

	/**
	 * Celdas.
	 *
	 * @param array<string,mixed> $item    Fila.
	 * @param string              $columna Columna.
	 * @return string
	 */
	public function column_default( $item, $columna ) {
		$datos   = json_decode( (string) $item['datos'], true ) ?: array();
		$detalle = json_decode( (string) $item['detalle'], true ) ?: array();
		$utm     = json_decode( (string) $item['utm'], true ) ?: array();
		switch ( $columna ) {
			case 'creado':
				$url = add_query_arg( array( 'page' => 'impakto', 'ver' => (int) $item['id'] ), admin_url( 'admin.php' ) );
				return sprintf( '<a href="%s"><strong>%s</strong></a>', esc_url( $url ), esc_html( mysql2date( 'd/m/Y H:i', $item['creado'] ) ) );
			case 'consecutivo':
				return esc_html( $item['consecutivo'] ? $item['consecutivo'] : '—' );
			case 'tipo':
				return esc_html( impakto_tipos_solicitud()[ $item['tipo'] ] ?? $item['tipo'] );
			case 'contacto':
				return esc_html( $datos['nombre'] ?? '' ) . '<br><span class="description">' . esc_html( trim( ( $datos['celular'] ?? '' ) . ' ' . ( $datos['correo'] ?? '' ) ) ) . '</span>';
			case 'detalle':
				return esc_html( $detalle['cotizacion']['servicio'] ?? ( $datos['servicio'] ?? '' ) );
			case 'total':
				return $item['total'] ? esc_html( impakto_cop( $item['total'], false ) ) : '—';
			case 'origen':
				return esc_html( $utm['utm_source'] ?? wp_parse_url( (string) $item['pagina'], PHP_URL_PATH ) ?? '' );
			case 'estado':
				$estados = impakto_estados_solicitud();
				return '<span class="impakto-estado impakto-estado--' . esc_attr( $item['estado'] ) . '">' . esc_html( $estados[ $item['estado'] ] ?? $item['estado'] ) . '</span>';
		}
		return '';
	}

	/**
	 * Filtros sobre la tabla.
	 *
	 * @param string $donde top|bottom.
	 */
	protected function extra_tablenav( $donde ) {
		if ( 'top' !== $donde ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$tipo  = isset( $_GET['tipo'] ) ? sanitize_key( $_GET['tipo'] ) : '';
		$est   = isset( $_GET['estado'] ) ? sanitize_key( $_GET['estado'] ) : '';
		$desde = isset( $_GET['desde'] ) ? sanitize_text_field( wp_unslash( $_GET['desde'] ) ) : '';
		$hasta = isset( $_GET['hasta'] ) ? sanitize_text_field( wp_unslash( $_GET['hasta'] ) ) : '';
		// phpcs:enable
		echo '<div class="alignleft actions">';
		echo '<select name="tipo"><option value="">' . esc_html__( 'Todos los tipos', 'impakto-core' ) . '</option>';
		foreach ( impakto_tipos_solicitud() as $k => $v ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $tipo, $k, false ), esc_html( $v ) );
		}
		echo '</select><select name="estado"><option value="">' . esc_html__( 'Todos los estados', 'impakto-core' ) . '</option>';
		foreach ( impakto_estados_solicitud() as $k => $v ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $est, $k, false ), esc_html( $v ) );
		}
		printf(
			'</select><label>%s <input type="date" name="desde" value="%s"></label> <label>%s <input type="date" name="hasta" value="%s"></label> ',
			esc_html__( 'Desde', 'impakto-core' ),
			esc_attr( $desde ),
			esc_html__( 'Hasta', 'impakto-core' ),
			esc_attr( $hasta )
		);
		submit_button( __( 'Filtrar', 'impakto-core' ), '', 'filtrar', false );
		$args = array_filter(
			array(
				'action' => 'impakto_csv',
				'tipo'   => $tipo,
				'estado' => $est,
				'desde'  => $desde,
				'hasta'  => $hasta,
			)
		);
		$csv  = wp_nonce_url( add_query_arg( $args, admin_url( 'admin-post.php' ) ), 'impakto_csv' );
		printf( ' <a class="button" href="%s">%s</a>', esc_url( $csv ), esc_html__( 'Exportar CSV', 'impakto-core' ) );
		echo '</div>';
	}

	/**
	 * Mensaje sin resultados.
	 */
	public function no_items() {
		esc_html_e( 'Aún no hay solicitudes con estos filtros.', 'impakto-core' );
	}
}

/**
 * Pantalla de solicitudes (lista o detalle).
 */
function impakto_pantalla_solicitudes() {
	if ( ! current_user_can( 'impakto_solicitudes' ) ) {
		return;
	}
	global $wpdb;
	$ver = isset( $_GET['ver'] ) ? absint( $_GET['ver'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<div class="wrap"><style>.impakto-estado{display:inline-block;padding:2px 10px;border-radius:999px;background:#f0f0f1;font-size:12px}.impakto-estado--nueva{background:#FFE8D1;color:#8a4100}.impakto-estado--contactada{background:#e5f0ff;color:#0a4b96}.impakto-estado--reservada{background:#e3f4e6;color:#1d6b2c}.impakto-estado--descartada{background:#eee;color:#666}.impakto-detalle td,.impakto-detalle th{padding:8px 10px;text-align:left;vertical-align:top}</style>';

	if ( $ver ) {
		$fila = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . impakto_tabla_solicitudes() . ' WHERE id = %d', $ver ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared
		if ( ! $fila ) {
			echo '<p>' . esc_html__( 'No existe esa solicitud.', 'impakto-core' ) . '</p></div>';
			return;
		}
		$datos   = json_decode( (string) $fila['datos'], true ) ?: array();
		$detalle = json_decode( (string) $fila['detalle'], true ) ?: array();
		$utm     = json_decode( (string) $fila['utm'], true ) ?: array();
		$volver  = admin_url( 'admin.php?page=impakto' );
		printf( '<h1>%s %s</h1><p><a href="%s">← %s</a></p>', esc_html__( 'Solicitud', 'impakto-core' ), esc_html( $fila['consecutivo'] ? $fila['consecutivo'] : '#' . $fila['id'] ), esc_url( $volver ), esc_html__( 'Volver a la lista', 'impakto-core' ) );
		if ( isset( $_GET['actualizado'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Estado actualizado.', 'impakto-core' ) . '</p></div>';
		}
		echo '<table class="widefat impakto-detalle" style="max-width:900px"><tbody>';
		$filas = array(
			__( 'Fecha', 'impakto-core' )   => mysql2date( 'd/m/Y H:i', $fila['creado'] ),
			__( 'Tipo', 'impakto-core' )    => impakto_tipos_solicitud()[ $fila['tipo'] ] ?? $fila['tipo'],
			__( 'Página', 'impakto-core' )  => $fila['pagina'],
			__( 'Origen', 'impakto-core' )  => $utm ? implode( ' · ', array_map( static fn( $k, $v ) => "$k=$v", array_keys( $utm ), $utm ) ) : '—',
		);
		foreach ( $datos as $k => $v ) {
			$filas[ ucfirst( $k ) ] = is_scalar( $v ) ? (string) $v : wp_json_encode( $v );
		}
		foreach ( $filas as $k => $v ) {
			printf( '<tr><th style="width:180px">%s</th><td>%s</td></tr>', esc_html( $k ), esc_html( $v ) );
		}
		if ( ! empty( $detalle['cotizacion']['lineas'] ) ) {
			echo '<tr><th>' . esc_html__( 'Cotización', 'impakto-core' ) . '</th><td><ul style="margin:0">';
			foreach ( $detalle['cotizacion']['lineas'] as $l ) {
				printf( '<li><strong>%s</strong> — %s · %s</li>', esc_html( $l['concepto'] ), esc_html( $l['detalle'] ), esc_html( impakto_cop( $l['valor'] ) ) );
			}
			printf( '</ul><p><strong>%s %s</strong></p>', esc_html__( 'Total:', 'impakto-core' ), esc_html( impakto_cop( $fila['total'] ) ) );
			if ( ! empty( $detalle['cotizacion']['notas'] ) ) {
				echo '<p class="description">' . esc_html( implode( ' · ', $detalle['cotizacion']['notas'] ) ) . '</p>';
			}
			$fecha = impakto_fecha_legible( $detalle['seleccion']['fecha'] ?? '', $detalle['seleccion']['hora'] ?? '' );
			if ( $fecha ) {
				echo '<p>' . esc_html__( 'Fecha deseada:', 'impakto-core' ) . ' ' . esc_html( $fecha ) . '</p>';
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
		if ( ! empty( $datos['celular'] ) ) {
			printf( '<p><a class="button button-primary" target="_blank" rel="noopener" href="%s">%s</a></p>', esc_url( 'https://wa.me/' . preg_replace( '/\D+/', '', ( str_starts_with( preg_replace( '/\D+/', '', $datos['celular'] ), '57' ) ? '' : '57' ) . $datos['celular'] ) ), esc_html__( 'Responder por WhatsApp', 'impakto-core' ) );
		}
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="impakto_estado"><input type="hidden" name="id" value="' . (int) $fila['id'] . '">';
		wp_nonce_field( 'impakto_estado_' . $fila['id'] );
		echo '<p><label>' . esc_html__( 'Estado', 'impakto-core' ) . ' <select name="estado">';
		foreach ( impakto_estados_solicitud() as $k => $v ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $k ), selected( $fila['estado'], $k, false ), esc_html( $v ) );
		}
		echo '</select></label> ';
		submit_button( __( 'Guardar estado', 'impakto-core' ), 'secondary', 'guardar', false );
		echo '</p></form></div>';
		return;
	}

	echo '<h1 class="wp-heading-inline">' . esc_html__( 'Solicitudes', 'impakto-core' ) . '</h1><hr class="wp-header-end">';
	$tabla = new Impakto_Tabla_Solicitudes();
	$tabla->prepare_items();
	echo '<form method="get"><input type="hidden" name="page" value="impakto">';
	$tabla->search_box( __( 'Buscar', 'impakto-core' ), 'impakto-buscar' );
	$tabla->display();
	echo '</form></div>';
}

/**
 * Cambiar estado.
 */
add_action(
	'admin_post_impakto_estado',
	static function () {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! current_user_can( 'impakto_solicitudes' ) || ! check_admin_referer( 'impakto_estado_' . $id ) ) {
			wp_die( esc_html__( 'No tienes permiso.', 'impakto-core' ) );
		}
		$estado = isset( $_POST['estado'] ) ? sanitize_key( $_POST['estado'] ) : '';
		if ( isset( impakto_estados_solicitud()[ $estado ] ) ) {
			global $wpdb;
			$wpdb->update( impakto_tabla_solicitudes(), array( 'estado' => $estado ), array( 'id' => $id ), array( '%s' ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}
		wp_safe_redirect( admin_url( 'admin.php?page=impakto&ver=' . $id . '&actualizado=1' ) );
		exit;
	}
);

/**
 * Exportar CSV con los filtros actuales.
 */
add_action(
	'admin_post_impakto_csv',
	static function () {
		if ( ! current_user_can( 'impakto_solicitudes' ) || ! check_admin_referer( 'impakto_csv' ) ) {
			wp_die( esc_html__( 'No tienes permiso.', 'impakto-core' ) );
		}
		global $wpdb;
		$filas = $wpdb->get_results( 'SELECT * FROM ' . impakto_tabla_solicitudes() . ' WHERE ' . impakto_where_solicitudes() . ' ORDER BY creado DESC', ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=solicitudes-impakto-' . wp_date( 'Y-m-d' ) . '.csv' );
		$salida = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $salida, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- BOM para Excel.
		fputcsv( $salida, array( 'Fecha', 'Consecutivo', 'Tipo', 'Estado', 'Nombre', 'Celular', 'Correo', 'Empresa', 'Servicio', 'Mensaje', 'Total COP', 'Página', 'utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid' ), ';' );
		foreach ( $filas as $f ) {
			$d   = json_decode( (string) $f['datos'], true ) ?: array();
			$det = json_decode( (string) $f['detalle'], true ) ?: array();
			$u   = json_decode( (string) $f['utm'], true ) ?: array();
			fputcsv(
				$salida,
				array(
					$f['creado'],
					$f['consecutivo'],
					impakto_tipos_solicitud()[ $f['tipo'] ] ?? $f['tipo'],
					impakto_estados_solicitud()[ $f['estado'] ] ?? $f['estado'],
					$d['nombre'] ?? '',
					$d['celular'] ?? '',
					$d['correo'] ?? '',
					$d['empresa'] ?? '',
					$det['cotizacion']['servicio'] ?? ( $d['servicio'] ?? '' ),
					$d['mensaje'] ?? '',
					$f['total'],
					$f['pagina'],
					$u['utm_source'] ?? '',
					$u['utm_medium'] ?? '',
					$u['utm_campaign'] ?? '',
					$u['gclid'] ?? '',
					$u['fbclid'] ?? '',
				),
				';'
			);
		}
		fclose( $salida ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
);
