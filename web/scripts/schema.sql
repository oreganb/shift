-- SHIFT Ontology Website — MySQL schema (shared hosting)
-- Database must already exist (e.g. db1379071_shift1 on Blacknight)

CREATE TABLE IF NOT EXISTS ontology_classes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(128) NOT NULL UNIQUE,
    iri         VARCHAR(512) NOT NULL,
    label       VARCHAR(256) NOT NULL,
    comment     TEXT,
    sub_class_of JSON,
    source_file VARCHAR(128),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ontology_properties (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(128) NOT NULL UNIQUE,
    iri         VARCHAR(512) NOT NULL,
    label       VARCHAR(256) NOT NULL,
    prop_type   ENUM('object','datatype') NOT NULL,
    domain_class VARCHAR(128),
    range_value  VARCHAR(256),
    comment     TEXT,
    source_file VARCHAR(128),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reasoning_rules (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    rule_id     VARCHAR(32) NOT NULL UNIQUE,
    iri         VARCHAR(512) NOT NULL,
    label       VARCHAR(512) NOT NULL,
    comment     TEXT,
    applies_to  VARCHAR(128),
    infers      VARCHAR(128),
    metadata    JSON,
    source_file VARCHAR(128),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vault_notes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(512) NOT NULL UNIQUE,
    title       VARCHAR(256) NOT NULL,
    folder      VARCHAR(256),
    body        MEDIUMTEXT,
    meta        JSON,
    preview     TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FULLTEXT KEY ft_search (title, body, preview)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS vault_links (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    source_slug VARCHAR(512) NOT NULL,
    target_slug VARCHAR(512),
    target_title VARCHAR(256) NOT NULL,
    alias       VARCHAR(256),
    resolved    TINYINT(1) DEFAULT 0,
    INDEX idx_source (source_slug),
    INDEX idx_target (target_slug)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS site_metadata (
    meta_key    VARCHAR(64) PRIMARY KEY,
    meta_value  TEXT,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
