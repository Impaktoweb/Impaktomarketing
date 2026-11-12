/**
 * Header que se compacta, mega menú de servicios, menú móvil y selector "Cotiza tu espacio".
 * Usa <dialog> nativo: atrapa el foco, cierra con Esc y devuelve el foco al botón que lo abrió.
 */
const header = document.querySelector( '[data-header]' );
const html = document.documentElement;

/* --- Header compacto al hacer scroll --- */
if ( header ) {
	const umbral = 24;
	let compacto = false;
	const revisar = () => {
		const debe = window.scrollY > umbral;
		if ( debe !== compacto ) {
			compacto = debe;
			header.classList.toggle( 'is-compacto', debe );
		}
	};
	revisar();
	window.addEventListener( 'scroll', revisar, { passive: true } );
}

/* --- Mega menú (escritorio): clic, hover con intención y teclado --- */
const toggle = document.querySelector( '[data-mega-toggle]' );
const mega = document.querySelector( '[data-mega]' );
if ( toggle && mega && header ) {
	const item = toggle.closest( '.nav-principal__item' );
	let temporizador;

	const abrir = ( abierto ) => {
		clearTimeout( temporizador );
		toggle.setAttribute( 'aria-expanded', String( abierto ) );
		mega.classList.toggle( 'is-abierto', abierto );
		header.classList.toggle( 'mega-abierto', abierto );
	};

	toggle.addEventListener( 'click', () => abrir( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) );

	const puedeHover = window.matchMedia( '(hover: hover) and (pointer: fine)' );
	const entrar = () => puedeHover.matches && ( temporizador = setTimeout( () => abrir( true ), 120 ) );
	const salir = () => {
		clearTimeout( temporizador );
		if ( puedeHover.matches ) {
			temporizador = setTimeout( () => abrir( false ), 200 );
		}
	};
	item.addEventListener( 'mouseenter', entrar );
	item.addEventListener( 'mouseleave', salir );
	mega.addEventListener( 'mouseenter', () => clearTimeout( temporizador ) );
	mega.addEventListener( 'mouseleave', salir );

	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' && mega.classList.contains( 'is-abierto' ) ) {
			abrir( false );
			toggle.focus();
		}
	} );
	document.addEventListener( 'click', ( e ) => {
		if ( mega.classList.contains( 'is-abierto' ) && ! header.contains( e.target ) ) {
			abrir( false );
		}
	} );
	// Al salir del panel con Tab, se cierra.
	mega.addEventListener( 'focusout', ( e ) => {
		if ( e.relatedTarget && ! mega.contains( e.relatedTarget ) && e.relatedTarget !== toggle ) {
			abrir( false );
		}
	} );
}

/* --- Diálogos: menú móvil y selector --- */
function conectarDialogo( dialogo, abridores, cerradores, claseHtml ) {
	if ( ! dialogo || typeof dialogo.showModal !== 'function' ) {
		return;
	}
	let origen = null;
	const botonesMenu = document.querySelectorAll( '[data-abre-menu]' );

	const abrir = ( e ) => {
		origen = e.currentTarget;
		document.querySelector( '[data-menu-movil][open]' )?.close();
		dialogo.showModal();
		if ( claseHtml ) {
			html.classList.add( claseHtml );
			botonesMenu.forEach( ( b ) => b.setAttribute( 'aria-expanded', 'true' ) );
		}
	};
	dialogo.addEventListener( 'close', () => {
		if ( claseHtml ) {
			html.classList.remove( claseHtml );
			botonesMenu.forEach( ( b ) => b.setAttribute( 'aria-expanded', 'false' ) );
		}
		origen?.focus?.();
	} );
	// Clic en el fondo (fuera del contenido) cierra el selector.
	dialogo.addEventListener( 'click', ( e ) => {
		if ( e.target === dialogo && ! claseHtml ) {
			dialogo.close();
		}
	} );
	document.querySelectorAll( abridores ).forEach( ( b ) => b.addEventListener( 'click', abrir ) );
	dialogo.querySelectorAll( cerradores ).forEach( ( b ) => b.addEventListener( 'click', () => dialogo.close() ) );
	// Un enlace interno (p. ej. ancla #cotizador) cierra el diálogo.
	dialogo.querySelectorAll( 'a[href]' ).forEach( ( a ) => a.addEventListener( 'click', () => dialogo.close() ) );
}

conectarDialogo( document.querySelector( '[data-menu-movil]' ), '[data-abre-menu]', '[data-cierra-menu]', 'menu-abierto' );
conectarDialogo( document.querySelector( '[data-selector]' ), '[data-abre-selector]', '[data-cierra-selector]', '' );

/* --- Acordeón de servicios en el menú móvil --- */
document.querySelectorAll( '[data-desplegar]' ).forEach( ( boton ) => {
	const panel = document.getElementById( boton.getAttribute( 'aria-controls' ) );
	boton.addEventListener( 'click', () => {
		const abierto = boton.getAttribute( 'aria-expanded' ) === 'true';
		boton.setAttribute( 'aria-expanded', String( ! abierto ) );
		panel.hidden = abierto;
	} );
} );

/* --- Si el menú móvil queda abierto al pasar a escritorio, se cierra --- */
window.matchMedia( '(min-width: 1280px)' ).addEventListener( 'change', ( e ) => {
	if ( e.matches ) {
		document.querySelector( '[data-menu-movil][open]' )?.close();
	}
} );
