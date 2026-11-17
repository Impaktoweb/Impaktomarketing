<?php
/**
 * Datos estructurados (BRIEF §13), coherentes con lo visible. Amplía el grafo de Rank Math:
 * - Organización global: ["ProfessionalService","LocalBusiness"] con NIT, dirección, geo, teléfono,
 *   areaServed, sameAs y horario (si está configurado).
 * - Estudio y Sala de podcast: Service + Offer con el precio "desde" (COP, IVA no incluido).
 * - Alquiler de equipos: Service con OfferCatalog por categoría.
 * - Servicios: Service (provider → organización). FAQPage lo genera el bloque FAQ de Rank Math.
 * - Proyectos: CreativeWork.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nodo de la organización.
 *
 * @return array<string,mixed>
 */
function impakto_schema_organizacion() {
	$id    = home_url( '/#organization' );
	$redes = array_values( array_filter( array_map( 'impakto_dato', array( 'instagram', 'facebook', 'linkedin', 'vimeo' ) ) ) );
	if ( impakto_dato( 'gbp_url' ) ) {
		$redes[] = impakto_dato( 'gbp_url' );
	}
	$nodo = array(
		'@type'      => array( 'ProfessionalService', 'LocalBusiness' ),
		'@id'        => $id,
		'name'       => impakto_dato( 'marca', 'Impakto Marketing' ),
		'legalName'  => impakto_dato( 'razon_social' ),
		'slogan'     => impakto_dato( 'lema' ),
		'taxID'      => impakto_dato( 'nit' ),
		'url'        => home_url( '/' ),
		'logo'       => array(
			'@type'  => 'ImageObject',
			'@id'    => home_url( '/#logo' ),
			'url'    => get_template_directory_uri() . '/assets/img/logo-impakto-color-1684.png',
			'width'  => 1684,
			'height' => 521,
		),
		'image'      => get_template_directory_uri() . '/assets/img/og-default.jpg',
		'telephone'  => impakto_dato( 'telefono' ),
		'address'    => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => impakto_dato( 'direccion' ),
			'addressLocality' => impakto_dato( 'ciudad' ),
			'addressRegion'   => impakto_dato( 'region' ),
			'postalCode'      => impakto_dato( 'codigo_postal' ),
			'addressCountry'  => 'CO',
		),
		'areaServed' => array(
			array( '@type' => 'City', 'name' => 'Manizales' ),
			array( '@type' => 'AdministrativeArea', 'name' => 'Caldas' ),
			array( '@type' => 'Place', 'name' => 'Eje Cafetero' ),
			array( '@type' => 'Country', 'name' => 'Colombia' ),
		),
		'sameAs'     => $redes,
		'knowsAbout' => array( 'Investigación de mercados', 'Naming', 'Branding', 'Producción audiovisual', 'Fotografía', 'Diseño gráfico', 'Redes sociales', 'Pauta digital', 'Desarrollo web y SEO', 'CRM y ventas digitales', 'Marketing inmobiliario' ),
	);
	if ( impakto_dato( 'correo_publico' ) ) {
		$nodo['email'] = impakto_dato( 'correo_publico' );
	}
	if ( impakto_dato( 'latitud' ) && impakto_dato( 'longitud' ) ) {
		$nodo['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) impakto_dato( 'latitud' ),
			'longitude' => (float) impakto_dato( 'longitud' ),
		);
	}
	if ( impakto_dato( 'horario_schema' ) ) {
		$nodo['openingHours'] = impakto_dato( 'horario_schema' );
	}
	$nodo['address'] = array_filter( $nodo['address'] );
	return array_filter( $nodo );
}

/**
 * Oferta con precio "desde" por hora.
 *
 * @param int $precio Precio.
 * @return array<string,mixed>
 */
function impakto_schema_oferta_desde( $precio ) {
	return array(
		'@type'              => 'Offer',
		'priceCurrency'      => 'COP',
		'price'              => $precio,
		'availability'       => 'https://schema.org/InStock',
		'priceSpecification' => array(
			'@type'                 => 'UnitPriceSpecification',
			'price'                 => $precio,
			'priceCurrency'         => 'COP',
			'unitText'              => 'hora',
			'unitCode'              => 'HUR',
			'valueAddedTaxIncluded' => false,
			'minPrice'              => $precio,
		),
		'areaServed'         => array( '@type' => 'City', 'name' => 'Manizales' ),
		'seller'             => array( '@id' => home_url( '/#organization' ) ),
	);
}

