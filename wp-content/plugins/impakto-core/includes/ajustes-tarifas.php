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
	},
	11
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
 * Guarda el formulario de edición de tarifas.
 */
add_action(
	'admin_post_impakto_tarifas_guardar',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permiso.', 'impakto-core' ) );
		}
		check_admin_referer( 'impakto_tarifas_guardar' );
		$t   = impakto_tarifas();
		$in  = isset( $_POST['t'] ) && is_array( $_POST['t'] ) ? wp_unslash( $_POST['t'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se sanea campo por campo.
		$num = static fn( $v ) => absint( preg_replace( '/\D+/', '', (string) $v ) );

		$t['deposito_reembolsable'] = $num( $in['deposito'] ?? $t['deposito_reembolsable'] );
		foreach ( $t['estudio']['espacios'] as $i => &$e ) {
			$e['nombre']      = sanitize_text_field( $in['estudio'][ $i ]['nombre'] ?? $e['nombre'] );
			$e['descripcion'] = sanitize_text_field( $in['estudio'][ $i ]['descripcion'] ?? $e['descripcion'] );
			$e['area_m2']     = $num( $in['estudio'][ $i ]['area'] ?? $e['area_m2'] );
			foreach ( array( 1, 4, 8 ) as $h ) {
				$e['bloques'][ $h ] = $num( $in['estudio'][ $i ]['b'][ $h ] ?? $e['bloques'][ $h ] );
			}
		}
		unset( $e );
		$t['estudio']['precio_desde'] = min( array_map( static fn( $e ) => (int) $e['bloques'][1], $t['estudio']['espacios'] ) );

		foreach ( $t['podcast']['paquetes'] as $i => &$p ) {
			$p['nombre']      = sanitize_text_field( $in['podcast'][ $i ]['nombre'] ?? $p['nombre'] );
			$p['precio_hora'] = $num( $in['podcast'][ $i ]['precio'] ?? $p['precio_hora'] );
		}
		unset( $p );
		$t['podcast']['precio_desde']           = min( array_map( static fn( $p ) => (int) $p['precio_hora'], $t['podcast']['paquetes'] ) );
		$t['podcast']['horas_tarifa_publicada'] = max( 1, min( 8, $num( $in['podcast_horas'] ?? $t['podcast']['horas_tarifa_publicada'] ) ) );
		foreach ( array( 'estandar', 'avanzada' ) as $tipo ) {
			foreach ( array_keys( $t['podcast']['edicion'][ $tipo ] ) as $h ) {
				$t['podcast']['edicion'][ $tipo ][ $h ] = $num( $in['edicion'][ $tipo ][ $h ] ?? $t['podcast']['edicion'][ $tipo ][ $h ] );
			}
		}
		foreach ( $t['adicionales'] as &$a ) {
			if ( isset( $a['precio'] ) ) {
				$a['precio'] = $num( $in['adicional'][ $a['id'] ] ?? $a['precio'] );
			}
			if ( isset( $a['precio_hora'] ) ) {
				$a['precio_hora'] = $num( $in['adicional'][ $a['id'] ] ?? $a['precio_hora'] );
			}
		}
		unset( $a );
		update_option( 'impakto_tarifas', $t, false );
		wp_safe_redirect( add_query_arg( 'impakto_aviso', 'guardado', admin_url( 'admin.php?page=impakto-tarifas' ) ) );
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

	if ( 'guardado' === $aviso ) {
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Tarifas guardadas. Los cotizadores y las páginas ya muestran los nuevos valores.', 'impakto-core' ) . '</p></div>';
	}
	if ( ! $tarifas ) {
		echo '<p>' . esc_html__( 'Aún no hay tarifas. Importa el archivo inicial.', 'impakto-core' ) . '</p>';
	} else {
		$campo = static fn( $nombre, $valor, $ancho = 'small-text' ) => sprintf( '<input type="text" inputmode="numeric" class="%3$s" style="%4$s" name="%1$s" value="%2$s">', esc_attr( $nombre ), esc_attr( $valor ), esc_attr( $ancho ), 'small-text' === $ancho ? 'width:120px' : '' );
		echo '<p>' . esc_html__( 'Valores en pesos colombianos con IVA incluido (escribe solo números). Los equipos se editan uno por uno en', 'impakto-core' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=equipo' ) ) . '">' . esc_html__( 'Equipos', 'impakto-core' ) . '</a>.</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="impakto_tarifas_guardar">';
		wp_nonce_field( 'impakto_tarifas_guardar' );

		echo '<h2>' . esc_html__( 'Estudio (bloques de horas)', 'impakto-core' ) . '</h2><p class="description">' . esc_html__( 'El cotizador usa la combinación más barata de bloques de 1, 4 y 8 horas. El precio "desde" es el bloque de 1 hora más bajo.', 'impakto-core' ) . '</p>';
		echo '<table class="widefat striped" style="max-width:980px"><thead><tr><th>' . esc_html__( 'Nombre', 'impakto-core' ) . '</th><th>' . esc_html__( 'Descripción', 'impakto-core' ) . '</th><th>m²</th><th>1 h</th><th>4 h</th><th>8 h</th></tr></thead><tbody>';
		foreach ( $tarifas['estudio']['espacios'] as $i => $e ) {
			echo '<tr><td>' . $campo( "t[estudio][$i][nombre]", $e['nombre'], 'regular-text' ) . '</td><td>' . $campo( "t[estudio][$i][descripcion]", $e['descripcion'], 'regular-text' ) . '</td><td>' . $campo( "t[estudio][$i][area]", $e['area_m2'] ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			foreach ( array( 1, 4, 8 ) as $h ) {
				echo '<td>' . $campo( "t[estudio][$i][b][$h]", $e['bloques'][ $h ] ?? 0 ) . '</td>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</tr>';
		}
		echo '</tbody></table>';

		echo '<h2>' . esc_html__( 'Sala de podcast (precio por hora)', 'impakto-core' ) . '</h2><table class="widefat striped" style="max-width:640px"><tbody>';
		foreach ( $tarifas['podcast']['paquetes'] as $i => $p ) {
			echo '<tr><td>' . $campo( "t[podcast][$i][nombre]", $p['nombre'], 'regular-text' ) . '</td><td>' . $campo( "t[podcast][$i][precio]", $p['precio_hora'] ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</tbody></table><p><label>' . esc_html__( 'Horas con tarifa publicada (más horas = "sujeto a confirmación"):', 'impakto-core' ) . ' ' . $campo( 't[podcast_horas]', $tarifas['podcast']['horas_tarifa_publicada'] ) . '</label></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '<h2>' . esc_html__( 'Edición de podcast (según horas grabadas)', 'impakto-core' ) . '</h2><table class="widefat striped" style="max-width:640px"><thead><tr><th>' . esc_html__( 'Horas', 'impakto-core' ) . '</th><th>' . esc_html__( 'Estándar', 'impakto-core' ) . '</th><th>' . esc_html__( 'Avanzada', 'impakto-core' ) . '</th></tr></thead><tbody>';
		foreach ( array_keys( $tarifas['podcast']['edicion']['estandar'] ) as $h ) {
			echo '<tr><td>' . esc_html( $h ) . ' h</td><td>' . $campo( "t[edicion][estandar][$h]", $tarifas['podcast']['edicion']['estandar'][ $h ] ) . '</td><td>' . $campo( "t[edicion][avanzada][$h]", $tarifas['podcast']['edicion']['avanzada'][ $h ] ?? 0 ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</tbody></table>';

		echo '<h2>' . esc_html__( 'Adicionales y depósito', 'impakto-core' ) . '</h2><table class="widefat striped" style="max-width:640px"><tbody>';
		foreach ( $tarifas['adicionales'] as $a ) {
			if ( isset( $a['precio'] ) || isset( $a['precio_hora'] ) ) {
				echo '<tr><td>' . esc_html( $a['nombre'] . ' (' . $a['unidad'] . ')' ) . '</td><td>' . $campo( 't[adicional][' . $a['id'] . ']', $a['precio'] ?? $a['precio_hora'] ) . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
		echo '<tr><td>' . esc_html__( 'Depósito de seguridad reembolsable', 'impakto-core' ) . '</td><td>' . $campo( 't[deposito]', $tarifas['deposito_reembolsable'] ) . '</td></tr></tbody></table>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		submit_button( __( 'Guardar tarifas', 'impakto-core' ) );
		echo '</form>';
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
