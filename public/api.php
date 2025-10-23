<?php
// API Router para TaskFlow
// Maneja solicitudes AJAX para CRUD

session_start();
require_once '../config.php';

// Verificar autenticación para APIs
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($request) {
    case '/api/proyectos':
        require_once '../app/controllers/ProyectoController.php';
        $controller = new ProyectoController();
        if ($method === 'GET') {
            $controller->obtenerProyectos();
        } elseif ($method === 'POST') {
            $controller->crear();
        }
        break;
    case '/api/proyectos/update':
        require_once '../app/controllers/ProyectoController.php';
        $controller = new ProyectoController();
        $controller->actualizar();
        break;
    case '/api/proyectos/delete':
        require_once '../app/controllers/ProyectoController.php';
        $controller = new ProyectoController();
        $controller->eliminar();
        break;
    case '/api/tareas':
        require_once '../app/controllers/TareaController.php';
        $controller = new TareaController();
        if ($method === 'GET') {
            $controller->obtenerPorProyecto();
        } elseif ($method === 'POST') {
            $controller->crear();
        }
        break;
    case '/api/tareas/update':
        require_once '../app/controllers/TareaController.php';
        $controller = new TareaController();
        $controller->actualizar();
        break;
    case '/api/tareas/delete':
        require_once '../app/controllers/TareaController.php';
        $controller = new TareaController();
        $controller->eliminar();
        break;
    case '/api/estadisticas':
        require_once '../app/controllers/TareaController.php';
        $controller = new TareaController();
        $controller->obtenerEstadisticas();
        break;
    default:
        echo json_encode(['error' => 'Ruta no encontrada']);
}
?>