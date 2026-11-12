<?php
/**
 * Contenido de las páginas de servicio (contenido/servicios/servicios.md) y constructor
 * del marcado de bloques con la plantilla única del BRIEF §6.2:
 * hero → qué incluye → cómo lo hacemos → para quién → proyectos → FAQ → relacionados → CTA.
 * Solo se pintan las secciones que tienen texto aprobado (no se inventa contenido).
 *
 * @package ImpaktoCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Datos de cada servicio.
 *
 * @return array<string,array<string,mixed>>
 */
function impakto_datos_servicios() {
	return array(
		'investigacion-de-mercados' => array(
			'seo_title'   => 'Investigación de mercados en Manizales | Impakto Marketing',
			'seo_desc'    => 'Análisis de mercado, benchmarking de la competencia, revisión de su pauta y mystery shopper para decidir con datos antes de invertir.',
			'keyword'     => 'investigación de mercados Manizales',
			'h1'          => 'Investigación de mercados <mark class="acento">para decidir con datos</mark>',
			'respuesta'   => 'Analizamos tu mercado, tu competencia y a tus clientes antes de crear cualquier pieza. Así la estrategia, la marca y la pauta salen de evidencia, no de suposiciones. Es el primer paso de todo proyecto en Impakto.',
			'incluye'     => array( 'Análisis del mercado y del proyecto o producto', 'Análisis de la pauta de la competencia (Meta Ads Library)', 'Benchmarking: debilidades y oportunidades frente a la competencia', 'Mystery shopper a puntos de venta y asesores', 'Construcción del buyer persona', 'Informe con hallazgos y recomendaciones accionables' ),
			'pasos'       => array( array( 'Descubrimiento del negocio', '' ), array( 'Levantamiento de información', '' ), array( 'Mystery shopper', '' ), array( 'Análisis', '' ), array( 'Presentación de hallazgos y plan de acción', '' ) ),
			'para_quien'  => 'Empresas que van a lanzar un producto o proyecto, marcas que perdieron participación y equipos comerciales que necesitan entender por qué no cierran.',
			'faq'         => array(
				array( '¿Qué es un mystery shopper?', 'Es una visita o contacto encubierto, como si fuéramos clientes, para evaluar la atención, el discurso comercial y la experiencia de compra tuya o de tu competencia.' ),
				array( '¿Cuánto tarda una investigación?', 'Depende del alcance. Te damos un cronograma claro en la propuesta.' ),
				array( '¿Qué recibo al final?', 'Un informe con hallazgos, oportunidades frente a la competencia y recomendaciones concretas para tu estrategia.' ),
			),
		),
		'naming'                    => array(
			'seo_title'  => 'Naming: creación de nombres de marca | Impakto Marketing',
			'seo_desc'   => 'Creamos nombres únicos, memorables y registrables para marcas, productos y proyectos inmobiliarios. Proceso de 7 pasos con validación final.',
			'keyword'    => 'naming de marca',
			'h1'         => 'Naming: <mark class="acento">un nombre que se recuerda</mark>',
			'respuesta'  => 'El naming es el proceso estratégico de crear el nombre de una marca, producto o servicio. Desarrollamos un nombre único, memorable y con significado, que refleje la esencia de la marca, conecte con su público y facilite su reconocimiento y posicionamiento.',
			'pasos'      => array(
				array( 'Descubrimiento', 'Conocemos el modelo de negocio, el objetivo y la personalidad de la marca.' ),
				array( 'Investigación de mercado', 'Analizamos los nombres de la competencia, su contexto lingüístico y sus referencias culturales.' ),
				array( 'Direccionamiento conceptual', 'Definimos enfoques coherentes con la estrategia.' ),
				array( 'Palabras clave', 'Listamos atributos, metáforas, símbolos, etimologías y asociaciones emocionales.' ),
				array( 'Exploración creativa', 'Combinamos y transformamos palabras para crear neologismos y propuestas originales.' ),
				array( 'Selección y filtrado', 'Elegimos por originalidad, memorabilidad, pronunciación y coherencia estratégica.' ),
				array( 'Validación y selección final', 'Presentamos las 7 mejores propuestas, validadas en viabilidad jurídica, connotación cultural, fuerza fonética y disponibilidad de dominio.' ),
			),
			'para_quien' => 'Nuevas marcas, líneas de producto, proyectos inmobiliarios y empresas en proceso de renovación.',
			'extra'      => array( array( 'Casos', 'Serranova (marca cárnica premium) y Del Estrado (café especial).' ) ),
			'faq'        => array(
				// [CONFIRMAR alcance del registro ante la SIC].
				array( '¿Verifican si el nombre se puede registrar?', 'Sí. Validamos la viabilidad jurídica y la disponibilidad de dominio de las propuestas finalistas. El registro formal ante la SIC lo hace el cliente o su asesor legal.' ),
				array( '¿Cuántas opciones recibo?', 'Las 7 mejores propuestas después del filtrado.' ),
			),
		),
		'branding'                  => array(
			'seo_title'  => 'Branding e identidad de marca en Manizales | Impakto Marketing',
			'seo_desc'   => 'Brand essence, brandbook e identidad visual para marcas y proyectos inmobiliarios. Una marca coherente en todos los puntos de contacto.',
			'keyword'    => 'branding Manizales',
			'h1'         => 'Branding que <mark class="acento">construye confianza</mark>',
			'respuesta'  => 'Construimos marcas sólidas desde su esencia: propósito, personalidad, valores, tono de voz y sistema visual. Entregamos un Brand Essence (la estrategia) y un Brandbook (las reglas visuales y sus aplicaciones) para que tu marca sea coherente y reconocible en todos los puntos de contacto.',
			'incluye'    => array(
				'<strong>Brand Essence:</strong> investigación de mercado, personalidad y valores, propósito y eslogan, audiencias, tono y lenguaje, mensajes clave y narrativa de marca.',
				'<strong>Brandbook:</strong> concepto de marca, logotipo y sus versiones, paleta de color, tipografías, patrón gráfico, aplicaciones (papelería, dotación, vehículos, fachadas, señalética, espacios) y ecosistema de comunicación (redes, banners, íconos destacados).',
			),
			'pasos'      => array( array( 'Investigación', '' ), array( 'Personalidad y valores', '' ), array( 'Propósito y eslogan', '' ), array( 'Audiencia, tono y lenguaje', '' ), array( 'Mensajes clave', '' ), array( 'Narrativa', '' ), array( 'Lineamientos gráficos', '' ), array( 'Aplicaciones', '' ) ),
			'para_quien' => 'Constructoras y proyectos inmobiliarios, empresas industriales, marcas de consumo y negocios que quieren verse tan bien como trabajan.',
			'extra'      => array( array( 'Casos', 'Axis Plaza, Dyme Metals, Axel Park, Elite Metals y Reserva de Hungría.' ) ),
			'faq'        => array(
				array( '¿Cuál es la diferencia entre Brand Essence y Brandbook?', 'El Brand Essence define quién es la marca y cómo habla. El Brandbook define cómo se ve y cómo se aplica.' ),
				array( '¿Tienen experiencia en el sector inmobiliario?', 'Sí. Somos especialistas en branding estratégico para constructoras y proyectos de vivienda.' ),
			),
		),
		'produccion-audiovisual'    => array(
			'seo_title' => 'Producción audiovisual en Manizales | Impakto Marketing',
			'seo_desc'  => 'Videos para redes y pauta, recorridos, comerciales y videoclips con estudio propio, drones y equipos profesionales en Manizales.',
			'keyword'   => 'producción audiovisual Manizales',
			'h1'        => 'Producción audiovisual <mark class="acento">que cuenta y vende</mark>',
			'respuesta' => 'Producimos videos para redes sociales y pauta, recorridos de proyectos, comerciales, videoclips y contenido corporativo. Grabamos en nuestro estudio de 90 m² o en locación, con cámaras Sony, drones DJI, iluminación Nanlite y un equipo de productores y editores en Manizales.',
			'incluye'   => array( 'Guion y copy alineados a la marca', 'Preproducción', 'Grabación en estudio o en locación', 'Tomas aéreas con dron', 'Edición, color y motion graphics', 'Versiones para Reels, TikTok, YouTube y pauta' ),
			'pasos'     => array( array( 'Brief y objetivo', '' ), array( 'Guion y plan de rodaje', '' ), array( 'Producción', '' ), array( 'Postproducción', '' ), array( 'Entrega y versiones para cada canal', '' ) ),
			'extra'     => array( array( 'Por qué con nosotros', 'Calidad de nivel nacional con la inmediatez de tenerlo todo aquí, sin depender de equipos de otras ciudades ni pagar sobrecostos logísticos.' ) ),
			'faq'       => array(
				array( '¿Graban fuera de Manizales?', 'Sí, nos desplazamos según el proyecto.' ), // [CONFIRMAR cobertura].
				array( '¿Puedo usar solo el estudio con mi propio equipo?', 'Sí. Revisa el <a href="/estudio/">alquiler del estudio</a>.' ),
			),
		),
		'fotografia'                => array(
			'seo_title' => 'Fotografía profesional en Manizales | Impakto Marketing',
			'seo_desc'  => 'Fotografía de producto, corporativa, de arquitectura y de moda, en estudio propio con sinfín o en locación en Manizales.',
			'keyword'   => 'fotografía profesional Manizales',
			'h1'        => 'Fotografía profesional <mark class="acento">para marcas</mark>',
			'respuesta' => 'Hacemos fotografía de producto, corporativa, de arquitectura e interiores, y de moda, en nuestro estudio con sinfín o en locación. Cada sesión se planea según el uso: catálogo, redes, pauta o impresos.',
			'incluye'   => array( 'Planeación y moodboard', 'Dirección de arte', 'Sesión en estudio o en locación', 'Retoque', 'Entrega optimizada para web y para impresión' ),
			'faq'       => array(
				array( '¿Tienen fondo infinito?', 'Sí, un sinfín en U de 90 m² y uno en L de 45 m².' ),
				array( '¿Hay zona de maquillaje?', 'Sí: tocadores, vestier y racks de ropa.' ),
			),
		),
		'diseno-grafico'            => array(
			'seo_title' => 'Diseño gráfico y material publicitario en Manizales | Impakto',
			'seo_desc'  => 'Vallas, paneles para ferias, brochures, carpetas corporativas, volantes y piezas digitales con estrategia de marca.',
			'keyword'   => 'diseño gráfico Manizales',
			'h1'        => 'Diseño que <mark class="acento">se nota en la calle y en la pantalla</mark>',
			'respuesta' => 'Diseñamos material comercial y publicitario premium para cada etapa de tu proyecto: vallas tradicionales y de aproximación, pasacalles, paneles para ferias, brochures, carpetas corporativas, volantes, revistas y piezas digitales.',
			'incluye'   => array( 'Concepto creativo', 'Diseño de piezas impresas y digitales', 'Artes finales listos para producción', 'Adaptación a todos los formatos' ),
		),
		'redes-sociales'            => array(
			'seo_title' => 'Manejo de redes sociales en Manizales | Impakto Marketing',
			'seo_desc'  => 'Planeación de contenido, videos, carruseles e historias con objetivos de negocio. Gestión de redes que construye marca y apoya las ventas.',
			'keyword'   => 'manejo de redes sociales Manizales',
			'h1'        => 'Redes sociales <mark class="acento">con objetivos de negocio</mark>',
			'respuesta' => 'Planificamos y producimos el contenido de tus redes sociales enfocado en los objetivos del negocio. Partimos de las necesidades del equipo comercial para crear videos, carruseles e historias, y al mismo tiempo construimos marca y comunidad.',
			'incluye'   => array( 'Estrategia y parrilla mensual', 'Línea gráfica', 'Producción de videos, carruseles e historias', 'Copywriting', 'Gestión de comunidad', 'Informe mensual con métricas' ),
			'extra'     => array( array( 'Caso', 'Constructora Hungría: 613,6 mil visualizaciones en Facebook y 387,4 mil en Instagram en enero de 2026.' ) ),
		),
		'pauta-digital'             => array(
			'seo_title' => 'Pauta digital en Meta y Google Ads en Manizales | Impakto',
			'seo_desc'  => 'Campañas de generación de clientes potenciales en Meta y Google, con costo por lead desde $1.990 en Manizales y audiencias en el exterior.',
			'keyword'   => 'pauta digital Manizales',
			'h1'        => 'Pauta digital <mark class="acento">que trae clientes, no solo clics</mark>',
			'respuesta' => 'Construimos y optimizamos campañas en Meta (Facebook e Instagram) y Google Ads con el objetivo de generar clientes potenciales. Segmentamos audiencias locales e internacionales y medimos lo que importa: costo por lead, calidad del lead y ventas.',
			'incluye'   => array( 'Estrategia de campañas', 'Segmentación por audiencias y zonas de influencia', 'Creatividades', 'Formularios instantáneos y landing pages', 'Optimización continua', 'Informes de CPL, CAC y ROAS' ),
			'extra'     => array( array( 'Resultados', 'CPL desde $1.990 en Manizales y desde $4.487 en Estados Unidos y España. 300 leads para Altaviva entre febrero y marzo de 2026, con un CPL 20 % más bajo que el de anuncios similares, según Meta.' ) ),
			'faq'       => array(
				array( '¿Cuánto debo invertir en pauta?', 'Depende del objetivo y del ticket de tu producto. En la propuesta te recomendamos un presupuesto y una meta de leads.' ),
				array( '¿Pueden pautar para colombianos en el exterior?', 'Sí. Tenemos campañas activas para audiencias en Estados Unidos y España.' ),
			),
		),
		'desarrollo-web-seo'        => array(
			'seo_title' => 'Diseño de páginas web y SEO en Manizales | Impakto Marketing',
			'seo_desc'  => 'Sitios web y landing pages rápidos, enfocados en convertir visitas en leads, con SEO para aparecer en Google y en buscadores con IA.',
			'keyword'   => 'diseño de páginas web Manizales',
			'h1'        => 'Sitios web <mark class="acento">que convierten visitas en clientes</mark>',
			'respuesta' => 'Desarrollamos sitios web y landing pages estratégicos, rápidos y fáciles de administrar, con llamados a la acción hacia WhatsApp y formularios conectados a tu CRM. Los optimizamos para posicionarse en Google y en buscadores con inteligencia artificial.',
			'incluye'   => array( 'Arquitectura y UX', 'Diseño a la medida', 'Desarrollo en WordPress', 'Integración con CRM y WhatsApp', 'SEO técnico y de contenidos', 'Analítica (GA4, Search Console, Pixel)' ),
			'extra'     => array( array( 'Caso', 'El sitio de Constructora Hungría logró 2,44 mil clics orgánicos, 19,5 mil impresiones, un CTR de 12,5 % y una posición media de 9,1 entre enero y junio de 2026. Hoy la búsqueda orgánica es su principal canal de tráfico.' ) ),
		),
		'crm-ventas-digitales'      => array(
			'seo_title' => 'CRM y ventas digitales para equipos comerciales | Impakto',
			'seo_desc'  => 'Implementamos CRM, automatización, WhatsApp Business y speech comercial para que ningún lead se pierda y tu equipo cierre más.',
			'keyword'   => 'implementación CRM',
			'h1'        => 'CRM y ventas digitales: <mark class="acento">que ningún lead se pierda</mark>',
			'respuesta' => 'Creamos el canal de ventas digitales y sus procesos: implementamos el CRM, automatizamos respuestas y la distribución de leads, organizamos WhatsApp Business por etapas del embudo y capacitamos al equipo comercial en speech y cierre.',
			'incluye'   => array( 'Implementación y administración del CRM (HubSpot u otros)', 'Distribución automática de leads', 'Embudo con tiempos máximos por etapa', 'Speech por proyecto y etapa', 'WhatsApp Business con etiquetas y respuestas rápidas', 'Capacitación y monitoreo de asesores', 'Indicadores: CAC, ROI y ROAS' ),
			'extra'     => array(
				array( 'Dato clave', 'El 40 % de los leads que no se contactan en los primeros 10 minutos no vuelven a responder.' ),
				array( 'Resultados', 'Ventas del canal digital entre el 28 % y el 50 % del total mensual, y al menos una venta del exterior al mes (Constructora Hungría).' ),
			),
		),
		'animacion-3d'              => array( // [CONFIRMAR si sigue activo] — queda en borrador.
			'h1'        => 'Animación y modelación 3D <mark class="acento">para mostrar lo que aún no existe</mark>',
			'respuesta' => 'Creamos renders, recorridos virtuales y animaciones 3D para presentar productos y proyectos antes de que existan, ideales para la venta sobre planos.',
		),
		'desarrollo-de-producto'    => array( // [CONFIRMAR si sigue activo] — queda en borrador.
			'h1'        => 'Desarrollo de producto: <mark class="acento">de la idea al anaquel</mark>',
			'respuesta' => 'Acompañamos el desarrollo de nuevos productos: concepto, naming, empaque, identidad y estrategia de lanzamiento.',
		),
	);
}

