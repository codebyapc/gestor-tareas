<?php
// Modelo Usuario para TaskFlow
// Maneja operaciones CRUD para usuarios

try {
    require_once __DIR__ . '/../../config.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al cargar configuración de base de datos',
        'detalle' => $e->getMessage()
    ]);
    exit;
}

class Usuario {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Crear usuario (registro)
    public function crear($nombre, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password, activo) VALUES (?, ?, ?, 0)");
        return $stmt->execute([$nombre, $email, $hashedPassword]);
    }

    // Obtener usuario por email (login)
    public function obtenerPorEmail($email) {
        error_log("UsuarioModel: Buscando usuario por email: $email");
        $query = "SELECT * FROM usuarios WHERE email = ?";
        error_log("UsuarioModel: Query preparada: $query");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$email]);
        $rowCount = $stmt->rowCount();
        error_log("UsuarioModel: Número de filas devueltas: $rowCount");
        $result = $stmt->fetch();
        error_log("UsuarioModel: Usuario encontrado: " . ($result ? json_encode($result) : 'null'));
        return $result;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Verificar contraseña
    public function verificarPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    // Activar usuario
    public function activar($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Desactivar usuario
    public function desactivar($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Obtener usuarios pendientes de aprobación
    public function obtenerPendientes() {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE activo = 0 ORDER BY fecha_creacion DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>