<?php
// Punto de entrada principal para TaskFlow
// Inicia la sesión y enruta las solicitudes

error_log("Public index: Acceso a public/index.php - REQUEST_URI: " . $_SERVER['REQUEST_URI'] . ", SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . ", QUERY_STRING: " . $_SERVER['QUERY_STRING']);
session_start();

require_once __DIR__ . '/../config.php';

// Nota: Las variables de configuración se pasan directamente en dashboard.php para evitar problemas de timing

// Lógica de enrutamiento básica
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    error_log("Public index: Acción recibida: $action");
    if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
    } elseif ($action === 'registro' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->registro();
    } elseif ($action === 'logout') {
        require_once '../app/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
    } elseif ($action === 'admin') {
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->panel();
    } elseif ($action === 'aprobar_usuario' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->aprobarUsuario();
    } elseif ($action === 'desactivar_usuario' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once '../app/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->desactivarUsuario();
    } else {
        error_log("Public index: Redirigiendo a " . APP_URL . "/app/views/login.php desde action no POST");
        header('Location: ' . APP_URL . '/app/views/login.php');
        exit;
    }
} else {
    error_log("Public index: Redirigiendo a " . APP_URL . "/app/views/login.php sin action");
    header('Location: ' . APP_URL . '/app/views/login.php');
    exit;
}
?>