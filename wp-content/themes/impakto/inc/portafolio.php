<?php
/**
 * Portafolio: consulta del archivo, filtro "Casos de éxito" y tarjeta de proyecto reutilizable.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'query_vars',
	static function ( $vars ) {
		$vars[] = 'caso';
		return $vars;
	}
);

/**
 * Orden y paginación del archivo /portafolio/ y de sus categorías.
 */
add_action(
	'pre_get_posts',
	static function ( $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! ( $q->is_post_type_archive( 'proyecto' ) || $q->is_tax( 'categoria-portafolio' ) ) ) {
			return;
		}
		$q->set( 'posts_per_page', 9 );
		$q->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			)
		);
		if ( $q->get( 'caso' ) ) {
			$q->set(
				'meta_query',
				array(
					array(
						'key'   => '_impakto_caso_exito',
						'value' => '1',
					),
				)
			);
		}
	}
);

// "Casos de éxito" es una vista filtrada: canonical a /portafolio/ y sin indexar.
add_filter(
	'rank_math/frontend/canonical',
	static function ( $url ) {
		return get_query_var( 'caso' ) ? get_post_type_archive_link( 'proyecto' ) : $url;
	}
);

/**
 * Tarjeta de proyecto (bento, relacionados, archivo).
 *
 * @param int  $id     Proyecto.
 * @param bool $grande Tarjeta grande.
 * @param bool $pills  Mostrar categorías.
 * @return string
 */
function impakto_tarjeta_proyecto( $id, $grande = false, $pills = true ) {
	$res     = function_exists( 'impakto_resultados' ) ? impakto_resultados( $id ) : array();
	$caso    = get_post_meta( $id, '_impakto_caso_exito', true ) && $res;
	$cliente = get_post_meta( $id, '_impakto_cliente', true );
	$cats    = get_the_terms( $id, 'categoria-portafolio' );
	ob_start();
	?>
	<a class="tarjeta-proyecto" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
		<div class="tarjeta-proyecto__media">
			<?php
			echo has_post_thumbnail( $id )
				? get_the_post_thumbnail( $id, $grande ? 'impakto-destacado' : 'impakto-proyecto', array( 'loading' => 'lazy', 'sizes' => '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw' ) )
				: '<div class="marcador-imagen" aria-hidden="true"></div>';
			?>
		</div>
		<div class="tarjeta-proyecto__cuerpo">
			<?php if ( $caso ) : ?>
				<p class="tarjeta-proyecto__cifra"><span><?php echo esc_html( $res[0]['metrica'] ); ?></span><strong><?php echo esc_html( $res[0]['valor'] ); ?></strong></p>
			<?php endif; ?>
			<?php if ( $cliente ) : ?>
				<p class="tarjeta-proyecto__cliente"><?php echo esc_html( $cliente ); ?></p>
			<?php endif; ?>
			<h3 class="tarjeta-proyecto__titulo"><?php echo esc_html( get_the_title( $id ) ); ?></h3>
			<?php if ( $pills && $cats && ! is_wp_error( $cats ) ) : ?>
				<ul class="is-style-pills">
					<?php foreach ( array_slice( $cats, 0, 3 ) as $c ) : ?>
						<li><?php echo esc_html( $c->name ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</a>
	<?php
	return (string) ob_get_clean();
}
