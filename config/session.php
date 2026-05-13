<?php
session_start();

function auth(): bool {
    return isset($_SESSION['usuario_id']);
}

function authRol(string ...$roles): bool {
    if (!auth()) return false;
    return in_array($_SESSION['rol'], $roles);
}

function requireAuth(): void {
    if (!auth()) {
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}

function requireRol(string ...$roles): void {
    requireAuth();
    if (!authRol(...$roles)) {
        header('Location: ' . APP_URL . '/sin-acceso');
        exit;
    }
}

function sessionSet(string $key, mixed $val): void {
    $_SESSION[$key] = $val;
}

function sessionGet(string $key, mixed $default = null): mixed {
    return $_SESSION[$key] ?? $default;
}

function sessionDestroy(): void {
    session_unset();
    session_destroy();
}
