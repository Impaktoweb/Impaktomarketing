<?php
/**
 * Title: Inicio · Preguntas frecuentes
 * Slug: impakto/inicio-faq
 * Categories: impakto-inicio,impakto
 * Inserter: yes
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_preguntas = array(
	array( '¿Qué servicios ofrece Impakto Marketing?', 'Investigación de mercados, naming, branding, producción audiovisual y fotografía, diseño gráfico, redes sociales, pauta en Meta y Google, sitios web con SEO, y CRM y ventas digitales. Además alquilamos nuestro estudio audiovisual, la sala de podcast y los equipos.' ),
	array( '¿Dónde está ubicada la agencia?', 'En Manizales, Caldas: Cra. 22 #35-62, detrás de Hyundai. Trabajamos con marcas de todo el Eje Cafetero, de Colombia y del exterior.' ),
	array( '¿Trabajan con empresas fuera de Manizales?', 'Sí. Hemos trabajado en proyectos de Colombia, Costa Rica, Panamá, Londres y Estados Unidos, y gestionamos campañas para audiencias locales y en el exterior.' ),
	array( '¿Cuánto cuesta alquilar el estudio o la sala de podcast?', 'El estudio cuesta desde $120.000 por hora y la sala de podcast desde $180.000 por hora, con IVA incluido. Puedes armar tu cotización en línea según el tiempo y los equipos que necesites.' ),
	array( '¿Cómo empiezo a trabajar con Impakto?', 'Escríbenos por WhatsApp y cuéntanos tu objetivo. Agendamos una conversación, entendemos tu negocio y te presentamos una propuesta a la medida.' ),
);
$impakto_faq = array(
	'titleWrapper' => 'h3',
	'questions'    => array(),
);
foreach ( $impakto_preguntas as $impakto_n => list( $impakto_q, $impakto_a ) ) {
	$impakto_faq['questions'][] = array(
		'id'      => 'faq-inicio-' . ( $impakto_n + 1 ),
		'title'   => $impakto_q,
		'content' => $impakto_a,
		'visible' => true,
	);
}
?>
<!-- wp:group {"align":"full","className":"is-style-seccion-gris","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-gris"><!-- wp:columns {"className":"columnas-faq"} -->
<div class="wp-block-columns columnas-faq"><!-- wp:column {"width":"38%"} -->
<div class="wp-block-column" style="flex-basis:38%"><!-- wp:group {"className":"encabezado-seccion","layout":{"type":"default"}} -->
<div class="wp-block-group encabezado-seccion"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Preguntas frecuentes</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lo que más <mark class="acento">nos preguntan</mark></h2>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"62%"} -->
<div class="wp-block-column" style="flex-basis:62%"><!-- wp:rank-math/faq-block <?php echo serialize_block_attributes( $impakto_faq ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> -->
<div class="wp-block-rank-math-faq-block"><?php foreach ( $impakto_faq['questions'] as $impakto_q ) : ?><div class="rank-math-faq-item"><h3 class="rank-math-question"><?php echo esc_html( $impakto_q['title'] ); ?></h3><div class="rank-math-answer"><?php echo esc_html( $impakto_q['content'] ); ?></div></div><?php endforeach; ?></div>
<!-- /wp:rank-math/faq-block --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
