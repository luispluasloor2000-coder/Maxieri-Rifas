-- Importa este archivo dentro de la base de datos ya creada.
-- En local puedes crearla con database/create_user.sql antes de importar.

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL COMMENT 'Nombre visible del usuario administrador',
    username VARCHAR(60) NOT NULL UNIQUE COMMENT 'Usuario de acceso',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hash seguro generado con password_hash',
    role ENUM('admin','operator') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Usuarios autorizados para administrar rifas';

CREATE TABLE raffles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    prize VARCHAR(180) NOT NULL,
    draw_date DATETIME NOT NULL,
    numbers_quantity INT UNSIGNED NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active','finished','hidden') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_draw_date (draw_date)
) ENGINE=InnoDB COMMENT='Rifas administradas por el sistema';

CREATE TABLE raffle_numbers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    raffle_id INT UNSIGNED NOT NULL,
    number_value INT UNSIGNED NOT NULL,
    status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
    buyer_name VARCHAR(140) NULL,
    buyer_phone VARCHAR(40) NULL,
    buyer_city VARCHAR(100) NULL,
    notes TEXT NULL,
    registered_at DATETIME NULL,
    registered_by INT UNSIGNED NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_raffle_number (raffle_id, number_value),
    INDEX idx_status (status),
    INDEX idx_buyer_phone (buyer_phone),
    CONSTRAINT fk_numbers_raffle FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE,
    CONSTRAINT fk_numbers_user FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Números individuales de cada rifa';

CREATE TABLE number_audits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    raffle_number_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    old_data JSON NULL,
    new_data JSON NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_number_created (raffle_number_id, created_at),
    CONSTRAINT fk_audit_number FOREIGN KEY (raffle_number_id) REFERENCES raffle_numbers(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Bitácora verificable de cambios realizados sobre números';

CREATE TABLE draw_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    raffle_id INT UNSIGNED NOT NULL,
    raffle_number_id INT UNSIGNED NOT NULL,
    prize VARCHAR(180) NOT NULL,
    winner_name VARCHAR(140) NULL,
    number_value INT UNSIGNED NOT NULL,
    drawn_at DATETIME NOT NULL,
    drawn_by INT UNSIGNED NULL,
    CONSTRAINT fk_draw_raffle FOREIGN KEY (raffle_id) REFERENCES raffles(id) ON DELETE CASCADE,
    CONSTRAINT fk_draw_number FOREIGN KEY (raffle_number_id) REFERENCES raffle_numbers(id) ON DELETE CASCADE,
    CONSTRAINT fk_draw_user FOREIGN KEY (drawn_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Historial de sorteos realizados';

INSERT IGNORE INTO users (name, username, password_hash, role)
VALUES ('Administrador', 'admin', '$2y$12$9CBp6tx14UCdORgjr3cR4uZcYemGmckfX5s7.YfnUEdkf7KnRPuYG', 'admin');
