<?php
// Punto de entrada principal para TaskFlow
// Inicia la sesión y enruta las solicitudes

error_log("Public index: Acceso a public/index.php - REQUEST_URI: " . $_SERVER['REQUEST_URI'] . ", SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
session_start();

require_once __DIR__ . '/../config.php';

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
    } else {
        error_log("Public index: Redirigiendo a http://localhost/gestor-tareas/app/views/login.php desde action no POST");
        header('Location: http://localhost/gestor-tareas/app/views/login.php');
        exit;
    }
} else {
    error_log("Public index: Redirigiendo a http://localhost/gestor-tareas/app/views/login.php sin action");
    header('Location: http://localhost/gestor-tareas/app/views/login.php');
    exit;
}
?>