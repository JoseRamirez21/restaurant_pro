<?php
require_once BASE_PATH . '/app/models/Usuario.php';

class AuthController {

    public function index($param = null) {
        $this->login();
    }

    public function login($param = null) {
        if (auth()) {
            $this->redirigirPorRol();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Por favor ingresa tu correo y contraseña.';
            } else {
                $usuario = Usuario::buscarPorEmail($email);

                if ($usuario && $usuario['password'] === md5($password)) {
                    if (!$usuario['activo']) {
                        $error = 'Tu cuenta está desactivada. Contacta al administrador.';
                    } else {
                        $_SESSION['usuario_id'] = $usuario['id'];
                        $_SESSION['nombre']     = $usuario['nombre'];
                        $_SESSION['apellido']   = $usuario['apellido'];
                        $_SESSION['email']      = $usuario['email'];
                        $_SESSION['rol']        = $usuario['rol'];

                        $this->redirigirPorRol();
                    }
                } else {
                    $error = 'Correo o contraseña incorrectos.';
                }
            }
        }

        require_once APP_PATH . '/views/auth/login.php';
    }

    public function logout($param = null) {
        sessionDestroy();
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function sinAcceso($param = null) {
        http_response_code(403);
        require_once APP_PATH . '/views/auth/sin_acceso.php';
    }

    private function redirigirPorRol(): void {
        $destinos = [
            'administrador' => APP_URL . '/admin',
            'supervisor'    => APP_URL . '/admin',
            'mesero'        => APP_URL . '/mesas',
            'cocinero'      => APP_URL . '/cocina',
            'cajero'        => APP_URL . '/caja',
        ];
        $rol = $_SESSION['rol'] ?? 'mesero';
        header('Location: ' . ($destinos[$rol] ?? APP_URL . '/mesas'));
        exit;
    }
}
