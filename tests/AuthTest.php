<?php

require_once __DIR__ . '/../app/models/Usuario.php';

class AuthTest extends PHPUnit\Framework\TestCase
{
    private $pdo;
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
            password TEXT NOT NULL
        )");

        // Inyectar PDO en Usuario
        $this->usuarioModel = new Usuario();
        $reflection = new ReflectionClass($this->usuarioModel);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue($this->usuarioModel, $this->pdo);
    }

    public function testRegistroExitoso()
    {
        $result = $this->usuarioModel->crear('Test User', 'test@example.com', 'password123');
        $this->assertTrue($result);

        $user = $this->usuarioModel->obtenerPorEmail('test@example.com');
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['nombre']);
        $this->assertTrue($this->usuarioModel->verificarPassword('password123', $user['password']));
    }

    public function testRegistroConDatosInvalidos()
    {
        // Email duplicado
        $this->usuarioModel->crear('User1', 'test@example.com', 'password123');
        $result = $this->usuarioModel->crear('User2', 'test@example.com', 'password456');
        $this->assertFalse($result);
    }

    public function testEncriptacionContrasena()
    {
        $this->usuarioModel->crear('Test User', 'test@example.com', 'password123');
        $user = $this->usuarioModel->obtenerPorEmail('test@example.com');
        $this->assertNotEquals('password123', $user['password']);
        $this->assertTrue(password_verify('password123', $user['password']));
    }
}