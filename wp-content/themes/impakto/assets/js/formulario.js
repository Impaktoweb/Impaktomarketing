/**
 * Formulario de contacto: validación accesible, envío a /impakto/v1/contacto,
 * evento generate_lead y redirección a /gracias/.
 */
const T0 = Date.now();
const form = document.querySelector( '[data-formulario-contacto]' );

if ( form ) {
	const cfg = JSON.parse( form.dataset.config );
	const error = form.querySelector( '.formulario__error' );
	const marcar = ( nombre, texto ) => {
		const campo = form.elements[ nombre ];
		const msg = form.querySelector( `#fc-${ nombre }-error` );
		campo?.setAttribute( 'aria-invalid', texto ? 'true' : 'false' );
		if ( msg ) {
			msg.textContent = texto || '';
			msg.hidden = ! texto;
		}
	};

	form.addEventListener( 'submit', async ( e ) => {
		e.preventDefault();
		const f = new FormData( form );
		const datos = Object.fromEntries( f.entries() );
		const fallas = {};
		if ( ( datos.nombre || '' ).trim().length < 2 ) fallas.nombre = 'Escribe tu nombre.';
		if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( datos.correo || '' ) ) fallas.correo = 'Escribe un correo válido.';
		if ( ( datos.celular || '' ).replace( /\D/g, '' ).length < 7 ) fallas.celular = 'Escribe un celular válido.';
		if ( ! datos.acepta ) fallas.acepta = 'Acepta la política de tratamiento de datos.';
		[ 'nombre', 'correo', 'celular', 'acepta' ].forEach( ( n ) => marcar( n, fallas[ n ] ) );
		if ( Object.keys( fallas ).length ) {
			form.elements[ Object.keys( fallas )[ 0 ] ].focus();
			return;
		}
		const boton = form.querySelector( 'button[type=submit]' );
		boton.disabled = true;
		form.setAttribute( 'aria-busy', 'true' );
		error.hidden = true;
		try {
			const resp = await fetch( cfg.rest, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
				body: JSON.stringify( { ...datos, acepta: true, tiempo: Math.round( ( Date.now() - T0 ) / 1000 ), pagina: location.href, utm: window.impaktoUTM || {} } ),
			} );
			const r = await resp.json();
			if ( ! resp.ok ) {
				throw new Error( r.message || 'No pudimos enviar tu mensaje.' );
			}
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push( { event: 'generate_lead', formulario: 'contacto' } );
			document.dispatchEvent( new CustomEvent( 'impakto:evento', { detail: { nombre: 'generate_lead', datos: { formulario: 'contacto' } } } ) );
			setTimeout( () => ( location.href = r.gracias ), 400 );
		} catch ( err ) {
			error.textContent = err.message + ' También puedes escribirnos por WhatsApp.';
			error.hidden = false;
			boton.disabled = false;
			form.removeAttribute( 'aria-busy' );
		}
	} );
}
