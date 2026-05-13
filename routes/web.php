<?php
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$partes = explode('/', $url);

$controlador = $partes[0] ?? '';
$accion      = $partes[1] ?? 'index';
$param       = $partes[2] ?? null;

// Si no hay controlador, ir al login
if (empty($controlador)) {
    $controlador = 'login';
}

$rutas = [
    'login'      => ['clase' => 'AuthController',   'accion' => 'login'],
    'logout'     => ['clase' => 'AuthController',   'accion' => 'logout'],
    'admin'      => ['clase' => 'AdminController',  'accion' => 'index'],
    'mesas'      => ['clase' => 'MesaController',   'accion' => 'index'],
    'pedidos'    => ['clase' => 'PedidoController', 'accion' => 'index'],
    'productos'  => ['clase' => 'ProductoController','accion' => 'index'],
    'cocina'     => ['clase' => 'CocinaController', 'accion' => 'index'],
    'caja'       => ['clase' => 'CajaController',   'accion' => 'index'],
    'sin-acceso' => ['clase' => 'AuthController',   'accion' => 'sinAcceso'],
];

if (!array_key_exists($controlador, $rutas)) {
    http_response_code(404);
    die('<h2>Página no encontrada</h2><a href="' . APP_URL . '">Volver</a>');
}

$clase   = $rutas[$controlador]['clase'];
$metodo  = $rutas[$controlador]['accion'];

// Si hay segmento extra en URL, usarlo como acción (ej: /admin/usuarios)
if (!empty($partes[1]) && $controlador !== 'logout') {
    $metodo = $partes[1];
}

$archivo = APP_PATH . '/controllers/' . $clase . '.php';

if (!file_exists($archivo)) {
    die('Controlador no encontrado: ' . $clase);
}

require_once $archivo;
$obj = new $clase();

if (method_exists($obj, $metodo)) {
    $obj->$metodo($param);
} else {
    $obj->index($param);
}