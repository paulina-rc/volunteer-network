-- ============================================================
-- Enlaza — Seed data (demo dataset)
--
-- Run order:  schema.sql  ->  migration_001.sql  ->  seeds.sql
--
-- Every account uses the password:  enlaza123
-- Stored with PHP's password_hash(PASSWORD_DEFAULT) — RNF02.
-- ============================================================

USE enlaza;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE opportunity_skill;
TRUNCATE TABLE volunteer_skill;
TRUNCATE TABLE volunteer_interest;
TRUNCATE TABLE enrollments;
TRUNCATE TABLE opportunities;
TRUNCATE TABLE organizations;
TRUNCATE TABLE volunteers;
TRUNCATE TABLE users;
TRUNCATE TABLE skills;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- categories
-- ------------------------------------------------------------
INSERT INTO categories (id, name, color_hex, icon) VALUES
  (1, 'Ambiental',   '#A8C9A1', 'fa-leaf'),
  (2, 'Educativo',   '#E9A227', 'fa-book-open'),
  (3, 'Salud',       '#5C8A78', 'fa-heart-pulse'),
  (4, 'Comunitario', '#0F4C5C', 'fa-people-group'),
  (5, 'Cultural',    '#F26B4A', 'fa-masks-theater');

-- ------------------------------------------------------------
-- skills
-- ------------------------------------------------------------
INSERT INTO skills (id, name) VALUES
  (1,  'Trabajo con niños'),
  (2,  'Primeros auxilios'),
  (3,  'Idiomas'),
  (4,  'Manejo de redes sociales'),
  (5,  'Jardinería / agricultura'),
  (6,  'Cocina'),
  (7,  'Enseñanza y tutorías'),
  (8,  'Logística y organización'),
  (9,  'Atención a adultos mayores'),
  (10, 'Fotografía'),
  (11, 'Música'),
  (12, 'Trabajo en equipo');

