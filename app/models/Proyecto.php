<?php
// Modelo Proyecto para TaskFlow
// Maneja operaciones CRUD para proyectos

require_once '../../config.php';

class Proyecto {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Crear proyecto
    public function crear($nombre, $descripcion, $usuario_id) {
        $stmt = $this->pdo->prepare("INSERT INTO proyectos (nombre, descripcion, usuario_id) VALUES (?, ?, ?)");
        return $stmt->execute([$nombre, $descripcion, $usuario_id]);
    }

    // Obtener todos los proyectos de un usuario
    public function obtenerPorUsuario($usuario_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proyectos WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll();
    }

    // Obtener proyecto por ID
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Actualizar proyecto
    public function actualizar($id, $nombre, $descripcion) {
        $stmt = $this->pdo->prepare("UPDATE proyectos SET nombre = ?, descripcion = ? WHERE id = ?");
        return $stmt->execute([$nombre, $descripcion, $id]);
    }

    // Eliminar proyecto
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM proyectos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>