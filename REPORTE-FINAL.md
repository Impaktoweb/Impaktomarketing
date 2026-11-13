# Reporte final — sitio web de Impakto Marketing (v1.0.0)

Entorno: WordPress 7.1.4 · PHP 8.4 · WordPress Studio (local) · fecha: 13 de noviembre de 2026.
Commits por fase: `d34dcd4` (1) · `dc9cb34` (2) · `d08b1ca` + `be7f67b` (3) · `f17133f` (4) · `6d4cc6c` (5) · `021fb2a` (6) · Fase 7 (este reporte).

## 1. Lista de verificación (BRIEF §15)

| Punto | Resultado |
|---|---|
| Sin errores PHP/JS con `WP_DEBUG` | ✅ 63 URL recorridas (sitemap + `/llms.txt`, `/robots.txt`, 404, búsqueda, paginación, REST): `debug.log` vacío. 92 archivos PHP sin errores de sintaxis. Consola del navegador sin errores de JS en 13 plantillas. |
| PHPCS / WordPress Coding Standards | ⚠️ **No ejecutado:** el equipo no tiene PHP ni Composer instalados fuera de Studio. El código sigue WPCS (prefijos, escapado, `$wpdb->prepare`, nonces, sanitización). Ejecutar en el staging o en CI: `phpcs --standard=WordPress wp-content/themes/impakto wp-content/plugins/impakto-core`. |
| Lighthouse móvil (Inicio, servicio, Estudio, proyecto, artículo) | ⚠️ **Pendiente en el staging** (Lighthouse/PageSpeed necesita una URL pública). Medido en local: Inicio ≈ 190 KB sin imágenes + ≈ 95 KB de fuentes (meta < 1,5 MB), 0 jQuery, **CLS 0**, JS diferido en módulos, fuentes 400/600 precargadas, imágenes con `width`/`height`, `loading="lazy"` y WebP. |
| Title, description, canonical, OG y un solo H1 por página; JSON-LD válido | ✅ 55 URL del sitemap con canonical, `og:image`, Twitter Card, un H1 y `lang="es-CO"`. JSON-LD parseable en todas. ⚠️ Fuera del rango 50–60 / 140–155 por venir así en los textos aprobados: título de Estudio (68) y de Términos de alquiler (66); descripciones de Términos de uso (54), Política de datos (102) y Cookies (91). |
| Cotizador: cálculos §7.2, WhatsApp, correo al cliente, copia a CEO/KAM, registro y eventos | ✅ `wp impakto probar-calculo`: medio estudio 5 h = $470.000, 6 h = $590.000, 7 h = $700.000; completo 8 h = $1.200.000; podcast 2c+2m × 3 h = $600.000; ejemplo del BRIEF (completo 5 h + A7 IV + 2 FC120C + maquillaje) = $1.490.000. Envíos por correo y WhatsApp con consecutivo `IMP-2026-NNNN`, registro en Solicitudes, correo HTML al cliente y aviso interno. Eventos `cotizador_inicio`, `cotizacion_email`, `cotizacion_whatsapp` en `dataLayer`. ⚠️ GA4 DebugView y Meta "Probar eventos" requieren los IDs reales (staging). |
| Formulario de contacto de punta a punta | ✅ Validación accesible, registro con origen UTM, correo interno, `/gracias/` (noindex) y `generate_lead`. Funciona aunque la página venga de la caché (nonce fresco). |
| Banner de cookies: nada de analítica antes de aceptar | ✅ Comprobado: sin GA4, Pixel ni cookie de campaña antes de decidir; con "solo analítica" carga GA4 y no el Pixel. Consent Mode v2 por defecto `denied`. |
| Redirecciones §14 | ✅ `/portfolio-item/{slug}/`, `/portafolio-item/{slug}/`, `/portafolio-categoria/{slug}/`, `/portfolio/`, `/hello-world/`, `/user-interface-graphic-design/`, `/category/{slug}/` → 301 al destino correcto. Anclas `/servicios#branding` → `/servicios/branding/` por JS. |
| Revisión visual a 375, 768 y 1440 px | ✅ Todas las plantillas revisadas en cada fase; ninguna con desbordamiento horizontal a 375 px. |
| Accesibilidad | ⚠️ axe-core (WCAG 2.1 A/AA) en 13 plantillas a 375 y 1440 px: 0 violaciones antes de la decisión de marca del punto 3.1; con #FF8000 en títulos, números, enlaces y etiquetas sobre fondo claro, axe vuelve a marcar su contraste (2,5:1). Resto sin violaciones: Saltar al contenido, foco visible, teclado en menús, mega menú y diálogos, `label` en todos los campos, total del cotizador con `aria-live`, `prefers-reduced-motion`. |
| Lista final de `[CONFIRMAR]` | ✅ Sección 4. |

