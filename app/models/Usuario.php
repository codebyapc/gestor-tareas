<?php
// Modelo Usuario para TaskFlow
// Maneja operaciones CRUD para usuarios

require_once '../../config.php';

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
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
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