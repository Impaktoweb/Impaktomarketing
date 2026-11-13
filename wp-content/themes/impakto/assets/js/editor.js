/**
 * Editor: registra los bloques dinámicos del tema con controles generados
 * desde block.json (sin paso de compilación) y el formato "Resaltado naranja".
 */
( function ( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps, RichTextToolbarButton } = wp.blockEditor;
	const { PanelBody, TextControl, TextareaControl, ToggleControl, SelectControl, Disabled, Placeholder } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { registerFormatType, toggleFormat } = wp.richText;
	const { __ } = wp.i18n;

	const config = window.impaktoBloques || {};

	/** Control de un atributo según su tipo. */
	function control( nombre, definicion, valor, cambiar, meta ) {
		const label = ( meta.etiquetas && meta.etiquetas[ nombre ] ) || nombre;
		const opciones = meta.opciones && meta.opciones[ nombre ];
		if ( opciones || definicion.enum ) {
			const lista = opciones
				? Object.keys( opciones ).map( ( v ) => ( { value: v, label: opciones[ v ] } ) )
				: definicion.enum.map( ( v ) => ( { value: v, label: v } ) );
			return el( SelectControl, { key: nombre, label, value: valor, options: lista, onChange: cambiar, __nextHasNoMarginBottom: true, __next40pxDefaultSize: true } );
		}
		if ( 'boolean' === definicion.type ) {
			return el( ToggleControl, { key: nombre, label, checked: !! valor, onChange: cambiar, __nextHasNoMarginBottom: true } );
		}
		if ( 'number' === definicion.type ) {
			return el( TextControl, { key: nombre, label, type: 'number', value: valor, onChange: ( v ) => cambiar( parseInt( v, 10 ) || 0 ), __nextHasNoMarginBottom: true, __next40pxDefaultSize: true } );
		}
		if ( meta.multilinea && meta.multilinea.includes( nombre ) ) {
			return el( TextareaControl, { key: nombre, label, value: valor || '', rows: 8, onChange: cambiar, __nextHasNoMarginBottom: true } );
		}
		return el( TextControl, { key: nombre, label, value: valor || '', onChange: cambiar, __nextHasNoMarginBottom: true, __next40pxDefaultSize: true } );
	}

	Object.keys( config ).forEach( function ( nombre ) {
		const meta = config[ nombre ];
		// Atributos, título y demás metadatos llegan desde block.json (registrado en PHP).
		registerBlockType( nombre, {
			edit: function Editar( props ) {
				const atributos = ( wp.blocks.getBlockType( nombre ) || {} ).attributes || {};
				const blockProps = useBlockProps();
				const controles = Object.keys( atributos )
					.filter( ( a ) => ! [ 'className', 'lock', 'metadata', 'style' ].includes( a ) )
					.map( ( a ) => control( a, atributos[ a ], props.attributes[ a ], ( v ) => props.setAttributes( { [ a ]: v } ), meta ) );

				return el(
					wp.element.Fragment,
					null,
					controles.length
						? el( InspectorControls, null, el( PanelBody, { title: __( 'Ajustes', 'impakto' ), initialOpen: true }, controles ) )
						: null,
					el(
						'div',
						blockProps,
						el( Disabled, null, el( ServerSideRender, {
							block: nombre,
							attributes: props.attributes,
							EmptyResponsePlaceholder: () => el( Placeholder, { label: ( wp.blocks.getBlockType( nombre ) || {} ).title, instructions: __( 'Aún no hay contenido para mostrar con estos ajustes.', 'impakto' ) } ),
						} ) )
					)
				);
			},
			save: () => null,
		} );
	} );

	// "Respuesta rápida" como bloque propio en el insertor (párrafo con el estilo AEO).
	wp.blocks.registerBlockVariation( 'core/paragraph', {
		name: 'impakto-respuesta-rapida',
		title: __( 'Respuesta rápida', 'impakto' ),
		description: __( 'Resumen de 2 o 3 líneas al inicio del artículo: qué es, para quién, cuánto y dónde.', 'impakto' ),
		category: 'impakto',
		icon: 'lightbulb',
		attributes: { className: 'is-style-respuesta-rapida', placeholder: __( 'Escribe la respuesta directa en 2 o 3 líneas…', 'impakto' ) },
		scope: [ 'inserter' ],
		isActive: ( a ) => ( a.className || '' ).includes( 'is-style-respuesta-rapida' ),
	} );

	// Formato "Resaltado naranja" para el patrón bicolor de los títulos.
	registerFormatType( 'impakto/acento', {
		title: __( 'Resaltado naranja', 'impakto' ),
		tagName: 'mark',
		className: 'acento',
		edit: function BotonAcento( { isActive, value, onChange } ) {
			return el( RichTextToolbarButton, {
				icon: 'admin-appearance',
				title: __( 'Resaltado naranja', 'impakto' ),
				isActive,
				onClick: () => onChange( toggleFormat( value, { type: 'impakto/acento' } ) ),
			} );
		},
	} );
} )( window.wp );
