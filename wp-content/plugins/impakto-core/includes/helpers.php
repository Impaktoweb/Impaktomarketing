<?php
/**
 * Funciones de acceso a datos compartidas por el plugin y el tema.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores iniciales de Impakto → Ajustes (BRIEF §2).
 * Los campos vacíos son datos [CONFIRMAR] que Impakto debe completar.
 *
 * @return array<string,mixed>
 */
function impakto_ajustes_defecto() {
	return array(
		// Empresa.
		'marca'            => 'Impakto Marketing',
		'lema'             => 'The Marketing Factory',
		'razon_social'     => 'Impakto Marketing S.A.S.',
		'nit'              => '901.856.272-9',
		// NAP. [CONFIRMAR: el contrato dice "Cra 22 A Calle 35 A"].
		'direccion'        => 'Cra. 22 #35-62',
		'referencia'       => 'detrás de Hyundai',
		'ciudad'           => 'Manizales',
		'region'           => 'Caldas',
		'pais'             => 'CO',
		'codigo_postal'    => '',
		'latitud'          => '', // [CONFIRMAR].
		'longitud'         => '', // [CONFIRMAR].
		'mapa_url'         => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( 'Impakto Marketing, Cra. 22 #35-62, Manizales, Caldas' ),
		'gbp_url'          => '', // [CONFIRMAR].
		// Contacto. [CONFIRMAR].
		'telefono'         => '+57 312 552 5901',
		'whatsapp'         => '573125525901',
		'correo_publico'   => '', // [CONFIRMAR].
		'horario'          => '', // [CONFIRMAR].
		'disponibilidad'   => 'Estudio y sala de podcast 24/7 con reserva',
		// Destinatarios internos de formularios. [CONFIRMAR].
		'correo_ceo'       => '',
		'correo_kam'       => '',
		// Redes.
		'instagram'        => 'https://www.instagram.com/impaktomarketing/',
		'facebook'         => 'https://www.facebook.com/impaktomarketingagency',
		'linkedin'         => 'https://www.linkedin.com/company/impakto-marketing/',
		'vimeo'            => 'https://vimeo.com/294843577',
		'hashtag'          => '#Creaciónqueimpakta',
		// Analítica.
		'ga4_id'           => '',
		'meta_pixel_id'    => '',
		'gsc_verificacion' => '',
	);
}

/**
 * Devuelve todos los ajustes mezclados con los valores por defecto.
 *
 * @return array<string,mixed>
 */
function impakto_ajustes() {
	$guardados = get_option( 'impakto_ajustes', array() );
	return wp_parse_args( is_array( $guardados ) ? $guardados : array(), impakto_ajustes_defecto() );
}

/**
 * Devuelve un ajuste.
 *
 * @param string $clave   Clave del ajuste.
 * @param mixed  $defecto Valor si no existe o está vacío.
 * @return mixed
 */
function impakto_ajuste( $clave, $defecto = '' ) {
	$ajustes = impakto_ajustes();
	return ( isset( $ajustes[ $clave ] ) && '' !== $ajustes[ $clave ] ) ? $ajustes[ $clave ] : $defecto;
}

/**
 * Tarifas de espacios, podcast, edición y adicionales (sin el catálogo de equipos).
 *
 * @return array<string,mixed>
 */
function impakto_tarifas() {
	$tarifas = get_option( 'impakto_tarifas', array() );
	return is_array( $tarifas ) ? $tarifas : array();
}

/**
 * Formatea un valor en pesos colombianos: $1.200.000 COP.
 *
 * @param int|float $valor  Valor.
 * @param bool      $moneda Añadir " COP".
 * @return string
 */
function impakto_cop( $valor, $moneda = true ) {
	$texto = '$' . number_format( (float) $valor, 0, ',', '.' );
	return $moneda ? $texto . ' COP' : $texto;
}

/**
 * Precio "desde" de un espacio o del catálogo, leído de las tarifas.
 *
 * @param string $tipo estudio|podcast|equipos.
 * @return int
 */
function impakto_precio_desde( $tipo ) {
	$tarifas = impakto_tarifas();
	if ( 'equipos' === $tipo ) {
		$minimo = impakto_equipo_precio_minimo( 'camaras' );
		return $minimo ? $minimo : 0;
	}
	return isset( $tarifas[ $tipo ]['precio_desde'] ) ? (int) $tarifas[ $tipo ]['precio_desde'] : 0;
}

/**
 * Precio mínimo publicado de una categoría de equipos.
 *
 * @param string $categoria Slug de categoria-equipo.
 * @return int
 */
function impakto_equipo_precio_minimo( $categoria ) {
	$ids = get_posts(
		array(
			'post_type'      => 'equipo',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'categoria-equipo',
					'field'    => 'slug',
					'terms'    => $categoria,
				),
			),
		)
	);
	$precios = array_filter( array_map( static fn( $id ) => (int) get_post_meta( $id, '_impakto_precio', true ), $ids ) );
	return $precios ? min( $precios ) : 0;
}

/**
 * Número de WhatsApp sin signos (solo dígitos).
 *
 * @return string
 */
function impakto_whatsapp_numero() {
	return preg_replace( '/\D+/', '', (string) impakto_ajuste( 'whatsapp', '573125525901' ) );
}

/**
 * Grupos del proceso comercial (índice de servicios y mega menú).
 *
 * @return array<string,string>
 */
function impakto_bloques_proceso() {
	return array(
		'investigacion-marca'  => __( 'Investigación y marca', 'impakto-core' ),
		'contenido-produccion' => __( 'Contenido y producción', 'impakto-core' ),
		'marketing-digital'    => __( 'Marketing digital', 'impakto-core' ),
		'ventas'               => __( 'Ventas', 'impakto-core' ),
	);
}

/**
 * Enlace de WhatsApp para imprimir en un href conservando los saltos de línea (%0A).
 * esc_url() los elimina, así que aquí solo se acepta un enlace https://wa.me/ armado por el
 * propio plugin (el texto ya va con rawurlencode); cualquier otra cosa devuelve ''.
 *
 * @param string $url Enlace.
 * @return string
 */
function impakto_url_whatsapp_segura( $url ) {
	return preg_match( '#^https://wa\.me/\d+\?text=[A-Za-z0-9%._~\-]*$#', (string) $url ) ? $url : '';
}
