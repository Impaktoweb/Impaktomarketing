<?php
/**
 * Title: Página Gracias
 * Slug: impakto/pagina-gracias
 * Categories: impakto
 * Post Types: page
 * Inserter: no
 *
 * Confirmación tras el formulario de contacto (noindex).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"is-style-hero-curvas","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull is-style-hero-curvas"><!-- wp:group {"className":"encabezado-seccion encabezado-hero","layout":{"type":"default"}} -->
<div class="wp-block-group encabezado-seccion encabezado-hero"><!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Mensaje recibido</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">¡Gracias! <mark class="acento">Te contactamos pronto</mark></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-bajada"} -->
<p class="is-style-bajada">Recibimos tu mensaje. Si es urgente, escríbenos por WhatsApp y te respondemos de inmediato.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"fila-botones is-apilado-movil","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group fila-botones is-apilado-movil"><!-- wp:impakto/cta {"ubicacion":"gracias"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/portafolio/' ) ); ?>">Ver portafolio</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
