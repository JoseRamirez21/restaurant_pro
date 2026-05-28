<?php
class Impresora {

    // Configuración — ajustar según impresora
    private static function config(): array {
        return [
            'tipo'      => 'red',           // 'red' o 'usb'
            'ip'        => '192.168.1.100', // IP de la impresora en red
            'puerto'    => 9100,            // Puerto ESC/POS estándar
            'timeout'   => 5,               // segundos
            'ancho'     => 32,              // caracteres por línea (80mm = 32-48)
        ];
    }

    // Imprimir boleta de venta
    public static function imprimirBoleta(array $pedido, array $detalle): array {
        $cfg  = self::config();
        $data = self::generarBoleta($pedido, $detalle, $cfg['ancho']);
        return self::enviar($data, $cfg);
    }

    // Imprimir comanda para cocina
    public static function imprimirComanda(array $pedido, array $items): array {
        $cfg  = self::config();
        $data = self::generarComanda($pedido, $items, $cfg['ancho']);
        return self::enviar($data, $cfg);
    }

    // Generar texto de boleta con comandos ESC/POS
    private static function generarBoleta(array $pedido, array $detalle, int $ancho): string {
        $ESC = "\x1B";
        $GS  = "\x1D";
        $out = '';

        // Inicializar impresora
        $out .= $ESC . "@";

        // Centrar texto
        $out .= $ESC . "a" . "\x01";

        // Texto grande y negrita para nombre
        $out .= $ESC . "!" . "\x30";
        $out .= "RestaurantePro\n";

        // Texto normal
        $out .= $ESC . "!" . "\x00";
        $out .= "Lima, Peru\n";
        $out .= date('d/m/Y H:i') . "\n";
        $out .= self::linea($ancho) . "\n";

        // Info mesa — alinear izquierda
        $out .= $ESC . "a" . "\x00";
        $out .= "Mesa: " . $pedido['mesa_numero'] . "\n";
        $out .= "Mozo: " . ($pedido['mesero_nombre'] ?? '') . "\n";
        $out .= "Personas: " . $pedido['personas'] . "\n";
        $out .= self::linea($ancho) . "\n";

        // Items
        foreach ($detalle as $item) {
            $nombre = mb_substr($item['producto_nombre'], 0, $ancho - 10);
            $precio = number_format($item['subtotal'], 2);
            $cant   = $item['cantidad'] . 'x ';
            $espacio = $ancho - mb_strlen($cant . $nombre) - mb_strlen($precio);
            $espacio = max(1, $espacio);
            $out .= $cant . $nombre . str_repeat(' ', $espacio) . $precio . "\n";
        }

        $out .= self::linea($ancho) . "\n";

        // Total en negrita
        $out .= $ESC . "!" . "\x08"; // negrita
        $total = "TOTAL  S/ " . number_format($pedido['subtotal'], 2);
        $out .= str_pad($total, $ancho, ' ', STR_PAD_LEFT) . "\n";
        $out .= $ESC . "!" . "\x00";

        // Método de pago
        $out .= str_pad("Pago: " . ucfirst($pedido['metodo_pago'] ?? 'efectivo'), $ancho, ' ', STR_PAD_LEFT) . "\n";
        $out .= self::linea($ancho) . "\n";

        // Mensaje de gracias — centrado
        $out .= $ESC . "a" . "\x01";
        $out .= "Gracias por su visita!\n";
        $out .= "Esperamos verle pronto\n";
        $out .= "Ref: #" . str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) . "\n";

        // Cortar papel
        $out .= "\n\n\n";
        $out .= $GS . "V" . "\x41" . "\x03"; // corte parcial

        return $out;
    }

    // Generar comanda para cocina
    private static function generarComanda(array $pedido, array $items, int $ancho): string {
        $ESC = "\x1B";
        $GS  = "\x1D";
        $out = '';

        $out .= $ESC . "@";
        $out .= $ESC . "a" . "\x01";

        // Título grande
        $out .= $ESC . "!" . "\x30";
        $out .= "*** COCINA ***\n";
        $out .= $ESC . "!" . "\x00";

        $out .= $ESC . "a" . "\x00";
        $out .= "Mesa: " . $pedido['mesa_numero'] . "\n";
        $out .= "Mozo: " . ($pedido['mesero_nombre'] ?? '') . "\n";
        $out .= date('H:i:s') . "\n";
        $out .= self::linea($ancho) . "\n";

        foreach ($items as $item) {
            // Cantidad grande y negrita
            $out .= $ESC . "!" . "\x08";
            $out .= $item['cantidad'] . "x " . mb_substr($item['producto_nombre'], 0, $ancho - 5) . "\n";
            $out .= $ESC . "!" . "\x00";

            if (!empty($item['observaciones'])) {
                $out .= "  >> " . $item['observaciones'] . "\n";
            }
        }

        $out .= self::linea($ancho) . "\n";
        $out .= "\n\n\n";
        $out .= $GS . "V" . "\x41" . "\x03";

        return $out;
    }

    // Línea separadora
    private static function linea(int $ancho): string {
        return str_repeat('-', $ancho);
    }

    // Enviar datos a la impresora
    private static function enviar(string $data, array $cfg): array {
        if ($cfg['tipo'] === 'red') {
            return self::enviarRed($data, $cfg);
        }
        return self::enviarUSB($data, $cfg);
    }

    // Impresora en red (LAN/WiFi)
    private static function enviarRed(string $data, array $cfg): array {
        $socket = @fsockopen(
            $cfg['ip'],
            $cfg['puerto'],
            $errno,
            $errstr,
            $cfg['timeout']
        );

        if (!$socket) {
            return [
                'ok'    => false,
                'error' => "No se pudo conectar a la impresora ({$cfg['ip']}:{$cfg['puerto']}): $errstr"
            ];
        }

        fwrite($socket, $data);
        fclose($socket);

        return ['ok' => true, 'msg' => 'Impreso correctamente'];
    }

    // Impresora USB (Windows — ruta de impresora compartida)
    private static function enviarUSB(string $data, array $cfg): array {
        // En Windows: copiar a la impresora compartida
        $impresora = $cfg['nombre_usb'] ?? 'POS-80';
        $tmp = tempnam(sys_get_temp_dir(), 'pos_');
        file_put_contents($tmp, $data);

        // Comando Windows para enviar a impresora
        $cmd = 'copy /b "' . $tmp . '" "\\\\localhost\\' . $impresora . '" > nul 2>&1';
        exec($cmd, $output, $code);
        unlink($tmp);

        return $code === 0
            ? ['ok' => true,  'msg'   => 'Impreso correctamente']
            : ['ok' => false, 'error' => 'Error al imprimir por USB'];
    }
}