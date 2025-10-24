<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - TaskFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo str_replace('/app/views', '', dirname($_SERVER['SCRIPT_NAME'])); ?>/public/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>TaskFlow</h1>
        <div class="form-container">
            <h2>Registrarse</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form action="http://localhost/gestor-tareas/public/index.php?action=registro" method="POST">
                <input type="hidden" name="csrf_token" value="<?php
                    require_once __DIR__ . '/../controllers/AuthController.php';
                    $auth = new AuthController();
                    echo $auth->getCsrfToken();
                ?>">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Registrarse</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="/app/views/login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>