/* ---------- Constructor de marcado de bloques ---------- */

/**
 * Bloque genérico con atributos serializados.
 *
 * @param string              $nombre  Nombre (core/… o impakto/…).
 * @param array<string,mixed> $attrs   Atributos.
 * @param string|null         $interno HTML interno (null = bloque sin contenido).
 * @return string
 */
function impakto_bk( $nombre, $attrs = array(), $interno = null ) {
	$nombre = str_starts_with( $nombre, 'core/' ) ? substr( $nombre, 5 ) : $nombre;
	$json   = $attrs ? ' ' . serialize_block_attributes( $attrs ) : '';
	if ( null === $interno ) {
		return "<!-- wp:$nombre$json /-->";
	}
	return "<!-- wp:$nombre$json -->\n$interno\n<!-- /wp:$nombre -->";
}

/**
 * Párrafo.
 *
 * @param string $html  Contenido.
 * @param string $clase Clase.
 * @return string
 */
function impakto_bk_p( $html, $clase = '' ) {
	return impakto_bk( 'core/paragraph', $clase ? array( 'className' => $clase ) : array(), '<p' . ( $clase ? ' class="' . $clase . '"' : '' ) . ">$html</p>" );
}

/**
 * Encabezado.
 *
 * @param string $html  Contenido.
 * @param int    $nivel Nivel.
 * @return string
 */
