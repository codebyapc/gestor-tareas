<?php
// Modelo Usuario para TaskFlow
// Maneja operaciones CRUD para usuarios

require_once __DIR__ . '/../../config.php';

class Usuario {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Crear usuario (registro)
    public function crear($nombre, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
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
}
?>