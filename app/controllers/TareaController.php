<?php
// Controlador de Tareas para TaskFlow
// Maneja operaciones CRUD para tareas

require_once '../models/Tarea.php';

class TareaController {
    private $tareaModel;

    public function __construct() {
        $this->tareaModel = new Tarea();
    }

    // Obtener tareas por proyecto
    public function obtenerPorProyecto() {
        $proyecto_id = filter_input(INPUT_GET, 'proyecto_id', FILTER_VALIDATE_INT);
        if (!$proyecto_id) {
            echo json_encode(['error' => 'Proyecto ID es obligatorio']);
            return;
        }
        $tareas = $this->tareaModel->obtenerPorProyecto($proyecto_id);
        echo json_encode($tareas);
    }

    // Crear tarea
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
            $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
            $proyecto_id = filter_input(INPUT_POST, 'proyecto_id', FILTER_VALIDATE_INT);
            $fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento', FILTER_SANITIZE_STRING);

            if (empty($titulo) || !$proyecto_id) {
                echo json_encode(['error' => 'Título y proyecto son obligatorios']);
                return;
            }

            if ($this->tareaModel->crear($titulo, $descripcion, $estado, $usuario_id, $proyecto_id, $fecha_vencimiento)) {
                echo json_encode(['success' => 'Tarea creada']);
            } else {
                echo json_encode(['error' => 'Error al crear tarea']);
            }
        }
    }

    // Actualizar tarea
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
            $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
            $fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento', FILTER_SANITIZE_STRING);

            if (!$id || empty($titulo)) {
                echo json_encode(['error' => 'ID y título son obligatorios']);
                return;
            }

            if ($this->tareaModel->actualizar($id, $titulo, $descripcion, $estado, $usuario_id, $fecha_vencimiento)) {
                echo json_encode(['success' => 'Tarea actualizada']);
            } else {
                echo json_encode(['error' => 'Error al actualizar tarea']);
            }
        }
    }

    // Eliminar tarea
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode(['error' => 'ID es obligatorio']);
                return;
            }

            if ($this->tareaModel->eliminar($id)) {
                echo json_encode(['success' => 'Tarea eliminada']);
            } else {
                echo json_encode(['error' => 'Error al eliminar tarea']);
            }
        }
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        $estadisticas = $this->tareaModel->obtenerEstadisticas();
        echo json_encode($estadisticas);
    }
}
?>