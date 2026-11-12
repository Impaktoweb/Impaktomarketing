<?php
/**
 * Cifra destacada con contador (contadores en reveal.js). El número final queda en el HTML.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_valor = (string) $attributes['valor'];
// Valor numérico para el contador: "1.990" → 1990; "12,5" → 12.5.
$impakto_num      = (float) str_replace( array( '.', ',' ), array( '', '.' ), $impakto_valor );
$impakto_decimals = str_contains( $impakto_valor, ',' ) ? strlen( substr( strrchr( $impakto_valor, ',' ), 1 ) ) : 0;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'cifra' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="cifra__valor">
		<?php if ( $attributes['prefijo'] ) : ?><span class="cifra__prefijo"><?php echo esc_html( $attributes['prefijo'] ); ?></span><?php endif; ?><span class="cifra__numero" data-contador="<?php echo esc_attr( $impakto_num ); ?>" data-decimales="<?php echo esc_attr( $impakto_decimals ); ?>"><?php echo esc_html( $impakto_valor ); ?></span><?php if ( $attributes['sufijo'] ) : ?><span class="cifra__sufijo"><?php echo esc_html( $attributes['sufijo'] ); ?></span><?php endif; ?>
	</p>
	<?php if ( $attributes['texto'] ) : ?>
		<p class="cifra__texto"><?php echo esc_html( $attributes['texto'] ); ?></p>
	<?php endif; ?>
	<?php if ( $attributes['nota'] ) : ?>
		<p class="cifra__nota"><?php echo esc_html( $attributes['nota'] ); ?></p>
	<?php endif; ?>
</div>