function impakto_bk_h( $html, $nivel = 2 ) {
	return impakto_bk( 'core/heading', 2 === $nivel ? array() : array( 'level' => $nivel ), "<h$nivel class=\"wp-block-heading\">$html</h$nivel>" );
}

/**
 * Lista.
 *
 * @param string[] $items    Elementos (HTML).
 * @param string   $clase    Estilo.
 * @param bool     $ordenada Ordenada.
 * @return string
 */
function impakto_bk_lista( $items, $clase, $ordenada = false ) {
	$tag   = $ordenada ? 'ol' : 'ul';
	$lis   = implode( "\n\n", array_map( static fn( $i ) => impakto_bk( 'core/list-item', array(), "<li>$i</li>" ), $items ) );
	$attrs = $ordenada ? array( 'ordered' => true, 'className' => $clase ) : array( 'className' => $clase );
	return impakto_bk( 'core/list', $attrs, "<$tag class=\"wp-block-list $clase\">$lis</$tag>" );
}

/**
 * Grupo.
 *
 * @param string $clase   Clases.
 * @param string $interno Bloques internos.
 * @param string $tipo    seccion|default|flex|flex-centro.
 * @return string
 */
function impakto_bk_grupo( $clase, $interno, $tipo = 'seccion' ) {
	$layouts = array(
		'seccion'     => array( 'type' => 'constrained', 'contentSize' => '1280px' ),
		'default'     => array( 'type' => 'default' ),
		'flex'        => array( 'type' => 'flex', 'flexWrap' => 'wrap' ),
		'flex-centro' => array( 'type' => 'flex', 'flexWrap' => 'wrap', 'justifyContent' => 'center' ),
	);
	$attrs = array( 'className' => $clase, 'layout' => $layouts[ $tipo ] );
	$align = '';
	if ( 'seccion' === $tipo ) {
		$attrs = array( 'align' => 'full' ) + $attrs;
		$align = ' alignfull';
	}
	return impakto_bk( 'core/group', $attrs, "<div class=\"wp-block-group$align $clase\">$interno</div>" );
}

