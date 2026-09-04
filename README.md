# Enlaza — Plataforma de voluntariado

Plataforma web que conecta voluntarios con organizaciones sin fines de lucro.
Proyecto TIC — Benemérito Colegio Agropecuario de San Carlos.

Stack: **PHP nativo (PDO) + MySQL/MariaDB + HTML/CSS/JS vanilla**, sin frameworks.

El contexto completo del proyecto (identidad visual, convenciones de nombres,
reglas de negocio y plan de desarrollo) está en [`CLAUDE.md`](CLAUDE.md).

## Instalación local (XAMPP / Laragon)

1. Copiá esta carpeta dentro de `htdocs` (XAMPP) o `www` (Laragon), por ejemplo como `enlaza/`.
2. Importá los **tres** archivos SQL **en este orden**:

   ```
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/migration_001.sql
   mysql -u root -p < database/seeds.sql
   ```

   - `schema.sql` — crea la base `enlaza` y las 10 tablas.
   - `migration_001.sql` — agrega lo que las pantallas necesitan y el esquema base
     todavía no tenía: `opportunities.requirements`, el estado `draft` en
     `opportunities.status`, el estado `completed` en `enrollments.status`,
     `organizations.category_id` y `organizations.founded_year`, más los índices
     de búsqueda.
   - `seeds.sql` — datos de demo. **Hace `TRUNCATE` de todas las tablas antes de
     insertar**, así que no lo corrás sobre datos que quieras conservar.

   El orden importa: `seeds.sql` inserta en columnas que crea `migration_001.sql`,
   así que saltarse la migración hace fallar la importación.

3. Revisá `config/config.php` y ajustá `DB_USER` / `DB_PASS` si tu MySQL local los
   requiere. `BASE_URL` está en `/enlaza/public/`: cambialo si usás otra carpeta.
4. Configurá el servidor para que el **document root** apunte a la carpeta `public/`
   (o accedé directamente a `http://localhost/enlaza/public/`).

## Cuentas de prueba

Todas las cuentas del seed usan la contraseña **`enlaza123`**.

### Organizaciones

| Correo | Organización |
|---|---|
| `contacto@verdenorte.org` | Fundación Verde Norte |
| `info@aprenderjuntos.org` | Asociación Aprender Juntos |
| `sancarlos@cruzroja.org` | Cruz Roja — sede local |
| `cultura@zarcero.org` | Casa de la Cultura Zarcero |

### Voluntarios

| Correo | Nombre |
|---|---|
| `paulina.rojas@correo.com` | Paulina Rojas |
| `ana.rodriguez@correo.com` | Ana Rodríguez |
| `luis.vargas@correo.com` | Luis Vargas |
| `kimberly.solano@correo.com` | Kimberly Solano |
| `josue.alfaro@correo.com` | Josué Alfaro |
| `melany.rojas@correo.com` | Melany Rojas |
| `diego.cespedes@correo.com` | Diego Céspedes |
| `fabiola.quesada@correo.com` | Fabiola Quesada |

El seed trae además 5 categorías, 12 habilidades, 13 oportunidades (10 activas,
2 cerradas, 1 borrador) y 28 inscripciones en estados mezclados.

## Estructura

```
/public              → index.php (entrada y router), /assets (css, js, img)
/app
  /controllers       → una clase por módulo
  /models            → una clase por entidad, PDO
  /views             → plantillas .php de cada pantalla + /partials
  /helpers           → functions.php (helpers de vista), view.php
/config              → conexión a BD, constantes globales
/database            → schema.sql, migration_001.sql, seeds.sql
/design-reference    → prototipos *.dc.html (referencia visual, no se ejecutan)
```

## Rutas

El router es un mapa `$routes` de `?action=` a `[Controlador, método]` en
`public/index.php`. Rutas disponibles:

| Acción | Controlador | Estado |
|---|---|---|
| `home` | `OpportunityController::home` | Vista real |
| `register` | `UserController::showRegister` | Vista real |
| `register_user` | `UserController::register` | Implementada |
| `login` / `logout` | `UserController::login` / `logout` | Implementada |
| `view_volunteer_profile` | `VolunteerController::viewProfile` | Vista real, datos de ejemplo |
| `save_volunteer_profile` | `VolunteerController::saveProfile` | Pendiente (Paso 3) |
| `recommendations` | `VolunteerController::recommendations` | Pendiente (Paso 7) |
| `view_organization_profile` | `OrganizationController::viewProfile` | Vista real, datos de ejemplo |
| `save_organization_profile` | `OrganizationController::saveProfile` | Pendiente (Paso 3) |
| `search_opportunities` | `OpportunityController::search` | Vista real, datos de ejemplo |
| `view_opportunity` | `OpportunityController::view` | Vista real, datos de ejemplo |
| `publish_opportunity` | `OpportunityController::publish` | Vista real, formulario sin conectar |
| `edit_opportunity` / `close_opportunity` | `OpportunityController` | Pendiente (Paso 4) |
| `manage_enrollments` | `EnrollmentController::manage` | Vista real, datos de ejemplo |
| `enroll` | `EnrollmentController::enroll` | Pendiente (Paso 6) |
| `accept_enrollment` / `reject_enrollment` | `EnrollmentController` | Pendiente (Paso 6) |

Una acción desconocida devuelve 404. Las acciones "pendientes" muestran la
pantalla de `renderPending()` indicando en qué paso del plan se implementan.

## Estado actual

- [x] **Paso 1** — Estructura de carpetas, conexión PDO, esquema SQL, router
- [x] **Paso 2** — Registro y login (ambos roles), sesiones, validación de formularios
- [x] Las 9 pantallas del prototipo convertidas a vistas PHP reales (`app/views/`)
- [x] Esquema completo: `schema.sql` + `migration_001.sql` + datos de demo en `seeds.sql`
- [x] Helpers compartidos de vista (`app/helpers/functions.php`)
- [ ] **Paso 3** — Perfiles de voluntario y organización (crear/editar)
- [ ] **Paso 4** — Publicar / editar / cerrar oportunidades
- [ ] **Paso 5** — Búsqueda y filtrado de oportunidades
- [ ] **Paso 6** — Inscripciones + gestión (aceptar/rechazar)
- [ ] **Paso 7** — Recomendaciones (RF10)
- [ ] **Paso 8** — Pulido visual, validaciones de seguridad y pruebas

> Las vistas ya existen y se ven completas, pero la mayoría todavía renderiza
> arreglos de ejemplo declarados en el propio archivo. Cada una tiene un `TODO`
> que indica el paso del plan y el modelo que la va a alimentar. Lo único que hoy
> lee de la base de datos son las categorías, el login y el chip de usuario del
> encabezado.

Ver la documentación técnica completa (`Enlaza_Documentacion_Completa.docx`) para el
detalle de requisitos, reglas de negocio, modelo de datos y rutas.
