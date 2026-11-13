<?php
/**
 * Menú "Impakto" y pantalla Impakto → Ajustes.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Secciones y campos de los ajustes.
 *
 * @return array<string,array{titulo:string,campos:array<string,array<string,string>>}>
 */
function impakto_ajustes_esquema() {
	return array(
		'empresa'   => array(
			'titulo' => __( 'Empresa y dirección (NAP)', 'impakto-core' ),
			'campos' => array(
				'marca'         => array( 'etiqueta' => __( 'Marca', 'impakto-core' ) ),
				'lema'          => array( 'etiqueta' => __( 'Lema', 'impakto-core' ) ),
				'razon_social'  => array( 'etiqueta' => __( 'Razón social', 'impakto-core' ) ),
				'nit'           => array( 'etiqueta' => __( 'NIT', 'impakto-core' ) ),
				'direccion'     => array( 'etiqueta' => __( 'Dirección', 'impakto-core' ), 'ayuda' => __( '[CONFIRMAR] El contrato dice "Cra 22 A Calle 35 A".', 'impakto-core' ) ),
				'referencia'    => array( 'etiqueta' => __( 'Referencia', 'impakto-core' ) ),
				'ciudad'        => array( 'etiqueta' => __( 'Ciudad', 'impakto-core' ) ),
				'region'        => array( 'etiqueta' => __( 'Departamento', 'impakto-core' ) ),
				'codigo_postal' => array( 'etiqueta' => __( 'Código postal', 'impakto-core' ) ),
				'latitud'       => array( 'etiqueta' => __( 'Latitud', 'impakto-core' ), 'ayuda' => __( '[CONFIRMAR] Para el schema LocalBusiness.', 'impakto-core' ) ),
				'longitud'      => array( 'etiqueta' => __( 'Longitud', 'impakto-core' ) ),
				'mapa_url'      => array( 'etiqueta' => __( 'Enlace "Cómo llegar" (Google Maps)', 'impakto-core' ), 'tipo' => 'url' ),
				'gbp_url'       => array( 'etiqueta' => __( 'Google Business Profile', 'impakto-core' ), 'tipo' => 'url' ),
			),
		),
		'contacto'  => array(
			'titulo' => __( 'Contacto', 'impakto-core' ),
			'campos' => array(
				'telefono'       => array( 'etiqueta' => __( 'Teléfono visible', 'impakto-core' ) ),
				'whatsapp'       => array( 'etiqueta' => __( 'WhatsApp (solo números, con 57)', 'impakto-core' ) ),
				'correo_publico' => array( 'etiqueta' => __( 'Correo público', 'impakto-core' ), 'tipo' => 'email', 'ayuda' => __( 'Vacío = no se muestra en el sitio.', 'impakto-core' ) ),
				'horario'        => array( 'etiqueta' => __( 'Horario de atención', 'impakto-core' ), 'ayuda' => __( 'Vacío = no se muestra.', 'impakto-core' ) ),
				'disponibilidad' => array( 'etiqueta' => __( 'Disponibilidad de espacios', 'impakto-core' ) ),
			),
		),
		'destinos'  => array(
			'titulo' => __( 'Destinatarios de formularios y cotizaciones', 'impakto-core' ),
			'campos' => array(
				'correo_ceo' => array( 'etiqueta' => __( 'Correo CEO', 'impakto-core' ), 'tipo' => 'email' ),
				'correo_kam' => array( 'etiqueta' => __( 'Correo KAM', 'impakto-core' ), 'tipo' => 'email' ),
			),
		),
		'redes'     => array(
			'titulo' => __( 'Redes sociales', 'impakto-core' ),
			'campos' => array(
				'instagram' => array( 'etiqueta' => 'Instagram', 'tipo' => 'url' ),
				'facebook'  => array( 'etiqueta' => 'Facebook', 'tipo' => 'url' ),
				'linkedin'  => array( 'etiqueta' => 'LinkedIn', 'tipo' => 'url' ),
				'vimeo'     => array( 'etiqueta' => 'Vimeo', 'tipo' => 'url' ),
				'hashtag'   => array( 'etiqueta' => __( 'Hashtag', 'impakto-core' ) ),
			),
		),
		'analitica' => array(
			'titulo' => __( 'Analítica', 'impakto-core' ),
			'campos' => array(
				'ga4_id'           => array( 'etiqueta' => __( 'ID de medición GA4 (G-XXXXXXX)', 'impakto-core' ) ),
				'meta_pixel_id'    => array( 'etiqueta' => __( 'ID del Pixel de Meta', 'impakto-core' ) ),
				'gsc_verificacion' => array( 'etiqueta' => __( 'Verificación de Search Console (contenido del meta tag)', 'impakto-core' ), 'ayuda' => __( 'Opcional; se recomienda verificar por DNS.', 'impakto-core' ) ),
			),
		),
	);
}

