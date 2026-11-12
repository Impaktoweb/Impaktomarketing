<?php
/**
 * Contenido inicial (studio wp impakto sembrar). Textos tomados de impakto-web/contenido/.
 * Fase 1: estructura y datos de tarjetas. Las páginas completas de servicio (Fase 2),
 * los casos (Fase 4) y el cuerpo de los artículos (Fase 4) se cargan en sus fases.
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Crea o actualiza una entrada por slug.
 *
 * @param array<string,mixed> $datos  Campos de wp_insert_post + 'meta' + 'terminos'.
 * @param bool                $forzar Sobrescribir si existe.
 * @return int|string Id o 'igual'.
 */
function impakto_semilla_entrada( $datos, $forzar ) {
	$existente = get_page_by_path( $datos['post_name'], OBJECT, $datos['post_type'] );
	if ( $existente && ! $forzar ) {
		return 'igual';
	}
	$meta     = $datos['meta'] ?? array();
	$terminos = $datos['terminos'] ?? array();
	unset( $datos['meta'], $datos['terminos'] );
	if ( $existente ) {
		$datos['ID'] = $existente->ID;
	}
	$id = wp_insert_post( wp_slash( $datos ), true );
	if ( is_wp_error( $id ) ) {
		return 'error: ' . $id->get_error_message();
	}
	foreach ( $meta as $clave => $valor ) {
		update_post_meta( $id, '_impakto_' . $clave, $valor );
	}
	foreach ( $terminos as $taxonomia => $slugs ) {
		wp_set_object_terms( $id, $slugs, $taxonomia );
	}
	return $id;
}

/**
 * Servicios (BRIEF §6.2 + contenido/paginas/servicios-indice.md).
 *
 * @param bool $forzar Sobrescribir.
 * @return string
 */
