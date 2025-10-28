<?php

require_once __DIR__ . '/../app/models/Proyecto.php';
require_once __DIR__ . '/../app/models/Usuario.php';

class CrearProyectoTest extends PHPUnit\Framework\TestCase
{
    private $pdo;
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
    }

    public function testProyectoCreadoCorrectamente()
    {
        // Given que el usuario está autenticado
        // Crear un usuario de prueba
        $this->usuarioModel->crear('Test User', 'test@example.com', 'password123');
        $usuario = $this->usuarioModel->obtenerPorEmail('test@example.com');
        $usuarioId = $usuario['id'];

        // When completa el formulario de nuevo proyecto con datos válidos
        $nombreProyecto = 'Proyecto de Prueba';
        $descripcionProyecto = 'Descripción del proyecto de prueba';
        $result = $this->proyectoModel->crear($nombreProyecto, $descripcionProyecto, $usuarioId);

        // Then el sistema guarda el proyecto en la base de datos
        $this->assertTrue($result, 'El proyecto debería crearse exitosamente');

        // And el nuevo proyecto aparece en el listado del usuario
        $proyectos = $this->proyectoModel->obtenerPorUsuario($usuarioId);
        $this->assertCount(1, $proyectos, 'Debería haber exactamente un proyecto para el usuario');

        $proyecto = $proyectos[0];
        $this->assertEquals($nombreProyecto, $proyecto['nombre'], 'El nombre del proyecto debería coincidir');
        $this->assertEquals($descripcionProyecto, $proyecto['descripcion'], 'La descripción del proyecto debería coincidir');
        $this->assertEquals($usuarioId, $proyecto['usuario_id'], 'El ID del usuario debería coincidir');
        $this->assertArrayHasKey('id', $proyecto, 'El proyecto debería tener un ID');
        $this->assertArrayHasKey('fecha_creacion', $proyecto, 'El proyecto debería tener fecha de creación');
    }
}