/**
 * Dos columnas (38 % / 62 %).
 *
 * @param string $izq   Bloques de la izquierda.
 * @param string $der   Bloques de la derecha.
 * @param string $clase Clase.
 * @return string
 */
function impakto_bk_columnas( $izq, $der, $clase = 'columnas-seccion' ) {
	$col = static fn( $ancho, $html ) => impakto_bk( 'core/column', array( 'width' => $ancho ), "<div class=\"wp-block-column\" style=\"flex-basis:$ancho\">$html</div>" );
	return impakto_bk( 'core/columns', array( 'className' => $clase ), "<div class=\"wp-block-columns $clase\">" . $col( '38%', $izq ) . "\n\n" . $col( '62%', $der ) . '</div>' );
}

/**
 * Encabezado de sección (kicker + H2).
 *
 * @param string $kicker Kicker.
 * @param string $h2     H2 (HTML).
 * @return string
 */
function impakto_bk_encabezado( $kicker, $h2 ) {
	return impakto_bk_grupo( 'encabezado-seccion', impakto_bk_p( $kicker, 'is-style-kicker' ) . "\n\n" . impakto_bk_h( $h2 ), 'default' );
}

/**
 * FAQ de Rank Math.
 *
 * @param array<int,array{0:string,1:string}> $preguntas Preguntas.
 * @param string                              $prefijo   Prefijo de id.
 * @return string
 */
