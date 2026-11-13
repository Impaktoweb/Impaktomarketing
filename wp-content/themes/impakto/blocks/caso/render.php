<?php
/**
 * Vista de proyecto (BRIEF §9): portada a pantalla completa → ficha → reto / solución /
 * resultados (cifras con contador) → contenido libre → galería → video → testimonio.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_id = get_queried_object_id();
if ( ! $impakto_id || 'proyecto' !== get_post_type( $impakto_id ) ) {
	return;
}
$impakto_m      = static fn( $k ) => (string) get_post_meta( $impakto_id, '_impakto_' . $k, true );
$impakto_cats   = get_the_terms( $impakto_id, 'categoria-portafolio' );
$impakto_cats   = $impakto_cats && ! is_wp_error( $impakto_cats ) ? $impakto_cats : array();
$impakto_res    = function_exists( 'impakto_resultados' ) ? impakto_resultados( $impakto_id ) : array();
$impakto_sol    = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $impakto_m( 'solucion' ) ) ) );
$impakto_pills  = array_filter( array_map( 'trim', explode( ',', $impakto_m( 'pills' ) ) ) );
$impakto_ficha  = array_filter(
	array(
		__( 'Cliente', 'impakto' )   => $impakto_m( 'cliente' ),
		__( 'Sector', 'impakto' )    => $impakto_m( 'sector' ),
		__( 'Año', 'impakto' )       => $impakto_m( 'anio' ),
		__( 'Ubicación', 'impakto' ) => $impakto_m( 'ubicacion' ),
	)
);

/**
 * URL de inserción para YouTube o Vimeo.
 */