function impakto_sembrar_servicios( $forzar ) {
	$servicios = array(
		// slug, título, bloque, categoría, extracto, relacionados, artículo, estado.
		array( 'investigacion-de-mercados', 'Investigación de mercados', 'investigacion-marca', 'branding', 'Análisis del mercado, benchmarking de la competencia, revisión de su pauta y mystery shopper.', array( 'naming', 'branding' ), 'como-elegir-agencia-marketing-digital-manizales', 'publish' ),
		array( 'naming', 'Naming', 'investigacion-marca', 'naming', 'Nombres únicos, memorables y registrables, validados con criterios de registro, recordación y fonética.', array( 'branding', 'investigacion-de-mercados' ), 'marketing-inmobiliario-generar-leads-vender-proyectos', 'publish' ),
		array( 'branding', 'Branding', 'investigacion-marca', 'branding', 'Brand essence, brandbook e identidad visual para que tu marca se vea y suene igual en todos los puntos de contacto.', array( 'naming', 'diseno-grafico' ), 'marketing-inmobiliario-generar-leads-vender-proyectos', 'publish' ),
		array( 'produccion-audiovisual', 'Producción audiovisual', 'contenido-produccion', 'produccion-audiovisual', 'Videos para redes y pauta, recorridos, comerciales, videoclips y podcast.', array( 'fotografia', 'redes-sociales' ), 'alquiler-equipos-audiovisuales-manizales', 'publish' ),
		array( 'fotografia', 'Fotografía', 'contenido-produccion', 'fotografia', 'Producto, corporativa, arquitectura e interiorismo, y moda.', array( 'produccion-audiovisual', 'diseno-grafico' ), 'alquiler-estudio-fotografico-audiovisual-manizales', 'publish' ),
		array( 'diseno-grafico', 'Diseño gráfico', 'contenido-produccion', 'diseno-grafico', 'Vallas, paneles para ferias, brochures, carpetas corporativas, volantes y material POP.', array( 'branding', 'redes-sociales' ), 'marketing-inmobiliario-generar-leads-vender-proyectos', 'publish' ),
		array( 'animacion-3d', 'Animación y modelación 3D', 'contenido-produccion', 'produccion-audiovisual', 'Renders, recorridos virtuales y animaciones para presentar productos y proyectos.', array( 'produccion-audiovisual', 'branding' ), '', 'draft' ), // [CONFIRMAR si sigue activo].
		array( 'redes-sociales', 'Redes sociales', 'marketing-digital', 'comunicacion-digital-redes', 'Planeación, contenido, carruseles, historias y comunidad.', array( 'pauta-digital', 'produccion-audiovisual' ), 'alquiler-sala-de-podcast-manizales', 'publish' ),
		array( 'pauta-digital', 'Pauta digital', 'marketing-digital', 'pauta-digital', 'Campañas en Meta y Google Ads con segmentación local e internacional.', array( 'crm-ventas-digitales', 'desarrollo-web-seo' ), 'marketing-inmobiliario-generar-leads-vender-proyectos', 'publish' ),
		array( 'desarrollo-web-seo', 'Desarrollo web y SEO', 'marketing-digital', 'sitio-web-seo', 'Sitios rápidos, landing pages y posicionamiento orgánico.', array( 'pauta-digital', 'crm-ventas-digitales' ), 'como-elegir-agencia-marketing-digital-manizales', 'publish' ),
		array( 'crm-ventas-digitales', 'CRM y ventas digitales', 'ventas', 'crm-ventas-digitales', 'Implementación de CRM, automatización, WhatsApp Business, speech y capacitación del equipo comercial.', array( 'pauta-digital', 'desarrollo-web-seo' ), 'marketing-inmobiliario-generar-leads-vender-proyectos', 'publish' ),
		array( 'desarrollo-de-producto', 'Desarrollo de producto', 'ventas', '', 'Concepto, empaque y salida al mercado de nuevos productos.', array( 'naming', 'branding' ), '', 'draft' ), // [CONFIRMAR si sigue activo].
	);

	$creados = 0;
	foreach ( $servicios as $orden => list( $slug, $titulo, $bloque, $categoria, $extracto, , , $estado ) ) {
		$r = impakto_semilla_entrada(
			array(
				'post_type'    => 'servicio',
				'post_name'    => $slug,
				'post_title'   => $titulo,
				'post_excerpt' => $extracto,
				'post_status'  => $estado,
				'menu_order'   => $orden + 1,
				'meta'         => array(
					'bloque'               => $bloque,
					'categoria_portafolio' => $categoria,
				),
			),
			$forzar
		);
		$creados += is_int( $r ) ? 1 : 0;
	}

	// Relaciones (cuando ya existen todos los servicios y los artículos).
	foreach ( $servicios as list( $slug, , , , , $relacionados, $articulo ) ) {
		$servicio = get_page_by_path( $slug, OBJECT, 'servicio' );
		if ( ! $servicio ) {
			continue;
		}
		foreach ( array_values( $relacionados ) as $i => $rel ) {
			$otro = get_page_by_path( $rel, OBJECT, 'servicio' );
			if ( $otro ) {
				update_post_meta( $servicio->ID, '_impakto_relacionado_' . ( $i + 1 ), $otro->ID );
			}
		}
		$post = $articulo ? get_page_by_path( $articulo, OBJECT, 'post' ) : null;
		if ( $post ) {
			update_post_meta( $servicio->ID, '_impakto_articulo', $post->ID );
		}
	}
	return "$creados creados/actualizados";
}

/**
 * Proyectos del portafolio (contenido/portafolio/portafolio.md).
 *
 * @param bool $forzar Sobrescribir.
 * @return string
 */
