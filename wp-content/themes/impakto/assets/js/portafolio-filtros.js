/**
 * Portafolio: los chips cambian el filtro sin recargar (la URL se actualiza y sigue siendo
 * indexable) y "Ver más" agrega la siguiente página. Sin JS, ambos son enlaces normales.
 */
const raiz = document.querySelector( '[data-portafolio]' );

async function traer( url ) {
	const resp = await fetch( url, { headers: { 'X-Requested-With': 'fetch' } } );
	if ( ! resp.ok ) {
		throw new Error( resp.status );
	}
	return new DOMParser().parseFromString( await resp.text(), 'text/html' );
}

function conectarVerMas() {
	const boton = raiz.querySelector( '[data-ver-mas]' );
	boton?.addEventListener( 'click', async ( e ) => {
		e.preventDefault();
		boton.setAttribute( 'aria-busy', 'true' );
		try {
			const doc = await traer( boton.href );
			const nuevos = doc.querySelectorAll( '[data-portafolio-rejilla] > li' );
			const rejilla = raiz.querySelector( '[data-portafolio-rejilla]' );
			nuevos.forEach( ( li ) => rejilla.append( li ) );
			nuevos[ 0 ]?.querySelector( 'a' )?.focus();
			const siguiente = doc.querySelector( '[data-ver-mas]' );
			if ( siguiente ) {
				boton.href = siguiente.href;
				boton.removeAttribute( 'aria-busy' );
			} else {
				boton.parentElement.remove();
			}
		} catch {
			location.href = boton.href;
		}
	} );
}

async function filtrar( url, empujar = true ) {
	raiz.setAttribute( 'aria-busy', 'true' );
	try {
		const doc = await traer( url );
		const nuevo = doc.querySelector( '[data-portafolio]' );
		raiz.innerHTML = nuevo.innerHTML;
		document.title = doc.title;
		if ( empujar ) {
			history.pushState( { portafolio: true }, '', url );
		}
		conectar();
		raiz.querySelector( '[data-filtro][aria-current]' )?.focus( { preventScroll: true } );
	} catch {
		location.href = url;
	} finally {
		raiz.removeAttribute( 'aria-busy' );
	}
}

function conectar() {
	raiz.querySelectorAll( '[data-filtro]' ).forEach( ( a ) => a.addEventListener( 'click', ( e ) => {
		if ( e.metaKey || e.ctrlKey || e.shiftKey ) {
			return;
		}
		e.preventDefault();
		filtrar( a.href );
	} ) );
	conectarVerMas();
}

if ( raiz ) {
	conectar();
	window.addEventListener( 'popstate', () => filtrar( location.href, false ) );
}
