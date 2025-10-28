<?php
// Controlador de Administración para TaskFlow
// Maneja la aprobación de cuentas de usuario

require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__) . '/models/Usuario.php';

class AdminController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Mostrar panel de administración
    public function panel() {
        // Verificar que el usuario esté logueado y sea admin
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Por ahora, asumimos que el primer usuario registrado es admin
        // En producción, se debería tener un campo 'rol' en la tabla usuarios
        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);
        if (!$usuario || $usuario['id'] != 1) { // Solo el usuario con ID 1 es admin
            $_SESSION['error'] = 'Acceso denegado. No tienes permisos de administrador.';
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Obtener usuarios pendientes
        $pendientes = $this->usuarioModel->obtenerPendientes();
        $todos = $this->usuarioModel->obtenerTodos();

        require_once dirname(__DIR__) . '/views/admin.php';
    }

    // Aprobar cuenta de usuario
    public function aprobarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Error de seguridad.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        if (!$userId) {
            $_SESSION['error'] = 'ID de usuario inválido.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        if ($this->usuarioModel->activar($userId)) {
            $_SESSION['success'] = 'Usuario aprobado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al aprobar el usuario.';
        }

        header('Location: ' . APP_URL . '/admin');
        exit;
    }

    // Desactivar cuenta de usuario
    public function desactivarUsuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Error de seguridad.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        if (!$userId) {
            $_SESSION['error'] = 'ID de usuario inválido.';
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        if ($this->usuarioModel->desactivar($userId)) {
            $_SESSION['success'] = 'Usuario desactivado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al desactivar el usuario.';
        }

        header('Location: ' . APP_URL . '/admin');
        exit;
    }

    // Generar CSRF token
    private function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Obtener CSRF token para formularios
    public function getCsrfToken() {
        return $this->generateCsrfToken();
    }
}
?>