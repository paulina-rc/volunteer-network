# Enlaza — Project Context

> Context file for Claude Code (repo root) and for the Claude Project. Any Claude session that opens this file should be ready to work on Enlaza without further explanation.

---

## 1. What is Enlaza

A web platform that connects **volunteers** with **non-profit organizations** that need support. TIC (ICT) project of the Benemérito Colegio Agropecuario de San Carlos, a team of 4 students.

Slogan: *"La tecnología al servicio de la comunidad."*
Brand phrase: *"Conecta. Participa. Transforma."*
Closing line: *"Juntos generamos cambios que importan."*

> These three lines are Enlaza's brand copy and are kept in Spanish on purpose — they are user-facing marketing text, not code.

System roles: **Volunteer** and **Organization** (one account = one role only).

---

## 2. Technical stack (fixed)

- **Backend:** native PHP (no framework), data access with **PDO**
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5 + CSS3 + vanilla JavaScript (no Bootstrap or generic libraries)
- **Authentication:** PHP sessions (`$_SESSION`) + `password_hash()` / `password_verify()`
- **Local environment:** XAMPP / Laragon
- **Version control:** Git + GitHub

Do not introduce additional frameworks or libraries without discussing it first.

---

## 3. Folder structure

```
/public              → index.php (entry point), /assets (css, js, img)
/app
  /controllers       → one class per module
  /models            → one class per entity, PDO
  /views             → .php templates for each screen
  /helpers           → shared utility functions
/config              → database connection, global constants
/database            → schema.sql, seeds.sql, migrations if applicable
```

This structure is already in place in the repo (see section 7).

---

## 4. Visual identity (official)

**Palette:**

| Color | Hex | Use |
|---|---|---|
| Petrol green | `#0F4C5C` | Primary — trust, community, stability |
| Warm coral | `#F26B4A` | Action accent — primary buttons, active links |
| Soft mustard | `#E9A227` | Impact — badges, highlights, positive alerts |
| Sage green | `#A8C9A1` | Nature — soft backgrounds, illustrations |
| Cream white | `#F8F7F4` | Main background |
| Dark gray | `#343434` | Main text |

**Typography:**
- **Comfortaa** — main headings and logo (Light, Regular, Medium, SemiBold, Bold)
- **Poppins** — body copy, UI, web text (Light, Regular, Medium, SemiBold, Bold)

Both are loaded from Google Fonts.

**Iconography:** symbols of community, solidarity, nature, impact, technology, inclusion. Font Awesome is used as the icon library.

---

## 5. Naming conventions (important — keep consistent)

> **Naming convention change:** as of the English naming migration, all code and technical documentation — database, PHP classes/methods/variables, file names, code comments, this file, route names, CSS classes — moved from Spanish to English. The **one exception is the visible content inside `.php` views** (labels, buttons, titles, error/success messages): that text stays in **Spanish**, because it's the language the end user (volunteer or organization) actually sees. Commit messages were already in English before this change and are unaffected.

### Database
- **Tables:** `snake_case` plural in English → `users`, `volunteers`, `organizations`, `opportunities`, `enrollments`, `skills`, `categories`
- **Columns:** `snake_case` in English → `id`, `email`, `password_hash`, `created_at`, `is_active`
- **Primary key:** `id` (auto-increment)
- **Foreign keys:** `<singular_table>_id` → `user_id`, `opportunity_id`
- **Booleans:** `is_` prefix → `is_active`, `is_verified`, `profile_complete` (exception already in use, see `organizations`)
- **Dates:** `<something>_at` / `<something>_date` → `created_at`, `activity_date`, `enrollment_date`

### PHP
- **Classes:** `PascalCase` in English → `UserController`, `OpportunityModel`
- **Files with a class:** same name as the class → `UserController.php`
- **Methods:** `camelCase` in English → `createUser()`, `findByEmail()`
- **Variables:** `camelCase` in English → `$currentUser`, `$opportunityList`
- **Constants:** `UPPER_SNAKE_CASE` → `MAX_SLOTS_DEFAULT`, `DB_HOST`
- **Views:** `snake_case` in English for the **file name** → `volunteer_profile.php`, `opportunity_list.php`. The **visible HTML content** inside each view (labels, buttons, titles, error/success messages) stays in **Spanish**.

