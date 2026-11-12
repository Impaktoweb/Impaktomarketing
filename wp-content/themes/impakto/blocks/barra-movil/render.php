<?php
/**
 * Barra inferior fija en móvil: WhatsApp + CTA principal de la página.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_ctx = impakto_contexto_pagina();
?>
<div class="barra-movil" data-barra-movil>
	<?php
	echo impakto_boton_whatsapp( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		array(
			'texto'     => __( 'WhatsApp', 'impakto' ),
			'ubicacion' => 'flotante',
			'clase'     => 'boton boton--secundario',
		)
	);
	if ( $impakto_ctx['cta_selector'] ) {
		echo impakto_boton_cotizar( $impakto_ctx['cta_texto'], 'boton' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		printf( '<a class="boton" href="%s">%s</a>', esc_url( $impakto_ctx['cta_url'] ), esc_html( $impakto_ctx['cta_texto'] ) );
	}
	?>
</div>
