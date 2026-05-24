<?php
class Configuracion {

    private static array $cache = [];

    public static function get(string $clave, string $default = ''): string {
        if (isset(self::$cache[$clave])) return self::$cache[$clave];
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = :clave LIMIT 1");
            $stmt->execute([':clave' => $clave]);
            $row  = $stmt->fetch();
            $val  = $row ? ($row['valor'] ?? $default) : $default;
            self::$cache[$clave] = $val;
            return $val;
        } catch (Exception $e) {
            return $default;
        }
    }

    public static function set(string $clave, string $valor): bool {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE configuracion SET valor = :valor WHERE clave = :clave");
        $ok   = $stmt->execute([':valor' => $valor, ':clave' => $clave]);
        if ($ok) self::$cache[$clave] = $valor;
        return $ok;
    }

    public static function porGrupo(): array {
        $db   = Database::getInstance()->getConnection();
        $rows = $db->query("SELECT * FROM configuracion ORDER BY grupo ASC, id ASC")->fetchAll();
        $grupos = [];
        foreach ($rows as $r) {
            $grupos[$r['grupo']][] = $r;
        }
        return $grupos;
    }

    public static function guardarTodo(array $datos): void {
        foreach ($datos as $clave => $valor) {
            self::set($clave, $valor);
        }
    }
}