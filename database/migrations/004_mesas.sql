CREATE TABLE IF NOT EXISTS mesas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero      INT          NOT NULL UNIQUE,
    nombre      VARCHAR(50)  NULL,
    capacidad   INT          NOT NULL DEFAULT 4,
    zona        ENUM('salon_principal','terraza','privado','bar') NOT NULL DEFAULT 'salon_principal',
    estado      ENUM('libre','ocupada','reservada','mantenimiento') NOT NULL DEFAULT 'libre',
    pos_x       INT          NOT NULL DEFAULT 0,
    pos_y       INT          NOT NULL DEFAULT 0,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO mesas (numero, nombre, capacidad, zona, pos_x, pos_y) VALUES
(1,  'Mesa 1',  4, 'salon_principal', 60,  60),
(2,  'Mesa 2',  4, 'salon_principal', 200, 60),
(3,  'Mesa 3',  4, 'salon_principal', 340, 60),
(4,  'Mesa 4',  4, 'salon_principal', 480, 60),
(5,  'Mesa 5',  6, 'salon_principal', 60,  200),
(6,  'Mesa 6',  6, 'salon_principal', 200, 200),
(7,  'Mesa 7',  6, 'salon_principal', 340, 200),
(8,  'Mesa 8',  2, 'bar',             480, 200),
(9,  'Mesa 9',  4, 'terraza',         60,  340),
(10, 'Mesa 10', 8, 'privado',         200, 340);
