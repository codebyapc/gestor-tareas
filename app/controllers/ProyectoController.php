<?php
// Controlador de Proyectos para TaskFlow
// Maneja operaciones CRUD para proyectos

require_once '../models/Proyecto.php';

class ProyectoController {
    private $proyectoModel;

    public function __construct() {
        $this->proyectoModel = new Proyecto();
    }

    // Obtener todos los proyectos del usuario
    public function obtenerProyectos() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'No autenticado']);
            return;
        }
        $proyectos = $this->proyectoModel->obtenerPorUsuario($_SESSION['user_id']);
        echo json_encode($proyectos);
    }

    // Crear proyecto
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);

            if (empty($nombre)) {
                echo json_encode(['error' => 'Nombre es obligatorio']);
                return;
            }

            if ($this->proyectoModel->crear($nombre, $descripcion, $_SESSION['user_id'])) {
                echo json_encode(['success' => 'Proyecto creado']);
            } else {
                echo json_encode(['error' => 'Error al crear proyecto']);
            }
        }
    }

    // Actualizar proyecto
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);

            if (!$id || empty($nombre)) {
                echo json_encode(['error' => 'ID y nombre son obligatorios']);
                return;
            }

            if ($this->proyectoModel->actualizar($id, $nombre, $descripcion)) {
                echo json_encode(['success' => 'Proyecto actualizado']);
            } else {
                echo json_encode(['error' => 'Error al actualizar proyecto']);
            }
        }
    }

    // Eliminar proyecto
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if (!$id) {
                echo json_encode(['error' => 'ID es obligatorio']);
                return;
            }

            if ($this->proyectoModel->eliminar($id)) {
                echo json_encode(['success' => 'Proyecto eliminado']);
            } else {
                echo json_encode(['error' => 'Error al eliminar proyecto']);
            }
        }
    }
}
?>