add_action(
	'admin_menu',
	static function () {
		add_menu_page(
			__( 'Impakto', 'impakto-core' ),
			__( 'Impakto', 'impakto-core' ),
			'impakto_solicitudes',
			'impakto',
			'impakto_pantalla_solicitudes',
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240"><defs><mask id="m"><circle cx="120" cy="120" r="118" fill="#fff"/><rect x="113" width="14" height="240"/><rect y="113" width="240" height="14"/><rect x="86" y="86" width="68" height="68" rx="13"/></mask></defs><rect width="240" height="240" fill="#a7aaad" mask="url(#m)"/></svg>' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			3
		);
		add_submenu_page( 'impakto', __( 'Solicitudes', 'impakto-core' ), __( 'Solicitudes', 'impakto-core' ), 'impakto_solicitudes', 'impakto', 'impakto_pantalla_solicitudes' );
		add_submenu_page( 'impakto', __( 'Ajustes', 'impakto-core' ), __( 'Ajustes', 'impakto-core' ), 'manage_options', 'impakto-ajustes', 'impakto_pantalla_ajustes' );
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'impakto_ajustes',
			'impakto_ajustes',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'impakto_sanear_ajustes',
				'default'           => impakto_ajustes_defecto(),
			)
		);
		foreach ( impakto_ajustes_esquema() as $seccion_id => $seccion ) {
			add_settings_section( 'impakto_' . $seccion_id, $seccion['titulo'], '__return_false', 'impakto' );
			foreach ( $seccion['campos'] as $clave => $campo ) {
				add_settings_field(
					$clave,
					$campo['etiqueta'],
					'impakto_pintar_campo_ajuste',
					'impakto',
					'impakto_' . $seccion_id,
					array_merge( $campo, array( 'clave' => $clave, 'label_for' => 'impakto-' . $clave ) )
				);
			}
		}
	}
);

/**
 * Sanea todos los ajustes.
 *
 * @param mixed $entrada Valores enviados.
 * @return array<string,string>
 */
function impakto_sanear_ajustes( $entrada ) {
	$entrada = is_array( $entrada ) ? $entrada : array();
	$salida  = impakto_ajustes();
	foreach ( impakto_ajustes_esquema() as $seccion ) {
		foreach ( $seccion['campos'] as $clave => $campo ) {
			if ( ! array_key_exists( $clave, $entrada ) ) {
				continue;
			}
			$valor = wp_unslash( $entrada[ $clave ] );
			switch ( $campo['tipo'] ?? 'text' ) {
				case 'url':
					$salida[ $clave ] = esc_url_raw( trim( $valor ) );
					break;
				case 'email':
					$salida[ $clave ] = sanitize_email( $valor );
					break;
				default:
					$salida[ $clave ] = sanitize_text_field( $valor );
			}
		}
	}
	$salida['whatsapp'] = preg_replace( '/\D+/', '', $salida['whatsapp'] );
	return $salida;
}

/**
 * Pinta un campo.
 *
 * @param array<string,string> $args Definición.
 */
function impakto_pintar_campo_ajuste( $args ) {
	$valor = impakto_ajustes()[ $args['clave'] ] ?? '';
	$tipo  = in_array( $args['tipo'] ?? '', array( 'url', 'email' ), true ) ? $args['tipo'] : 'text';
	printf(
		'<input type="%1$s" class="regular-text" id="%2$s" name="impakto_ajustes[%3$s]" value="%4$s">',
		esc_attr( $tipo ),
		esc_attr( $args['label_for'] ),
		esc_attr( $args['clave'] ),
		esc_attr( $valor )
	);
	if ( ! empty( $args['ayuda'] ) ) {
		echo '<p class="description">' . esc_html( $args['ayuda'] ) . '</p>';
	}
}

/**
 * Pantalla de ajustes.
 */
function impakto_pantalla_ajustes() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Impakto → Ajustes', 'impakto-core' ) . '</h1>';
	echo '<p>' . esc_html__( 'Estos datos se usan en todo el sitio: header, footer, botones de WhatsApp, datos estructurados, correos y /llms.txt.', 'impakto-core' ) . '</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'impakto_ajustes' );
	do_settings_sections( 'impakto' );
	submit_button();
	echo '</form></div>';
}
