<?php
session_start();
error_log("Login view: Acceso a login.php - REQUEST_URI: " . $_SERVER['REQUEST_URI'] . ", SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo str_replace('/app/views', '', dirname($_SERVER['SCRIPT_NAME'])); ?>/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>TaskFlow</h1>
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            <?php
            error_log("Login view: Acceso a login.php, error: " . (isset($_SESSION['error']) ? $_SESSION['error'] : 'none'));
            if (isset($_SESSION['error'])): ?>
                            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form action="http://localhost/gestor-tareas/public/index.php?action=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?php
                    require_once __DIR__ . '/../controllers/AuthController.php';
                    $auth = new AuthController();
                    echo $auth->getCsrfToken();
                ?>">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
            <p>¿No tienes cuenta? <a href="/app/views/registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>