-- ------------------------------------------------------------
-- users  (1-4 organizations · 5-12 volunteers)
-- Password for all of them: enlaza123
-- ------------------------------------------------------------
INSERT INTO users (id, email, password_hash, role, created_at) VALUES
  (1,  'contacto@verdenorte.org',    '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'organization', '2026-01-15 09:00:00'),
  (2,  'info@aprenderjuntos.org',    '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'organization', '2026-02-03 10:30:00'),
  (3,  'sancarlos@cruzroja.org',     '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'organization', '2026-02-20 08:15:00'),
  (4,  'cultura@zarcero.org',        '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'organization', '2026-03-11 14:00:00'),
  (5,  'paulina.rojas@correo.com',   '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-03-05 16:20:00'),
  (6,  'ana.rodriguez@correo.com',   '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-03-18 11:45:00'),
  (7,  'luis.vargas@correo.com',     '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-04-02 09:10:00'),
  (8,  'kimberly.solano@correo.com', '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-04-14 19:00:00'),
  (9,  'josue.alfaro@correo.com',    '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-05-06 13:25:00'),
  (10, 'melany.rojas@correo.com',    '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-05-22 08:40:00'),
  (11, 'diego.cespedes@correo.com',  '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-06-09 17:30:00'),
  (12, 'fabiola.quesada@correo.com', '$2y$10$fMbq0GjD266PQ5HG5L41ruyaniFSbmGZNJL5xwhEGY0xpoXHWyC5G', 'volunteer',    '2026-07-01 10:05:00');

-- ------------------------------------------------------------
-- organizations — profile_complete = 1 so they can publish (RN02)
-- ------------------------------------------------------------
INSERT INTO organizations (id, user_id, category_id, name, description, location, contact, founded_year, profile_complete, created_at) VALUES
  (1, 1, 1, 'Fundación Verde Norte',
   'Organización dedicada a la reforestación y educación ambiental en la Zona Norte. Trabajamos con comunidades ribereñas para restaurar bosques de galería y proteger las fuentes de agua.',
   'San Carlos, Alajuela', '2460-1122', 2019, 1, '2026-01-15 09:00:00'),

  (2, 2, 2, 'Asociación Aprender Juntos',
   'Acompañamos a niños y niñas de escuelas rurales con tutorías gratuitas de matemática y lectura, apoyadas por personas voluntarias de la comunidad.',
   'Ciudad Quesada, Alajuela', '2461-3344', 2015, 1, '2026-02-03 10:30:00'),

  (3, 3, 3, 'Cruz Roja — sede local',
   'Sede local de la Cruz Roja. Organizamos ferias de salud, capacitaciones en primeros auxilios y jornadas de atención comunitaria.',
   'Florencia, Alajuela', '2462-5566', 1885, 1, '2026-02-20 08:15:00'),

  (4, 4, 5, 'Casa de la Cultura Zarcero',
   'Espacio cultural comunitario que rescata las tradiciones boyeras, la música y los oficios artesanales de la zona de Zarcero.',
   'Zarcero, Alajuela', '2463-7788', 2008, 1, '2026-03-11 14:00:00');

-- ------------------------------------------------------------
-- volunteers
-- ------------------------------------------------------------
INSERT INTO volunteers (id, user_id, full_name, location, availability, about_me, created_at) VALUES
  (1, 5,  'Paulina Rojas',    'Ciudad Quesada, Alajuela', 'Fines de semana',          'Estudiante de informática. Me gusta el trabajo ambiental y enseñar.',  '2025-03-10 10:00:00'),
  (2, 6,  'Ana Rodríguez',    'San Carlos, Alajuela',     'Fines de semana',          'Enfermera auxiliar, disponible para jornadas de salud comunitaria.',   '2025-06-02 10:00:00'),
  (3, 7,  'Luis Vargas',      'San Carlos, Alajuela',     'Entre semana en la tarde', 'Agricultor. Aporto experiencia en siembra y manejo de vivero.',        '2025-08-19 10:00:00'),
  (4, 8,  'Kimberly Solano',  'Florencia, Alajuela',      'Horario flexible',         'Estudiante de educación. Me encanta trabajar con niños.',              '2025-11-04 10:00:00'),
  (5, 9,  'Josué Alfaro',     'Zarcero, Alajuela',        'Fines de semana',          'Músico y fotógrafo aficionado, interesado en proyectos culturales.',   '2026-01-21 10:00:00'),
  (6, 10, 'Melany Rojas',     'Ciudad Quesada, Alajuela', 'Sábados',                  'Trabajo en logística y me ofrezco para organizar actividades.',        '2026-02-13 10:00:00'),
  (7, 11, 'Diego Céspedes',   'Aguas Zarcas, Alajuela',   'Fines de semana',          'Estudiante de nutrición, con interés en ferias de salud.',             '2026-04-08 10:00:00'),
  (8, 12, 'Fabiola Quesada',  'Zarcero, Alajuela',        'Entre semana',             'Adulta mayor activa, acompaño a otras personas de mi comunidad.',      '2026-05-30 10:00:00');

-- ------------------------------------------------------------
-- volunteer_skill / volunteer_interest — feed the recommendation (RN08)
-- ------------------------------------------------------------
INSERT INTO volunteer_skill (volunteer_id, skill_id) VALUES
  (1,7),(1,5),(1,4),(1,12),
  (2,2),(2,9),(2,12),
  (3,5),(3,8),(3,12),
  (4,1),(4,7),(4,12),
  (5,10),(5,11),(5,3),
  (6,8),(6,6),(6,12),
  (7,2),(7,6),
  (8,9),(8,12);

INSERT INTO volunteer_interest (volunteer_id, category_id) VALUES
  (1,1),(1,2),
  (2,3),(2,4),
  (3,1),(3,4),
  (4,2),(4,4),
  (5,5),(5,2),
  (6,4),(6,1),
  (7,3),(7,1),
  (8,4),(8,5);

-- ------------------------------------------------------------
-- opportunities
-- available_slots is kept consistent with the accepted enrollments
-- inserted further down (total_slots - accepted).
-- ------------------------------------------------------------
INSERT INTO opportunities (id, organization_id, category_id, title, description, requirements, location, activity_date, `time`, total_slots, available_slots, status, created_at) VALUES
  (1, 1, 1, 'Jornada de reforestación río San Carlos',
   'Sembramos especies nativas en la ribera del río San Carlos junto a la comunidad de Aguas Zarcas, para restaurar el bosque de galería y proteger la fuente de agua. La actividad incluye una breve charla sobre las especies a sembrar y el cierre con un refrigerio para los participantes.',
   'Ropa de manga larga y botas cerradas\nDisponibilidad de 5 horas\nNo se requiere experiencia previa',
   'San Carlos, Alajuela', '2026-09-24', '7:00 a. m. – 12:00 m.', 4, 2, 'active', '2026-08-01 09:00:00'),

  (2, 2, 2, 'Tutorías de matemáticas para primaria',
   'Acompañamiento semanal a estudiantes de cuarto a sexto grado que necesitan reforzar matemática. Trabajamos en grupos pequeños dentro de la biblioteca municipal, con material ya preparado por la asociación.',
   'Paciencia y gusto por enseñar\nCompromiso de asistir cuatro sesiones',
   'Ciudad Quesada, Alajuela', '2026-09-02', '2:00 p. m. – 4:00 p. m.', 8, 7, 'active', '2026-08-03 11:20:00'),

  (3, 3, 3, 'Feria de salud comunitaria',
   'Jornada de tamizaje gratuito de presión y glicemia, charlas de nutrición y demostraciones de primeros auxilios en el parque de Florencia. Las personas voluntarias apoyan en registro, orientación y logística de las estaciones.',
   'Mayor de 18 años\nDisponibilidad de jornada completa',
   'Florencia, Alajuela', '2026-09-14', '8:00 a. m. – 3:00 p. m.', 15, 13, 'active', '2026-08-05 15:45:00'),

  (4, 1, 1, 'Vivero comunitario — mantenimiento',
   'Mantenimiento quincenal del vivero de la fundación: llenado de bolsas, trasplante de almácigos, riego y control de malezas. Es una actividad tranquila, ideal para quien quiere aprender sobre propagación de especies nativas.',
   'No se requiere experiencia previa\nTraer sombrero y agua',
   'Aguas Zarcas, Alajuela', '2026-09-19', '8:00 a. m. – 11:00 a. m.', 10, 9, 'active', '2026-08-06 08:30:00'),

  (5, 4, 5, 'Rescate de tradiciones orales boyeras',
   'Visitamos a personas adultas mayores de Zarcero para grabar sus relatos sobre la tradición boyera y la vida en el campo. El material se edita después para el archivo cultural de la casa comunal.',
   'Disponibilidad de 4 horas\nTrato respetuoso con personas adultas mayores',
   'Zarcero, Alajuela', '2026-10-11', '9:00 a. m. – 1:00 p. m.', 12, 10, 'active', '2026-08-08 10:00:00'),

  (6, 3, 4, 'Acompañamiento a personas adultas mayores',
   'Visitas semanales al hogar de larga estancia de Quesada Centro para conversar, leer en voz alta y acompañar en actividades recreativas. Buscamos personas constantes, porque el vínculo es lo más valioso de esta actividad.',
   'Compromiso de asistencia mensual',
   'Quesada Centro, Alajuela', '2026-09-18', '3:00 p. m. – 5:00 p. m.', 6, 5, 'active', '2026-08-10 17:10:00'),

  (7, 2, 2, 'Club de lectura en escuela rural',
   'Lectura compartida con estudiantes de segundo ciclo en la escuela de Pital. Cada sesión combina lectura en voz alta, conversación sobre el texto y una actividad creativa corta.',
   'Gusto por la lectura',
   'Pital, Alajuela', '2026-10-03', '1:00 p. m. – 3:00 p. m.', 6, 6, 'active', '2026-08-12 09:25:00'),

  (8, 1, 1, 'Limpieza de quebrada y clasificación de residuos',
   'Recolección de residuos sólidos en la quebrada La Vieja y clasificación en el centro de acopio de la comunidad. Se entregan guantes y bolsas; el cierre incluye una charla corta sobre separación de residuos.',
   'Botas de hule\nGuantes propios si tiene',
   'San Carlos, Alajuela', '2026-10-05', '7:30 a. m. – 12:00 m.', 18, 17, 'active', '2026-08-14 14:50:00'),

  (9, 4, 5, 'Taller de música tradicional para jóvenes',
   'Apoyo en el taller sabatino de música tradicional: preparación de instrumentos, acompañamiento a los grupos de principiantes y montaje del espacio.',
   'Conocimiento musical básico',
   'Zarcero, Alajuela', '2026-10-17', '9:00 a. m. – 12:00 m.', 5, 5, 'active', '2026-08-15 11:00:00'),

  (10, 3, 3, 'Capacitación en primeros auxilios para vecinos',
   'Jornada de capacitación abierta a la comunidad. Las personas voluntarias apoyan en el registro de asistentes, el armado de estaciones de práctica y la entrega de materiales.',
   'Mayor de 18 años',
   'Florencia, Alajuela', '2026-10-24', '8:00 a. m. – 12:00 m.', 10, 10, 'active', '2026-08-16 16:30:00'),

  -- Closed because its slots filled up — demonstrates RN05 already applied
  (11, 1, 2, 'Taller de reciclaje en escuela rural',
   'Taller práctico con estudiantes de la escuela de La Palmera sobre separación de residuos y reutilización creativa de materiales.',
   'Trabajo con niños',
   'La Palmera, Alajuela', '2026-08-22', '9:00 a. m. – 12:00 m.', 4, 0, 'closed', '2026-07-20 10:00:00'),

  -- Past activity, used for the "completed" enrollments
  (12, 2, 4, 'Feria vocacional para colegios',
   'Apoyo en la feria vocacional dirigida a estudiantes de undécimo año: orientación en los stands, control de filas y aplicación de encuestas de salida.',
   'Facilidad para hablar en público',
   'Ciudad Quesada, Alajuela', '2026-08-14', '8:00 a. m. – 2:00 p. m.', 8, 6, 'closed', '2026-07-10 09:15:00'),

  -- Draft, only visible to its own organization
  (13, 1, 1, 'Siembra de árboles en la escuela de Venecia',
   'Actividad en preparación con la escuela de Venecia para sembrar árboles en el patio y armar un pequeño huerto escolar.',
   'Trabajo con niños',
   'Venecia, Alajuela', '2026-11-07', '8:00 a. m. – 11:00 a. m.', 15, 15, 'draft', '2026-08-20 12:00:00');

-- ------------------------------------------------------------
-- opportunity_skill — required skills (RN03, feeds RN08)
-- ------------------------------------------------------------
INSERT INTO opportunity_skill (opportunity_id, skill_id) VALUES
  (1,5),(1,12),
  (2,7),(2,1),
  (3,2),(3,8),
  (4,5),(4,12),
  (5,10),(5,9),
  (6,9),(6,12),
  (7,7),(7,1),
  (8,8),(8,12),
  (9,11),(9,1),
  (10,2),(10,8),
  (11,1),(11,7),
  (12,4),(12,8),
  (13,1),(13,5);

-- ------------------------------------------------------------
-- enrollments — mixed states (RN05, RN06, RF11)
-- Opportunity 11 has 4 slots and 4 accepted/completed, which is why
-- it is already 'closed' with available_slots = 0.
-- ------------------------------------------------------------
INSERT INTO enrollments (opportunity_id, volunteer_id, status, enrollment_date) VALUES
  -- 1 · Reforestación (4 slots, 2 accepted -> 2 available)
  -- Accepting the two pending ones fills it and triggers RN05 (auto-close).
  (1, 2, 'accepted', '2026-08-10 10:40:00'),
  (1, 3, 'pending',  '2026-08-11 08:05:00'),
  (1, 4, 'pending',  '2026-08-12 20:15:00'),
  (1, 5, 'rejected', '2026-08-13 12:00:00'),
  (1, 6, 'accepted', '2026-08-13 18:30:00'),
  -- 2 · Tutorías (8 slots, 1 accepted -> 7)
  (2, 4, 'accepted', '2026-08-06 15:00:00'),
  (2, 1, 'rejected', '2026-08-07 09:30:00'),
  (2, 8, 'pending',  '2026-08-09 11:10:00'),
  -- 3 · Feria de salud (15 slots, 2 accepted -> 13)
  (3, 2, 'accepted', '2026-08-07 08:00:00'),
  (3, 7, 'accepted', '2026-08-08 19:20:00'),
  (3, 1, 'pending',  '2026-08-14 07:45:00'),
  -- 4 · Vivero (10 slots, 1 accepted -> 9)
  (4, 3, 'accepted', '2026-08-09 06:50:00'),
  (4, 6, 'pending',  '2026-08-11 21:00:00'),
  -- 5 · Tradiciones orales (12 slots, 2 accepted -> 10)
  (5, 5, 'accepted', '2026-08-12 10:10:00'),
  (5, 8, 'accepted', '2026-08-12 14:35:00'),
  (5, 1, 'pending',  '2026-08-15 16:00:00'),
  -- 6 · Adultos mayores (6 slots, 1 accepted -> 5)
  (6, 8, 'accepted', '2026-08-13 09:00:00'),
  (6, 2, 'pending',  '2026-08-16 11:25:00'),
  -- 7 · Club de lectura (6 slots, 0 accepted -> 6)
  (7, 4, 'pending',  '2026-08-17 13:40:00'),
  -- 8 · Limpieza de quebrada (18 slots, 1 accepted -> 17)
  (8, 6, 'accepted', '2026-08-18 08:20:00'),
  (8, 3, 'pending',  '2026-08-19 10:00:00'),
  -- 11 · Taller de reciclaje (4 slots, 4 taken -> closed, RN05)
  (11, 1, 'completed', '2026-07-25 09:00:00'),
  (11, 4, 'accepted',  '2026-07-26 10:30:00'),
  (11, 6, 'accepted',  '2026-07-27 08:45:00'),
  (11, 8, 'accepted',  '2026-07-28 15:20:00'),
  -- 12 · Feria vocacional (8 slots, 2 completed -> 6)
  (12, 1, 'completed', '2026-07-15 09:00:00'),
  (12, 5, 'completed', '2026-07-16 11:00:00');

-- ------------------------------------------------------------
-- Consistency check for available_slots.
-- Recomputes the counter from the enrollments above, so the seed
-- data can never drift from the accepted enrollments.
-- ------------------------------------------------------------
UPDATE opportunities o
SET o.available_slots = GREATEST(0, o.total_slots - (
      SELECT COUNT(*) FROM enrollments e
      WHERE e.opportunity_id = o.id
        AND e.status IN ('accepted', 'completed')
    ));