function impakto_sembrar_proyectos( $forzar ) {
	foreach ( impakto_categorias_portafolio() as $slug => $nombre ) {
		if ( ! term_exists( $slug, 'categoria-portafolio' ) ) {
			wp_insert_term( $nombre, 'categoria-portafolio', array( 'slug' => $slug ) );
		}
	}

	$proyectos = array(
		array(
			'slug'       => 'constructora-hungria-ventas-digitales',
			'titulo'     => 'Constructora Hungría: ventas digitales de punta a punta',
			'resumen'    => 'Más de seis años acompañando la comercialización de los proyectos de Constructora Hungría, en alianza con Perfek.',
			'cliente'    => 'Constructora Hungría',
			'sector'     => 'Construcción e inmobiliario',
			'ubicacion'  => 'Manizales',
			'anio'       => '2026',
			'cats'       => array( 'comunicacion-digital-redes', 'sitio-web-seo', 'crm-ventas-digitales', 'pauta-digital' ),
			'destacado'  => true,
			'caso'       => true,
			'pills'      => 'Estrategia, Performance, CRM, SEO',
			'resultados' => "ROAS | 836,7 | Proyecto 1, enero de 2026\nROAS | 738,6 | Proyecto 2, enero de 2026\nVentas del canal digital | 28 % – 50 % | Del total mensual\nVentas del exterior | 1 al mes | Mínimo, cada mes\nClics orgánicos | 2,44 mil | Sitio web, enero a junio de 2026\nImpresiones | 19,5 mil | Sitio web, enero a junio de 2026\nCTR orgánico | 12,5 % | Sitio web, enero a junio de 2026\nPosición media | 9,1 | Google, enero a junio de 2026\nVisualizaciones en Facebook | 613,6 mil | Enero de 2026 (+28,9 %)\nVisualizaciones en Instagram | 387,4 mil | Enero de 2026 (+22,3 %)",
			'reto'       => 'Convertir el canal digital en una fuente constante de ventas y lograr que ningún lead se perdiera entre la pauta y el asesor.',
		),
		array(
			'slug'       => 'altaviva',
			'titulo'     => 'Altaviva: lanzamiento y generación de leads',
			'resumen'    => 'Campaña de lanzamiento de Altaviva Homes & Suites (Constructora Hungría y Perfek) en el sector Ancón Sur.',
			'cliente'    => 'Altaviva Homes & Suites',
			'sector'     => 'Inmobiliario',
			'ubicacion'  => 'Sabaneta (Antioquia)',
			'anio'       => '2026',
			'cats'       => array( 'pauta-digital', 'diseno-grafico', 'produccion-audiovisual' ),
			'destacado'  => true,
			'caso'       => true,
			'pills'      => '',
			'resultados' => "Leads | 300 | Formulario en Meta, 1 de febrero al 18 de marzo de 2026\nCosto por lead | $3.014 | 20 % más bajo que la mediana de anuncios similares, según Meta\nInversión | $904.267 | Pauta en Meta, febrero y marzo de 2026",
		),
		array(
			'slug'      => 'reserva-de-hungria',
			'titulo'    => 'Reserva de Hungría: marca, contenido y ayudaventas',
			'resumen'   => 'Brand essence, líneas gráficas para redes, mailing y ayudaventas interactivo para el condominio Reserva de Hungría.',
			'cliente'   => 'Reserva de Hungría',
			'sector'    => 'Inmobiliario',
			'ubicacion' => 'Manizales',
			'cats'      => array( 'branding', 'comunicacion-digital-redes', 'diseno-grafico' ),
			'destacado' => true,
			'pills'     => 'Aspiracional, Serena, Exclusiva',
		),
		array(
			'slug'    => 'serranova',
			'titulo'  => 'Serranova: naming de marca cárnica premium',
			'resumen' => 'Nombre para una marca cárnica premium con origen rural.',
			'cliente' => 'Serranova',
			'cats'    => array( 'naming' ),
			'pills'   => 'Memorable, Premium, Registrable, Alta sonoridad, Escalable, Diferenciador',
		),
		array(
			'slug'    => 'del-estrado',
			'titulo'  => 'Del Estrado: naming de café especial',
			'resumen' => 'Nombre para una marca de café especial de altura.',
			'cliente' => 'Del Estrado',
			'cats'    => array( 'naming' ),
			'pills'   => 'Lugar de honor, Premium, Registrable, Legado cultural, Escalable, Alta sonoridad',
		),
		array(
			'slug'      => 'axis-plaza',
			'titulo'    => 'Axis Plaza: brandbook',
			'resumen'   => 'Brandbook completo de un centro comercial.',
			'cliente'   => 'Axis Plaza',
			'sector'    => 'Centro comercial',
			'ubicacion' => 'Punta Cana (República Dominicana)',
			'cats'      => array( 'branding' ),
			'destacado' => true,
		),
		array(
			'slug'      => 'dyme-metals',
			'titulo'    => 'Dyme Metals: brandbook',
			'resumen'   => 'Identidad para un proveedor de bobinas y componentes metálicos para techos.',
			'cliente'   => 'Dyme Metals',
			'sector'    => 'Industria metalmecánica',
			'ubicacion' => 'Estados Unidos',
			'cats'      => array( 'branding', 'sitio-web-seo' ),
		),
		array(
			'slug'      => 'axel-park',
			'titulo'    => 'Axel Park: brandbook',
			'resumen'   => 'Marca del "Eje logístico de Punta Cana".',
			'cliente'   => 'Axel Park',
			'sector'    => 'Parque logístico',
			'ubicacion' => 'Santo Domingo / Punta Cana',
			'cats'      => array( 'branding', 'diseno-grafico' ),
		),
		array(
			'slug'      => 'elite-metals',
			'titulo'    => 'Elite Metals: brand essence',
			'resumen'   => 'Brand essence para un proveedor de suministros para techos: personalidad, valores, propósito y tagline.',
			'cliente'   => 'Elite Metals',
			'sector'    => 'Suministros para techos (roofing)',
			'ubicacion' => 'Estados Unidos',
			'cats'      => array( 'branding' ),
		),
		array(
			'slug'      => 'boma',
			'titulo'    => 'Bôma: lanzamiento',
			'resumen'   => 'Valla de gran lanzamiento y paneles para feria de apartamentos de 50 a 66 m².',
			'cliente'   => 'Bôma',
			'sector'    => 'Inmobiliario',
			'ubicacion' => 'La Estrella (Antioquia)',
			'cats'      => array( 'diseno-grafico', 'produccion-audiovisual' ),
		),
		array(
			'slug'      => 'makura',
			'titulo'    => 'Makura: últimos apartamentos',
			'resumen'   => 'Panel de feria "Últimos apartamentos: un proyecto en el centro de todo" y landing del proyecto con formulario y WhatsApp.',
			'cliente'   => 'Makura',
			'sector'    => 'Inmobiliario',
			'ubicacion' => 'Manizales',
			'cats'      => array( 'diseno-grafico', 'sitio-web-seo' ),
		),
		array(
			'slug'    => 'carpeta-corporativa-constructora-hungria',
			'titulo'  => 'Constructora Hungría: carpeta corporativa',
			'resumen' => 'Carpeta corporativa "Construimos el hogar de tus sueños".',
			'cliente' => 'Constructora Hungría',
			'cats'    => array( 'diseno-grafico' ),
		),
		array(
			'slug'    => 'recorrido-mesa-de-yeguas',
			'titulo'  => 'Mesa de Yeguas: recorrido audiovisual',
			'resumen' => 'Recorrido audiovisual del proyecto Mesa de Yeguas.',
			'cliente' => 'Mesa de Yeguas',
			'cats'    => array( 'produccion-audiovisual' ),
		),
		array(
			'slug'    => 'videos-redes-pauta-inmobiliario',
			'titulo'  => 'Videos para redes y pauta en Meta',
			'resumen' => 'Reels y videos de pauta para Altaviva, Reserva de Hungría (Torre Tucán) y apartamentos tipo mariposa: testimoniales, recorridos y ofertas del mes.',
			'cats'    => array( 'produccion-audiovisual' ),
		),
	);

	$n = 0;
	foreach ( $proyectos as $orden => $p ) {
		$contenido = '';
		if ( ! empty( $p['reto'] ) ) {
			$contenido = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Reto</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>" . esc_html( $p['reto'] ) . "</p>\n<!-- /wp:paragraph -->";
		}
		$r = impakto_semilla_entrada(
			array(
				'post_type'    => 'proyecto',
				'post_name'    => $p['slug'],
				'post_title'   => $p['titulo'],
				'post_excerpt' => $p['resumen'],
				'post_content' => $contenido,
				'post_status'  => 'publish',
				'menu_order'   => $orden + 1,
				'meta'         => array(
					'cliente'    => $p['cliente'] ?? '',
					'sector'     => $p['sector'] ?? '',
					'ubicacion'  => $p['ubicacion'] ?? '',
					'anio'       => $p['anio'] ?? '',
					'destacado'  => ! empty( $p['destacado'] ),
					'caso_exito' => ! empty( $p['caso'] ),
					'pills'      => $p['pills'] ?? '',
					'resultados' => $p['resultados'] ?? '',
				),
				'terminos'     => array( 'categoria-portafolio' => $p['cats'] ),
			),
			$forzar
		);
		$n += is_int( $r ) ? 1 : 0;
	}
	return "$n creados/actualizados";
}

