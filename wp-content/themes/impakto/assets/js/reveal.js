/**
 * Animaciones sutiles: entrada al hacer scroll (fade + 16 px, 300 ms), contadores
 * y líneas de tiempo que se dibujan. Acordeón accesible para el FAQ de Rank Math.
 * Con prefers-reduced-motion no se anima nada.
 */
const reducido = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

/* --- Entrada al hacer scroll --- */
const SELECTOR = [
	'.encabezado-seccion',
	'.fila-encabezado',
	'.is-style-linea-tiempo',
	'.servicios-franja > li',
	'.espacios > li',
	'.bento > li',
	'.articulos > li',
	'.cifra',
	'.columnas-numeradas > .wp-block-column',
	'.rank-math-block',
	'[data-revelar]',
].join( ',' );

if ( ! reducido && 'IntersectionObserver' in window ) {
	const observador = new IntersectionObserver(
		( entradas ) => {
			entradas.forEach( ( entrada ) => {
				if ( entrada.isIntersecting ) {
					entrada.target.classList.add( 'es-visible' );
					observador.unobserve( entrada.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
	);
	const alto = window.innerHeight;
	document.querySelectorAll( SELECTOR ).forEach( ( el, i ) => {
		// Lo que ya está en el primer pantallazo no se oculta (sin parpadeo ni retraso del LCP).
		if ( el.getBoundingClientRect().top < alto * 0.9 ) {
			return;
		}
		el.classList.add( 'revelar' );
		el.style.transitionDelay = `${ ( i % 4 ) * 60 }ms`;
		observador.observe( el );
	} );
}

/* --- Contadores --- */
const formato = ( n, decimales ) =>
	n.toLocaleString( 'es-CO', { minimumFractionDigits: decimales, maximumFractionDigits: decimales } );

const contadores = document.querySelectorAll( '[data-contador]' );
if ( ! reducido && contadores.length && 'IntersectionObserver' in window ) {
	const obs = new IntersectionObserver(
		( entradas ) => {
			entradas.forEach( ( entrada ) => {
				if ( ! entrada.isIntersecting ) {
					return;
				}
				obs.unobserve( entrada.target );
				const el = entrada.target;
				const final = parseFloat( el.dataset.contador ) || 0;
				const decimales = parseInt( el.dataset.decimales, 10 ) || 0;
				const texto = el.textContent;
				const inicio = performance.now();
				const duracion = 1200;
				const paso = ( t ) => {
					const p = Math.min( 1, ( t - inicio ) / duracion );
					const suave = 1 - Math.pow( 1 - p, 3 );
					el.textContent = formato( final * suave, decimales );
					if ( p < 1 ) {
						requestAnimationFrame( paso );
					} else {
						el.textContent = texto; // El texto original exacto (p. ej. "1.990").
					}
				};
				requestAnimationFrame( paso );
			} );
		},
		{ threshold: 0.6 }
	);
	contadores.forEach( ( c ) => obs.observe( c ) );
}

/* --- FAQ de Rank Math como acordeón accesible --- */
document.querySelectorAll( '.rank-math-list-item' ).forEach( ( item, i ) => {
	const pregunta = item.querySelector( '.rank-math-question' );
	const respuesta = item.querySelector( '.rank-math-answer' );
	if ( ! pregunta || ! respuesta ) {
		return;
	}
	const id = respuesta.id || `faq-respuesta-${ i }`;
	respuesta.id = id;
	const boton = document.createElement( 'button' );
	boton.type = 'button';
	boton.setAttribute( 'aria-expanded', 'false' );
	boton.setAttribute( 'aria-controls', id );
	boton.append( ...pregunta.childNodes );
	pregunta.append( boton );
	boton.addEventListener( 'click', () => {
		const abierta = item.classList.toggle( 'is-abierta' );
		boton.setAttribute( 'aria-expanded', String( abierta ) );
	} );
} );
