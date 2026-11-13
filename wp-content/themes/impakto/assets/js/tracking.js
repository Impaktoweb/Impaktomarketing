/**
 * Medición (BRIEF §12): un solo lugar envía los eventos a GA4 (gtag) y a Meta (fbq).
 * Los eventos solo se registran si hay consentimiento (Consent Mode v2 y carga condicionada).
 *
 *  click_whatsapp      → Meta Contact   (clic en cualquier botón de WhatsApp)
 *  cotizacion_whatsapp → Meta Lead
 *  cotizacion_email    → Meta Lead
 *  generate_lead       → Meta Lead      (formulario de contacto)
 *  cotizador_inicio    → —
 *  click_telefono / click_email → Meta Contact
 */
const META = { click_whatsapp: 'Contact', click_telefono: 'Contact', click_email: 'Contact', cotizacion_whatsapp: 'Lead', cotizacion_email: 'Lead', generate_lead: 'Lead' };

function enviar( nombre, datos = {} ) {
	const params = { page_path: location.pathname, ...datos };
	window.gtag?.( 'event', nombre, params );
	if ( META[ nombre ] && window.fbq ) {
		const extra = datos.value ? { value: datos.value, currency: datos.currency || 'COP' } : {};
		window.fbq( 'track', META[ nombre ], { content_name: datos.tipo || datos.servicio || datos.formulario || datos.ubicacion || '', ...extra } );
	}
}

// Eventos emitidos por el cotizador y el formulario.
document.addEventListener( 'impakto:evento', ( e ) => enviar( e.detail.nombre, e.detail.datos ) );

// Clics en enlaces medibles (WhatsApp, teléfono, correo).
document.addEventListener( 'click', ( e ) => {
	const el = e.target.closest( '[data-evento]' );
	if ( ! el ) {
		return;
	}
	const nombre = el.dataset.evento;
	const datos = { ubicacion: el.dataset.ubicacion || '' };
	if ( el.dataset.servicio ) {
		datos.servicio = el.dataset.servicio;
	}
	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push( { event: nombre, ...datos } );
	enviar( nombre, datos );
} );
