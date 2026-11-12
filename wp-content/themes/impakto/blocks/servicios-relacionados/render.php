<?php
/**
 * Enlazado interno (BRIEF §13): 2 servicios relacionados + 1 artículo.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'servicio' ) ) {
	return;
}
$impakto_id    = get_the_ID();
$impakto_items = array();
foreach ( array( 'relacionado_1', 'relacionado_2' ) as $impakto_campo ) {
	$impakto_rel = (int) get_post_meta( $impakto_id, '_impakto_' . $impakto_campo, true );
	if ( $impakto_rel && 'publish' === get_post_status( $impakto_rel ) ) {
		$impakto_items[] = array(
			'tipo'   => __( 'Servicio', 'impakto' ),
			'titulo' => get_the_title( $impakto_rel ),
			'texto'  => get_the_excerpt( $impakto_rel ),
			'url'    => get_permalink( $impakto_rel ),
		);
	}
}
$impakto_art = (int) get_post_meta( $impakto_id, '_impakto_articulo', true );
if ( $impakto_art && 'publish' === get_post_status( $impakto_art ) ) {
	$impakto_items[] = array(
		'tipo'   => __( 'Del blog', 'impakto' ),
		'titulo' => get_the_title( $impakto_art ),
		'texto'  => wp_trim_words( get_the_excerpt( $impakto_art ), 20 ),
		'url'    => get_permalink( $impakto_art ),
	);
}
if ( ! $impakto_items ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'alignfull is-style-seccion-blanca seccion-relacionados' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="contenedor">
		<div class="encabezado-seccion">
			<h2><?php echo wp_kses( $attributes['titulo'], array( 'mark' => array( 'class' => true ) ) ); ?></h2>
		</div>
		<ul class="servicios-franja relacionados">
			<?php foreach ( $impakto_items as $impakto_i ) : ?>
				<li>
					<a class="tarjeta-servicio" href="<?php echo esc_url( $impakto_i['url'] ); ?>">
						<span class="tarjeta-servicio__tipo"><?php echo esc_html( $impakto_i['tipo'] ); ?></span>
						<h3 class="tarjeta-servicio__titulo"><?php echo esc_html( $impakto_i['titulo'] ); ?></h3>
						<?php if ( $impakto_i['texto'] ) : ?>
							<p class="tarjeta-servicio__texto"><?php echo esc_html( $impakto_i['texto'] ); ?></p>
						<?php endif; ?>
						<span class="tarjeta-servicio__flecha" aria-hidden="true"><?php echo impakto_icono( 'flecha-diagonal', 'icono--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
