-- ============================================================
--  RestaurantePro — Base de datos completa
--  Versión: 1.0 | Moneda: Soles (S/) | País: Perú
--  Ejecutar en phpMyAdmin o MySQL CLI
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS restaurante_pro
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE restaurante_pro;

-- ────────────────────────────────────────────
-- TABLA: usuarios
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100)  NOT NULL,
    apellido       VARCHAR(100)  NOT NULL,
    email          VARCHAR(150)  NOT NULL UNIQUE,
    password       VARCHAR(255)  NOT NULL,
    rol            ENUM('administrador','mesero','cocinero','cajero','supervisor') NOT NULL DEFAULT 'mesero',
    activo         TINYINT(1)    NOT NULL DEFAULT 1,
    avatar         VARCHAR(255)  NULL,
    creado_en      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- TABLA: categorias
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categorias (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL,
    descripcion VARCHAR(200) NULL,
    icono       VARCHAR(50)  NULL DEFAULT 'bi-grid',
    color       VARCHAR(20)  NULL DEFAULT '#6c757d',
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    orden       INT          NOT NULL DEFAULT 0,
    creado_en   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- TABLA: productos
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS productos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id    INT UNSIGNED NOT NULL,
    nombre          VARCHAR(120) NOT NULL,
    descripcion     TEXT         NULL,
    precio          DECIMAL(8,2) NOT NULL,
    costo           DECIMAL(8,2) NULL DEFAULT 0.00,
    imagen          VARCHAR(255) NULL,
    alergenos       VARCHAR(200) NULL,
    disponible      TINYINT(1)   NOT NULL DEFAULT 1,
    destacado       TINYINT(1)   NOT NULL DEFAULT 0,
    tiempo_prep_min INT          NOT NULL DEFAULT 10,
    creado_en       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- TABLA: mesas
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS mesas (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero         INT          NOT NULL UNIQUE,
    nombre         VARCHAR(50)  NULL,
    capacidad      INT          NOT NULL DEFAULT 4,
    zona           ENUM('salon_principal','terraza','privado','bar') NOT NULL DEFAULT 'salon_principal',
    estado         ENUM('libre','ocupada','reservada','mantenimiento') NOT NULL DEFAULT 'libre',
    pos_x          INT          NOT NULL DEFAULT 0,
    pos_y          INT          NOT NULL DEFAULT 0,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    actualizado_en TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- TABLA: pedidos
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pedidos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mesa_id        INT UNSIGNED  NOT NULL,
    usuario_id     INT UNSIGNED  NOT NULL,
    estado         ENUM('abierto','en_cocina','listo','cerrado','anulado') NOT NULL DEFAULT 'abierto',
    personas       INT           NOT NULL DEFAULT 1,
    observaciones  TEXT          NULL,
    subtotal       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    igv            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    servicio       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    creado_en      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    cerrado_en     TIMESTAMP     NULL,
    FOREIGN KEY (mesa_id)    REFERENCES mesas(id)    ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- TABLA: detalle_pedidos
-- ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id              INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED  NOT NULL,
    producto_id     INT UNSIGNED  NOT NULL,
    cantidad        INT           NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(8,2)  NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    estado          ENUM('pendiente','en_preparacion','listo','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
    observaciones   VARCHAR(255)  NULL,
    creado_en       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id)   REFERENCES pedidos(id)   ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────
-- DATOS INICIALES
-- ────────────────────────────────────────────
INSERT INTO usuarios (nombre, apellido, email, password, rol) VALUES
('Admin',  'Sistema',   'admin@restaurantepro.pe',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Carlos', 'Quispe',    'cquispe@restaurantepro.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mesero'),
('Maria',  'Flores',    'mflores@restaurantepro.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cocinero'),
('Rosa',   'Huaman',    'rhuaman@restaurantepro.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero'),
('Jorge',  'Mamani',    'jmamani@restaurantepro.pe', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor');

-- Nota: la password hasheada corresponde a "password" — cambia antes de producción

INSERT INTO categorias (nombre, icono, color, orden) VALUES
('Entradas',    'bi-egg-fried',       '#e67e22', 1),
('Sopas',       'bi-cup-hot',         '#e74c3c', 2),
('Fondos',      'bi-bag',             '#8e44ad', 3),
('Parrillas',   'bi-fire',            '#c0392b', 4),
('Postres',     'bi-cake2',           '#f39c12', 5),
('Bebidas',     'bi-cup-straw',       '#2980b9', 6),
('Cócteles',    'bi-stars',           '#16a085', 7),
('Menú del día','bi-calendar-check',  '#27ae60', 8);

INSERT INTO productos (categoria_id, nombre, descripcion, precio, costo, tiempo_prep_min, destacado) VALUES
(1, 'Causa limeña',        'Causa de papa amarilla rellena de pollo al ají amarillo',      28.00, 8.00,  15, 1),
(1, 'Ceviche de pescado',  'Con ají limo, canchita, choclo y leche de tigre',              45.00, 14.00, 20, 1),
(1, 'Tiradito de lenguado','Láminas de lenguado en salsa de rocoto',                       42.00, 13.00, 15, 0),
(2, 'Aguadito de pollo',   'Sopa verde con cilantro, arroz y pollo',                       32.00, 9.00,  25, 0),
(2, 'Parihuela',           'Sopa de mariscos al estilo peruano',                           48.00, 16.00, 30, 1),
(3, 'Lomo saltado',        'Con papas fritas, tomate, cebolla y arroz',                    55.00, 18.00, 20, 1),
(3, 'Ají de gallina',      'En salsa de ají amarillo, nueces y aceitunas con arroz',       48.00, 14.00, 20, 1),
(3, 'Arroz con leche',     'Pollo guisado en salsa de leche con especias',                 44.00, 13.00, 25, 0),
(4, 'Anticucho de corazón','Con papas y choclo asado al carbón',                           38.00, 10.00, 15, 0),
(4, 'Parrillada mixta',    'Res, pollo y chorizo con papas y ensalada (2 personas)',       120.00, 40.00, 35, 1),
(5, 'Suspiro limeño',      'Dulce de manjar blanco con merengue de oporto',                22.00, 5.00,  5,  1),
(5, 'Picarones',           'Con miel de chancaca al estilo limeño',                        18.00, 4.00,  10, 0),
(6, 'Chicha morada',       'Bebida natural de maíz morado — jarra 1L',                    18.00, 3.00,  2,  0),
(6, 'Inca Kola',           '500ml',                                                         8.00, 2.00,  1,  0),
(6, 'Agua San Luis',       '625ml con o sin gas',                                           5.00, 1.00,  1,  0);

INSERT INTO mesas (numero, nombre, capacidad, zona, pos_x, pos_y) VALUES
(1,  'Mesa 1',   4, 'salon_principal', 60,  60),
(2,  'Mesa 2',   4, 'salon_principal', 200, 60),
(3,  'Mesa 3',   4, 'salon_principal', 340, 60),
(4,  'Mesa 4',   4, 'salon_principal', 480, 60),
(5,  'Mesa 5',   6, 'salon_principal', 60,  200),
(6,  'Mesa 6',   6, 'salon_principal', 200, 200),
(7,  'Mesa 7',   6, 'salon_principal', 340, 200),
(8,  'Mesa 8',   2, 'bar',             480, 200),
(9,  'Mesa 9',   4, 'terraza',         60,  340),
(10, 'Mesa 10',  8, 'privado',         200, 340);

SET FOREIGN_KEY_CHECKS = 1;
