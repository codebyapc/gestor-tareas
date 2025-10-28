<?php

require_once __DIR__ . '/../app/models/Tarea.php';
require_once __DIR__ . '/../app/models/Proyecto.php';
require_once __DIR__ . '/../app/models/Usuario.php';

class CrearTareaTest extends PHPUnit\Framework\TestCase
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

    public function testTareaCreadaCorrectamente()
    {
        // Given que el usuario tiene un proyecto seleccionado
        // Crear un usuario de prueba
        $this->usuarioModel->crear('Test User', 'test@example.com', 'password123');
        $usuario = $this->usuarioModel->obtenerPorEmail('test@example.com');
        $usuarioId = $usuario['id'];

        // Crear un proyecto de prueba
        $this->proyectoModel->crear('Proyecto de Prueba', 'Descripción del proyecto', $usuarioId);
        $proyectos = $this->proyectoModel->obtenerPorUsuario($usuarioId);
        $proyectoId = $proyectos[0]['id'];

        // When introduce los datos de una nueva tarea válidos
        $tituloTarea = 'Tarea de Prueba';
        $descripcionTarea = 'Descripción de la tarea de prueba';
        $estadoTarea = 'pendiente';
        $fechaVencimiento = '2025-12-31';
        $result = $this->tareaModel->crear($tituloTarea, $descripcionTarea, $estadoTarea, $usuarioId, $proyectoId, $fechaVencimiento);

        // Then el sistema guarda la tarea en la base de datos
        $this->assertTrue($result, 'La tarea debería crearse exitosamente');

        // And la tarea aparece en la lista de tareas del proyecto
        $tareas = $this->tareaModel->obtenerPorProyecto($proyectoId);
        $this->assertCount(1, $tareas, 'Debería haber exactamente una tarea para el proyecto');

        $tarea = $tareas[0];
        $this->assertEquals($tituloTarea, $tarea['titulo'], 'El título de la tarea debería coincidir');
        $this->assertEquals($descripcionTarea, $tarea['descripcion'], 'La descripción de la tarea debería coincidir');
        $this->assertEquals($estadoTarea, $tarea['estado'], 'El estado de la tarea debería coincidir');
        $this->assertEquals($usuarioId, $tarea['usuario_id'], 'El ID del usuario debería coincidir');
        $this->assertEquals($proyectoId, $tarea['proyecto_id'], 'El ID del proyecto debería coincidir');
        $this->assertEquals($fechaVencimiento, $tarea['fecha_vencimiento'], 'La fecha de vencimiento debería coincidir');
        $this->assertArrayHasKey('id', $tarea, 'La tarea debería tener un ID');
        $this->assertArrayHasKey('fecha_creacion', $tarea, 'La tarea debería tener fecha de creación');
    }
}