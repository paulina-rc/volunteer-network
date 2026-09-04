-- ============================================================
-- Enlaza — Migration 001
-- Adds the columns and enum values the 9 screens need but the
-- current schema does not provide yet.
--
-- Run this AFTER schema.sql and BEFORE seeds.sql.
-- Safe to run on an existing local database: it only adds.
-- ============================================================

USE enlaza;

-- ------------------------------------------------------------
-- opportunities
-- ------------------------------------------------------------
-- The "Publicar oportunidad" screen has a requirements field and a
-- "Guardar borrador" button, so the status enum needs a draft value.
ALTER TABLE opportunities
  ADD COLUMN requirements TEXT NULL AFTER description;

ALTER TABLE opportunities
  MODIFY COLUMN status ENUM('active', 'closed', 'draft')
  NOT NULL DEFAULT 'active';

-- ------------------------------------------------------------
-- enrollments
-- ------------------------------------------------------------
-- The volunteer profile shows a "Completada" state and counts
-- completed activities, which the current enum cannot express.
ALTER TABLE enrollments
  MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'completed')
  NOT NULL DEFAULT 'pending';

-- ------------------------------------------------------------
-- organizations
-- ------------------------------------------------------------
-- The organization profile header shows the main category and
-- "Aliada desde <year>".
ALTER TABLE organizations
  ADD COLUMN category_id INT NULL AFTER user_id,
  ADD COLUMN founded_year SMALLINT NULL AFTER contact,
  ADD CONSTRAINT fk_organization_category FOREIGN KEY (category_id)
    REFERENCES categories(id) ON DELETE SET NULL;

-- ------------------------------------------------------------
-- Supporting indexes for search and filtering (RF06)
-- ------------------------------------------------------------
CREATE INDEX idx_opportunity_status   ON opportunities(status);
CREATE INDEX idx_opportunity_date     ON opportunities(activity_date);
CREATE INDEX idx_opportunity_category ON opportunities(category_id);
CREATE INDEX idx_enrollment_status    ON enrollments(status);
