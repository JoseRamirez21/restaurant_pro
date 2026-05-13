<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar — RestaurantePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f1eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e0ddd6;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
        }
        .logo-circle {
            width: 64px; height: 64px;
            background: #8e44ad;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .logo-circle i { font-size: 28px; color: #fff; }
        .btn-login {
            background: #8e44ad;
            color: #fff;
            border: none;
            padding: .7rem;
            font-weight: 500;
            border-radius: 8px;
            width: 100%;
        }
        .btn-login:hover { background: #7d3c98; color: #fff; }
        .form-control:focus { border-color: #8e44ad; box-shadow: 0 0 0 3px rgba(142,68,173,.15); }
        .form-label { font-size: 14px; font-weight: 500; color: #555; }
        .version { font-size: 11px; color: #aaa; text-align: center; margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="login-card shadow-sm">

    <div class="logo-circle">
        <i class="bi bi-shop"></i>
    </div>

    <h1 class="text-center fw-500 mb-1" style="font-size:1.4rem; font-weight:500;">RestaurantePro</h1>
    <p class="text-center text-muted mb-4" style="font-size:13px;">Sistema de gestión interno</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2" style="font-size:14px;">
            <i class="bi bi-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/login" novalidate>

        <div class="mb-3">
            <label class="form-label" for="email">
                <i class="bi bi-envelope me-1"></i> Correo electrónico
            </label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                placeholder="usuario@restaurantepro.pe"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
                autofocus
            >
        </div>

        <div class="mb-4">
            <label class="form-label" for="password">
                <i class="bi bi-lock me-1"></i> Contraseña
            </label>
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="••••••••"
                    required
                >
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword()"
                    title="Mostrar contraseña"
                    tabindex="-1"
                >
                    <i class="bi bi-eye" id="ojo"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login btn">
            <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al sistema
        </button>

    </form>

    <div class="version">RestaurantePro v1.0 &nbsp;·&nbsp; Perú</div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const ojo   = document.getElementById('ojo');
    if (input.type === 'password') {
        input.type = 'text';
        ojo.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        ojo.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>