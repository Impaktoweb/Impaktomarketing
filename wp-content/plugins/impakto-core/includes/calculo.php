<?php
/**
 * Reglas de precio del cotizador (BRIEF §7.2).
 * El mismo algoritmo vive en assets/js/cotizador.js; el servidor siempre recalcula.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Combinación más barata de bloques que cubre al menos $horas.
 *
 * @param array<int|string,int> $bloques Mapa horas_del_bloque => precio (p. ej. 1 => 120000, 4 => 350000, 8 => 700000).
 * @param int                   $horas   Horas pedidas (≥ 1).
 * @return array{total:int,bloques:array<int,int>} Total y cantidad de cada bloque usado.
 */
function impakto_precio_bloques( array $bloques, $horas ) {
	$horas   = max( 1, (int) $horas );
	$bloques = array_filter(
		array_combine( array_map( 'intval', array_keys( $bloques ) ), array_map( 'intval', $bloques ) ),
		static fn( $precio, $duracion ) => $duracion > 0 && $precio > 0,
		ARRAY_FILTER_USE_BOTH
	);
	if ( ! $bloques ) {
		return array(
			'total'   => 0,
			'bloques' => array(),
		);
	}
	$maximo = $horas + max( array_keys( $bloques ) );

	// costo[h] = costo mínimo para cubrir exactamente h horas con bloques.
	$costo   = array_fill( 0, $maximo + 1, PHP_INT_MAX );
	$eleccion = array_fill( 0, $maximo + 1, 0 );
	$costo[0] = 0;
	for ( $h = 1; $h <= $maximo; $h++ ) {
		foreach ( $bloques as $duracion => $precio ) {
			if ( $duracion <= $h && PHP_INT_MAX !== $costo[ $h - $duracion ] && $costo[ $h - $duracion ] + $precio < $costo[ $h ] ) {
				$costo[ $h ]    = $costo[ $h - $duracion ] + $precio;
				$eleccion[ $h ] = $duracion;
			}
		}
	}

	// La mejor opción que cubre al menos $horas.
	$mejor = $horas;
	for ( $h = $horas; $h <= $maximo; $h++ ) {
		if ( $costo[ $h ] < $costo[ $mejor ] ) {
			$mejor = $h;
		}
	}

	$usados = array();
	for ( $h = $mejor; $h > 0; $h -= $eleccion[ $h ] ) {
		$usados[ $eleccion[ $h ] ] = ( $usados[ $eleccion[ $h ] ] ?? 0 ) + 1;
	}
	krsort( $usados );

	return array(
		'total'   => (int) $costo[ $mejor ],
		'bloques' => $usados,
	);
}

/**
 * Precio de un espacio del estudio.
 *
 * @param string $espacio_id medio-estudio|estudio-completo.
 * @param int    $horas      Horas.
 * @return int
 */
function impakto_precio_estudio( $espacio_id, $horas ) {
	$tarifas = impakto_tarifas();
	foreach ( $tarifas['estudio']['espacios'] ?? array() as $espacio ) {
		if ( $espacio['id'] === $espacio_id ) {
			return impakto_precio_bloques( $espacio['bloques'], $horas )['total'];
		}
	}
	return 0;
}

/**
 * Precio de la sala de podcast: precio por hora del paquete × horas.
 *
 * @param string $paquete_id Id del paquete.
 * @param int    $horas      Horas.
 * @return int
 */
function impakto_precio_podcast( $paquete_id, $horas ) {
	$tarifas = impakto_tarifas();
	foreach ( $tarifas['podcast']['paquetes'] ?? array() as $paquete ) {
		if ( $paquete['id'] === $paquete_id ) {
			return (int) $paquete['precio_hora'] * max( 1, (int) $horas );
		}
	}
	return 0;
}
