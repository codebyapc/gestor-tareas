<?php
// API Router para TaskFlow
// Maneja solicitudes AJAX para CRUD

// Agregar logging para depuración
error_log("API accessed: " . $_SERVER['REQUEST_URI'] . " Method: " . $_SERVER['REQUEST_METHOD']);

session_start();
require_once dirname(__DIR__) . '/config.php';

// Verificar autenticación para APIs
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$request = str_replace('/gestor-tareas/api', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$method = $_SERVER['REQUEST_METHOD'];

error_log("Request: $request, Method: $method");

try {
    switch ($request) {
        case '/proyectos':
            require_once dirname(__DIR__) . '/app/controllers/ProyectoController.php';
            $controller = new ProyectoController();
            if ($method === 'GET') {
                $controller->obtenerProyectos();
            } elseif ($method === 'POST') {
                $controller->crear();
            }
            break;
        case '/proyectos/update':
            require_once dirname(__DIR__) . '/app/controllers/ProyectoController.php';
            $controller = new ProyectoController();
            $controller->actualizar();
            break;
        case '/proyectos/delete':
            require_once dirname(__DIR__) . '/app/controllers/ProyectoController.php';
            $controller = new ProyectoController();
            $controller->eliminar();
            break;
        case '/tareas':
            require_once dirname(__DIR__) . '/app/controllers/TareaController.php';
            $controller = new TareaController();
            if ($method === 'GET') {
                $controller->obtenerPorProyecto();
            } elseif ($method === 'POST') {
                $controller->crear();
            }
            break;
        case '/tareas/update':
            require_once dirname(__DIR__) . '/app/controllers/TareaController.php';
            $controller = new TareaController();
            $controller->actualizar();
            break;
        case '/tareas/delete':
            require_once dirname(__DIR__) . '/app/controllers/TareaController.php';
            $controller = new TareaController();
            $controller->eliminar();
            break;
        case '/estadisticas':
            require_once dirname(__DIR__) . '/app/controllers/TareaController.php';
            $controller = new TareaController();
            $controller->obtenerEstadisticas();
            break;
        case '/estadisticas/usuario':
            require_once dirname(__DIR__) . '/app/controllers/TareaController.php';
            $controller = new TareaController();
            $controller->obtenerEstadisticasPorUsuario();
            break;
        default:
            echo json_encode(['error' => 'Ruta no encontrada']);
    }
} catch (Throwable $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor']);
    exit;
}
?>