/**
 * Categorías y artículos del blog (contenido/blog/). Fase 1: título, extracto y categoría.
 *
 * @param bool $forzar Sobrescribir.
 * @return string
 */
function impakto_sembrar_articulos( $forzar ) {
	// La categoría por defecto pasa a ser "Marketing digital".
	$defecto = (int) get_option( 'default_category' );
	if ( $defecto && 'sin-categoria' === get_term( $defecto )->slug ) {
		wp_update_term(
			$defecto,
			'category',
			array(
				'name' => 'Marketing digital',
				'slug' => 'marketing-digital',
			)
		);
	}
	foreach ( array( 'produccion-audiovisual' => 'Producción audiovisual', 'podcast' => 'Podcast', 'branding' => 'Branding', 'marketing-inmobiliario' => 'Marketing inmobiliario', 'marketing-digital' => 'Marketing digital' ) as $slug => $nombre ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( $nombre, 'category', array( 'slug' => $slug ) );
		}
	}

	$articulos = array(
		array( 'alquiler-sala-de-podcast-manizales', 'Alquiler de sala de podcast en Manizales: precios, qué incluye y cómo reservar', 'podcast', 'En Manizales puedes alquilar una sala de podcast profesional en Impakto desde $180.000 por hora (1 cámara y 1 micrófono) hasta $240.000 por hora (3 cámaras y 4 micrófonos), con IVA incluido. La sala tiene 24 m² insonorizados, micrófonos y consolas Rode, iluminación y acompañamiento técnico. Se reserva con un día de anticipación.' ),
		array( 'alquiler-estudio-fotografico-audiovisual-manizales', '¿Cuánto cuesta alquilar un estudio fotográfico o audiovisual en Manizales?', 'produccion-audiovisual', 'En Impakto (Manizales) alquilas un medio estudio de 45 m² desde $120.000 por hora o el estudio completo de 90 m² con sinfín en U desde $200.000 por hora, con IVA incluido. Por bloques de 4 y 8 horas el valor por hora baja. Incluye iluminación profesional, trípodes, grip y fondos, y puedes sumar cámaras, luces, drones y un productor.' ),
		array( 'como-elegir-agencia-marketing-digital-manizales', 'Cómo elegir una agencia de marketing digital en Manizales: 10 preguntas antes de contratar', 'marketing-digital', 'Para elegir una agencia de marketing digital en Manizales, revisa cinco cosas: que empiece por entender tu negocio (investigación), que mida resultados de ventas y no solo "likes", que muestre casos con cifras, que tenga capacidad de producción propia y que te ayude a convertir los leads en ventas. Estas 10 preguntas te ayudan a compararlas.' ),
		array( 'alquiler-equipos-audiovisuales-manizales', 'Alquiler de equipos audiovisuales en Manizales: cómo armar tu kit según tu producción', 'produccion-audiovisual', 'Para alquilar equipos audiovisuales en Manizales, define primero el tipo de producción y luego arma el kit en este orden: cámara y lentes, audio, iluminación, soporte o movimiento, y energía. En Impakto alquilamos cámaras Sony desde $180.000, lentes desde $70.000, iluminación desde $35.000 y micrófonos desde $20.000 por jornada, con IVA incluido.' ),
		array( 'marketing-inmobiliario-generar-leads-vender-proyectos', 'Marketing inmobiliario: cómo generar leads y vender proyectos de vivienda con estrategia digital', 'marketing-inmobiliario', 'El marketing inmobiliario que vende conecta cinco etapas: investigación del mercado y de la competencia, marca del proyecto (naming, brand essence y brandbook), contenido que muestre los diferenciales (renders, recorridos y testimonios), pauta para generar leads calificados y un proceso comercial con CRM y tiempos de respuesta. Si una etapa falla, las demás pierden fuerza.' ),
	);
	$n     = 0;
	$fecha = strtotime( '2026-11-02 09:00:00' );
	foreach ( $articulos as $i => list( $slug, $titulo, $categoria, $respuesta ) ) {
		$r = impakto_semilla_entrada(
			array(
				'post_type'     => 'post',
				'post_name'     => $slug,
				'post_title'    => $titulo,
				'post_excerpt'  => $respuesta,
				'post_content'  => "<!-- wp:paragraph -->\n<p><strong>Respuesta rápida:</strong> " . esc_html( $respuesta ) . "</p>\n<!-- /wp:paragraph -->",
				'post_status'   => 'publish',
				'post_date'     => gmdate( 'Y-m-d H:i:s', $fecha + $i * DAY_IN_SECONDS ),
				'post_category' => array( (int) get_term_by( 'slug', $categoria, 'category' )->term_id ),
			),
			$forzar
		);
		$n += is_int( $r ) ? 1 : 0;
	}
	// Borra la entrada de ejemplo de WordPress.
	$hola = get_page_by_path( 'hola-mundo', OBJECT, 'post' ) ? get_page_by_path( 'hola-mundo', OBJECT, 'post' ) : get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hola ) {
		wp_trash_post( $hola->ID );
	}
	return "$n creados/actualizados";
}

