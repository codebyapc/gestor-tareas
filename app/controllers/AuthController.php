<?php
// Controlador de Autenticación para TaskFlow
// Maneja registro, login y logout

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    // Procesar registro
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("AuthController registro: No es POST, redirigiendo a http://localhost/gestor-tareas/app/views/registro.php");
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            error_log("AuthController registro: CSRF token inválido");
            $_SESSION['error'] = 'Error de seguridad. Intenta de nuevo.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validaciones
        if (empty($nombre) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Formato de email inválido.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        if (strlen($nombre) < 2 || strlen($nombre) > 50) {
            $_SESSION['error'] = 'El nombre debe tener entre 2 y 50 caracteres.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        // Verificar si el email ya existe
        if ($this->usuarioModel->obtenerPorEmail($email)) {
            $_SESSION['error'] = 'El email ya está registrado.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }

        // Crear usuario
        if ($this->usuarioModel->crear($nombre, $email, $password)) {
            $_SESSION['success'] = 'Registro exitoso. Inicia sesión.';
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        } else {
            $_SESSION['error'] = 'Error al registrar. Intenta de nuevo.';
            header('Location: http://localhost/gestor-tareas/app/views/registro.php');
            exit;
        }
    }

    // Procesar login
    public function login() {
        error_log("AuthController: Método login iniciado");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("AuthController login: No es POST, redirigiendo a http://localhost/gestor-tareas/app/views/login.php");
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        }
        // Verificar CSRF token
        error_log("AuthController: Verificando CSRF token");
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            error_log("AuthController login: CSRF token inválido, redirigiendo a http://localhost/gestor-tareas/app/views/login.php");
            $_SESSION['error'] = 'Error de seguridad. Intenta de nuevo.';
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        error_log("AuthController: Email recibido: $email");

        // Validaciones
        if (empty($email) || empty($password)) {
            error_log("AuthController: Campos vacíos");
            $_SESSION['error'] = 'Email y contraseña son obligatorios.';
            error_log("AuthController logout: Redirigiendo a http://localhost/gestor-tareas/app/views/login.php");
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("AuthController: Email inválido: $email");
            $_SESSION['error'] = 'Formato de email inválido.';
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        }

        // Rate limiting: verificar intentos fallidos
        error_log("AuthController: Verificando intentos para $email");
        $attempts = $this->checkLoginAttempts($email);
        if ($attempts >= 5) {
            error_log("AuthController: Demasiados intentos: $attempts");
            $_SESSION['error'] = 'Demasiados intentos fallidos. Intenta más tarde.';
            header('Location: http://localhost/gestor-tareas/app/views/login.php');
            exit;
        }

        // Obtener usuario
        error_log("AuthController: Obteniendo usuario para $email");
        $usuario = $this->usuarioModel->obtenerPorEmail($email);
        if ($usuario) {
            error_log("AuthController: Usuario encontrado: " . $usuario['id']);
            if ($this->usuarioModel->verificarPassword($password, $usuario['password'])) {
                error_log("AuthController: Contraseña verificada, login exitoso");
                // Login exitoso: limpiar intentos
                $this->clearLoginAttempts($email);
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);
                error_log("AuthController login: Login exitoso, redirigiendo a http://localhost/gestor-tareas/app/views/dashboard.php");
                header('Location: http://localhost/gestor-tareas/app/views/dashboard.php');
                exit;
            } else {
                error_log("AuthController: Contraseña incorrecta");
            }
        } else {
            error_log("AuthController: Usuario no encontrado");
        }
        // Login fallido: registrar intento
        $this->recordLoginAttempt($email);
        error_log("AuthController login: Credenciales incorrectas, redirigiendo a http://localhost/gestor-tareas/app/views/login.php");
        $_SESSION['error'] = 'Credenciales incorrectas.';
        header('Location: http://localhost/gestor-tareas/app/views/login.php');
        exit;
    }

    // Logout
    public function logout() {
        // Limpiar sesión de forma segura
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: http://localhost/gestor-tareas/app/views/login.php');
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

    // Rate limiting para login
    private function checkLoginAttempts($email) {
        $file = '../logs/login_attempts_' . md5($email) . '.txt';
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['last_attempt'] > 3600) { // Reset after 1 hour
                unlink($file);
                return 0;
            }
            return $data['attempts'];
        }
        return 0;
    }

    private function recordLoginAttempt($email) {
        $file = '../logs/login_attempts_' . md5($email) . '.txt';
        $attempts = $this->checkLoginAttempts($email) + 1;
        $data = [
            'attempts' => $attempts,
            'last_attempt' => time()
        ];
        file_put_contents($file, json_encode($data));
    }

    private function clearLoginAttempts($email) {
        $file = '../logs/login_attempts_' . md5($email) . '.txt';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
?>