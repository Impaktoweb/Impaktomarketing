<?php
/**
 * Campos personalizados (meta) y meta boxes nativos para servicio, proyecto y equipo.
 * Cada campo se define una sola vez aquí: se registra en la REST API y se pinta en su caja.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Definición de campos por tipo de contenido.
 *
 * @return array<string,array<string,array<string,mixed>>>
 */
function impakto_campos() {
	return array(
		'servicio' => array(
			'bloque'               => array(
				'etiqueta' => __( 'Bloque del proceso comercial', 'impakto-core' ),
				'tipo'     => 'select',
				'opciones' => 'impakto_bloques_proceso',
				'ayuda'    => __( 'Agrupa el servicio en el índice y en el mega menú.', 'impakto-core' ),
			),
			'categoria_portafolio' => array(
				'etiqueta' => __( 'Categoría del portafolio vinculada', 'impakto-core' ),
				'tipo'     => 'select',
				'opciones' => 'impakto_opciones_categorias_portafolio',
				'ayuda'    => __( 'Alimenta "Proyectos relacionados" y el enlace al portafolio.', 'impakto-core' ),
			),
			'whatsapp_mensaje'     => array(
				'etiqueta' => __( 'Mensaje precargado de WhatsApp', 'impakto-core' ),
				'tipo'     => 'text',
				'ayuda'    => __( 'Vacío = "Hola Impakto, quiero información sobre {Servicio}."', 'impakto-core' ),
			),
			'relacionado_1'        => array(
				'etiqueta' => __( 'Servicio relacionado 1', 'impakto-core' ),
				'tipo'     => 'post',
				'post'     => 'servicio',
			),
			'relacionado_2'        => array(
				'etiqueta' => __( 'Servicio relacionado 2', 'impakto-core' ),
				'tipo'     => 'post',
				'post'     => 'servicio',
			),
			'articulo'             => array(
				'etiqueta' => __( 'Artículo relacionado', 'impakto-core' ),
				'tipo'     => 'post',
				'post'     => 'post',
			),
		),
		'proyecto' => array(
			'cliente'    => array(
				'etiqueta' => __( 'Cliente', 'impakto-core' ),
				'tipo'     => 'text',
			),
			'sector'     => array(
				'etiqueta' => __( 'Sector', 'impakto-core' ),
				'tipo'     => 'text',
			),
			'anio'       => array(
				'etiqueta' => __( 'Año', 'impakto-core' ),
				'tipo'     => 'text',
			),
			'ubicacion'  => array(
				'etiqueta' => __( 'Ciudad / país', 'impakto-core' ),
				'tipo'     => 'text',
			),
			'destacado'  => array(
				'etiqueta' => __( 'Destacado (aparece en Inicio y en tamaño grande)', 'impakto-core' ),
				'tipo'     => 'checkbox',
			),
			'caso_exito' => array(
				'etiqueta' => __( 'Caso de éxito (tiene resultados con cifras)', 'impakto-core' ),
				'tipo'     => 'checkbox',
			),
			'pills'      => array(
				'etiqueta' => __( 'Pills de atributos (separados por coma)', 'impakto-core' ),
				'tipo'     => 'text',
			),
			'resultados' => array(
				'etiqueta' => __( 'Resultados (una línea por resultado: métrica | valor | nota con fecha)', 'impakto-core' ),
				'tipo'     => 'lineas',
				'ayuda'    => __( 'El primero se muestra como cifra principal en la tarjeta. Ej.: ROAS | 836 | Constructora Hungría, ene 2026', 'impakto-core' ),
			),
		),
		'equipo'   => array(
			'precio'            => array(
				'etiqueta' => __( 'Precio (COP, IVA incluido)', 'impakto-core' ),
				'tipo'     => 'number',
				'ayuda'    => __( 'Vacío o 0 = "Consultar precio" y no suma al total.', 'impakto-core' ),
			),
			'unidad'            => array(
				'etiqueta' => __( 'Unidad del precio', 'impakto-core' ),
				'tipo'     => 'select',
				'opciones' => 'impakto_unidades_precio',
				'ayuda'    => __( '[CONFIRMAR] unidad de la lista de equipos.', 'impakto-core' ),
			),
			'requiere_operador' => array(
				'etiqueta' => __( 'Requiere operador o piloto de Impakto', 'impakto-core' ),
				'tipo'     => 'checkbox',
			),
			'specs'             => array(
				'etiqueta' => __( 'Especificaciones clave (una por línea, 2 o 3)', 'impakto-core' ),
				'tipo'     => 'lineas',
			),
		),
	);
}

/**
 * Opciones de unidad de precio para equipos.
 *
 * @return array<string,string>
 */
function impakto_unidades_precio() {
	return array(
		'jornada' => __( 'Por jornada', 'impakto-core' ),
		'dia'     => __( 'Por día', 'impakto-core' ),
		'hora'    => __( 'Por hora', 'impakto-core' ),
	);
}

/**
 * Opciones de categoría del portafolio (slug => nombre).
 *
 * @return array<string,string>
 */
function impakto_opciones_categorias_portafolio() {
	return impakto_categorias_portafolio();
}

/**
 * Lee un campo con su prefijo interno.
 *
 * @param int    $post_id Id.
 * @param string $campo   Nombre del campo.
 * @return mixed
 */
function impakto_meta( $post_id, $campo ) {
	return get_post_meta( $post_id, '_impakto_' . $campo, true );
}

