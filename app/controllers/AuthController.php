<?php
// Controlador de Autenticación para TaskFlow
// Maneja registro, login y logout

require_once '../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Procesar registro
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action']) && $_GET['action'] === 'registro') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            // Validaciones
            if (empty($nombre) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Todos los campos son obligatorios.';
                header('Location: ../views/registro.php');
                exit;
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Las contraseñas no coinciden.';
                header('Location: ../views/registro.php');
                exit;
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
                header('Location: ../views/registro.php');
                exit;
            }

            // Verificar si el email ya existe
            if ($this->usuarioModel->obtenerPorEmail($email)) {
                $_SESSION['error'] = 'El email ya está registrado.';
                header('Location: ../views/registro.php');
                exit;
            }

            // Crear usuario
            if ($this->usuarioModel->crear($nombre, $email, $password)) {
                $_SESSION['success'] = 'Registro exitoso. Inicia sesión.';
                header('Location: ../views/login.php');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar. Intenta de nuevo.';
                header('Location: ../views/registro.php');
                exit;
            }
        }
    }

    // Procesar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action']) && $_GET['action'] === 'login') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            // Validaciones
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Email y contraseña son obligatorios.';
                header('Location: ../views/login.php');
                exit;
            }

            // Obtener usuario
            $usuario = $this->usuarioModel->obtenerPorEmail($email);
            if ($usuario && $this->usuarioModel->verificarPassword($password, $usuario['password'])) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                header('Location: ../views/dashboard.php');
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas.';
                header('Location: ../views/login.php');
                exit;
            }
        }
    }

    // Logout
    public function logout() {
        session_destroy();
        header('Location: ../views/login.php');
        exit;
    }
}
?>