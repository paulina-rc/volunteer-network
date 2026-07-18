-- ============================================================
-- Enlaza — Esquema de base de datos
-- Corresponde a la sección 7 (Modelo de datos) de la
-- documentación técnica del proyecto.
-- ============================================================

CREATE DATABASE IF NOT EXISTS enlaza
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE enlaza;

-- ------------------------------------------------------------
-- usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  correo            VARCHAR(150) NOT NULL UNIQUE,
  contrasena_hash   VARCHAR(255) NOT NULL,
  rol               ENUM('voluntario', 'organizacion') NOT NULL,
  esta_activo       TINYINT(1) NOT NULL DEFAULT 1,
  fecha_creacion    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- voluntarios (1 a 1 con usuarios)
-- ------------------------------------------------------------
CREATE TABLE voluntarios (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario        INT NOT NULL UNIQUE,
  nombre_completo   VARCHAR(150) NOT NULL,
  ubicacion         VARCHAR(150),
  disponibilidad    VARCHAR(100),
  sobre_mi          TEXT,
  fecha_creacion    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_voluntario_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- organizaciones (1 a 1 con usuarios)
-- ------------------------------------------------------------
CREATE TABLE organizaciones (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario        INT NOT NULL UNIQUE,
  nombre            VARCHAR(150) NOT NULL,
  descripcion       TEXT,
  ubicacion         VARCHAR(150),
  contacto          VARCHAR(150),
  perfil_completo   TINYINT(1) NOT NULL DEFAULT 0,
  fecha_creacion    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_organizacion_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- categorias (catálogo fijo)
-- ------------------------------------------------------------
CREATE TABLE categorias (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50) NOT NULL UNIQUE,
  color_hex   VARCHAR(7),
  icono       VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- habilidades (catálogo abierto)
-- ------------------------------------------------------------
CREATE TABLE habilidades (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- voluntario_interes (N a M: voluntarios <-> categorias)
-- ------------------------------------------------------------
CREATE TABLE voluntario_interes (
  id_voluntario   INT NOT NULL,
  id_categoria    INT NOT NULL,
  PRIMARY KEY (id_voluntario, id_categoria),
  CONSTRAINT fk_interes_voluntario FOREIGN KEY (id_voluntario)
    REFERENCES voluntarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_interes_categoria FOREIGN KEY (id_categoria)
    REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- voluntario_habilidad (N a M: voluntarios <-> habilidades)
-- ------------------------------------------------------------
CREATE TABLE voluntario_habilidad (
  id_voluntario   INT NOT NULL,
  id_habilidad    INT NOT NULL,
  PRIMARY KEY (id_voluntario, id_habilidad),
  CONSTRAINT fk_volhab_voluntario FOREIGN KEY (id_voluntario)
    REFERENCES voluntarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_volhab_habilidad FOREIGN KEY (id_habilidad)
    REFERENCES habilidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- oportunidades
-- ------------------------------------------------------------
CREATE TABLE oportunidades (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  id_organizacion     INT NOT NULL,
  id_categoria        INT NOT NULL,
  titulo              VARCHAR(150) NOT NULL,
  descripcion         TEXT,
  ubicacion           VARCHAR(150),
  fecha_actividad     DATE NOT NULL,
  hora                VARCHAR(50),
  cupos_totales       INT NOT NULL,
  cupos_disponibles   INT NOT NULL,
  estado              ENUM('activa', 'cerrada') NOT NULL DEFAULT 'activa',
  fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_oportunidad_organizacion FOREIGN KEY (id_organizacion)
    REFERENCES organizaciones(id) ON DELETE CASCADE,
  CONSTRAINT fk_oportunidad_categoria FOREIGN KEY (id_categoria)
    REFERENCES categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- oportunidad_habilidad (N a M: oportunidades <-> habilidades requeridas)
-- ------------------------------------------------------------
CREATE TABLE oportunidad_habilidad (
  id_oportunidad  INT NOT NULL,
  id_habilidad    INT NOT NULL,
  PRIMARY KEY (id_oportunidad, id_habilidad),
  CONSTRAINT fk_ophab_oportunidad FOREIGN KEY (id_oportunidad)
    REFERENCES oportunidades(id) ON DELETE CASCADE,
  CONSTRAINT fk_ophab_habilidad FOREIGN KEY (id_habilidad)
    REFERENCES habilidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- inscripciones
-- ------------------------------------------------------------
CREATE TABLE inscripciones (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  id_voluntario       INT NOT NULL,
  id_oportunidad      INT NOT NULL,
  estado              ENUM('pendiente', 'aceptada', 'rechazada') NOT NULL DEFAULT 'pendiente',
  fecha_inscripcion   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unico_voluntario_oportunidad (id_voluntario, id_oportunidad),
  CONSTRAINT fk_inscripcion_voluntario FOREIGN KEY (id_voluntario)
    REFERENCES voluntarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_inscripcion_oportunidad FOREIGN KEY (id_oportunidad)
    REFERENCES oportunidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
