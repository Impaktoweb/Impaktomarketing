/**
 * Cotizador de Impakto (BRIEF §7). Un solo componente con 3 configuraciones:
 * estudio, podcast y equipos. Los precios vienen de Impakto → Tarifas (JSON en la página)
 * y se recalculan en el servidor al enviar. Accesible con teclado; el total se anuncia con aria-live.
 */

const LEYENDA = 'Este valor es una estimación. Todo el servicio se puede personalizar según los equipos y el tiempo que necesites. Confirmamos disponibilidad y te sugerimos lo que haga falta.';
const T0 = Date.now();

const cop = ( n, moneda = true ) => '$' + Math.round( n ).toLocaleString( 'es-CO' ) + ( moneda ? ' COP' : '' );
const esc = ( s ) => String( s ?? '' ).replace( /[&<>"']/g, ( c ) => ( { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ c ] ) );
const plural = ( n, uno, varios ) => `${ n } ${ n === 1 ? uno : varios }`;

/** Nonce fresco (las páginas pueden venir de la caché de LiteSpeed). */
async function nonceFresco( rest, respaldo ) {
	try {
		const r = await fetch( rest.replace( /solicitudes$/, 'nonce' ), { cache: 'no-store', credentials: 'omit' } );
		return ( await r.json() ).nonce || respaldo;
	} catch {
		return respaldo;
	}
}

/** Analítica: dataLayer (tracking.js de la Fase 5 lo envía a GA4 y Meta con consentimiento). */
function evento( nombre, datos = {} ) {
	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push( { event: nombre, ...datos } );
	document.dispatchEvent( new CustomEvent( 'impakto:evento', { detail: { nombre, datos } } ) );
}

/** Combinación más barata de bloques que cubre al menos `horas` (igual que impakto_precio_bloques en PHP). */
export function precioBloques( bloques, horas ) {
	const dur = Object.keys( bloques ).map( Number ).filter( ( d ) => d > 0 && bloques[ d ] > 0 );
	if ( ! dur.length ) {
		return { total: 0, usados: {} };
	}
	const max = horas + Math.max( ...dur );
	const costo = Array( max + 1 ).fill( Infinity );
	const eleccion = Array( max + 1 ).fill( 0 );
	costo[ 0 ] = 0;
	for ( let h = 1; h <= max; h++ ) {
		for ( const d of dur ) {
			if ( d <= h && costo[ h - d ] + Number( bloques[ d ] ) < costo[ h ] ) {
				costo[ h ] = costo[ h - d ] + Number( bloques[ d ] );
				eleccion[ h ] = d;
			}
		}
	}
	let mejor = horas;
	for ( let h = horas; h <= max; h++ ) {
		if ( costo[ h ] < costo[ mejor ] ) {
			mejor = h;
		}
	}
	const usados = {};
	for ( let h = mejor; h > 0; h -= eleccion[ h ] ) {
		usados[ eleccion[ h ] ] = ( usados[ eleccion[ h ] ] || 0 ) + 1;
	}
	return { total: costo[ mejor ], usados };
}

class Cotizador {
	constructor( raiz ) {
		this.raiz = raiz;
		this.c = JSON.parse( raiz.querySelector( '.cotizador__datos' ).textContent );
		this.tipo = this.c.tipo;
		this.indice = {};
		this.c.catalogo.forEach( ( cat ) => cat.items.forEach( ( it ) => ( this.indice[ it.id ] = { ...it, categoria: cat.nombre } ) ) );
		this.s = {
			paso: 1,
			espacio: this.c.estudio?.espacios?.[ 0 ]?.id || '',
			paquete: this.c.podcast?.paquetes?.[ 0 ]?.id || '',
			horas: 1,
			dias: 1,
			fecha: '',
			hora: '',
			edicion: '',
			equipos: {},
			maquillaje: false,
			productor: 0,
		};
		this.iniciado = false;
		this.uid = 'cot-' + this.tipo;
		this.pintar();
		this.conectar();
		this.actualizar();
	}

	/* ---------- Cálculo ---------- */
	calcular() {
		const s = this.s;
		const lineas = [];
		const notas = [];
		const horas = s.horas;
		const dias = this.tipo === 'equipos' ? s.dias : 1;
		let servicio = '';
		if ( this.tipo === 'estudio' ) {
			const e = this.c.estudio.espacios.find( ( x ) => x.id === s.espacio );
			if ( e ) {
				const r = precioBloques( e.bloques, horas );
				const det = Object.keys( r.usados ).sort( ( a, b ) => b - a ).map( ( d ) => `${ r.usados[ d ] } × bloque de ${ d } h` ).join( ' + ' );
				servicio = `${ e.nombre } (${ e.area_m2 } m²) – ${ plural( horas, 'hora', 'horas' ) }`;
				lineas.push( { concepto: servicio, detalle: det, valor: r.total } );
			}
		} else if ( this.tipo === 'podcast' ) {
			const p = this.c.podcast.paquetes.find( ( x ) => x.id === s.paquete );
			if ( p ) {
				servicio = `Sala de podcast, ${ p.nombre } – ${ plural( horas, 'hora', 'horas' ) }`;
				lineas.push( { concepto: servicio, detalle: `${ cop( p.precio_hora, false ) } × ${ horas } h`, valor: p.precio_hora * horas } );
				if ( horas > ( this.c.podcast.horas_tarifa_publicada || 4 ) ) {
					notas.push( this.c.podcast.nota_horas_extra || 'De 5 a 8 horas: sujeto a confirmación' );
				}
			}
			if ( s.edicion ) {
				const tabla = this.c.podcast.edicion[ s.edicion ] || {};
				const tope = Math.max( ...Object.keys( tabla ).map( Number ) );
				const h = Math.min( horas, tope );
				lineas.push( { concepto: `Edición de podcast ${ s.edicion === 'estandar' ? 'estándar' : 'avanzada' }`, detalle: `${ h } h grabadas`, valor: Number( tabla[ h ] || 0 ) } );
				if ( horas > tope ) {
					notas.push( `Edición de más de ${ tope } horas: sujeta a confirmación` );
				}
			}
		} else {
			servicio = `Alquiler de equipos – ${ plural( dias, 'jornada', 'jornadas' ) }`;
		}
		Object.entries( s.equipos ).forEach( ( [ id, n ] ) => {
			const it = this.indice[ id ];
			if ( ! it || ! n || ! it.precio ) {
				return;
			}
			lineas.push( { concepto: `${ n }× ${ it.nombre }`, detalle: `${ cop( it.precio, false ) } × ${ n }${ dias > 1 ? ` × ${ dias } jornadas` : ' · 1 jornada' }`, valor: it.precio * n * dias } );
			if ( it.requiere_operador ) {
				notas.push( `${ it.nombre }: incluye piloto/operador — sujeto a confirmación` );
			}
		} );
		this.c.adicionales.forEach( ( a ) => {
			if ( a.id === 'maquillaje' && s.maquillaje ) {
				lineas.push( { concepto: a.nombre, detalle: a.unidad, valor: a.precio } );
			}
			if ( a.id === 'productor' && s.productor ) {
				const hp = this.tipo === 'equipos' ? 8 * dias : horas;
				lineas.push( { concepto: `${ s.productor }× ${ a.nombre }`, detalle: `${ cop( a.precio_hora, false ) } × ${ hp } h × ${ s.productor }`, valor: a.precio_hora * hp * s.productor } );
				if ( this.tipo === 'equipos' ) {
					notas.push( 'Productor en alquiler de equipos: jornada de 8 horas, sujeto a confirmación' );
				}
			}
		} );
		return { servicio, lineas, total: lineas.reduce( ( t, l ) => t + l.valor, 0 ), notas: [ ...new Set( notas ) ] };
	}

	/* ---------- Marcado ---------- */
	pintar() {
		const t = this.tipo;
		const pasos = t === 'equipos' ? [ 'Días y fecha', 'Equipos', 'Resumen' ] : [ t === 'estudio' ? 'Espacio y tiempo' : 'Paquete y tiempo', 'Equipos y adicionales', 'Resumen' ];
		this.raiz.querySelector( '.cotizador__cargando' )?.remove();
		this.raiz.insertAdjacentHTML(
			'beforeend',
			`<div class="cotizador__marco">
				<div class="cotizador__principal">
					<ol class="cotizador__progreso" aria-label="Pasos del cotizador">
						${ pasos.map( ( p, i ) => `<li><button type="button" data-ir="${ i + 1 }"><span>${ String( i + 1 ).padStart( 2, '0' ) }</span>${ p }</button></li>` ).join( '' ) }
					</ol>
					<section class="cotizador__paso" data-paso="1" aria-label="${ esc( pasos[ 0 ] ) }">${ this.paso1() }</section>
					<section class="cotizador__paso" data-paso="2" aria-label="${ esc( pasos[ 1 ] ) }" hidden>${ this.paso2() }</section>
					<section class="cotizador__paso" data-paso="3" aria-label="${ esc( pasos[ 2 ] ) }" hidden>${ this.paso3() }</section>
				</div>
				<aside class="cotizador__resumen" aria-label="Resumen de tu cotización">
					<div class="cotizador__resumen-cabecera">
						<p class="kicker">Tu cotización</p>
						<button type="button" class="cotizador__resumen-cerrar boton-icono" data-resumen-cerrar aria-label="Cerrar resumen">×</button>
					</div>
					<ul class="cotizador__lineas"></ul>
					<p class="cotizador__total"><span>Total estimado <small>IVA incluido</small></span><strong aria-live="polite" aria-atomic="true"></strong></p>
					<ul class="cotizador__notas"></ul>
					<p class="cotizador__leyenda">${ LEYENDA }</p>
					${ this.c.deposito ? `<p class="cotizador__deposito">Depósito de seguridad reembolsable: <strong>${ cop( this.c.deposito ) }</strong>.</p>` : '' }
					<button type="button" class="boton boton--bloque cotizador__resumen-cta" data-ir="3">Continuar</button>
				</aside>
			</div>
			<div class="cotizador__barra" data-barra>
				<button type="button" class="cotizador__barra-total" data-resumen-abrir aria-expanded="false"><span>Total estimado</span><strong></strong></button>
				<button type="button" class="boton boton--sm" data-ir="3">Ver resumen</button>
			</div>`
		);
	}

	contador( clave, valor, min, max, etiqueta, sufijo = '' ) {
		return `<div class="contador" data-contador-de="${ clave }">
			<button type="button" class="contador__btn" data-menos="${ clave }" aria-label="Quitar ${ esc( etiqueta ) }">−</button>
			<output class="contador__valor" aria-live="polite">${ valor }${ sufijo }</output>
			<button type="button" class="contador__btn" data-mas="${ clave }" aria-label="Agregar ${ esc( etiqueta ) }">+</button>
		</div>`;
	}

	paso1() {
		const u = this.uid;
		const hoy = new Date().toISOString().slice( 0, 10 );
		if ( this.tipo === 'equipos' ) {
			return `<h3 class="cotizador__titulo">¿Cuántas jornadas y cuándo?</h3>
				<div class="cotizador__fila">
					<div class="campo"><span class="campo__etiqueta" id="${ u }-dias">Días de alquiler</span>${ this.contador( 'dias', 1, 1, 7, 'un día' ) }</div>
					<div class="campo"><label class="campo__etiqueta" for="${ u }-fecha">Fecha de recogida <small>(opcional)</small></label><input class="campo__input" type="date" id="${ u }-fecha" data-campo="fecha" min="${ hoy }"></div>
				</div>
				<p class="cotizador__ayuda">Precio por jornada. Si no sabes qué necesitas, te asesoramos.</p>
				<div class="cotizador__nav"><button type="button" class="boton" data-ir="2">Elegir equipos</button></div>`;
		}
		const opciones = this.tipo === 'estudio'
			? this.c.estudio.espacios.map( ( e ) => ( { id: e.id, titulo: e.nombre, sub: e.descripcion, precio: `Desde ${ cop( e.bloques[ 1 ], false ) } / hora` } ) )
			: this.c.podcast.paquetes.map( ( p ) => ( { id: p.id, titulo: p.nombre, sub: `${ plural( p.camaras, 'cámara', 'cámaras' ) } · ${ plural( p.microfonos, 'micrófono', 'micrófonos' ) }`, precio: `${ cop( p.precio_hora, false ) } / hora` } ) );
		const campo = this.tipo === 'estudio' ? 'espacio' : 'paquete';
		return `<fieldset class="cotizador__opciones cotizador__opciones--${ opciones.length }">
				<legend class="cotizador__titulo">${ this.tipo === 'estudio' ? 'Elige el formato' : 'Elige tu paquete de cámaras y micrófonos' }</legend>
				${ opciones.map( ( o ) => `<label class="opcion">
					<input type="radio" name="${ u }-${ campo }" value="${ o.id }" data-campo="${ campo }" ${ this.s[ campo ] === o.id ? 'checked' : '' }>
					<span class="opcion__cuerpo"><strong>${ esc( o.titulo ) }</strong><span>${ esc( o.sub ) }</span><span class="opcion__precio">${ esc( o.precio ) }</span></span>
				</label>` ).join( '' ) }
			</fieldset>
			<div class="cotizador__fila">
				<div class="campo"><span class="campo__etiqueta">Horas</span>${ this.contador( 'horas', 1, 1, 8, 'una hora', ' h' ) }</div>
				<div class="campo"><label class="campo__etiqueta" for="${ u }-fecha">Fecha deseada <small>(opcional)</small></label><input class="campo__input" type="date" id="${ u }-fecha" data-campo="fecha" min="${ hoy }"></div>
				<div class="campo"><label class="campo__etiqueta" for="${ u }-hora">Hora <small>(opcional)</small></label><input class="campo__input" type="time" id="${ u }-hora" data-campo="hora" step="1800"></div>
			</div>
			${ this.tipo === 'estudio' ? '<p class="cotizador__ayuda">Por 4 u 8 horas aplican tarifas por bloque más económicas: calculamos la combinación más barata.</p>' : `<p class="cotizador__ayuda">Tarifa publicada hasta ${ this.c.podcast.horas_tarifa_publicada || 4 } horas; de 5 a 8 horas, sujeta a confirmación.</p>` }
			<div class="cotizador__nav"><button type="button" class="boton" data-ir="2">Siguiente: equipos y adicionales</button></div>`;
	}

	paso2() {
		const u = this.uid;
		const tarjetas = this.tipo === 'equipos';
		const catalogo = this.c.catalogo.map( ( cat ) => {
			const items = cat.items.map( ( it ) => `<li class="equipo${ tarjetas ? ' equipo--tarjeta' : '' }" data-equipo="${ it.id }" data-busqueda="${ esc( ( it.nombre + ' ' + cat.nombre ).toLowerCase() ) }">
					${ tarjetas ? `<div class="equipo__foto">${ it.foto ? `<img src="${ esc( it.foto ) }" alt="" width="150" height="150" loading="lazy">` : '<span class="marcador-imagen" style="--ratio:1/1" aria-hidden="true"></span>' }</div>` : '' }
					<div class="equipo__info">
						${ tarjetas ? `<span class="equipo__categoria">${ esc( cat.nombre ) }</span>` : '' }
						<strong class="equipo__nombre">${ esc( it.nombre ) }</strong>
						<span class="equipo__precio">${ cop( it.precio, false ) } <small>/ jornada</small></span>
						${ it.requiere_operador ? '<span class="equipo__nota">Incluye piloto — sujeto a confirmación</span>' : '' }
						${ it.specs?.length ? `<ul class="equipo__specs">${ it.specs.map( ( sp ) => `<li>${ esc( sp ) }</li>` ).join( '' ) }</ul>` : '' }
					</div>
					${ this.contador( 'eq:' + it.id, 0, 0, 20, it.nombre ) }
				</li>` ).join( '' );
			return tarjetas
				? `<section class="catalogo__grupo" id="${ u }-${ cat.id }" data-grupo><h4 class="catalogo__titulo">${ esc( cat.nombre ) }</h4><ul class="catalogo__lista catalogo__lista--tarjetas">${ items }</ul></section>`
				: `<details class="catalogo__grupo" data-grupo><summary>${ esc( cat.nombre ) } <span class="catalogo__cuenta" data-cuenta="${ cat.id }"></span></summary><ul class="catalogo__lista">${ items }</ul></details>`;
		} ).join( '' );

		const kits = ( this.c.kits || [] ).map( ( k, i ) => {
			const total = k.items.reduce( ( t, id ) => t + ( this.indice[ id ]?.precio || 0 ), 0 );
			return `<li class="kit"><strong>${ esc( k.nombre ) }</strong><span>${ k.items.map( ( id ) => esc( this.indice[ id ]?.nombre || id ) ).join( ' + ' ) }</span><span class="kit__precio">${ cop( total, false ) } / jornada</span><button type="button" class="boton boton--sm boton--secundario" data-kit="${ i }">Agregar kit</button></li>`;
		} ).join( '' );

		const adicionales = this.c.adicionales.map( ( a ) => a.id === 'maquillaje'
			? `<label class="interruptor"><input type="checkbox" role="switch" data-campo="maquillaje"><span class="interruptor__pista" aria-hidden="true"></span><span>${ esc( a.nombre ) } <small>+${ cop( a.precio, false ) } ${ esc( a.unidad ) }</small></span></label>`
			: `<div class="campo campo--linea"><span class="campo__etiqueta">${ esc( a.nombre ) } <small>${ cop( a.precio_hora, false ) } ${ esc( a.unidad ) }</small></span>${ this.contador( 'productor', 0, 0, 5, 'un productor' ) }</div>` ).join( '' );

		const edicion = this.tipo === 'podcast' ? `<fieldset class="cotizador__edicion"><legend class="campo__etiqueta">Edición del episodio</legend>
				${ [ [ '', 'Sin edición' ], [ 'estandar', 'Estándar' ], [ 'avanzada', 'Avanzada' ] ].map( ( [ v, n ] ) => `<label class="opcion opcion--chica"><input type="radio" name="${ u }-edicion" value="${ v }" data-campo="edicion" ${ v === '' ? 'checked' : '' }><span class="opcion__cuerpo"><strong>${ n }</strong>${ v ? `<span class="opcion__precio" data-precio-edicion="${ v }"></span>` : '' }</span></label>` ).join( '' ) }
			</fieldset>` : '';

		return `${ tarjetas ? '' : `<h3 class="cotizador__titulo">${ this.tipo === 'podcast' ? 'Edición, adicionales y equipos' : 'Suma equipos y adicionales' }</h3>` }
			${ edicion }
			${ adicionales ? `<div class="cotizador__adicionales">${ adicionales }</div>` : '' }
			${ kits ? `<div class="cotizador__kits"><h3 class="cotizador__titulo">Kits sugeridos</h3><ul class="kits">${ kits }</ul></div>` : '' }
			<div class="catalogo">
				<div class="catalogo__barra">
					<label class="sr-only" for="${ u }-buscar">Buscar equipos</label>
					<input class="campo__input catalogo__buscar" type="search" id="${ u }-buscar" placeholder="Buscar equipo (p. ej. Sony, Nanlite, DJI)" data-buscar autocomplete="off">
					${ tarjetas ? `<nav class="catalogo__chips" aria-label="Categorías de equipos">${ this.c.catalogo.map( ( c ) => `<a href="#${ u }-${ c.id }">${ esc( c.nombre ) }</a>` ).join( '' ) }</nav>` : '' }
				</div>
				${ catalogo }
				<p class="catalogo__vacio" hidden>No encontramos ese equipo. Escríbenos y te ayudamos a conseguirlo.</p>
			</div>
			<div class="cotizador__nav"><button type="button" class="boton boton--secundario" data-ir="1">Atrás</button><button type="button" class="boton" data-ir="3">Ver resumen y enviar</button></div>`;
	}

	paso3() {
		const u = this.uid;
		return `<h3 class="cotizador__titulo">Tus datos</h3>
			<p class="cotizador__ayuda">Revisa el resumen y envíanos tu solicitud para confirmar disponibilidad.</p>
			<form class="cotizador__form" novalidate>
				<div class="cotizador__fila">
					<div class="campo"><label class="campo__etiqueta" for="${ u }-nombre">Nombre <span aria-hidden="true">*</span></label><input class="campo__input" id="${ u }-nombre" name="nombre" autocomplete="name" required></div>
					<div class="campo"><label class="campo__etiqueta" for="${ u }-celular">Celular <span aria-hidden="true">*</span></label><input class="campo__input" id="${ u }-celular" name="celular" type="tel" inputmode="tel" autocomplete="tel" required></div>
				</div>
				<div class="campo"><label class="campo__etiqueta" for="${ u }-correo">Correo <small>(solo si quieres la cotización por correo)</small></label><input class="campo__input" id="${ u }-correo" name="correo" type="email" autocomplete="email"></div>
				<div class="campo campo--oculto" aria-hidden="true"><label for="${ u }-web">Sitio web</label><input id="${ u }-web" name="sitio_web" tabindex="-1" autocomplete="off"></div>
				<label class="casilla"><input type="checkbox" name="acepta" required><span>Acepto los <a href="${ esc( this.c.terminos ) }" target="_blank" rel="noopener">términos de alquiler</a> y la <a href="${ esc( this.c.privacidad ) }" target="_blank" rel="noopener">política de tratamiento de datos</a>. <span aria-hidden="true">*</span></span></label>
				<p class="cotizador__error" role="alert" hidden></p>
				<div class="cotizador__envio">
					<button type="submit" class="boton" data-accion="whatsapp"><svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.6a8.5 8.5 0 0 1-12.4 7.6L3.5 20.5l1.3-4.4a8.5 8.5 0 1 1 15.7-4.5Z"/></svg><span>Validar disponibilidad por WhatsApp</span></button>
					<button type="submit" class="boton boton--secundario" data-accion="email">Enviarme la cotización por correo</button>
				</div>
				<div class="cotizador__nav"><button type="button" class="boton boton--enlace cotizador__atras" data-ir="2">Volver a equipos</button></div>
			</form>
			<div class="cotizador__exito" hidden tabindex="-1"></div>`;
	}

	/* ---------- Interacción ---------- */
	conectar() {
		const r = this.raiz;
		r.addEventListener( 'click', ( e ) => {
			const b = e.target.closest( 'button' );
			if ( ! b || ! r.contains( b ) ) {
				return;
			}
			if ( b.dataset.ir ) {
				this.ir( Number( b.dataset.ir ) );
			} else if ( b.dataset.mas || b.dataset.menos ) {
				this.cambiarContador( b.dataset.mas || b.dataset.menos, b.dataset.mas ? 1 : -1 );
			} else if ( b.dataset.kit ) {
				this.c.kits[ Number( b.dataset.kit ) ].items.forEach( ( id ) => {
					if ( this.indice[ id ] ) {
						this.s.equipos[ id ] = ( this.s.equipos[ id ] || 0 ) + 1;
					}
				} );
				this.tocar();
			} else if ( 'resumenAbrir' in b.dataset ) {
				this.abrirResumen( ! r.classList.contains( 'resumen-abierto' ) );
			} else if ( 'resumenCerrar' in b.dataset ) {
				this.abrirResumen( false );
			}
		} );
		r.addEventListener( 'change', ( e ) => {
			const campo = e.target.dataset.campo;
			if ( ! campo ) {
				return;
			}
			this.s[ campo ] = e.target.type === 'checkbox' ? e.target.checked : e.target.value;
			this.tocar();
		} );
		r.querySelector( '[data-buscar]' )?.addEventListener( 'input', ( e ) => this.filtrar( e.target.value ) );
		const form = r.querySelector( '.cotizador__form' );
		form.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			this.enviar( e.submitter?.dataset.accion || 'whatsapp' );
		} );

		// La barra móvil del cotizador reemplaza a la barra global mientras el cotizador está en pantalla.
		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver( ( [ en ] ) => document.body.classList.toggle( 'cotizador-en-vista', en.isIntersecting ), { threshold: 0 } ).observe( r );
		}
	}

	tocar() {
		if ( ! this.iniciado ) {
			this.iniciado = true;
			evento( 'cotizador_inicio', { tipo: this.tipo } );
		}
		this.actualizar();
	}

	cambiarContador( clave, delta ) {
		if ( clave.startsWith( 'eq:' ) ) {
			const id = clave.slice( 3 );
			this.s.equipos[ id ] = Math.max( 0, Math.min( 20, ( this.s.equipos[ id ] || 0 ) + delta ) );
		} else {
			const limites = { horas: [ 1, 8 ], dias: [ 1, 7 ], productor: [ 0, 5 ] }[ clave ];
			this.s[ clave ] = Math.max( limites[ 0 ], Math.min( limites[ 1 ], this.s[ clave ] + delta ) );
		}
		this.tocar();
	}

	filtrar( texto ) {
		const q = texto.trim().toLowerCase();
		let visibles = 0;
		this.raiz.querySelectorAll( '[data-equipo]' ).forEach( ( li ) => {
			const ok = ! q || li.dataset.busqueda.includes( q );
			li.hidden = ! ok;
			visibles += ok ? 1 : 0;
		} );
		this.raiz.querySelectorAll( '[data-grupo]' ).forEach( ( g ) => {
			const hay = g.querySelector( '[data-equipo]:not([hidden])' );
			g.hidden = ! hay;
			if ( q && hay && g.tagName === 'DETAILS' ) {
				g.open = true;
			}
		} );
		this.raiz.querySelector( '.catalogo__vacio' ).hidden = visibles > 0;
	}

	ir( paso ) {
		this.s.paso = paso;
		this.raiz.classList.toggle( 'en-paso-3', paso === 3 );
		this.raiz.querySelectorAll( '[data-paso]' ).forEach( ( p ) => ( p.hidden = Number( p.dataset.paso ) !== paso ) );
		this.abrirResumen( false );
		this.actualizar();
		const destino = this.raiz.querySelector( `[data-paso="${ paso }"]` );
		const arriba = this.raiz.getBoundingClientRect().top;
		if ( arriba < 0 || arriba > window.innerHeight * 0.6 ) {
			this.raiz.scrollIntoView( { behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 'auto' : 'smooth', block: 'start' } );
		}
		destino.querySelector( 'input, button' )?.focus( { preventScroll: true } );
	}

	abrirResumen( abierto ) {
		this.raiz.classList.toggle( 'resumen-abierto', abierto );
		this.raiz.querySelector( '[data-resumen-abrir]' ).setAttribute( 'aria-expanded', String( abierto ) );
	}

	actualizar() {
		const r = this.raiz;
		const s = this.s;
		const res = this.calcular();
		this.ultimo = res;

		// Progreso.
		r.querySelectorAll( '[data-ir]' ).forEach( ( b ) => {
			if ( b.closest( '.cotizador__progreso' ) ) {
				const n = Number( b.dataset.ir );
				b.parentElement.classList.toggle( 'es-actual', n === s.paso );
				b.parentElement.classList.toggle( 'es-hecho', n < s.paso );
				b.setAttribute( 'aria-current', n === s.paso ? 'step' : 'false' );
			}
		} );

		// Contadores.
		r.querySelectorAll( '[data-contador-de]' ).forEach( ( el ) => {
			const clave = el.dataset.contadorDe;
			const v = clave.startsWith( 'eq:' ) ? s.equipos[ clave.slice( 3 ) ] || 0 : s[ clave ];
			const sufijo = clave === 'horas' ? ' h' : '';
			el.querySelector( 'output' ).textContent = v + sufijo;
			const lim = clave.startsWith( 'eq:' ) ? [ 0, 20 ] : { horas: [ 1, 8 ], dias: [ 1, 7 ], productor: [ 0, 5 ] }[ clave ];
			el.querySelector( '[data-menos]' ).disabled = v <= lim[ 0 ];
			el.querySelector( '[data-mas]' ).disabled = v >= lim[ 1 ];
			el.closest( '[data-equipo]' )?.classList.toggle( 'es-elegido', v > 0 );
		} );
		r.querySelectorAll( '[data-cuenta]' ).forEach( ( el ) => {
			const cat = this.c.catalogo.find( ( c ) => c.id === el.dataset.cuenta );
			const n = cat.items.reduce( ( t, it ) => t + ( s.equipos[ it.id ] || 0 ), 0 );
			el.textContent = n ? `(${ n })` : '';
		} );
		r.querySelectorAll( '[data-precio-edicion]' ).forEach( ( el ) => {
			const tabla = this.c.podcast.edicion[ el.dataset.precioEdicion ];
			const h = Math.min( s.horas, Math.max( ...Object.keys( tabla ).map( Number ) ) );
			el.textContent = cop( tabla[ h ], false ) + ` · ${ h } h`;
		} );

		// Resumen.
		r.querySelector( '.cotizador__lineas' ).innerHTML = res.lineas.length
			? res.lineas.map( ( l ) => `<li><span><strong>${ esc( l.concepto ) }</strong><small>${ esc( l.detalle ) }</small></span><span>${ cop( l.valor, false ) }</span></li>` ).join( '' )
			: '<li class="cotizador__vacio">Agrega equipos para ver tu cotización.</li>';
		r.querySelector( '.cotizador__notas' ).innerHTML = res.notas.map( ( n ) => `<li>${ esc( n ) }</li>` ).join( '' );
		r.querySelector( '.cotizador__total strong' ).textContent = cop( res.total );
		r.querySelector( '[data-barra] strong' ).textContent = cop( res.total );
		r.querySelector( '.cotizador__resumen-cta' ).hidden = s.paso === 3;
	}

	/* ---------- Envío ---------- */
	async enviar( accion ) {
		const form = this.raiz.querySelector( '.cotizador__form' );
		const error = form.querySelector( '.cotizador__error' );
		const f = new FormData( form );
		const contacto = { nombre: ( f.get( 'nombre' ) || '' ).trim(), celular: ( f.get( 'celular' ) || '' ).trim(), correo: ( f.get( 'correo' ) || '' ).trim() };
		const fallas = [];
		if ( contacto.nombre.length < 2 ) {
			fallas.push( [ 'nombre', 'Escribe tu nombre.' ] );
		}
		if ( contacto.celular.replace( /\D/g, '' ).length < 7 ) {
			fallas.push( [ 'celular', 'Escribe un celular válido.' ] );
		}
		if ( accion === 'email' && ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( contacto.correo ) ) {
			fallas.push( [ 'correo', 'Escribe tu correo para enviarte la cotización.' ] );
		}
		if ( ! f.get( 'acepta' ) ) {
			fallas.push( [ 'acepta', 'Acepta los términos de alquiler y la política de datos.' ] );
		}
		if ( this.tipo === 'equipos' && ! Object.values( this.s.equipos ).some( Boolean ) ) {
			fallas.push( [ null, 'Agrega al menos un equipo.' ] );
		}
		form.querySelectorAll( '[aria-invalid]' ).forEach( ( el ) => el.removeAttribute( 'aria-invalid' ) );
		if ( fallas.length ) {
			fallas.forEach( ( [ n ] ) => n && form.elements[ n ]?.setAttribute( 'aria-invalid', 'true' ) );
			error.textContent = fallas.map( ( x ) => x[ 1 ] ).join( ' ' );
			error.hidden = false;
			( fallas[ 0 ][ 0 ] && form.elements[ fallas[ 0 ][ 0 ] ] )?.focus();
			return;
		}
		error.hidden = true;

		// Se abre la pestaña de WhatsApp ya, dentro del clic, para que el navegador no la bloquee.
		const ventana = accion === 'whatsapp' ? window.open( '', '_blank' ) : null;
		const botones = form.querySelectorAll( 'button[type=submit]' );
		botones.forEach( ( b ) => ( b.disabled = true ) );
		form.setAttribute( 'aria-busy', 'true' );

		const s = this.s;
		try {
			const nonce = await nonceFresco( this.c.rest, this.c.nonce );
			const resp = await fetch( this.c.rest, {
				method: 'POST',
				credentials: 'omit', // Envío anónimo: el nonce fresco se creó sin sesión.
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
				body: JSON.stringify( {
					tipo: this.tipo,
					accion,
					seleccion: { espacio: s.espacio, paquete: s.paquete, horas: s.horas, dias: s.dias, fecha: s.fecha, hora: s.hora, edicion: s.edicion, equipos: s.equipos, adicionales: { maquillaje: s.maquillaje, productor: s.productor } },
					contacto,
					acepta: true,
					sitio_web: f.get( 'sitio_web' ) || '',
					tiempo: Math.round( ( Date.now() - T0 ) / 1000 ),
					pagina: location.href,
					utm: window.impaktoUTM || {},
				} ),
			} );
			const datos = await resp.json();
			if ( ! resp.ok ) {
				throw new Error( datos.message || 'No pudimos enviar tu solicitud.' );
			}
			evento( accion === 'whatsapp' ? 'cotizacion_whatsapp' : 'cotizacion_email', { tipo: this.tipo, value: datos.total, currency: 'COP', consecutivo: datos.consecutivo } );
			if ( ventana ) {
				ventana.location.href = datos.whatsapp_url;
			}
			this.exito( accion, datos );
		} catch ( err ) {
			ventana?.close();
			error.textContent = err.message + ' También puedes escribirnos por WhatsApp.';
			error.hidden = false;
		} finally {
			botones.forEach( ( b ) => ( b.disabled = false ) );
			form.removeAttribute( 'aria-busy' );
		}
	}

	exito( accion, datos ) {
		const caja = this.raiz.querySelector( '.cotizador__exito' );
		caja.innerHTML = `<p class="kicker">Cotización ${ esc( datos.consecutivo ) }</p>
			<h3>${ accion === 'whatsapp' ? '¡Listo! Te esperamos en WhatsApp' : '¡Listo! Revisa tu correo' }</h3>
			<p>${ accion === 'whatsapp' ? 'Abrimos WhatsApp con el resumen de tu cotización. Envía el mensaje y confirmamos disponibilidad.' : 'Te enviamos la cotización con el resumen y las condiciones clave. También puedes validar disponibilidad por WhatsApp.' }</p>
			<p class="cotizador__total"><span>Total estimado <small>IVA incluido</small></span><strong>${ cop( datos.total ) }</strong></p>
			<a class="boton" href="${ esc( datos.whatsapp_url ) }" target="_blank" rel="noopener" data-evento="click_whatsapp" data-ubicacion="cotizador">${ accion === 'whatsapp' ? 'Abrir WhatsApp de nuevo' : 'Validar disponibilidad por WhatsApp' }</a>`;
		this.raiz.querySelector( '.cotizador__form' ).hidden = true;
		caja.hidden = false;
		caja.focus();
	}
}

document.querySelectorAll( '[data-cotizador]' ).forEach( ( el ) => new Cotizador( el ) );