## 2. Entregables
- `impakto-web/entrega/impakto.zip` — tema (v1.0.0).
- `impakto-web/entrega/impakto-core.zip` — plugin (v1.0.0).
- `impakto-web/entrega/impakto-contenido.xml` — WXR: 17 páginas, 5 artículos, 12 servicios, 14 proyectos, 73 equipos y menús.
- `impakto-web/entrega/tarifas-impakto.json` — tarifas y catálogo para Impakto → Tarifas → Importar.
- `INSTALACION-STAGING.md` — paso a paso en Hostinger. `README.md` — estructura y cómo editar.
- `wp-content/themes/impakto/contenido/GUIA-PUBLICAR-ARTICULO.md` — guía para el equipo.

## 3. Cambios respecto a los textos y al BRIEF (aprobados por Impakto el 13/11/2026)
1. **Naranja de Impakto en todo el sitio (decisión de marca):** títulos, números, enlaces y etiquetas usan el naranja del logo **#FF8000** también sobre fondo claro, como en los PDF de la marca. Contraste 2,5:1: por debajo de WCAG AA (4,5:1 en texto pequeño y 3:1 en grande), así que axe y Lighthouse lo marcarán y la puntuación de Accesibilidad puede quedar bajo 95. Los botones naranjas siguen con texto grafito (6,8:1).
2. **Sección naranja** con texto grafito (el blanco sobre naranja no cumple WCAG).
3. **Podcast:** "hasta 4 cámaras" → "hasta 3 cámaras" (Inicio y meta descripción), coherente con los paquetes.
4. **Líneas de tiempo:** se quitaron los dos puntos tras cada título y el texto empieza con mayúscula. "Para quién" con mayúscula inicial.
5. **Títulos de sección** agregados donde el `.md` solo nombraba la sección ("Qué incluye", "Cómo lo hacemos", "¿Para quién es?", "Lo que más nos preguntan", "Reserva en 3 pasos", "Condiciones clave"…) y textos de interfaz (mensajes de WhatsApp precargados por página, botones, confirmaciones, `/gracias/`, banner de cookies).
6. **Secciones sin texto aprobado no se publican** (no se inventó contenido): "Qué incluye" de Naming; "Cómo lo hacemos" de Fotografía, Diseño, Redes, Pauta, Web y CRM; algunas "Para quién" y FAQ.
7. **Legales (revisión legal hecha):** anticipo **20 %** y saldo **al finalizar el servicio** (tomados del PDF de Términos & condiciones); el correo se muestra desde Ajustes (si falta, remite a WhatsApp); vigencia = fecha de publicación; se retiraron las notas internas `[CONFIRMAR]` del texto visible.
8. **Blog:** autor "Equipo Impakto" para los 5 artículos; se omitió la pregunta del parqueadero (sin respuesta); las respuestas del FAQ de los artículos quedaron sin enlaces.
9. **Cotizador:** equipos sin precio no se muestran; productor en alquiler de equipos = jornada de 8 h "sujeto a confirmación"; kits = suma de sus equipos; podcast con equipos adicionales de micrófonos, teleprompters e iluminación.
10. **Portafolio:** solo 4 proyectos marcados "Destacado" (Inicio muestra 4, no 5–6).
11. Descripciones de categorías del blog redactadas para SEO (neutras, sin cifras).

