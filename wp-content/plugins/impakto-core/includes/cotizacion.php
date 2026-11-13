<?php
/**
 * Cotización en el servidor (BRIEF §7.2). Recalcula todo a partir de ids y cantidades:
 * nunca confía en el total que envía el navegador. assets/js/cotizador.js usa las mismas reglas.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Busca un equipo publicado por id (slug) en el catálogo.
 *
 * @param string $id Slug.
 * @return array<string,mixed>|null
 */
function impakto_equipo_por_id( $id ) {
	static $indice = null;
	if ( null === $indice ) {
		$indice = array();
		foreach ( impakto_equipos_catalogo()['categorias'] as $categoria ) {
			foreach ( $categoria['items'] as $item ) {
				$indice[ $item['id'] ] = $item;
			}
		}
	}
	return $indice[ $id ] ?? null;
}

/**
 * Calcula una cotización.
 *
 * @param string              $tipo estudio|podcast|equipos.
 * @param array<string,mixed> $sel  Selección del cliente.
 * @return array{tipo:string,servicio:string,lineas:array<int,array{concepto:string,detalle:string,valor:int}>,total:int,notas:string[],horas:int,dias:int}|WP_Error
 */
function impakto_cotizar( $tipo, $sel ) {
	$t      = impakto_tarifas();
	$lineas = array();
	$notas  = array();
	$horas  = max( 1, min( 8, absint( $sel['horas'] ?? 1 ) ) );
	$dias   = 'equipos' === $tipo ? max( 1, min( 7, absint( $sel['dias'] ?? 1 ) ) ) : 1;
	$servicio = '';

	if ( 'estudio' === $tipo ) {
		$espacio = null;
		foreach ( $t['estudio']['espacios'] ?? array() as $e ) {
			if ( ( $sel['espacio'] ?? '' ) === $e['id'] ) {
				$espacio = $e;
			}
		}
		if ( ! $espacio ) {
			return new WP_Error( 'impakto_espacio', __( 'Elige el formato del estudio.', 'impakto-core' ) );
		}
		$calculo  = impakto_precio_bloques( $espacio['bloques'], $horas );
		$detalle  = array();
		foreach ( $calculo['bloques'] as $duracion => $cantidad ) {
			$detalle[] = $cantidad . ' × bloque de ' . $duracion . ' h';
		}
		$servicio = sprintf( '%s (%d m²) – %d %s', $espacio['nombre'], $espacio['area_m2'], $horas, 1 === $horas ? 'hora' : 'horas' );
		$lineas[] = array(
			'concepto' => $servicio,
			'detalle'  => implode( ' + ', $detalle ),
			'valor'    => $calculo['total'],
		);
	} elseif ( 'podcast' === $tipo ) {
		$paquete = null;
		foreach ( $t['podcast']['paquetes'] ?? array() as $p ) {
			if ( ( $sel['paquete'] ?? '' ) === $p['id'] ) {
				$paquete = $p;
			}
		}
		if ( ! $paquete ) {
			return new WP_Error( 'impakto_paquete', __( 'Elige el paquete de cámaras y micrófonos.', 'impakto-core' ) );
		}
		$servicio = sprintf( 'Sala de podcast, %s – %d %s', $paquete['nombre'], $horas, 1 === $horas ? 'hora' : 'horas' );
		$lineas[] = array(
			'concepto' => $servicio,
			'detalle'  => impakto_cop( $paquete['precio_hora'], false ) . ' × ' . $horas . ' h',
			'valor'    => (int) $paquete['precio_hora'] * $horas,
		);
		if ( $horas > (int) ( $t['podcast']['horas_tarifa_publicada'] ?? 4 ) ) {
			$notas[] = $t['podcast']['nota_horas_extra'] ?? 'De 5 a 8 horas: sujeto a confirmación';
		}
		$edicion = sanitize_key( $sel['edicion'] ?? '' );
		if ( in_array( $edicion, array( 'estandar', 'avanzada' ), true ) ) {
			$tabla  = $t['podcast']['edicion'][ $edicion ] ?? array();
			$hora_e = (string) min( $horas, max( array_map( 'intval', array_keys( $tabla ) ) ) );
			$lineas[] = array(
				'concepto' => 'Edición de podcast ' . ( 'estandar' === $edicion ? 'estándar' : 'avanzada' ),
				'detalle'  => $hora_e . ' h grabadas',
				'valor'    => (int) ( $tabla[ $hora_e ] ?? 0 ),
			);
			if ( $horas > (int) $hora_e ) {
				$notas[] = 'Edición de más de ' . $hora_e . ' horas: sujeta a confirmación';
			}
		}
	} elseif ( 'equipos' === $tipo ) {
		$servicio = sprintf( 'Alquiler de equipos – %d %s', $dias, 1 === $dias ? 'jornada' : 'jornadas' );
	} else {
		return new WP_Error( 'impakto_tipo', __( 'Tipo de cotización no válido.', 'impakto-core' ) );
	}

	// Equipos (en estudio y podcast, 1 jornada).
	$equipos_txt = array();
	foreach ( (array) ( $sel['equipos'] ?? array() ) as $id => $cantidad ) {
		$cantidad = min( 20, absint( $cantidad ) );
		$equipo   = impakto_equipo_por_id( sanitize_title( $id ) );
		if ( ! $cantidad || ! $equipo || ! $equipo['precio'] ) {
			continue;
		}
		$lineas[]      = array(
			'concepto' => $cantidad . '× ' . $equipo['nombre'],
			'detalle'  => impakto_cop( $equipo['precio'], false ) . ' × ' . $cantidad . ( $dias > 1 ? ' × ' . $dias . ' jornadas' : ' · 1 jornada' ),
			'valor'    => (int) $equipo['precio'] * $cantidad * $dias,
		);
		$equipos_txt[] = $cantidad . '× ' . $equipo['nombre'];
		if ( $equipo['requiere_operador'] ) {
			$notas[] = $equipo['nombre'] . ': incluye piloto/operador — sujeto a confirmación';
		}
	}

	// Adicionales.
	$adicionales_txt = array();
	foreach ( $t['adicionales'] ?? array() as $a ) {
		if ( ! in_array( $tipo, $a['aplica'], true ) ) {
			continue;
		}
		if ( 'maquillaje' === $a['id'] && ! empty( $sel['adicionales']['maquillaje'] ) ) {
			$lineas[]          = array(
				'concepto' => $a['nombre'],
				'detalle'  => $a['unidad'],
				'valor'    => (int) $a['precio'],
			);
			$adicionales_txt[] = $a['nombre'];
		}
		if ( 'productor' === $a['id'] ) {
			$productores = min( 5, absint( $sel['adicionales']['productor'] ?? 0 ) );
			if ( $productores ) {
				$horas_p           = 'equipos' === $tipo ? 8 * $dias : $horas; // [CONFIRMAR] jornada de 8 h en alquiler de equipos.
				$lineas[]          = array(
					'concepto' => $productores . '× ' . $a['nombre'],
					'detalle'  => impakto_cop( $a['precio_hora'], false ) . ' × ' . $horas_p . ' h × ' . $productores,
					'valor'    => (int) $a['precio_hora'] * $horas_p * $productores,
				);
				$adicionales_txt[] = $productores . '× productor audiovisual';
				if ( 'equipos' === $tipo ) {
					$notas[] = 'Productor en alquiler de equipos: jornada de 8 horas, sujeto a confirmación';
				}
			}
		}
	}
	if ( ! empty( $sel['edicion'] ) && 'podcast' === $tipo && in_array( $sel['edicion'], array( 'estandar', 'avanzada' ), true ) ) {
		$adicionales_txt[] = 'Edición ' . ( 'estandar' === $sel['edicion'] ? 'estándar' : 'avanzada' );
	}

	if ( 'equipos' === $tipo && ! $equipos_txt ) {
		return new WP_Error( 'impakto_vacio', __( 'Agrega al menos un equipo.', 'impakto-core' ) );
	}

	return array(
		'tipo'        => $tipo,
		'servicio'    => $servicio,
		'lineas'      => $lineas,
		'total'       => array_sum( wp_list_pluck( $lineas, 'valor' ) ),
		'notas'       => array_values( array_unique( $notas ) ),
		'horas'       => $horas,
		'dias'        => $dias,
		'equipos'     => $equipos_txt,
		'adicionales' => $adicionales_txt,
	);
}

