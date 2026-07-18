# Enlaza — Base del proyecto

Plataforma web que conecta voluntarios con organizaciones sin fines de lucro.
Proyecto TIC — Benemérito Colegio Agropecuario de San Carlos.

Stack: **PHP nativo (PDO) + MySQL/MariaDB + HTML/CSS/JS vanilla**, sin frameworks.

Esta es la base generada en el Paso 1 del plan de desarrollo. Reemplaza cualquier
contenido anterior del repositorio (por ejemplo, un scaffold en Python/Flask).

## Instalación local (XAMPP / Laragon)

1. Copiá esta carpeta dentro de `htdocs` (XAMPP) o `www` (Laragon), por ejemplo como `enlaza/`.
2. Creá la base de datos e importá el esquema:
   ```
   mysql -u root -p < database/schema.sql
   mysql -u root -p < database/seeds.sql
   ```
3. Revisá `config/config.php` y ajustá `DB_USER` / `DB_PASS` si tu MySQL local los requiere.
4. Configurá el servidor para que el **document root** apunte a la carpeta `public/`
   (o accedé directamente a `http://localhost/enlaza/public/`).
5. Si todo está bien conectado, vas a ver la página de inicio con un mensaje de
   "Conexión a la base de datos exitosa" y las 5 categorías cargadas desde MySQL.

## Estructura

```
/public              → index.php (entrada), /assets (css, js, img)
/app
  /controllers        → una clase por módulo
  /models              → una clase por entidad, PDO
  /views               → plantillas .php de cada pantalla
  /helpers             → funciones utilitarias compartidas
/config               → conexión a BD, constantes globales
/database             → schema.sql, seeds.sql
```

## Estado actual

- [x] Estructura de carpetas
- [x] Conexión a base de datos (PDO)
- [x] Esquema completo de base de datos (`database/schema.sql`)
- [x] Router simple por `?accion=`
- [x] Modelo `CategoriaModel` funcional (prueba de conexión de punta a punta)
- [ ] Registro y login (Paso 2)
- [ ] Perfiles de voluntario y organización (Paso 3)
- [ ] Publicar / editar / cerrar oportunidades (Paso 4)
- [ ] Búsqueda y filtrado (Paso 5)
- [ ] Inscripciones (Paso 6)
- [ ] Recomendaciones — RF10 (Paso 7)
- [ ] Pulido visual y seguridad (Paso 8)

Ver la documentación técnica completa (`Enlaza_Documentacion_Completa.docx`) para el
detalle de requisitos, reglas de negocio, modelo de datos y rutas.
