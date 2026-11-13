/**
 * Banner de cookies: Aceptar / Rechazar / Configurar. Guarda la decisión 180 días en
 * impakto_consent, actualiza Consent Mode v2 y solo entonces carga GA4 y el Pixel de Meta.
 * Con consentimiento de analítica guarda el origen de campaña (impakto_utm, 90 días).
 */
const caja = document.querySelector( '[data-cookies]' );
const cfg = caja ? JSON.parse( caja.dataset.config ) : {};
const leer = ( n ) => {
	const m = document.cookie.match( new RegExp( '(?:^|; )' + n + '=([^;]+)' ) );
	try {
		return m ? JSON.parse( decodeURIComponent( m[ 1 ] ) ) : null;
	} catch {
		return null;
	}
};
const escribir = ( n, v, dias ) => {
	document.cookie = `${ n }=${ encodeURIComponent( JSON.stringify( v ) ) }; max-age=${ dias * 86400 }; path=/; SameSite=Lax${ location.protocol === 'https:' ? '; Secure' : '' }`;
};

function cargarGA4() {
	if ( ! cfg.ga4 || window.impaktoGA4 ) {
		return;
	}
	window.impaktoGA4 = true;
	const s = document.createElement( 'script' );
	s.async = true;
	s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent( cfg.ga4 );
	document.head.append( s );
	window.gtag( 'js', new Date() );
	window.gtag( 'config', cfg.ga4 );
}

function cargarPixel() {
	if ( ! cfg.pixel || window.fbq ) {
		return;
	}
	/* eslint-disable */
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
	/* eslint-enable */
	window.fbq( 'init', cfg.pixel );
	window.fbq( 'track', 'PageView' );
}

function guardarOrigen() {
	const q = new URLSearchParams( location.search );
	const claves = [ 'utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid' ];
	const actual = leer( 'impakto_utm' ) || {};
	const nuevo = {};
	claves.forEach( ( k ) => q.get( k ) && ( nuevo[ k ] = q.get( k ).slice( 0, 150 ) ) );
	if ( Object.keys( nuevo ).length || ! actual.entrada ) {
		const datos = Object.keys( nuevo ).length ? { ...nuevo, entrada: location.pathname } : { ...actual, entrada: actual.entrada || location.pathname };
		escribir( 'impakto_utm', datos, 90 );
	}
	window.impaktoUTM = leer( 'impakto_utm' ) || {};
}

function aplicar( c ) {
	window.gtag?.( 'consent', 'update', {
		analytics_storage: c.a ? 'granted' : 'denied',
		ad_storage: c.p ? 'granted' : 'denied',
		ad_user_data: c.p ? 'granted' : 'denied',
		ad_personalization: c.p ? 'granted' : 'denied',
	} );
	if ( c.a ) {
		guardarOrigen();
		cargarGA4();
	} else {
		document.cookie = 'impakto_utm=; max-age=0; path=/';
		window.impaktoUTM = {};
	}
	if ( c.p ) {
		cargarPixel();
	}
	document.dispatchEvent( new CustomEvent( 'impakto:consentimiento', { detail: c } ) );
}

function mostrar( abrirPanel = false ) {
	caja.hidden = false;
	document.body.classList.add( 'cookies-visibles' );
	const c = leer( 'impakto_consent' ) || { a: 0, p: 0 };
	caja.querySelector( '[data-cookie="a"]' ).checked = !! c.a;
	caja.querySelector( '[data-cookie="p"]' ).checked = !! c.p;
	panel( abrirPanel );
}

function panel( abierto ) {
	caja.querySelector( '#cookies-panel' ).hidden = ! abierto;
	caja.querySelector( '[data-cookies-guardar]' ).hidden = ! abierto;
	caja.querySelector( '[data-cookies-configurar]' ).setAttribute( 'aria-expanded', String( abierto ) );
	if ( abierto ) {
		caja.querySelector( '[data-cookie="a"]' ).focus();
	}
}

function decidir( c ) {
	escribir( 'impakto_consent', { ...c, v: 1, t: new Date().toISOString().slice( 0, 10 ) }, 180 );
	caja.hidden = true;
	document.body.classList.remove( 'cookies-visibles' );
	aplicar( c );
}

if ( caja ) {
	const previo = leer( 'impakto_consent' );
	if ( previo ) {
		aplicar( previo );
	} else {
		window.impaktoUTM = {};
		mostrar();
	}
	caja.querySelector( '[data-cookies-aceptar]' ).addEventListener( 'click', () => decidir( { a: 1, p: 1 } ) );
	caja.querySelector( '[data-cookies-rechazar]' ).addEventListener( 'click', () => decidir( { a: 0, p: 0 } ) );
	caja.querySelector( '[data-cookies-configurar]' ).addEventListener( 'click', () => panel( caja.querySelector( '#cookies-panel' ).hidden ) );
	caja.querySelector( '[data-cookies-guardar]' ).addEventListener( 'click', () => decidir( {
		a: caja.querySelector( '[data-cookie="a"]' ).checked ? 1 : 0,
		p: caja.querySelector( '[data-cookie="p"]' ).checked ? 1 : 0,
	} ) );
	document.addEventListener( 'click', ( e ) => {
		if ( e.target.closest( '[data-abre-cookies]' ) ) {
			mostrar( true );
		}
	} );
}