/**
 * Mensaje de WhatsApp con el resumen (formato del BRIEF §7.3).
 *
 * @param array<string,mixed> $cot         Cotización.
 * @param string              $consecutivo IMP-AAAA-NNNN.
 * @param array<string,mixed> $sel         Selección.
 * @param array<string,string> $contacto   Datos de contacto.
 * @return string
 */
function impakto_mensaje_whatsapp( $cot, $consecutivo, $sel, $contacto ) {
	$lineas = array(
		'Hola Impakto 👋 Quiero validar disponibilidad.',
		'Cotización ' . $consecutivo,
		'Servicio: ' . $cot['servicio'],
	);
	$fecha = impakto_fecha_legible( $sel['fecha'] ?? '', $sel['hora'] ?? '' );
	if ( $fecha ) {
		$lineas[] = ( 'equipos' === $cot['tipo'] ? 'Fecha de recogida: ' : 'Fecha deseada: ' ) . $fecha;
	}
	if ( $cot['equipos'] ) {
		$lineas[] = 'Equipos: ' . implode( ', ', $cot['equipos'] );
	}
	if ( $cot['adicionales'] ) {
		$lineas[] = 'Adicionales: ' . implode( ', ', $cot['adicionales'] );
	}
	$lineas[] = 'Total estimado: ' . impakto_cop( $cot['total'] ) . ' (IVA incluido)';
	$lineas[] = 'Nombre: ' . $contacto['nombre'] . ' · Celular: ' . $contacto['celular'];
	return implode( "\n", $lineas );
}

/**
 * "2026-11-14" + "09:00" → "14/11/2026 · 9:00 a. m.".
 *
 * @param string $fecha Fecha ISO.
 * @param string $hora  Hora HH:MM.
 * @return string
 */
function impakto_fecha_legible( $fecha, $hora ) {
	$texto = '';
	if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', (string) $fecha, $m ) ) {
		$texto = "$m[3]/$m[2]/$m[1]";
	}
	if ( preg_match( '/^(\d{1,2}):(\d{2})$/', (string) $hora, $m ) ) {
		$h      = (int) $m[1];
		$sufijo = $h < 12 ? 'a. m.' : 'p. m.';
		$h12    = 0 === $h % 12 ? 12 : $h % 12;
		$texto .= ( $texto ? ' · ' : '' ) . $h12 . ':' . $m[2] . ' ' . $sufijo;
	}
	return $texto;
}