function impakto_bk_faq( $preguntas, $prefijo ) {
	$q = array();
	foreach ( $preguntas as $n => list( $titulo, $texto ) ) {
		$q[] = array(
			'id'      => $prefijo . '-' . ( $n + 1 ),
			'title'   => $titulo,
			'content' => $texto,
			'visible' => true,
		);
	}
	return impakto_bk( 'rank-math/faq-block', array( 'titleWrapper' => 'h3', 'questions' => $q ), impakto_faq_html_guardado( $q ) );
}

/**
 * HTML que guarda el bloque FAQ de Rank Math (debe coincidir para que el editor lo valide).
 *
 * @param array<int,array<string,mixed>> $preguntas Preguntas con title/content.
 * @return string
 */
function impakto_faq_html_guardado( $preguntas ) {
	$html = '<div class="wp-block-rank-math-faq-block">';
	foreach ( $preguntas as $q ) {
		$html .= '<div class="rank-math-faq-item"><h3 class="rank-math-question">' . $q['title'] . '</h3><div class="rank-math-answer">' . $q['content'] . '</div></div>';
	}
	return $html . '</div>';
}

/**
 * Marcado completo de una página de servicio.
 *
 * @param string              $slug Slug.
 * @param array<string,mixed> $d    Datos.
 * @param string              $grupo Nombre del bloque del proceso.
 * @return string
 */
