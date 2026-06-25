CREATE DATABASE IF NOT EXISTS maxieri_rifas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'maxieri_user'@'localhost' IDENTIFIED BY 'MaxieriRifas2026!';
GRANT ALL PRIVILEGES ON maxieri_rifas.* TO 'maxieri_user'@'localhost';
FLUSH PRIVILEGES;
