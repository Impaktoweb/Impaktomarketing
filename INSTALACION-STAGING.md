# Instalación en el staging de Hostinger

Paquete: `impakto-web/entrega/` → `impakto.zip`, `impakto-core.zip`, `impakto-contenido.xml` (WXR) y `tarifas-impakto.json`.

> El sitio actual no se toca: todo se hace en el **staging** y se publica cuando esté aprobado.

## 1. Crear el staging
hPanel → **WordPress → Staging → Crear staging** (p. ej. `staging.impaktomarketing.com`). Mientras sea staging, deja **Ajustes → Lectura → "Disuadir a los motores de búsqueda"** marcado (el `robots.txt` del tema respeta ese bloqueo).

## 2. Requisitos
- WordPress 6.6 o superior (probado en 7.1) y **PHP 8.1 o superior** (probado en 8.4).
- Ajustes → Generales → Idioma: **Español de Colombia**.

## 3. Instalar plugins y tema
1. **Plugins → Añadir nuevo:** instala y activa **Rank Math SEO**, **FluentSMTP** y **LiteSpeed Cache**.
   - En el asistente de Rank Math puedes elegir **"Omitir"** la conexión de cuenta.
2. **Plugins → Subir:** `impakto-core.zip` → activar.
3. **Apariencia → Temas → Subir:** `impakto.zip` → activar.
4. Desactiva y borra los temas y plugins que no se usen (Borgholm, Slider Revolution, etc. **solo en el staging**).

## 4. Contenido — opción A (recomendada, con SSH)
hPanel → Avanzado → **Acceso SSH**. En la carpeta del sitio del staging:
```bash
wp impakto instalar
wp impakto probar-calculo
```
Crea páginas, servicios, proyectos, artículos, equipos, menús y legales con las URL del dominio correcto, importa las tarifas y configura Rank Math (títulos, sitemap, breadcrumbs, redirecciones, monitor 404, imagen para compartir y favicon).

## 4. Contenido — opción B (sin SSH)
1. **Herramientas → Importar → WordPress** → `impakto-contenido.xml` → asignar autor y **no** descargar adjuntos.
2. **Impakto → Tarifas → Importar archivo** → `tarifas-impakto.json`.
3. Cambiar las URL del entorno local por las del staging (los enlaces del menú y de las páginas son absolutos). Desde SSH: `wp search-replace 'http://localhost:8881' 'https://staging.impaktomarketing.com' --skip-columns=guid`. Sin SSH, usa la herramienta de búsqueda y reemplazo de Hostinger.
4. **Ajustes → Lectura:** página de inicio = *Inicio*; página de entradas = *Blog*.
5. **Ajustes → Enlaces permanentes:** estructura personalizada `/blog/%postname%/`, base de categorías `blog/categoria` → Guardar.
6. **Rank Math → Panel → Módulos:** activa Schema, Sitemap, Redirecciones y Monitor 404. **Rank Math → Ajustes generales → Breadcrumbs:** activar, separador `/`, inicio "Inicio". Títulos: separador `|`, tipo de entidad "Empresa", nombre "Impakto Marketing". (La opción A hace todo esto sola.)

## 5. Ajustes de Impakto
**Impakto → Ajustes:** correos de **CEO** y **KAM**, correo público, horario, latitud/longitud, URL de Google Business Profile, **ID de GA4** e **ID del Pixel**. Revisa la dirección y el WhatsApp.

## 6. Correo (FluentSMTP)
Conecta el buzón del dominio (p. ej. `notificaciones@impaktomarketing.com`) con el SMTP del proveedor. Envía una prueba a CEO y KAM y una cotización de prueba a un Gmail y a un Outlook: no deben llegar a spam. Verifica SPF, DKIM y DMARC en el DNS.

## 7. LiteSpeed Cache
- Activa caché de páginas, **CSS/JS combinados y minificados**, carga diferida de imágenes y WebP.
- **Excluir de la caché:** `/wp-json/impakto/v1/*` (el endpoint `nonce` ya envía `no-cache`), `/gracias/`.
- No combines los módulos JS (`type="module"`): si algo falla, desactiva "JS Combine" y deja solo "JS Minify".
- Purga la caché después de cada cambio de tarifas o de contenido importante.

## 8. Pruebas en el staging (antes de publicar)
- [ ] PageSpeed Insights (móvil) de Inicio, un servicio, Estudio, un proyecto y un artículo: Rendimiento ≥ 90; Accesibilidad, Buenas prácticas y SEO ≥ 95.
- [ ] Cotizador: medio estudio 5 h = $470.000; completo 8 h = $1.200.000; podcast 2c+2m × 3 h = $600.000. Envío por WhatsApp y por correo; la solicitud aparece en Impakto → Solicitudes; llega el correo a CEO y KAM.
- [ ] Formulario de Contacto → /gracias/, correo interno y registro.
- [ ] Banner de cookies: sin GA4 ni Pixel antes de aceptar (pestaña Red del navegador). GA4 → DebugView y Meta → Probar eventos: `click_whatsapp`, `cotizacion_whatsapp`, `cotizacion_email`, `generate_lead`.
- [ ] Redirecciones del BRIEF §14 y URLs indexadas exportadas de Search Console → ninguna en 404.
- [ ] `https://…/robots.txt`, `/llms.txt` y `/sitemap_index.xml`.

## 9. Publicar
hPanel → copia de seguridad del sitio actual → **Staging → ⋮ → Publicar**. Luego: desmarca "Disuadir a los motores de búsqueda", guarda Enlaces permanentes, purga LiteSpeed, envía el sitemap en Search Console y en Bing Webmaster Tools.
