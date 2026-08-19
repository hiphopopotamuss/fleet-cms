-- Fleet CMS
-- Import this file into MySQL, then run: php bin/seed.php
-- (Docker runs seed.php automatically on container start.)
--
-- Tenancy: every business-owned row stores `level` + `level_id`.
-- Application code MUST filter by the authenticated user's level/level_id.
-- Never trust level/level_id from the browser.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS fleet_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fleet_cms;

DROP TABLE IF EXISTS inspections;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS businesses;

-- A business is the tenant. Its primary key is used as users.level_id
-- when users.level = 'business'.
CREATE TABLE businesses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'driver') NOT NULL,
    level VARCHAR(50) NOT NULL DEFAULT 'business',
    level_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_tenant (level, level_id),
    CONSTRAINT fk_users_business
        FOREIGN KEY (level_id) REFERENCES businesses (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    level VARCHAR(50) NOT NULL DEFAULT 'business',
    level_id INT UNSIGNED NOT NULL,
    registration VARCHAR(20) NOT NULL,
    make VARCHAR(80) NOT NULL,
    model VARCHAR(80) NOT NULL,
    year SMALLINT UNSIGNED NOT NULL,
    mileage INT UNSIGNED NOT NULL DEFAULT 0,
    mot_expiry DATE NULL,
    tax_expiry DATE NULL,
    status ENUM('active', 'inactive', 'maintenance') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_vehicles_reg_tenant (level, level_id, registration),
    KEY idx_vehicles_tenant (level, level_id),
    CONSTRAINT fk_vehicles_business
        FOREIGN KEY (level_id) REFERENCES businesses (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inspections (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    level VARCHAR(50) NOT NULL DEFAULT 'business',
    level_id INT UNSIGNED NOT NULL,
    vehicle_id INT UNSIGNED NOT NULL,
    inspection_date DATE NOT NULL,
    mileage INT UNSIGNED NOT NULL,
    damage_reported TINYINT(1) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    status ENUM('pending', 'pass', 'fail') NOT NULL DEFAULT 'pending',
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_inspections_tenant (level, level_id),
    KEY idx_inspections_vehicle (vehicle_id),
    CONSTRAINT fk_inspections_business
        FOREIGN KEY (level_id) REFERENCES businesses (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_inspections_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES vehicles (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_inspections_user
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Explicit IDs match the brief (Company A = 10, Company B = 20).
INSERT INTO businesses (id, name) VALUES
    (10, 'Company A'),
    (20, 'Company B');

-- password_hash is set to 'pending' until php bin/seed.php hashes Password123!
INSERT INTO users (name, email, password_hash, role, level, level_id) VALUES
    ('Alice Admin',   'admin@companya.test',   'pending', 'admin',   'business', 10),
    ('Mark Manager',  'manager@companya.test', 'pending', 'manager', 'business', 10),
    ('Dana Driver',   'driver@companya.test',  'pending', 'driver',  'business', 10),
    ('Bob Admin',     'admin@companyb.test',   'pending', 'admin',   'business', 20);

INSERT INTO vehicles (level, level_id, registration, make, model, year, mileage, mot_expiry, tax_expiry, status) VALUES
    ('business', 10, 'AB10 AAA', 'Ford',   'Transit', 2021, 42000, '2026-11-01', '2026-09-30', 'active'),
    ('business', 10, 'AB10 BBB', 'Vauxhall', 'Vivaro', 2019, 78000, '2026-03-15', '2026-04-01', 'maintenance'),
    ('business', 20, 'CD20 CCC', 'Mercedes', 'Sprinter', 2022, 21000, '2027-01-20', '2026-12-31', 'active');

INSERT INTO inspections (level, level_id, vehicle_id, inspection_date, mileage, damage_reported, notes, status, created_by) VALUES
    ('business', 10, 1, '2026-08-01', 41800, 0, 'All good. Lights and tyres checked.', 'pass', 2),
    ('business', 20, 3, '2026-08-10', 20950, 1, 'Scuff on rear bumper.', 'pending', 4);
