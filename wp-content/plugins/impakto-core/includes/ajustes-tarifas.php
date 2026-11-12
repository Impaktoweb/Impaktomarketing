<?php
/**
 * Impakto → Tarifas. En la Fase 1: resumen, importación y exportación JSON.
 * El formulario de edición campo por campo llega en la Fase 3 (cotizador).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	static function () {
		add_submenu_page( 'impakto', __( 'Tarifas', 'impakto-core' ), __( 'Tarifas', 'impakto-core' ), 'manage_options', 'impakto-tarifas', 'impakto_pantalla_tarifas' );
	}
);

/**
 * Acciones de importar y exportar.
 */
add_action(
	'admin_post_impakto_tarifas',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permiso.', 'impakto-core' ) );
		}
		check_admin_referer( 'impakto_tarifas' );
		$accion = isset( $_POST['accion'] ) ? sanitize_key( $_POST['accion'] ) : '';

		if ( 'exportar' === $accion ) {
			$datos            = impakto_tarifas();
			$datos['equipos'] = impakto_equipos_catalogo( true );
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=tarifas-impakto-' . gmdate( 'Y-m-d' ) . '.json' );
			echo wp_json_encode( $datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			exit;
		}

		$json = '';
		if ( 'importar' === $accion && ! empty( $_FILES['archivo']['tmp_name'] ) ) {
			$json = file_get_contents( sanitize_text_field( $_FILES['archivo']['tmp_name'] ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		} elseif ( 'importar_defecto' === $accion ) {
			$json = file_get_contents( impakto_tarifas_json_defecto() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		}
		$resultado = $json ? impakto_importar_tarifas( $json, ! empty( $_POST['actualizar'] ) ) : new WP_Error( 'vacio', __( 'No se recibió ningún archivo.', 'impakto-core' ) );
		$aviso     = is_wp_error( $resultado ) ? 'error' : 'ok';
		wp_safe_redirect( add_query_arg( 'impakto_aviso', $aviso, admin_url( 'admin.php?page=impakto-tarifas' ) ) );
		exit;
	}
);

/**
 * Pantalla de tarifas.
 */
function impakto_pantalla_tarifas() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$tarifas = impakto_tarifas();
	$aviso   = isset( $_GET['impakto_aviso'] ) ? sanitize_key( $_GET['impakto_aviso'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<div class="wrap"><h1>' . esc_html__( 'Impakto → Tarifas', 'impakto-core' ) . '</h1>';
	if ( 'ok' === $aviso ) {
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Tarifas importadas.', 'impakto-core' ) . '</p></div>';
	} elseif ( 'error' === $aviso ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'No se pudo importar el archivo.', 'impakto-core' ) . '</p></div>';
	}

	if ( ! $tarifas ) {
		echo '<p>' . esc_html__( 'Aún no hay tarifas. Importa el archivo inicial.', 'impakto-core' ) . '</p>';
	} else {
		echo '<h2>' . esc_html__( 'Estudio (bloques de horas)', 'impakto-core' ) . '</h2><table class="widefat striped" style="max-width:720px"><thead><tr><th>' . esc_html__( 'Espacio', 'impakto-core' ) . '</th><th>1 h</th><th>4 h</th><th>8 h</th></tr></thead><tbody>';
		foreach ( $tarifas['estudio']['espacios'] ?? array() as $espacio ) {
			echo '<tr><td>' . esc_html( $espacio['nombre'] . ' · ' . $espacio['descripcion'] ) . '</td>';
			foreach ( array( 1, 4, 8 ) as $h ) {
				echo '<td>' . esc_html( impakto_cop( $espacio['bloques'][ $h ] ?? 0, false ) ) . '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table><h2>' . esc_html__( 'Sala de podcast (por hora)', 'impakto-core' ) . '</h2><table class="widefat striped" style="max-width:720px"><tbody>';
		foreach ( $tarifas['podcast']['paquetes'] ?? array() as $paquete ) {
			echo '<tr><td>' . esc_html( $paquete['nombre'] ) . '</td><td>' . esc_html( impakto_cop( $paquete['precio_hora'], false ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
		printf(
			'<p>%s <a href="%s">%s</a></p>',
			esc_html__( 'Los equipos se editan uno por uno en', 'impakto-core' ),
			esc_url( admin_url( 'edit.php?post_type=equipo' ) ),
			esc_html__( 'Equipos', 'impakto-core' )
		);
	}

	$url = admin_url( 'admin-post.php' );
	echo '<hr><h2>' . esc_html__( 'Importar / exportar', 'impakto-core' ) . '</h2>';
	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( $url ) . '">';
	wp_nonce_field( 'impakto_tarifas' );
	echo '<input type="hidden" name="action" value="impakto_tarifas"><p><input type="file" name="archivo" accept="application/json,.json"></p>';
	echo '<p><label><input type="checkbox" name="actualizar" value="1"> ' . esc_html__( 'Actualizar también los precios de los equipos que ya existen', 'impakto-core' ) . '</label></p>';
	echo '<p><button class="button button-primary" name="accion" value="importar">' . esc_html__( 'Importar archivo', 'impakto-core' ) . '</button> ';
	echo '<button class="button" name="accion" value="importar_defecto">' . esc_html__( 'Importar tarifas.json inicial', 'impakto-core' ) . '</button> ';
	echo '<button class="button" name="accion" value="exportar">' . esc_html__( 'Exportar JSON', 'impakto-core' ) . '</button></p></form></div>';
}
