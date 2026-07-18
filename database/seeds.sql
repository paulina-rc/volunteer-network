-- ============================================================
-- Enlaza — Datos semilla
-- ============================================================

USE enlaza;

INSERT INTO categorias (nombre, color_hex, icono) VALUES
  ('Ambiental',   '#A8C9A1', 'fa-leaf'),
  ('Educativo',   '#E9A227', 'fa-book-open'),
  ('Salud',       '#5C8A78', 'fa-heart-pulse'),
  ('Comunitario', '#0F4C5C', 'fa-people-group'),
  ('Cultural',    '#F26B4A', 'fa-masks-theater');

INSERT INTO habilidades (nombre) VALUES
  ('Trabajo con niños'),
  ('Primeros auxilios'),
  ('Idiomas'),
  ('Manejo de redes sociales'),
  ('Jardinería / agricultura'),
  ('Cocina'),
  ('Fotografía / video');