## 4. Datos `[CONFIRMAR]` pendientes
**Empresa y contacto**
- [ ] Dominio preferido: con o sin `www`.
- [ ] Dirección: "Cra. 22 #35-62 (detrás de Hyundai)" o "Cra 22 A Calle 35 A" (contrato). Debe coincidir con Google Business Profile.
- [ ] WhatsApp/teléfono +57 312 552 5901.
- [ ] Correo público; correos de **CEO** y **KAM**; proveedor de correo para FluentSMTP.
- [ ] Horario de atención; coordenadas (`geo`); URL de Google Business Profile.
- [ ] IDs de **GA4** y del **Pixel de Meta**; verificación de Search Console (DNS recomendado).

**Marca y material**
- [ ] Logos en **SVG** e **isotipo** oficial (el favicon, el patrón de curvas y la imagen para compartir son provisionales, dibujados desde el PNG).
- [ ] Fotos y video (hero de Inicio, espacios, ~60 equipos, portadas y galerías de proyectos, imágenes destacadas del blog, mapa y fachada) según `DISENOS-Y-MEDIDAS.md`.
- [ ] PDF "Plantilla de cotización Estudio y Podcast" (no llegó).

**Tarifas y alquiler**
- [x] Anticipo del 20 % y saldo al finalizar (aprobado). Penalidad del 50 % del valor del alquiler por cancelar con menos de 24 h (confirmada y publicada en la Política de cancelaciones y daños).
- [ ] Unidad del precio de equipos (se usó **jornada**).
- [ ] Precios de los 14 equipos en borrador y de los dos lentes "???".
- [ ] Medida del sinfín: 12 × 4 m o 10 × 4 m ("4 × 10 m" en Sobre Impakto; "más de 100 m², total 350 m²" en la portada de Términos).
- [ ] Podcast de 5 a 8 h y "hora adicional $120.000" del contrato; qué incluye la edición estándar y la avanzada; tiempos de entrega.
- [ ] Drones solo con piloto de Impakto (¿incluido en el precio?); requisitos para retirar equipos; specs por equipo; precios de los kits.
- [ ] Productor en alquiler de equipos (unidad).
- [ ] Valor de reposición de la Sony A7 IV ($12 M por unidad o $24 M por dos) y tabla de reposición actualizada.

**Contenido**
- [ ] Autorización de cada cliente para publicar marcas y cifras (Inicio, Portafolio, Pauta, Web, CRM, artículos).
- [ ] Animación 3D y Desarrollo de producto (en borrador): ¿siguen activos?
- [ ] Alcance del registro de naming ante la SIC; cobertura geográfica de producción.
- [ ] Autorización para nombrar instituciones de formación en Nosotros.
- [x] Autor de los artículos: "Equipo Impakto" (aprobado).
- [ ] Proyectos del sitio anterior que se migran; resultados de Bôma; video de Mesa de Yeguas y enlaces de reels; testimonios.
- [ ] Landing `/marketing-inmobiliario/` (solo hay títulos de sección en `servicios.md`).
- [ ] Parqueadero (FAQ del artículo del estudio).

## 5. Notas técnicas para producción
- El nonce de los formularios se pide fresco (`/wp-json/impakto/v1/nonce`), así que LiteSpeed puede cachear las páginas con cotizador.
- Al activar Rank Math por primera vez, ejecutar `wp impakto configurar` (crea las tablas de redirecciones y del monitor 404 y aplica la configuración).
- En local se usaron datos de prueba en Impakto → Solicitudes (IMP-2026-0001 a 0003 y contactos "Prueba"); no viajan con el tema ni con el plugin.
