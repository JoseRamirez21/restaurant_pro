<?php
$url = $_GET['url'] ?? 'login';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$partes = explode('/', $url);

$controlador = $partes[0] ?? 'login';
$accion      = $partes[1] ?? 'index';
$param       = $partes[2] ?? null;

$rutas = [
    'login'      => 'AuthController',
    'logout'     => 'AuthController',
    'admin'      => 'AdminController',
    'mesas'      => 'MesaController',
    'pedidos'    => 'PedidoController',
    'productos'  => 'ProductoController',
    'cocina'     => 'CocinaController',
    'caja'       => 'CajaController',
    'sin-acceso' => 'AuthController',
];

if (!array_key_exists($controlador, $rutas)) {
    http_response_code(404);
    die('Página no encontrada');
}

$clase = $rutas[$controlador];
$archivo = APP_PATH . '/controllers/' . $clase . '.php';

if (!file_exists($archivo)) {
    die('Controlador no encontrado');
}

require_once $archivo;
$obj = new $clase();

if (method_exists($obj, $accion)) {
    $obj->$accion($param);
} else {
    $obj->index($param);
}
