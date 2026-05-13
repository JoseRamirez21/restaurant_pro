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

INSERT INTO categorias (nombre, icono, color, orden) VALUES
('Entradas',   'bi-egg-fried',      '#e67e22', 1),
('Sopas',      'bi-cup-hot',        '#e74c3c', 2),
('Fondos',     'bi-bag',            '#8e44ad', 3),
('Parrillas',  'bi-fire',           '#c0392b', 4),
('Postres',    'bi-cake2',          '#f39c12', 5),
('Bebidas',    'bi-cup-straw',      '#2980b9', 6),
('Cócteles',   'bi-stars',          '#16a085', 7),
('Menú del día','bi-calendar-check','#27ae60', 8);
