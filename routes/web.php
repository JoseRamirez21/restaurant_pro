<?php
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$partes = explode('/', $url);

$controlador = $partes[0] ?? '';
$accion      = $partes[1] ?? 'index';
$param       = $partes[2] ?? null;

if (empty($controlador)) $controlador = 'login';

$rutas = [
    'login'          => 'AuthController',
    'logout'         => 'AuthController',
    'admin'          => 'AdminController',
    'mesas'          => 'MesaController',
    'pedidos'        => 'PedidoController',
    'productos'      => 'ProductoController',
    'categorias'     => 'CategoriaController',
    'cocina'         => 'CocinaController',
    'caja'           => 'CajaController',
    'cierre'         => 'CierreCajaController',
    'usuarios'       => 'UsuarioController',
    'reportes'       => 'ReporteController',
    'reservas'       => 'ReservaController',
    'inventario'     => 'InventarioController',
    'clientes'       => 'ClienteController',
    'perfil'         => 'PerfilController',
    'notificaciones' => 'NotificacionController',
    'sin-acceso'     => 'AuthController',
];

if (!array_key_exists($controlador, $rutas)) {
    http_response_code(404);
    die('<h2 style="font-family:sans-serif;padding:2rem">Página no encontrada</h2>
         <a href="' . APP_URL . '">Volver al inicio</a>');
}

$clase   = $rutas[$controlador];
$archivo = APP_PATH . '/controllers/' . $clase . '.php';

if (!file_exists($archivo)) {
    die('Controlador no encontrado: ' . htmlspecialchars($clase));
}

require_once $archivo;
$obj = new $clase();

if ($controlador === 'logout') { $obj->logout(); exit; }

if (!empty($accion) && $accion !== 'index' && method_exists($obj, $accion)) {
    $obj->$accion($param);
} else {
    $obj->index($param);
}