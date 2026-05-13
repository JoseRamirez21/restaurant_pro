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

INSERT INTO productos (categoria_id, nombre, descripcion, precio, costo, tiempo_prep_min, destacado) VALUES
(1, 'Causa limeña',       'Causa de papa amarilla rellena de pollo',        28.00, 8.00,  15, 1),
(1, 'Ceviche de pescado', 'Con ají limo, canchita y choclo',                 45.00, 14.00, 20, 1),
(2, 'Aguadito de pollo',  'Sopa verde con cilantro y arroz',                 32.00, 9.00,  25, 0),
(3, 'Lomo saltado',       'Con papas fritas, tomate y cebolla',              55.00, 18.00, 20, 1),
(3, 'Ají de gallina',     'En salsa de ají amarillo con nueces y aceitunas', 48.00, 14.00, 20, 1),
(4, 'Anticucho de corazón','Con papas y choclo asado',                       38.00, 10.00, 15, 0),
(5, 'Suspiro limeño',     'Dulce de manjar con merengue de oporto',          22.00, 5.00,  5,  1),
(6, 'Chicha morada',      'Bebida natural de maíz morado 1L',                18.00, 3.00,  2,  0),
(6, 'Inca Kola',          '500ml',                                           8.00,  2.00,  1,  0);