### Frontend
- **CSS classes:** `kebab-case` in English with a block prefix (light BEM) → `.btn`, `.btn--primary`, `.card`, `.card__title`
- **IDs:** `kebab-case` in English → `#register-form`
- **CSS/JS files:** `kebab-case` in English → `general-styles.css`, `form-validation.js`

### Git
- **Main branch:** `master`
- **Working branches:** `feature/short-name`, `fix/short-name`
- **Commits (in English):** imperative verb + what → `add volunteer registration`, `fix quota validation`, `update data model`

### Internal URLs
- `snake_case` in English for parameters → `?action=publish_opportunity`
- Action/page names in `snake_case` English

---

## 6. Key business rules

See **Enlaza_Documentacion_Completa.docx** (section 4) for the full detail. Summary:

- **RN01** — An account is Volunteer **or** Organization, never both.
- **RN02** — An organization must complete its profile before publishing opportunities.
- **RN03** — Every opportunity requires a title, description, skills, location, date, and slots.
- **RN04** — A volunteer can only enroll if slots are available and the date hasn't passed.
- **RN05** — Once all slots are filled, the opportunity automatically moves to "Closed".
- **RN06** — Every enrollment starts as "Pending" until the organization accepts or rejects it.
- **RN07** — A volunteer's personal data is only visible to organizations they enrolled with.
- **RN08** — The recommendation system prioritizes: skills > interests > location.
- **RN09** — An organization can edit or manually close an opportunity before its deadline.

---

## 7. Project status

| Area | Status |
|---|---|
| Complete technical documentation (requirements, rules, user stories, use cases, architecture, data model) | ✅ Done — see `Enlaza_Documentacion_Completa.docx` |
| Visual design and navigable prototype | ✅ Done — `*.dc.html` files (Claude Design) |
| Development (Claude Code) | ⏳ In progress — see Development plan, section 8 |

We no longer work in formal documentation/design "Sprints" — those are closed. Code development does move forward in steps (section 8), but continuously, without rigid waiting phases.

---

## 8. Development plan (Claude Code)

| Step | Content | Status |
|---|---|---|
| **1** | Project base: folder structure, DB connection, SQL schema (users, volunteers, organizations, categories, skills, opportunities, enrollments + bridge tables), router | ✅ Done |
| **2** | Registration and login (both roles), sessions, form validation | ✅ Done |
| **3** | Volunteer profile and organization profile (create/edit) | Pending |
| **4** | Publish / edit / close opportunities (organization) | Pending |
| **5** | Search and filtering of opportunities (volunteer) | Pending |
| **6** | Enrollment in opportunities + management (accept/reject) | Pending |
| **7** | Basic recommendation system (skills / interests / location) | Pending |
| **8** | Final visual polish, security validations, testing | Pending |

---

## 9. How to work each session

1. Explicitly state which Step (1–8) is being worked on.
2. Read this whole file before touching code: respect the stack, folder structure, and naming conventions defined above.
3. Don't mix several big steps in the same session — close one before opening the next.
4. When a step is finished, update the table in section 8, marking it as ✅ Done.
5. Code comments, variable names, view file names, and all technical documentation are in **English**. Visible view content (labels, buttons, titles, error/success messages) stays in **Spanish**. Commit messages are in **English** (see section 5, Git).

---

## 10. Reference documents and prototypes

- `Enlaza_Documentacion_Completa.docx` — full requirements, business rules, user stories, use cases, architecture, and data model.
- `*.dc.html` files (Home, Busqueda, DetalleOportunidad, Registro, PerfilVoluntario, PerfilOrganizacion, PublicarOportunidad, GestionInscripciones) — navigable visual reference prototype used to lay out each real PHP screen.