/**
 * Resultados de un proyecto como lista de arrays métrica/valor/nota.
 *
 * @param int $post_id Id.
 * @return array<int,array{metrica:string,valor:string,nota:string}>
 */
function impakto_resultados( $post_id ) {
	$salida = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) impakto_meta( $post_id, 'resultados' ) ) as $linea ) {
		$partes = array_map( 'trim', explode( '|', $linea ) );
		if ( count( $partes ) >= 2 && '' !== $partes[0] ) {
			$salida[] = array(
				'metrica' => $partes[0],
				'valor'   => $partes[1],
				'nota'    => $partes[2] ?? '',
			);
		}
	}
	return $salida;
}

/**
 * Registra los meta en la REST API.
 */
add_action(
	'init',
	static function () {
		foreach ( impakto_campos() as $tipo => $campos ) {
			foreach ( $campos as $nombre => $campo ) {
				$es_bool = 'checkbox' === $campo['tipo'];
				$es_num  = in_array( $campo['tipo'], array( 'number', 'post' ), true );
				register_post_meta(
					$tipo,
					'_impakto_' . $nombre,
					array(
						'type'              => $es_bool ? 'boolean' : ( $es_num ? 'integer' : 'string' ),
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => static fn( $valor ) => impakto_sanear_campo( $campo, $valor ),
						'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
					)
				);
			}
		}
	},
	11
);

/**
 * Sanea un valor según el tipo de campo.
 *
 * @param array<string,mixed> $campo Definición.
 * @param mixed               $valor Valor.
 * @return mixed
 */
function impakto_sanear_campo( $campo, $valor ) {
	switch ( $campo['tipo'] ) {
		case 'checkbox':
			return (bool) $valor;
		case 'number':
		case 'post':
			return absint( $valor );
		case 'url':
			return esc_url_raw( $valor );
		case 'lineas':
		case 'textarea':
			return sanitize_textarea_field( $valor );
		case 'select':
			$opciones = call_user_func( $campo['opciones'] );
			return array_key_exists( (string) $valor, $opciones ) ? (string) $valor : '';
		default:
			return sanitize_text_field( $valor );
	}
}

/**
 * Meta boxes.
 */
add_action(
	'add_meta_boxes',
	static function () {
		$titulos = array(
			'servicio' => __( 'Datos del servicio', 'impakto-core' ),
			'proyecto' => __( 'Ficha del proyecto', 'impakto-core' ),
			'equipo'   => __( 'Precio y detalles', 'impakto-core' ),
		);
		foreach ( $titulos as $tipo => $titulo ) {
			add_meta_box( 'impakto-campos', $titulo, 'impakto_pintar_meta_box', $tipo, 'normal', 'high' );
		}
	}
);

/**
 * Pinta la caja de campos.
 *
 * @param WP_Post $post Entrada.
 */
function impakto_pintar_meta_box( $post ) {
	$campos = impakto_campos()[ $post->post_type ] ?? array();
	wp_nonce_field( 'impakto_campos_guardar', 'impakto_campos_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $campos as $nombre => $campo ) {
		$id    = 'impakto_' . $nombre;
		$valor = impakto_meta( $post->ID, $nombre );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $campo['etiqueta'] ) . '</label></th><td>';
		switch ( $campo['tipo'] ) {
			case 'checkbox':
				printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>', esc_attr( $id ), checked( (bool) $valor, true, false ) );
				break;
			case 'number':
				printf( '<input type="number" min="0" step="1000" class="regular-text" id="%1$s" name="%1$s" value="%2$s">', esc_attr( $id ), esc_attr( $valor ) );
				break;
			case 'lineas':
			case 'textarea':
				printf( '<textarea class="large-text" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', esc_attr( $id ), esc_textarea( $valor ) );
				break;
			case 'select':
				printf( '<select id="%1$s" name="%1$s"><option value="">—</option>', esc_attr( $id ) );
				foreach ( call_user_func( $campo['opciones'] ) as $clave => $texto ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $clave ), selected( $valor, $clave, false ), esc_html( $texto ) );
				}
				echo '</select>';
				break;
			case 'post':
				$opciones = get_posts(
					array(
						'post_type'      => $campo['post'],
						'post_status'    => array( 'publish', 'draft' ),
						'posts_per_page' => 100,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'exclude'        => array( $post->ID ),
					)
				);
				printf( '<select id="%1$s" name="%1$s"><option value="">—</option>', esc_attr( $id ) );
				foreach ( $opciones as $opcion ) {
					printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $opcion->ID, selected( (int) $valor, $opcion->ID, false ), esc_html( get_the_title( $opcion ) ) );
				}
				echo '</select>';
				break;
			default:
				printf( '<input type="text" class="large-text" id="%1$s" name="%1$s" value="%2$s">', esc_attr( $id ), esc_attr( $valor ) );
		}
		if ( ! empty( $campo['ayuda'] ) ) {
			echo '<p class="description">' . esc_html( $campo['ayuda'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Guarda los campos de la caja.
 */
add_action(
	'save_post',
	static function ( $post_id, $post ) {
		if ( ! isset( $_POST['impakto_campos_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['impakto_campos_nonce'] ), 'impakto_campos_guardar' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( impakto_campos()[ $post->post_type ] ?? array() as $nombre => $campo ) {
			$clave = 'impakto_' . $nombre;
			$valor = isset( $_POST[ $clave ] ) ? wp_unslash( $_POST[ $clave ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se sanea abajo.
			update_post_meta( $post_id, '_' . $clave, impakto_sanear_campo( $campo, $valor ) );
		}
	},
	10,
	2
);
