<?php
/**
 * Title: Guía de estilo (sistema de diseño)
 * Slug: impakto/pagina-guia-estilo
 * Categories: impakto
 * Post Types: page
 * Inserter: no
 *
 * Página oculta /guia-de-estilo/ (noindex): tokens y componentes del sistema de diseño.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"is-style-hero-curvas","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-hero-curvas"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Sistema de diseño · v0.1</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-display"} -->
<h1 class="wp-block-heading is-style-display">Guía de estilo</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-bajada"} -->
<p class="is-style-bajada">Tokens, tipografía y componentes del sitio de Impakto Marketing, tomados de <em>Términos &amp; condiciones</em>, <em>Sobre Impakto</em> y <em>Portafolio y casos relevantes</em>. Página interna: no se indexa ni aparece en los menús.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-blanca","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-blanca"><!-- wp:heading -->
<h2 class="wp-block-heading">01 · <mark class="acento">Color</mark></h2>
<!-- /wp:heading -->

<!-- wp:impakto/guia-tokens /-->

<!-- wp:heading {"className":"guia-bloque-titulo"} -->
<h2 class="wp-block-heading guia-bloque-titulo">02 · <mark class="acento">Tipografía</mark>: Montserrat</h2>
<!-- /wp:heading -->

<!-- wp:impakto/guia-tokens {"seccion":"tipografia"} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">03 · <mark class="acento">Espaciado</mark>, retícula y forma</h2>
<!-- /wp:heading -->

<!-- wp:impakto/guia-tokens {"seccion":"espaciado"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-clara","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-clara"><!-- wp:heading -->
<h2 class="wp-block-heading">04 · <mark class="acento">Botones</mark></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-nota"} -->
<p class="is-style-nota">Principal (naranja, texto grafito 900), secundario (contorno), enlace con flecha y WhatsApp con mensaje precargado. Pasa el cursor o navega con Tab para ver hover y foco. Alto mínimo 48 px.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"fila-botones","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group fila-botones"><!-- wp:impakto/cta {"ubicacion":"guia"} /-->

<!-- wp:impakto/cta {"tipo":"cotizar","variante":"secundario"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Botón principal</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#">Secundario</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-flecha"} -->
<div class="wp-block-button is-style-flecha"><a class="wp-block-button__link wp-element-button" href="#">Ver todo</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-oscura es-oscuro con-pestana-logo","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-oscura es-oscuro con-pestana-logo"><!-- wp:group {"className":"encabezado-seccion","layout":{"type":"default"}} -->
<div class="wp-block-group encabezado-seccion"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">05 · Sección oscura · línea de tiempo</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Desarrollo de <mark class="acento">Naming</mark></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:list {"ordered":true,"className":"is-style-linea-tiempo"} -->
<ol class="wp-block-list is-style-linea-tiempo"><!-- wp:list-item -->
<li><strong>Descubrimiento</strong>Conocemos el modelo de negocio, el objetivo y la personalidad de la marca.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Investigación de mercado</strong>Analizamos los nombres de la competencia, su contexto lingüístico y sus referencias culturales.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Exploración creativa</strong>Combinamos y transformamos palabras para crear neologismos y propuestas originales.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Validación</strong>Presentamos las 7 mejores propuestas, validadas en viabilidad jurídica, fonética y dominio.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-clara","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-clara"><!-- wp:heading -->
<h2 class="wp-block-heading">06 · <mark class="acento">Elementos</mark> de la marca</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Bloque con barra vertical</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true,"className":"is-style-barra-vertical"} -->
<ol class="wp-block-list is-style-barra-vertical"><!-- wp:list-item -->
<li><strong>Consulta de reserva:</strong> reserva con al menos un día de anticipación.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Asignación de reserva:</strong> confirma con el anticipo indicado en los términos de alquiler.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Duración mínima:</strong> 1 hora.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Lista con flecha · Qué incluye</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-flecha"} -->
<ul class="wp-block-list is-style-flecha"><!-- wp:list-item -->
<li>Iluminación profesional, trípodes y grip</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Fondos estándar de diferentes colores</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Potencia eléctrica 110 V y 220 V</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Disponible 24/7 con reserva</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Tabla de tarifas · Medio estudio</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"is-style-tarifas"} -->
<figure class="wp-block-table is-style-tarifas"><table><thead><tr><th>Tiempo</th><th>Valor</th></tr></thead><tbody><tr><td>1 hora</td><td>$120.000 COP</td></tr><tr><td>4 horas</td><td>$350.000 COP</td></tr><tr><td>8 horas</td><td>$700.000 COP</td></tr></tbody></table><figcaption class="wp-element-caption">Valores sin IVA.</figcaption></figure>
<!-- /wp:table --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Chips de dato y pills</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"fila-chips","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group fila-chips"><!-- wp:impakto/chip-dato /-->

<!-- wp:impakto/chip-dato {"etiqueta":"Estudio completo","valor":"90 m²"} /-->

<!-- wp:impakto/chip-dato {"etiqueta":"Desde","valor":"$120.000 COP / hora","grande":true} /--></div>
<!-- /wp:group -->

<!-- wp:list {"className":"is-style-pills"} -->
<ul class="wp-block-list is-style-pills"><!-- wp:list-item -->
<li>Memorable</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Premium</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Registrable</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Alta sonoridad</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-naranja","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-naranja"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">07 · Sección naranja</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Bloques de impacto con <mark class="acento">degradado naranja</mark></h2>
<!-- /wp:heading -->

<!-- wp:impakto/cta {"ubicacion":"guia"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"is-style-seccion-gris","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-seccion-gris"><!-- wp:heading -->
<h2 class="wp-block-heading">08 · <mark class="acento">Tarjetas</mark> y cifras</h2>
<!-- /wp:heading -->

<!-- wp:impakto/servicios {"cantidad":4} /-->

<!-- wp:group {"className":"cifras","layout":{"type":"default"}} -->
<div class="wp-block-group cifras"><!-- wp:impakto/cifra {"valor":"300","texto":"clientes potenciales por formulario para el proyecto Altaviva","nota":"Pauta en Meta, 1 feb – 18 mar 2026"} /-->

<!-- wp:impakto/cifra {"prefijo":"$","valor":"1.990","texto":"costo por lead desde, en Manizales","nota":"Pauta en Meta, 2026"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
