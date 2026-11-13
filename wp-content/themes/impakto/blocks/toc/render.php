<?php
/**
 * Tabla de contenido automática (H2 del artículo).
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_indice = impakto_indice_articulo( get_queried_object_id() ? get_queried_object_id() : get_the_ID() );
if ( count( $impakto_indice ) < 2 ) {
	return;
}
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'toc' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-labelledby="toc-titulo">
	<details open>
		<summary id="toc-titulo"><?php esc_html_e( 'En este artículo', 'impakto' ); ?></summary>
		<ol>
			<?php foreach ( $impakto_indice as $impakto_h ) : ?>
				<li><a href="#<?php echo esc_attr( $impakto_h['ancla'] ); ?>"><?php echo esc_html( $impakto_h['texto'] ); ?></a></li>
			<?php endforeach; ?>
		</ol>
	</details>
</nav>
