<?php

require_once __DIR__ . '/../app/models/Usuario.php';

class RegistroUsuarioTest extends PHPUnit\Framework\TestCase
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
            password TEXT NOT NULL,
            activo INTEGER DEFAULT 0,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Inyectar PDO en Usuario
        $this->usuarioModel = new Usuario();
        $reflectionUsuario = new ReflectionClass($this->usuarioModel);
        $propertyUsuario = $reflectionUsuario->getProperty('pdo');
        $propertyUsuario->setAccessible(true);
        $propertyUsuario->setValue($this->usuarioModel, $this->pdo);
    }

    public function testRegistroUsuarioExitoso()
    {
        // Given que un usuario accede al formulario de registro
        $nombre = 'Juan Pérez';
        $email = 'juan@example.com';
        $password = 'password123';

        // When introduce datos válidos y envía el formulario
        $result = $this->usuarioModel->crear($nombre, $email, $password);

        // Then se crea un nuevo usuario en la base de datos con la contraseña encriptada
        $this->assertTrue($result, 'El usuario debería crearse exitosamente');

        // Verificar que el usuario se guardó en la base de datos
        $usuario = $this->usuarioModel->obtenerPorEmail($email);
        $this->assertNotNull($usuario, 'El usuario debería existir en la base de datos');
        $this->assertEquals($nombre, $usuario['nombre'], 'El nombre debería coincidir');
        $this->assertEquals($email, $usuario['email'], 'El email debería coincidir');
        $this->assertEquals(0, $usuario['activo'], 'El usuario debería estar inactivo por defecto');

        // Verificar que la contraseña está encriptada
        $this->assertNotEquals($password, $usuario['password'], 'La contraseña debería estar encriptada');
        $this->assertTrue(password_verify($password, $usuario['password']), 'La contraseña debería verificarse correctamente');

        // And el sistema muestra un mensaje de confirmación o redirige al login
        // (Esta parte se probaría en tests de integración con el controlador)
    }
}