# Enlaza — Contexto del Proyecto

> Archivo de contexto para Claude Code (raíz del repo) y para el Claude Project. Todo Claude que abra este archivo debería quedar listo para trabajar en Enlaza sin más explicaciones.

---

## 1. Qué es Enlaza

Plataforma web que conecta **voluntarios** con **organizaciones sin fines de lucro** que necesitan apoyo. Proyecto TIC del Benemérito Colegio Agropecuario de San Carlos, equipo de 4 estudiantes.

Slogan: *"La tecnología al servicio de la comunidad."*
Frase de marca: *"Conecta. Participa. Transforma."*
Cierre: *"Juntos generamos cambios que importan."*

Roles del sistema: **Voluntario** y **Organización** (una cuenta = un solo rol).

---

## 2. Stack técnico (fijo)

- **Backend:** PHP nativo (sin framework), acceso a datos con **PDO**
- **Base de datos:** MySQL / MariaDB
- **Frontend:** HTML5 + CSS3 + JavaScript vanilla (sin Bootstrap ni librerías genéricas)
- **Autenticación:** sesiones PHP (`$_SESSION`) + `password_hash()` / `password_verify()`
- **Entorno local:** XAMPP / Laragon
- **Control de versiones:** Git + GitHub

No introducir frameworks ni librerías adicionales sin discutirlo primero.

---

## 3. Estructura de carpetas

```
/public              → index.php (entrada), /assets (css, js, img)
/app
  /controllers       → una clase por módulo
  /models            → una clase por entidad, PDO
  /views             → plantillas .php de cada pantalla
  /helpers           → funciones utilitarias compartidas
/config              → conexión a BD, constantes globales
/database            → schema.sql, seeds.sql, migraciones si aplica
```

Esta estructura ya está creada en el repo (ver sección 7).

---

## 4. Identidad visual (oficial)

**Paleta:**

| Color | Hex | Uso |
|---|---|---|
| Verde petróleo | `#0F4C5C` | Primario — confianza, comunidad, estabilidad |
| Coral cálido | `#F26B4A` | Acento de acción — botones principales, links activos |
| Mostaza suave | `#E9A227` | Impacto — badges, destacados, alertas positivas |
| Verde salvia | `#A8C9A1` | Naturaleza — fondos suaves, ilustraciones |
| Blanco crema | `#F8F7F4` | Fondo principal |
| Gris oscuro | `#343434` | Texto principal |

**Tipografías:**
- **Comfortaa** — títulos principales y logo (Light, Regular, Medium, SemiBold, Bold)
- **Poppins** — cuerpo, UI, textos web (Light, Regular, Medium, SemiBold, Bold)

Ambas se cargan desde Google Fonts.

**Iconografía:** símbolos de comunidad, solidaridad, naturaleza, impacto, tecnología, inclusión. Se usa Font Awesome como librería de íconos.

---

## 5. Nomenclatura (importante — mantener consistente)

### Base de datos
- **Tablas:** `snake_case` plural en español → `usuarios`, `voluntarios`, `organizaciones`, `oportunidades`, `inscripciones`, `habilidades`, `categorias`
- **Columnas:** `snake_case` → `id`, `correo`, `contrasena_hash`, `fecha_creacion`, `esta_activo`
- **Clave primaria:** `id` (auto-increment)
- **Claves foráneas:** `id_<tabla_singular>` → `id_usuario`, `id_oportunidad`
- **Booleanos:** prefijo `es_` o `esta_` → `es_activo`, `esta_verificado`, `perfil_completo` (excepción ya en uso, ver `organizaciones`)
- **Fechas:** `fecha_<algo>` → `fecha_creacion`, `fecha_limite`

### PHP
- **Clases:** `PascalCase` en español → `UsuarioController`, `OportunidadModel`
- **Archivos con clase:** mismo nombre que la clase → `UsuarioController.php`
- **Métodos:** `camelCase` → `crearUsuario()`, `buscarPorCorreo()`
- **Variables:** `camelCase` → `$usuarioActual`, `$listaOportunidades`
- **Constantes:** `UPPER_SNAKE_CASE` → `MAX_CUPOS_DEFAULT`, `DB_HOST`
- **Vistas:** `snake_case` en español → `perfil_voluntario.php`, `lista_oportunidades.php`

### Frontend
- **Clases CSS:** `kebab-case` con prefijo por bloque (BEM ligero) → `.btn`, `.btn--primario`, `.card`, `.card__title`
- **IDs:** `kebab-case` → `#form-registro`
- **Archivos CSS/JS:** `kebab-case` → `estilos-generales.css`, `validacion-formularios.js`

