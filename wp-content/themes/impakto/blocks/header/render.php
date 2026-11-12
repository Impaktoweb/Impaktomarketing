<?php
/**
 * Header: logo, navegación, mega menú de servicios, "Cotiza tu espacio", menú móvil y selector.
 *
 * @package Impakto
 */

defined( 'ABSPATH' ) || exit;

$impakto_items    = impakto_items_menu( 'principal' );
$impakto_grupos   = impakto_servicios_agrupados();
$impakto_logo_c   = IMPAKTO_TEMA_URL . '/assets/img/logo-impakto-color.png';
$impakto_logo_b   = IMPAKTO_TEMA_URL . '/assets/img/logo-impakto-blanco.png';
$impakto_marca    = impakto_dato( 'marca', 'Impakto Marketing' );
$impakto_es_mega  = static fn( $url ) => '/servicios/' === trailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
$impakto_numero_g = 0;

/**
 * Mega menú (escritorio) o lista de servicios (móvil).
 */
$impakto_pintar_grupos = static function ( $grupos, $modo ) {
	$n = 0;
	foreach ( $grupos as $grupo ) {
		++$n;
		if ( 'mega' === $modo ) {
			echo '<div class="mega__col"><p class="mega__titulo"><span>' . esc_html( sprintf( '%02d', $n ) ) . '</span>' . esc_html( $grupo['titulo'] ) . '</p><ul class="mega__lista">';
		} else {
			echo '<p class="menu-movil__grupo">' . esc_html( $grupo['titulo'] ) . '</p><ul>';
		}
		foreach ( $grupo['servicios'] as $servicio ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $servicio ) ), esc_html( get_the_title( $servicio ) ) );
		}
		echo 'mega' === $modo ? '</ul></div>' : '</ul>';
	}
};
?>
<a class="saltar-contenido" href="#contenido"><?php esc_html_e( 'Saltar al contenido', 'impakto' ); ?></a>
<header class="sitio-header" data-header>
	<div class="sitio-header__barra contenedor">
		<a class="sitio-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<img class="logo-color" src="<?php echo esc_url( $impakto_logo_c ); ?>" width="150" height="47" alt="<?php echo esc_attr( $impakto_marca ); ?>">
			<img class="logo-blanco" src="<?php echo esc_url( $impakto_logo_b ); ?>" width="150" height="47" alt="<?php echo esc_attr( $impakto_marca ); ?>">
		</a>

		<nav class="nav-principal" aria-label="<?php esc_attr_e( 'Principal', 'impakto' ); ?>">
			<ul class="nav-principal__lista">
				<?php foreach ( $impakto_items as $impakto_item ) : ?>
					<li class="nav-principal__item">
						<a class="nav-principal__enlace" href="<?php echo esc_url( $impakto_item['url'] ); ?>"<?php echo $impakto_item['actual'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $impakto_item['titulo'] ); ?></a>
						<?php if ( $impakto_grupos && $impakto_es_mega( $impakto_item['url'] ) ) : ?>
							<button type="button" class="nav-principal__toggle" aria-expanded="false" aria-controls="mega-servicios" data-mega-toggle>
								<?php echo impakto_icono( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="screen-reader-text"><?php esc_html_e( 'Ver los servicios', 'impakto' ); ?></span>
							</button>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="sitio-header__acciones">
			<?php echo impakto_boton_cotizar( '', 'boton boton--sm sitio-header__cotizar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button type="button" class="sitio-header__menu" aria-controls="menu-movil" aria-expanded="false" data-abre-menu>
				<?php echo impakto_icono( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menú', 'impakto' ); ?></span>
			</button>
		</div>
	</div>

	<?php if ( $impakto_grupos ) : ?>
		<div class="mega es-oscuro" id="mega-servicios" data-mega>
			<div class="contenedor">
				<div class="mega__rejilla">
					<?php $impakto_pintar_grupos( $impakto_grupos, 'mega' ); ?>
				</div>
				<div class="mega__pie">
					<p><?php esc_html_e( '¿Tienes un proyecto inmobiliario?', 'impakto' ); ?> <a href="<?php echo esc_url( home_url( '/marketing-inmobiliario/' ) ); ?>"><?php esc_html_e( 'Conoce nuestro marketing inmobiliario', 'impakto' ); ?></a></p>
					<a class="boton--enlace boton" href="<?php echo esc_url( home_url( '/servicios/' ) ); ?>"><?php esc_html_e( 'Ver todos los servicios', 'impakto' ); ?></a>
				</div>
			</div>
		</div>
	<?php endif; ?>
</header>

<dialog class="menu-movil es-oscuro" id="menu-movil" aria-label="<?php esc_attr_e( 'Menú', 'impakto' ); ?>" data-menu-movil>
	<div class="menu-movil__cabecera contenedor">
		<img src="<?php echo esc_url( $impakto_logo_b ); ?>" width="150" height="47" alt="<?php echo esc_attr( $impakto_marca ); ?>" loading="lazy">
		<button type="button" class="boton-icono menu-movil__cerrar" data-cierra-menu>
			<?php echo impakto_icono( 'cerrar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Cerrar menú', 'impakto' ); ?></span>
		</button>
	</div>
	<div class="menu-movil__cuerpo contenedor">
		<nav aria-label="<?php esc_attr_e( 'Principal (móvil)', 'impakto' ); ?>">
			<ul class="menu-movil__lista">
				<?php foreach ( $impakto_items as $impakto_item ) : ?>
					<li>
						<?php if ( $impakto_grupos && $impakto_es_mega( $impakto_item['url'] ) ) : ?>
							<button type="button" class="menu-movil__desplegar" aria-expanded="false" aria-controls="menu-movil-servicios" data-desplegar>
								<?php echo esc_html( $impakto_item['titulo'] ); ?>
								<?php echo impakto_icono( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
							<div class="menu-movil__sub" id="menu-movil-servicios" hidden>
								<?php $impakto_pintar_grupos( $impakto_grupos, 'movil' ); ?>
								<ul><li><a href="<?php echo esc_url( $impakto_item['url'] ); ?>"><?php esc_html_e( 'Ver todos los servicios →', 'impakto' ); ?></a></li></ul>
							</div>
						<?php else : ?>
							<a class="menu-movil__enlace" href="<?php echo esc_url( $impakto_item['url'] ); ?>"<?php echo $impakto_item['actual'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $impakto_item['titulo'] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<div class="menu-movil__acciones">
			<?php
			echo impakto_boton_whatsapp( array( 'ubicacion' => 'header', 'clase' => 'boton boton--bloque' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo impakto_boton_cotizar( '', 'boton boton--secundario boton--bloque' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<div class="menu-movil__nap">
			<?php echo esc_html( impakto_dato( 'direccion' ) . ( impakto_dato( 'referencia' ) ? ' (' . impakto_dato( 'referencia' ) . ')' : '' ) . ', ' . impakto_dato( 'ciudad' ) . ', ' . impakto_dato( 'region' ) ); ?><br>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', impakto_dato( 'telefono' ) ) ); ?>" data-evento="click_telefono" data-ubicacion="menu-movil"><?php echo esc_html( impakto_dato( 'telefono' ) ); ?></a>
		</div>
	</div>
</dialog>

<dialog class="selector-espacio" id="selector-espacio" aria-labelledby="selector-espacio-titulo" data-selector>
	<div class="selector-espacio__cuerpo">
		<div class="selector-espacio__cabecera">
			<div>
				<p class="kicker"><?php esc_html_e( 'Estudio · Podcast · Equipos', 'impakto' ); ?></p>
				<h2 id="selector-espacio-titulo"><?php echo wp_kses( __( '¿Qué quieres <mark class="acento">cotizar?</mark>', 'impakto' ), array( 'mark' => array( 'class' => true ) ) ); ?></h2>
			</div>
			<button type="button" class="boton-icono" data-cierra-selector>
				<?php echo impakto_icono( 'cerrar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Cerrar', 'impakto' ); ?></span>
			</button>
		</div>
		<ul class="selector-espacio__opciones">
			<?php
			$impakto_opciones = array(
				array( __( 'Estudio audiovisual', 'impakto' ), __( 'Medio estudio de 45 m² o estudio completo de 90 m².', 'impakto' ), '/estudio/#cotizador', impakto_tema_desde( 'estudio' ), __( '/ hora', 'impakto' ), __( 'Desde %s', 'impakto' ) ),
				array( __( 'Sala de podcast', 'impakto' ), __( '24 m² insonorizados, de 1 a 3 cámaras y acompañamiento técnico.', 'impakto' ), '/sala-de-podcast/#cotizador', impakto_tema_desde( 'podcast' ), __( '/ hora', 'impakto' ), __( 'Desde %s', 'impakto' ) ),
				array( __( 'Alquiler de equipos', 'impakto' ), __( 'Cámaras, lentes, drones, iluminación, audio y más.', 'impakto' ), '/alquiler-de-equipos/#cotizador', impakto_tema_desde( 'equipos' ), __( '/ jornada', 'impakto' ), __( 'Cámaras desde %s', 'impakto' ) ),
			);
			foreach ( $impakto_opciones as list( $impakto_t, $impakto_d, $impakto_u, $impakto_p, $impakto_unidad, $impakto_formato ) ) :
				?>
				<li>
					<a class="selector-espacio__opcion" href="<?php echo esc_url( home_url( $impakto_u ) ); ?>">
						<strong><?php echo esc_html( $impakto_t ); ?></strong>
						<span><?php echo esc_html( $impakto_d ); ?></span>
						<?php if ( $impakto_p ) : ?>
							<span class="selector-espacio__precio"><?php echo esc_html( sprintf( $impakto_formato, impakto_tema_cop( $impakto_p, false ) ) . ' ' . $impakto_unidad ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="selector-espacio__nota is-style-nota"><?php esc_html_e( 'Valores con IVA incluido. Todo es personalizable según los equipos y el tiempo que necesites.', 'impakto' ); ?></p>
	</div>
</dialog>
