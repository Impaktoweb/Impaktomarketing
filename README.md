# Impakto Marketing — sitio web (tema `impakto` + plugin `impakto-core`)

WordPress con **tema de bloques a medida** y **plugin a medida**, sin constructores visuales. Este repositorio contiene solo:

```
wp-content/themes/impakto/        Presentación: plantillas, partes, patrones, bloques, CSS y JS
wp-content/plugins/impakto-core/  Datos y lógica: tipos de contenido, ajustes, tarifas, cotizaciones, solicitudes
```

Plugins de terceros permitidos: **Rank Math SEO**, **LiteSpeed Cache** (solo en Hostinger) y **FluentSMTP**.

---

## Estructura

### Tema `impakto`
| Carpeta | Qué hay |
|---|---|
| `theme.json` | Paleta, Montserrat local, escala tipográfica, espaciados y anchos (760 / 1280 px). |
| `assets/css/tokens.css` | **Fuente de verdad de los colores y medidas.** |
| `assets/css/components/` | Botones, secciones, elementos de marca, tarjetas, header, menú móvil, footer, formularios, cotizador. |
| `assets/js/` | `menu.js`, `reveal.js` (animaciones, contadores, FAQ, lightbox, mapa, video), `cotizador.js`, `formulario.js`, `consent.js`, `tracking.js`, `portafolio-filtros.js`. Módulos ES, sin jQuery. |
| `assets/fonts/` | Montserrat 300–700 en woff2 (licencia OFL incluida). |
| `templates/`, `parts/` | Plantillas de bloques (Inicio, páginas, servicio, proyecto, portafolio, blog, artículo, legal, 404). |
| `patterns/` | Páginas completas y secciones reutilizables (Inicio, Servicios, Nosotros, Estudio, Sala de podcast, Equipos, Contacto, CTA final, guía de estilo). |
| `blocks/` | Bloques dinámicos propios (`block.json` + `render.php`; el editor usa `assets/js/editor.js`, sin compilación). |
| `inc/` | `schema.php` (JSON-LD), `seo-helpers.php` (breadcrumbs, noindex, títulos), `whatsapp.php` (`whatsapp_link()`), `consentimiento.php` (cookies y Consent Mode v2), `portafolio.php`, `blog.php`, `navegacion.php`. |
| `contenido/GUIA-PUBLICAR-ARTICULO.md` | Guía de una página para publicar en el blog. |

### Plugin `impakto-core`
| Archivo | Qué hace |
|---|---|
| `cpt-servicio.php`, `cpt-proyecto.php`, `cpt-equipo.php`, `taxonomias.php`, `meta-campos.php` | Tipos de contenido y sus campos. |
| `ajustes.php` | **Impakto → Ajustes** (NAP, WhatsApp, correos CEO/KAM, redes, GA4, Pixel). |
| `ajustes-tarifas.php`, `migracion-tarifas.php` | **Impakto → Tarifas** (edición campo por campo, importar/exportar JSON). |
| `calculo.php`, `cotizacion.php` | Reglas de precio del BRIEF §7.2 (el servidor recalcula siempre). |
| `rest-api.php`, `rest-solicitudes.php`, `rest-contacto.php` | `GET /wp-json/impakto/v1/tarifas`, `POST /solicitudes`, `POST /contacto`, `GET /nonce`. |
| `leads-db.php`, `leads-admin.php` | Tabla `{prefix}impakto_solicitudes` e **Impakto → Solicitudes** (filtros, detalle, estado, CSV). |
| `correos.php`, `templates/emails/` | Aviso interno y cotización HTML al cliente. |
| `antispam.php` | Honeypot, 3 s mínimos, 5 envíos/h por IP, nonce. |
| `aeo.php` | `/llms.txt` y `robots.txt`. |
| `redirecciones.php` | Redirecciones 301 del sitio anterior (BRIEF §14). |
| `cli.php`, `semillas*.php`, `data/` | Comandos WP-CLI y contenido inicial. |

---

## Cómo editar (sin programar)

| Quiero… | Dónde |
|---|---|
| Cambiar un precio del estudio, del podcast, de la edición o de los adicionales | **Impakto → Tarifas** → editar → *Guardar tarifas*. El cotizador, las tablas, los precios "desde" y `/llms.txt` se actualizan solos. |
| Cambiar el precio de un equipo, agregar o quitar uno | **Equipos** → editar / *Añadir equipo* (precio, unidad, ¿requiere operador?, specs y foto de 1000 × 1000). Un equipo sin precio queda en borrador o muestra "Consultar". |
| Cambiar WhatsApp, dirección, correos que reciben solicitudes, redes o IDs de analítica | **Impakto → Ajustes**. |
| Ver y gestionar solicitudes y cotizaciones | **Impakto → Solicitudes** (Administrador y Editor). |
| Cargar un proyecto al portafolio | **Portafolio → Añadir proyecto**: título, resumen (extracto), *Ficha del proyecto* (cliente, sector, año, ubicación, destacado, caso de éxito, pills, resultados `métrica \| valor \| nota`, reto, solución, galería, video, testimonio), categorías e imagen destacada. Plantilla de datos: `impakto-web/contenido/portafolio/portafolio.md`. |
| Publicar un artículo | Ver `wp-content/themes/impakto/contenido/GUIA-PUBLICAR-ARTICULO.md`. |
| Editar textos de páginas | **Páginas** → editar con bloques. Bloques propios en el insertor, grupo **Impakto**. |
| Menús | **Apariencia → Menús** (Principal y columnas del footer). El mega menú de Servicios se arma solo con los servicios publicados y su "bloque del proceso". |
| Redirecciones nuevas | **Rank Math → Redirecciones**. |

## Comandos WP-CLI
```bash
wp impakto instalar          # En un WordPress nuevo: tarifas + contenido + configuración SEO
wp impakto configurar        # Solo ajustes del sitio y de Rank Math
wp impakto importar-tarifas  # Tarifas desde data/tarifas.json (o --archivo=)
wp impakto sembrar           # Contenido inicial (no sobrescribe; --forzar para reemplazar)
wp impakto probar-calculo    # Verifica los ejemplos de precio del BRIEF
```
En WordPress Studio, antepón `studio`: `studio wp impakto probar-calculo`.

## Desarrollo local
WordPress Studio, PHP 8.4, SQLite. `WP_DEBUG` y `WP_DEVELOPMENT_MODE=theme` activos (los patrones no se cachean). Los correos quedan en `~/.studio/email/`.