### Git
- **Rama principal:** `master`
- **Ramas de trabajo:** `feature/nombre-corto`, `fix/nombre-corto`
- **Commits (en inglés):** imperative verb + qué → `add volunteer registration`, `fix quota validation`, `update data model`

> Cambio de convención: los commits pasaron de español a **inglés** a partir del commit base del proyecto. El resto de la documentación, comentarios de código, nombres de variables, vistas, etc. se mantienen en **español**.

### URLs internas
- `snake_case` en parámetros → `?accion=publicar_oportunidad`
- Nombres de páginas en `snake_case`

---

## 6. Reglas de negocio clave

Ver **Enlaza_Documentacion_Completa.docx** (sección 4) para el detalle completo. Resumen:

- **RN01** — Una cuenta es Voluntario **o** Organización, nunca ambos.
- **RN02** — Una organización debe completar su perfil antes de publicar oportunidades.
- **RN03** — Toda oportunidad requiere título, descripción, habilidades, ubicación, fecha y cupos.
- **RN04** — Un voluntario solo se inscribe si hay cupos disponibles y la fecha no ha vencido.
- **RN05** — Al llenarse los cupos, la oportunidad pasa automáticamente a "Cerrada".
- **RN06** — Toda inscripción inicia en "Pendiente" hasta que la organización acepta o rechaza.
- **RN07** — Los datos personales del voluntario solo son visibles para las organizaciones a las que se inscribió.
- **RN08** — El sistema de recomendación prioriza: habilidades > intereses > ubicación.
- **RN09** — Una organización puede editar o cerrar manualmente una oportunidad antes de su fecha límite.

---

## 7. Estado del proyecto

| Área | Estado |
|---|---|
| Documentación técnica completa (requisitos, reglas, historias, casos de uso, arquitectura, modelo de datos) | ✅ Terminada — ver `Enlaza_Documentacion_Completa.docx` |
| Diseño visual y prototipo navegable | ✅ Terminado — archivos `*.dc.html` (Claude Design) |
| Desarrollo (Claude Code) | ⏳ En progreso — ver Plan de desarrollo, sección 8 |

No se trabaja más por "Sprints" formales de documentación/diseño — ya están cerrados. El desarrollo del código sí avanza por pasos (sección 8), pero de forma continua, sin fases rígidas de espera.

---

## 8. Plan de desarrollo (Claude Code)

| Paso | Contenido | Estado |
|---|---|---|
| **1** | Base del proyecto: estructura de carpetas, conexión a BD, esquema SQL (usuarios, voluntarios, organizaciones, categorias, habilidades, oportunidades, inscripciones + tablas puente), router | ✅ Terminado |
| **2** | Registro y login (ambos roles), sesiones, validación de formularios | ✅ Terminado |
| **3** | Perfil de voluntario y perfil de organización (crear/editar) | Pendiente |
| **4** | Publicar / editar / cerrar oportunidades (organización) | Pendiente |
| **5** | Búsqueda y filtrado de oportunidades (voluntario) | Pendiente |
| **6** | Inscripción a oportunidades + gestión (aceptar/rechazar) | Pendiente |
| **7** | Sistema de recomendación básico (habilidades / intereses / ubicación) | Pendiente |
| **8** | Pulido visual final, validaciones de seguridad, pruebas | Pendiente |

---

## 9. Cómo trabajar cada sesión

1. Decir explícitamente qué Paso (1–8) se va a trabajar.
2. Leer este archivo completo antes de tocar código: respeta el stack, la estructura de carpetas y la nomenclatura definidos arriba.
3. No mezclar varios pasos grandes en la misma sesión — cerrar uno antes de abrir el siguiente.
4. Al terminar un paso, actualizar la tabla de la sección 8 marcándolo como ✅ Terminado.
5. Comentarios de código, nombres de variables, vistas y toda la documentación van en **español**. Los **mensajes de commit van en inglés** (ver sección 5, Git).

---

## 10. Documentos y prototipos de referencia

- `Enlaza_Documentacion_Completa.docx` — requisitos, reglas de negocio, historias de usuario, casos de uso, arquitectura y modelo de datos completos.
- Archivos `*.dc.html` (Home, Busqueda, DetalleOportunidad, Registro, PerfilVoluntario, PerfilOrganizacion, PublicarOportunidad, GestionInscripciones) — prototipo navegable de referencia visual para maquetar cada pantalla PHP real.
