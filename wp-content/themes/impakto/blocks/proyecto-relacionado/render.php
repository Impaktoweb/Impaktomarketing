<?php
/**
 * Tarjeta de un proyecto del portafolio.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_p = $attributes['slug'] ? get_page_by_path( sanitize_title( $attributes['slug'] ), OBJECT, 'proyecto' ) : null;
if ( ! $impakto_p || 'publish' !== $impakto_p->post_status ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'proyecto-relacionado' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="kicker"><?php esc_html_e( 'Caso relacionado', 'impakto' ); ?></p>
	<?php echo impakto_tarjeta_proyecto( $impakto_p->ID, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
