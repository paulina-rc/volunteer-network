-- ============================================================
-- Enlaza — Database schema
-- Corresponds to section 7 (Data model) of the project's
-- technical documentation.
--
-- BREAKING CHANGE (English naming convention):
-- Every table and column was renamed from Spanish to English
-- (see CLAUDE.md, section 5). If you already ran a previous
-- version of this schema locally, DROP the "enlaza" database
-- and re-import this file — there is no in-place migration.
-- ============================================================

CREATE DATABASE IF NOT EXISTS enlaza
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE enlaza;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE users (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  email             VARCHAR(150) NOT NULL UNIQUE,
  password_hash     VARCHAR(255) NOT NULL,
  role              ENUM('volunteer', 'organization') NOT NULL,
  is_active         TINYINT(1) NOT NULL DEFAULT 1,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- volunteers (1 to 1 with users)
-- ------------------------------------------------------------
CREATE TABLE volunteers (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  user_id           INT NOT NULL UNIQUE,
  full_name         VARCHAR(150) NOT NULL,
  location          VARCHAR(150),
  availability      VARCHAR(100),
  about_me          TEXT,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_volunteer_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- organizations (1 to 1 with users)
-- ------------------------------------------------------------
CREATE TABLE organizations (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  user_id           INT NOT NULL UNIQUE,
  name              VARCHAR(150) NOT NULL,
  description       TEXT,
  location          VARCHAR(150),
  contact           VARCHAR(150),
  profile_complete  TINYINT(1) NOT NULL DEFAULT 0,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_organization_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- categories (fixed catalog)
-- ------------------------------------------------------------
CREATE TABLE categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(50) NOT NULL UNIQUE,
  color_hex   VARCHAR(7),
  icon        VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- skills (open catalog)
-- ------------------------------------------------------------
CREATE TABLE skills (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  name    VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- volunteer_interest (N to M: volunteers <-> categories)
-- ------------------------------------------------------------
CREATE TABLE volunteer_interest (
  volunteer_id    INT NOT NULL,
  category_id     INT NOT NULL,
  PRIMARY KEY (volunteer_id, category_id),
  CONSTRAINT fk_volunteer_interest_volunteer FOREIGN KEY (volunteer_id)
    REFERENCES volunteers(id) ON DELETE CASCADE,
  CONSTRAINT fk_volunteer_interest_category FOREIGN KEY (category_id)
    REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- volunteer_skill (N to M: volunteers <-> skills)
-- ------------------------------------------------------------
CREATE TABLE volunteer_skill (
  volunteer_id    INT NOT NULL,
  skill_id        INT NOT NULL,
  PRIMARY KEY (volunteer_id, skill_id),
  CONSTRAINT fk_volunteer_skill_volunteer FOREIGN KEY (volunteer_id)
    REFERENCES volunteers(id) ON DELETE CASCADE,
  CONSTRAINT fk_volunteer_skill_skill FOREIGN KEY (skill_id)
    REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- opportunities
-- ------------------------------------------------------------
CREATE TABLE opportunities (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  organization_id     INT NOT NULL,
  category_id         INT NOT NULL,
  title               VARCHAR(150) NOT NULL,
  description         TEXT,
  location            VARCHAR(150),
  activity_date       DATE NOT NULL,
  time                VARCHAR(50),
  total_slots         INT NOT NULL,
  available_slots     INT NOT NULL,
  status              ENUM('active', 'closed') NOT NULL DEFAULT 'active',
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_opportunity_organization FOREIGN KEY (organization_id)
    REFERENCES organizations(id) ON DELETE CASCADE,
  CONSTRAINT fk_opportunity_category FOREIGN KEY (category_id)
    REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- opportunity_skill (N to M: opportunities <-> required skills)
-- ------------------------------------------------------------
CREATE TABLE opportunity_skill (
  opportunity_id  INT NOT NULL,
  skill_id        INT NOT NULL,
  PRIMARY KEY (opportunity_id, skill_id),
  CONSTRAINT fk_opportunity_skill_opportunity FOREIGN KEY (opportunity_id)
    REFERENCES opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_opportunity_skill_skill FOREIGN KEY (skill_id)
    REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- enrollments
-- ------------------------------------------------------------
CREATE TABLE enrollments (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  volunteer_id        INT NOT NULL,
  opportunity_id      INT NOT NULL,
  status              ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
  enrollment_date     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_volunteer_opportunity (volunteer_id, opportunity_id),
  CONSTRAINT fk_enrollment_volunteer FOREIGN KEY (volunteer_id)
    REFERENCES volunteers(id) ON DELETE CASCADE,
  CONSTRAINT fk_enrollment_opportunity FOREIGN KEY (opportunity_id)
    REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
