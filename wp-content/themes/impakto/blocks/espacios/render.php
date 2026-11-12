<?php
/**
 * Espacios propios: Estudio, Sala de podcast y Equipos, con precio "desde" de Impakto → Tarifas.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_espacios = array(
	array(
		'titulo' => __( 'Estudio audiovisual', 'impakto' ),
		'texto'  => $attributes['textoEstudio'],
		'precio' => impakto_tema_desde( 'estudio' ),
		'unidad' => __( '/ hora', 'impakto' ),
		'prefijo' => __( 'Desde', 'impakto' ),
		'boton'  => __( 'Cotizar estudio', 'impakto' ),
		'url'    => '/estudio/',
		'foto'   => __( 'Foto del estudio', 'impakto' ),
	),
	array(
		'titulo' => __( 'Sala de podcast', 'impakto' ),
		'texto'  => $attributes['textoPodcast'],
		'precio' => impakto_tema_desde( 'podcast' ),
		'unidad' => __( '/ hora', 'impakto' ),
		'prefijo' => __( 'Desde', 'impakto' ),
		'boton'  => __( 'Cotizar sala', 'impakto' ),
		'url'    => '/sala-de-podcast/',
		'foto'   => __( 'Foto de la sala', 'impakto' ),
	),
	array(
		'titulo' => __( 'Alquiler de equipos', 'impakto' ),
		'texto'  => $attributes['textoEquipos'],
		'precio' => impakto_tema_desde( 'equipos' ),
		'unidad' => __( '/ jornada', 'impakto' ),
		'prefijo' => __( 'Cámaras desde', 'impakto' ),
		'boton'  => __( 'Ver catálogo', 'impakto' ),
		'url'    => '/alquiler-de-equipos/',
		'foto'   => __( 'Foto de equipos', 'impakto' ),
	),
);
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'espacios' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $impakto_espacios as $impakto_e ) : ?>
		<li>
			<article class="tarjeta-espacio">
				<div class="tarjeta-espacio__media">
					<?php /* [CONFIRMAR] Foto real 1200×1500 (DISENOS-Y-MEDIDAS §2). */ ?>
					<div class="marcador-imagen" style="--ratio: 4 / 5" role="img" aria-label="<?php echo esc_attr( $impakto_e['foto'] ); ?>"><span><?php echo esc_html( $impakto_e['foto'] ); ?></span></div>
					<?php if ( $impakto_e['precio'] ) : ?>
						<p class="tarjeta-espacio__precio"><?php echo esc_html( $impakto_e['prefijo'] . ' ' . impakto_tema_cop( $impakto_e['precio'], false ) . ' ' . $impakto_e['unidad'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="tarjeta-espacio__cuerpo">
					<h3 class="tarjeta-espacio__titulo"><?php echo esc_html( $impakto_e['titulo'] ); ?></h3>
					<?php if ( $impakto_e['texto'] ) : ?>
						<p class="tarjeta-espacio__texto"><?php echo esc_html( $impakto_e['texto'] ); ?></p>
					<?php endif; ?>
					<a class="boton boton--secundario boton--sm" href="<?php echo esc_url( home_url( $impakto_e['url'] ) ); ?>"><?php echo esc_html( $impakto_e['boton'] ); ?> <span aria-hidden="true">→</span></a>
				</div>
			</article>
		</li>
	<?php endforeach; ?>
</ul>