$impakto_embed = static function ( $url ) {
	if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([\w-]{11})~', $url, $m ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0';
	}
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&dnt=1';
	}
	return '';
};
$impakto_video = $impakto_embed( $impakto_m( 'video' ) );
?>
<article class="caso">
	<header class="caso__hero es-oscuro">
		<div class="caso__portada">
			<?php echo has_post_thumbnail( $impakto_id ) ? get_the_post_thumbnail( $impakto_id, 'full', array( 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '100vw' ) ) : '<div class="marcador-imagen" aria-hidden="true"></div>'; ?>
		</div>
		<div class="contenedor caso__hero-texto">
			<?php if ( $impakto_cats ) : ?>
				<p class="kicker"><?php echo esc_html( implode( ' · ', wp_list_pluck( $impakto_cats, 'name' ) ) ); ?></p>
			<?php endif; ?>
			<h1><?php echo esc_html( get_the_title( $impakto_id ) ); ?></h1>
			<?php if ( has_excerpt( $impakto_id ) ) : ?>
				<p class="is-style-bajada"><?php echo esc_html( get_the_excerpt( $impakto_id ) ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( $impakto_ficha || $impakto_cats ) : ?>
		<section class="caso__ficha">
			<div class="contenedor">
				<dl class="ficha">
					<?php foreach ( $impakto_ficha as $impakto_k => $impakto_v ) : ?>
						<div><dt><?php echo esc_html( $impakto_k ); ?></dt><dd><?php echo esc_html( $impakto_v ); ?></dd></div>
					<?php endforeach; ?>
					<?php if ( $impakto_cats ) : ?>
						<div class="ficha__servicios"><dt><?php esc_html_e( 'Servicios', 'impakto' ); ?></dt><dd>
							<?php foreach ( $impakto_cats as $impakto_i => $impakto_c ) : ?>
								<a href="<?php echo esc_url( get_term_link( $impakto_c ) ); ?>"><?php echo esc_html( $impakto_c->name ); ?></a><?php echo $impakto_i < count( $impakto_cats ) - 1 ? ' · ' : ''; ?>
							<?php endforeach; ?>
						</dd></div>
					<?php endif; ?>
				</dl>
				<?php if ( $impakto_pills ) : ?>
					<ul class="is-style-pills caso__pills">
						<?php foreach ( $impakto_pills as $impakto_p ) : ?>
							<li><?php echo esc_html( $impakto_p ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $impakto_m( 'reto' ) || $impakto_sol ) : ?>
		<section class="alignfull is-style-seccion-blanca caso__historia">
			<div class="contenedor caso__columnas">
				<?php if ( $impakto_m( 'reto' ) ) : ?>
					<div>
						<p class="kicker"><?php esc_html_e( 'El reto', 'impakto' ); ?></p>
						<p class="caso__reto"><?php echo esc_html( $impakto_m( 'reto' ) ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( $impakto_sol ) : ?>
					<div>
						<p class="kicker"><?php esc_html_e( 'La solución', 'impakto' ); ?></p>
						<ul class="wp-block-list is-style-flecha">
							<?php foreach ( $impakto_sol as $impakto_linea ) : ?>
								<?php $impakto_partes = explode( ':', $impakto_linea, 2 ); ?>
								<li><?php echo 2 === count( $impakto_partes ) && mb_strlen( $impakto_partes[0] ) < 40 ? '<strong>' . esc_html( $impakto_partes[0] ) . ':</strong>' . esc_html( $impakto_partes[1] ) : esc_html( $impakto_linea ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $impakto_res ) : ?>
		<section class="alignfull is-style-seccion-oscura es-oscuro caso__resultados">
			<div class="contenedor">
				<div class="encabezado-seccion"><p class="kicker"><?php esc_html_e( 'Resultados', 'impakto' ); ?></p><h2><?php echo wp_kses( __( 'Lo que <mark class="acento">medimos</mark>', 'impakto' ), array( 'mark' => array( 'class' => true ) ) ); ?></h2></div>
				<div class="cifras">
					<?php
					foreach ( $impakto_res as $impakto_r ) {
						preg_match( '/^(\$?)\s*([\d.,]+)\s*(.*)$/u', $impakto_r['valor'], $impakto_x );
						$impakto_attrs = $impakto_x
							? array( 'prefijo' => $impakto_x[1], 'valor' => $impakto_x[2], 'sufijo' => $impakto_x[3] ? ' ' . $impakto_x[3] : '', 'texto' => $impakto_r['metrica'], 'nota' => $impakto_r['nota'] )
							: array( 'prefijo' => '', 'valor' => $impakto_r['valor'], 'sufijo' => '', 'texto' => $impakto_r['metrica'], 'nota' => $impakto_r['nota'] );
						echo render_block( array( 'blockName' => 'impakto/cifra', 'attrs' => $impakto_attrs, 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$impakto_contenido = trim( (string) get_post_field( 'post_content', $impakto_id ) );
	if ( $impakto_contenido ) :
		?>
		<section class="alignfull is-style-seccion-clara caso__contenido">
			<div class="contenedor contenedor--texto"><?php echo apply_filters( 'the_content', $impakto_contenido ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</section>
	<?php endif; ?>

	<section class="alignfull is-style-seccion-clara caso__galeria">
		<div class="contenedor">
			<?php
			echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'blockName'    => 'impakto/galeria',
					'attrs'        => array( 'ids' => $impakto_m( 'galeria' ), 'marcadores' => 6, 'etiqueta' => __( 'Pieza del proyecto', 'impakto' ) ),
					'innerBlocks'  => array(),
					'innerHTML'    => '',
					'innerContent' => array(),
				)
			);
			?>
			<?php if ( $impakto_video ) : ?>
				<div class="video-facade" data-video="<?php echo esc_url( $impakto_video ); ?>">
					<button type="button" class="video-facade__boton"><?php echo impakto_icono( 'play', 'icono--relleno' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Ver video del proyecto', 'impakto' ); ?></span></button>
				</div>
			<?php endif; ?>
			<?php if ( $impakto_m( 'testimonio' ) ) : ?>
				<figure class="testimonio">
					<blockquote><p><?php echo esc_html( $impakto_m( 'testimonio' ) ); ?></p></blockquote>
					<?php if ( $impakto_m( 'testimonio_autor' ) ) : ?>
						<figcaption><?php echo esc_html( $impakto_m( 'testimonio_autor' ) ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
		</div>
	</section>
</article>