/**
 * Páginas: Inicio (patrón del tema), Blog (página de entradas) y Guía de estilo.
 *
 * @param bool $forzar Sobrescribir.
 * @return string
 */
function impakto_sembrar_paginas( $forzar ) {
	$registro = WP_Block_Patterns_Registry::get_instance();
	$patron   = static fn( $slug ) => $registro->is_registered( $slug ) ? $registro->get_registered( $slug )['content'] : '';

	$paginas = array(
		array( 'inicio', 'Inicio', $patron( 'impakto/pagina-inicio' ), '' ),
		array( 'blog', 'Blog', '', '' ),
		array( 'guia-de-estilo', 'Guía de estilo', $patron( 'impakto/pagina-guia-estilo' ), 'page-sin-titulo' ),
	);
	$ids = array();
	foreach ( $paginas as list( $slug, $titulo, $contenido, $plantilla ) ) {
		$ids[ $slug ] = impakto_semilla_entrada(
			array(
				'post_type'     => 'page',
				'post_name'     => $slug,
				'post_title'    => $titulo,
				'post_content'  => $contenido,
				'post_status'   => 'publish',
				'page_template' => $plantilla,
			),
			$forzar
		);
	}
	$inicio = get_page_by_path( 'inicio' );
	$blog   = get_page_by_path( 'blog' );
	if ( $inicio && $blog ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $inicio->ID );
		update_option( 'page_for_posts', $blog->ID );
	}
	// Página de ejemplo de WordPress.
	$ejemplo = get_page_by_path( 'pagina-de-ejemplo' ) ? get_page_by_path( 'pagina-de-ejemplo' ) : get_page_by_path( 'sample-page' );
	if ( $ejemplo ) {
		wp_trash_post( $ejemplo->ID );
	}
	return wp_json_encode( $ids );
}

/**
 * Menús editables con los mismos enlaces de respaldo del tema.
 *
 * @param bool $forzar Sobrescribir.
 * @return string
 */
function impakto_sembrar_menus( $forzar ) {
	if ( ! function_exists( 'impakto_menus_defecto' ) ) {
		return 'el tema Impakto no está activo';
	}
	$nombres    = array(
		'principal'       => 'Principal',
		'footer-espacios' => 'Footer · Espacios y equipos',
		'footer-impakto'  => 'Footer · Impakto',
		'footer-legal'    => 'Footer · Legal',
	);
	$ubicaciones = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $nombres as $ubicacion => $nombre ) {
		$menu = wp_get_nav_menu_object( $nombre );
		if ( $menu && ! $forzar ) {
			$ubicaciones[ $ubicacion ] = $menu->term_id;
			continue;
		}
		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}
		$menu_id = wp_create_nav_menu( $nombre );
		foreach ( impakto_menus_defecto()[ $ubicacion ] as $orden => list( $titulo, $ruta ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $titulo,
					'menu-item-url'      => home_url( $ruta ),
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => $orden + 1,
				)
			);
		}
		$ubicaciones[ $ubicacion ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $ubicaciones );
	return 'menús asignados';
}