add_filter(
	'rank_math/json_ld',
	static function ( $data ) {
		$org = impakto_schema_organizacion();

		// Reemplaza la organización de Rank Math por la completa.
		foreach ( $data as $clave => $nodo ) {
			if ( is_array( $nodo ) && isset( $nodo['@id'] ) && home_url( '/#organization' ) === $nodo['@id'] ) {
				$data[ $clave ] = $org;
			}
		}
		if ( ! isset( $data['publisher'] ) ) {
			$data['publisher'] = $org;
		}

		$url  = get_permalink( get_queried_object_id() );
		$base = array(
			'@id'      => $url . '#service',
			'url'      => $url,
			'provider' => array( '@id' => home_url( '/#organization' ) ),
		);

		if ( is_page( array( 'estudio', 'sala-de-podcast' ) ) && function_exists( 'impakto_tarifas' ) ) {
			$es_estudio      = is_page( 'estudio' );
			$data['servicio'] = array_merge(
				array( '@type' => 'Service' ),
				$base,
				array(
					'name'        => $es_estudio ? 'Alquiler de estudio audiovisual y fotográfico en Manizales' : 'Alquiler de sala de podcast en Manizales',
					'serviceType' => $es_estudio ? 'Alquiler de estudio audiovisual' : 'Alquiler de sala de podcast',
					'description' => wp_strip_all_tags( (string) get_post_meta( get_queried_object_id(), 'rank_math_description', true ) ),
					'areaServed'  => array( '@type' => 'City', 'name' => 'Manizales' ),
					'offers'      => impakto_schema_oferta_desde( impakto_precio_desde( $es_estudio ? 'estudio' : 'podcast' ) ),
				)
			);
		}

		if ( is_page( 'alquiler-de-equipos' ) && function_exists( 'impakto_equipos_catalogo' ) ) {
			$catalogo = array();
			foreach ( impakto_equipos_catalogo()['categorias'] as $cat ) {
				$catalogo[] = array(
					'@type'           => 'OfferCatalog',
					'name'            => $cat['nombre'],
					'itemListElement' => array_map(
						static fn( $it ) => array(
							'@type'              => 'Offer',
							'itemOffered'        => array( '@type' => 'Product', 'name' => $it['nombre'] ),
							'priceCurrency'      => 'COP',
							'price'              => $it['precio'],
							'priceSpecification' => array(
								'@type'                 => 'UnitPriceSpecification',
								'price'                 => $it['precio'],
								'priceCurrency'         => 'COP',
								'unitText'              => 'jornada',
								'valueAddedTaxIncluded' => false,
							),
						),
						$cat['items']
					),
				);
			}
			$data['servicio'] = array_merge(
				array( '@type' => 'Service' ),
				$base,
				array(
					'name'            => 'Alquiler de equipos audiovisuales en Manizales',
					'serviceType'     => 'Alquiler de equipos audiovisuales',
					'areaServed'      => array( '@type' => 'City', 'name' => 'Manizales' ),
					'hasOfferCatalog' => array(
						'@type'           => 'OfferCatalog',
						'name'            => 'Catálogo de alquiler de equipos audiovisuales',
						'itemListElement' => $catalogo,
					),
				)
			);
		}

		if ( is_singular( 'servicio' ) ) {
			$data['servicio'] = array_merge(
				array( '@type' => 'Service' ),
				$base,
				array(
					'name'        => get_the_title( get_queried_object_id() ),
					'serviceType' => get_the_title( get_queried_object_id() ),
					'description' => get_the_excerpt( get_queried_object_id() ),
					'areaServed'  => $org['areaServed'],
				)
			);
		}

		if ( is_singular( 'proyecto' ) ) {
			$id   = get_queried_object_id();
			$cats = get_the_terms( $id, 'categoria-portafolio' );
			$data['proyecto'] = array_filter(
				array(
					'@type'       => 'CreativeWork',
					'@id'         => $url . '#creativework',
					'name'        => get_the_title( $id ),
					'description' => get_the_excerpt( $id ),
					'url'         => $url,
					'creator'     => array( '@id' => home_url( '/#organization' ) ),
					'dateCreated' => get_post_meta( $id, '_impakto_anio', true ),
					'about'       => $cats && ! is_wp_error( $cats ) ? wp_list_pluck( $cats, 'name' ) : array(),
					'image'       => has_post_thumbnail( $id ) ? get_the_post_thumbnail_url( $id, 'full' ) : '',
					'inLanguage'  => 'es-CO',
				)
			);
		}
		return $data;
	},
	99
);
