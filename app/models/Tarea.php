<?php
// Modelo Tarea para TaskFlow
// Maneja operaciones CRUD para tareas

require_once '../../config.php';

class Tarea {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Crear tarea
    public function crear($titulo, $descripcion, $estado, $usuario_id, $proyecto_id, $fecha_vencimiento) {
        $stmt = $this->pdo->prepare("INSERT INTO tareas (titulo, descripcion, estado, usuario_id, proyecto_id, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$titulo, $descripcion, $estado, $usuario_id, $proyecto_id, $fecha_vencimiento]);
    }

    // Obtener tareas por proyecto
    public function obtenerPorProyecto($proyecto_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tareas WHERE proyecto_id = ? ORDER BY fecha_creacion DESC");
        $stmt->execute([$proyecto_id]);
        return $stmt->fetchAll();
    }

    // Obtener tarea por ID
    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tareas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Actualizar tarea
    public function actualizar($id, $titulo, $descripcion, $estado, $usuario_id, $fecha_vencimiento) {
        $stmt = $this->pdo->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, estado = ?, usuario_id = ?, fecha_vencimiento = ? WHERE id = ?");
        return $stmt->execute([$titulo, $descripcion, $estado, $usuario_id, $fecha_vencimiento, $id]);
    }

    // Eliminar tarea
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tareas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Obtener estadísticas de tareas por estado
    public function obtenerEstadisticas() {
        $stmt = $this->pdo->prepare("SELECT estado, COUNT(*) as count FROM tareas GROUP BY estado");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>