function impakto_marcado_servicio( $slug, $d, $grupo ) {
	$s = array();

	// 1. Hero con H1 + respuesta corta + WhatsApp / Ver proyectos.
	$s[] = impakto_bk_grupo(
		'is-style-hero-oscuro es-oscuro hero-compacto',
		impakto_bk_p( 'Servicios · ' . esc_html( $grupo ), 'is-style-kicker' ) . "\n\n"
		. impakto_bk_h( $d['h1'], 1 ) . "\n\n"
		. impakto_bk_p( esc_html( $d['respuesta'] ), 'is-style-bajada' ) . "\n\n"
		. impakto_bk_grupo(
			'fila-botones is-apilado-movil',
			impakto_bk( 'impakto/cta', array( 'ubicacion' => 'servicio' ) ) . "\n\n" . impakto_bk( 'impakto/cta', array( 'tipo' => 'proyectos', 'variante' => 'secundario' ) ),
			'flex'
		)
	);

	// 2. Qué incluye.
	if ( ! empty( $d['incluye'] ) ) {
		$s[] = impakto_bk_grupo(
			'is-style-seccion-clara',
			impakto_bk_columnas( impakto_bk_encabezado( 'Qué incluye', 'Qué <mark class="acento">incluye</mark>' ), impakto_bk_lista( $d['incluye'], 'is-style-flecha' ) )
		);
	}

	// Datos de apoyo aprobados (casos, resultados, dato clave, por qué con nosotros).
	foreach ( $d['extra'] ?? array() as list( $etiqueta, $texto ) ) {
		$s[] = impakto_bk_grupo(
			'is-style-seccion-blanca seccion-dato',
			impakto_bk_columnas( impakto_bk_p( esc_html( $etiqueta ), 'is-style-kicker' ), impakto_bk_p( esc_html( $texto ), 'is-style-bajada destacado' ) )
		);
	}

	// 3. Cómo lo hacemos.
	if ( ! empty( $d['pasos'] ) ) {
		$pasos = array_map( static fn( $p ) => '<strong>' . esc_html( $p[0] ) . '</strong>' . esc_html( $p[1] ), $d['pasos'] );
		$s[]   = impakto_bk_grupo(
			'is-style-seccion-oscura es-oscuro con-pestana-logo',
			impakto_bk_encabezado( 'Cómo lo hacemos', 'Cómo lo <mark class="acento">hacemos</mark>' ) . "\n\n" . impakto_bk_lista( $pasos, 'is-style-linea-tiempo', true )
		);
	}

	// 4. Para quién es.
	if ( ! empty( $d['para_quien'] ) ) {
		$s[] = impakto_bk_grupo(
			'is-style-seccion-gris',
			impakto_bk_columnas( impakto_bk_encabezado( 'Para quién es', '¿Para quién <mark class="acento">es</mark>?' ), impakto_bk_p( esc_html( $d['para_quien'] ), 'is-style-bajada' ) )
		);
	}

	// 5. Proyectos relacionados (consulta automática al portafolio).
	$s[] = impakto_bk( 'impakto/proyectos-relacionados', array( 'align' => 'full' ) );

	// 6. Preguntas frecuentes.
	if ( ! empty( $d['faq'] ) ) {
		$s[] = impakto_bk_grupo(
			'is-style-seccion-gris',
			impakto_bk_columnas( impakto_bk_encabezado( 'Preguntas frecuentes', 'Lo que más <mark class="acento">nos preguntan</mark>' ), impakto_bk_faq( $d['faq'], 'faq-' . $slug ) )
		);
	}

	// Enlazado interno: 2 servicios + 1 artículo.
	$s[] = impakto_bk( 'impakto/servicios-relacionados', array( 'align' => 'full' ) );

	// 7. CTA final con WhatsApp.
	$s[] = impakto_bk( 'core/pattern', array( 'slug' => 'impakto/cta-final' ) );

	return implode( "\n\n", $s );
}

/**
 * Siembra el contenido completo de los servicios (Fase 2).
 *
 * @param bool $forzar Reemplazar el contenido existente.
 * @return string
 */
function impakto_sembrar_contenido_servicios( $forzar ) {
	$grupos = impakto_bloques_proceso();
	$n      = 0;
	foreach ( impakto_datos_servicios() as $slug => $d ) {
		$post = get_page_by_path( $slug, OBJECT, 'servicio' );
		if ( ! $post || ( '' !== trim( $post->post_content ) && ! $forzar ) ) {
			continue;
		}
		$grupo = $grupos[ get_post_meta( $post->ID, '_impakto_bloque', true ) ] ?? '';
		wp_update_post(
			wp_slash(
				array(
					'ID'           => $post->ID,
					'post_content' => impakto_marcado_servicio( $slug, $d, $grupo ),
				)
			)
		);
		foreach ( array( 'seo_title' => 'rank_math_title', 'seo_desc' => 'rank_math_description', 'keyword' => 'rank_math_focus_keyword' ) as $clave => $meta ) {
			if ( ! empty( $d[ $clave ] ) ) {
				update_post_meta( $post->ID, $meta, $d[ $clave ] );
			}
		}
		++$n;
	}
	return "$n servicios con contenido";
}
