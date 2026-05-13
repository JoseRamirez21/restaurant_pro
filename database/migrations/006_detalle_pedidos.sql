CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED NOT NULL,
    producto_id     INT UNSIGNED NOT NULL,
    cantidad        INT          NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(8,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    estado          ENUM('pendiente','en_preparacion','listo','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
    observaciones   VARCHAR(255) NULL,
    creado_en       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id)   REFERENCES pedidos(id)   ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
