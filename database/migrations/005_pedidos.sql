CREATE TABLE IF NOT EXISTS pedidos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mesa_id         INT UNSIGNED NOT NULL,
    usuario_id      INT UNSIGNED NOT NULL,
    estado          ENUM('abierto','en_cocina','listo','cerrado','anulado') NOT NULL DEFAULT 'abierto',
    personas        INT          NOT NULL DEFAULT 1,
    observaciones   TEXT         NULL,
    subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    igv             DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    servicio        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    creado_en       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    cerrado_en      TIMESTAMP    NULL,
    FOREIGN KEY (mesa_id)    REFERENCES mesas(id)    ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
