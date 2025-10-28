<?php

require_once __DIR__ . '/../app/models/Tarea.php';
require_once __DIR__ . '/../app/models/Proyecto.php';
require_once __DIR__ . '/../app/models/Usuario.php';

class EditarTareaTest extends PHPUnit\Framework\TestCase
{
    private $pdo;
    private $tareaModel;
    private $proyectoModel;
    private $usuarioModel;

    protected function setUp(): void
    {
        // Crear DB en memoria
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla usuarios
        $this->pdo->exec("CREATE TABLE usuarios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            activo INTEGER DEFAULT 0,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Crear tabla proyectos
        $this->pdo->exec("CREATE TABLE proyectos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            descripcion TEXT,
            usuario_id INTEGER NOT NULL,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        )");

        // Crear tabla tareas
        $this->pdo->exec("CREATE TABLE tareas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL,
            descripcion TEXT,
            estado TEXT DEFAULT 'pendiente',
            usuario_id INTEGER,
            proyecto_id INTEGER NOT NULL,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_vencimiento DATE,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE
        )");

        // Inyectar PDO en Usuario
        $this->usuarioModel = new Usuario();
        $reflectionUsuario = new ReflectionClass($this->usuarioModel);
        $propertyUsuario = $reflectionUsuario->getProperty('pdo');
        $propertyUsuario->setAccessible(true);
        $propertyUsuario->setValue($this->usuarioModel, $this->pdo);

        // Inyectar PDO en Proyecto
        $this->proyectoModel = new Proyecto();
        $reflectionProyecto = new ReflectionClass($this->proyectoModel);
        $propertyProyecto = $reflectionProyecto->getProperty('pdo');
        $propertyProyecto->setAccessible(true);
        $propertyProyecto->setValue($this->proyectoModel, $this->pdo);

        // Inyectar PDO en Tarea
        $this->tareaModel = new Tarea();
        $reflectionTarea = new ReflectionClass($this->tareaModel);
        $propertyTarea = $reflectionTarea->getProperty('pdo');
        $propertyTarea->setAccessible(true);
        $propertyTarea->setValue($this->tareaModel, $this->pdo);
    }

    public function testEdicionExitosa()
    {
        // Given que existe una tarea en un proyecto
        // Crear un usuario de prueba
        $this->usuarioModel->crear('Test User', 'test@example.com', 'password123');
        $usuario = $this->usuarioModel->obtenerPorEmail('test@example.com');
        $usuarioId = $usuario['id'];

        // Crear un proyecto de prueba
        $this->proyectoModel->crear('Proyecto de Prueba', 'Descripción del proyecto', $usuarioId);
        $proyectos = $this->proyectoModel->obtenerPorUsuario($usuarioId);
        $proyectoId = $proyectos[0]['id'];

        // Crear una tarea de prueba
        $tituloOriginal = 'Tarea Original';
        $descripcionOriginal = 'Descripción original de la tarea';
        $estadoOriginal = 'pendiente';
        $fechaVencimiento = '2025-12-31';
        $this->tareaModel->crear($tituloOriginal, $descripcionOriginal, $estadoOriginal, $usuarioId, $proyectoId, $fechaVencimiento);

        // Obtener la tarea creada
        $tareas = $this->tareaModel->obtenerPorProyecto($proyectoId);
        $tareaId = $tareas[0]['id'];

        // When el usuario edita su descripción o estado y guarda
        $nuevaDescripcion = 'Descripción actualizada de la tarea';
        $nuevoEstado = 'en_progreso';
        $result = $this->tareaModel->actualizar($tareaId, null, $nuevaDescripcion, $nuevoEstado, null, null);

        // Then el sistema actualiza la información en la base de datos
        $this->assertTrue($result, 'La tarea debería actualizarse exitosamente');

        // Verificar que los cambios se guardaron
        $tareaActualizada = $this->tareaModel->obtenerPorId($tareaId);
        $this->assertEquals($tituloOriginal, $tareaActualizada['titulo'], 'El título debería permanecer igual');
        $this->assertEquals($nuevaDescripcion, $tareaActualizada['descripcion'], 'La descripción debería actualizarse');
        $this->assertEquals($nuevoEstado, $tareaActualizada['estado'], 'El estado debería actualizarse');
        $this->assertEquals($usuarioId, $tareaActualizada['usuario_id'], 'El ID del usuario debería permanecer igual');
        $this->assertEquals($proyectoId, $tareaActualizada['proyecto_id'], 'El ID del proyecto debería permanecer igual');
        $this->assertEquals($fechaVencimiento, $tareaActualizada['fecha_vencimiento'], 'La fecha de vencimiento debería permanecer igual');
    }
}