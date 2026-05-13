CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)  NOT NULL,
    apellido    VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    rol         ENUM('administrador','mesero','cocinero','cajero','supervisor') NOT NULL DEFAULT 'mesero',
    activo      TINYINT(1)    NOT NULL DEFAULT 1,
    avatar      VARCHAR(255)  NULL,
    creado_en   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Admin',    'RestaurantePro', 'admin@restaurantepro.pe',    '$2y$12$placeholder_admin',    'administrador'),
('Carlos',   'Quispe',         'cquispe@restaurantepro.pe',  '$2y$12$placeholder_mesero',   'mesero'),
('Maria',    'Flores',         'mflores@restaurantepro.pe',  '$2y$12$placeholder_cocinero',  'cocinero'),
('Rosa',     'Huaman',         'rhuaman@restaurantepro.pe',  '$2y$12$placeholder_cajero',    'cajero'),
('Jorge',    'Mamani',         'jmamani@restaurantepro.pe',  '$2y$12$placeholder_supervisor','supervisor');
