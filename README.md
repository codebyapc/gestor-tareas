# Gestor de Tareas

Un gestor de tareas colaborativo tipo Trello, desarrollado con PHP 8, MySQL y JavaScript. Permite a los usuarios crear proyectos, asignar tareas y gestionar su progreso en tiempo real mediante AJAX.

## Características

- **Autenticación segura**: Registro y login con contraseñas encriptadas (bcrypt).
- **Gestión de proyectos**: CRUD completo para proyectos.
- **Gestión de tareas**: CRUD completo para tareas con estados (pendiente, en progreso, completada).
- **Dashboard interactivo**: Estadísticas y gráficos en tiempo real.
- **Interfaz responsive**: Diseño limpio con CSS Grid y Flexbox.
- **Operaciones AJAX**: Sin recargas de página para una experiencia fluida.
- **Configuración flexible**: Variables de entorno para fácil despliegue en diferentes entornos.

## Tecnologías

- **Backend**: PHP 8 con arquitectura MVC, PDO para MySQL.
- **Frontend**: HTML5, CSS3, JavaScript (Fetch API).
- **Base de datos**: MySQL 5.7+.
- **Control de versiones**: Git.
- **Gestión de dependencias**: Composer (para Dotenv y PHPUnit).

## Instalación y Configuración

### Requisitos

- PHP 8.0 o superior.
- MySQL 5.7 o superior (recomendado 8.0+).
- Servidor web (Apache con mod_rewrite habilitado, recomendado XAMPP/LAMP).
- Composer (requerido para gestión de dependencias).
- Extensiones PHP: pdo, pdo_mysql, mbstring, openssl.

### Instalación Rápida con XAMPP (Recomendado para desarrollo local)

1. **Instalar XAMPP**:
   - Descarga e instala XAMPP desde [apachefriends.org](https://www.apachefriends.org/).
   - Inicia los módulos Apache y MySQL.

2. **Instalar dependencias**:
   - Instala Composer desde [getcomposer.org](https://getcomposer.org/).
   - Abre una terminal en la raíz del proyecto y ejecuta:
     ```bash
     composer install
     ```

3. **Configurar la base de datos**:
   - Abre phpMyAdmin (http://localhost/phpmyadmin).
   - Crea una nueva base de datos llamada `taskflow` (charset: utf8mb4_general_ci).
   - Importa el archivo `database.sql` para crear las tablas y datos iniciales.
   - Alternativamente, puedes ejecutar el script desde la línea de comandos:
     ```bash
     mysql -u root -p taskflow < database.sql
     ```

4. **Configurar el proyecto**:
   - Clona o descarga el proyecto en `htdocs` de XAMPP (ej: `C:\xampp\htdocs\gestor-tareas`).
   - Copia `.env.example` a `.env` y ajusta las variables si es necesario:
     ```
     # Database Configuration
     DB_HOST=localhost
     DB_NAME=taskflow
     DB_USER=root
     DB_PASS=

     # Application Configuration
     APP_URL=http://localhost/gestor-tareas
     API_URL=${APP_URL}/api
     UPLOADS_PATH=storage/uploads
     ```
   - Crea los directorios necesarios:
     ```bash
     mkdir storage
     mkdir storage\uploads
     mkdir logs
     ```
   - Asegúrate de que los directorios `storage/uploads` y `logs` tengan permisos de escritura.

5. **Ejecutar**:
   - Accede a `http://localhost/gestor-tareas/public/` en tu navegador.
   - Regístrate e inicia sesión para usar la aplicación.

### Instalación con Docker (Para entornos de producción o desarrollo avanzado)

1. **Requisitos**: Docker y Docker Compose instalados.

2. **Configurar**:
   - Clona el proyecto.
   - Copia `.env.example` a `.env` y ajusta para Docker (ej: host=mysql, db=taskflow, user=root, pass=password).
   - Crea los directorios necesarios:
     ```bash
     mkdir storage
     mkdir storage\uploads
     mkdir logs
     ```

3. **Ejecutar**:
   ```bash
   docker-compose up -d
   ```
   - Accede a `http://localhost:8080/public/`.

4. **Base de datos**:
   - El contenedor MySQL ejecutará automáticamente el script `database.sql` al iniciar.
   - Si necesitas ejecutar manualmente: conecta al contenedor y ejecuta el script.

### Despliegue en Producción

1. **Configurar servidor**:
   - Sube el proyecto a tu servidor (ej: usando FTP o Git).
   - Asegúrate de que PHP 8+ y MySQL estén instalados.
   - Instala Composer y ejecuta `composer install` en el servidor.

2. **Configurar .env**:
   - Actualiza `APP_URL` con la URL de tu dominio (ej: `https://midominio.com`).
   - Configura las credenciales de la base de datos.
   - Crea los directorios necesarios:
     ```bash
     mkdir storage
     mkdir storage/uploads
     mkdir logs
     ```
   - Asegúrate de que los directorios `storage/` y `logs/` tengan permisos 755.

3. **Base de datos**:
   - Crea la base de datos con charset utf8mb4_general_ci.
   - Importa el archivo `database.sql` para crear las tablas y datos iniciales.

4. **Permisos**:
   - Asegúrate de que los directorios `storage/` y `logs/` tengan permisos de escritura (755).

5. **Acceso**:
   - Accede a tu dominio y registra una cuenta.

## Estructura del Proyecto

```
gestor-tareas/
├── .env.example          # Ejemplo de variables de entorno
├── .gitignore            # Archivos ignorados por Git
├── composer.json         # Dependencias de Composer
├── config.php            # Configuración centralizada
├── database.sql          # Script de base de datos
├── index.php             # Punto de entrada principal
├── requirements.md       # Requisitos funcionales
├── app/
│   ├── controllers/      # Lógica de controladores
│   │   ├── AuthController.php
│   │   ├── ProyectoController.php
│   │   └── TareaController.php
│   ├── models/           # Modelos de datos
│   │   ├── Proyecto.php
│   │   ├── Tarea.php
│   │   └── Usuario.php
│   └── views/            # Vistas HTML
│       ├── dashboard.php
│       ├── login.php
│       └── registro.php
├── api/                  # API REST
│   ├── .htaccess
│   └── index.php
├── public/               # Archivos públicos
│   ├── .htaccess
│   ├── index.php
│   ├── css/
│   │   └── styles.css
│   └── js/
│       └── dashboard.js
├── storage/              # Archivos subidos
│   └── uploads/
├── logs/                 # Logs de errores
├── tests/                # Pruebas unitarias
└── vendor/               # Dependencias de Composer (generado)
```

## Uso

1. **Registro/Login**: Crea una cuenta o inicia sesión.
2. **Proyectos**: Crea proyectos y gestiona tareas.
3. **Tareas**: Asigna, actualiza estados y elimina tareas.
4. **Dashboard**: Visualiza estadísticas y progreso.

## Configuración de Variables de Entorno

El proyecto usa un archivo `.env` para configuraciones flexibles:

- **DB_HOST**: Host de la base de datos (ej: localhost).
- **DB_NAME**: Nombre de la base de datos (ej: taskflow).
- **DB_USER**: Usuario de la base de datos.
- **DB_PASS**: Contraseña de la base de datos.
- **APP_URL**: URL base de la aplicación (ej: http://localhost/gestor-tareas).
- **API_URL**: URL de la API (generalmente ${APP_URL}/api).
- **UPLOADS_PATH**: Ruta para archivos subidos.

Si no usas `.env`, el proyecto usa valores por defecto en `config.php`.

## Contribución

1. Fork el proyecto.
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcion`).
3. Commit tus cambios (`git commit -am 'Agrega nueva funcion'`).
4. Push a la rama (`git push origin feature/nueva-funcion`).
5. Abre un Pull Request.

## Troubleshooting

### Problemas comunes y soluciones

1. **Error: "Composer autoload no encontrado"**
   - Solución: Ejecuta `composer install` en la raíz del proyecto.

2. **Error de conexión a la base de datos**
   - Verifica que las credenciales en `.env` sean correctas.
   - Asegúrate de que MySQL esté ejecutándose.
   - Comprueba que la base de datos `taskflow` existe.

3. **Error 500 al acceder a la aplicación**
   - Verifica que el archivo `.env` existe y contiene todas las variables requeridas.
   - Comprueba los permisos de los directorios `storage/` y `logs/`.
   - Revisa los logs de errores en `logs/` o los logs de Apache/PHP.

4. **Página en blanco o errores de rutas**
   - Asegúrate de que `mod_rewrite` esté habilitado en Apache.
   - Verifica que la URL en `APP_URL` coincida con la ruta real del proyecto.

5. **Problemas con AJAX**
   - Verifica que `API_URL` esté configurada correctamente en `.env`.
   - Comprueba que no haya errores de CORS (aunque debería estar configurado).

6. **No se pueden subir archivos**
   - Verifica que el directorio `storage/uploads` existe y tiene permisos de escritura.
   - Comprueba el límite de tamaño de archivos en `php.ini`.

### Comandos útiles para desarrollo

```bash
# Limpiar cache de Composer
composer clear-cache

# Actualizar dependencias
composer update

# Ejecutar pruebas (si están configuradas)
./vendor/bin/phpunit

# Ver logs en tiempo real
tail -f logs/error.log
```

## Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## Soporte y Contribución

Si encuentras algún problema o tienes sugerencias para mejorar el proyecto:

1. Revisa la sección de [Troubleshooting](#troubleshooting) para problemas comunes.
2. Crea un issue en el repositorio de GitHub con detalles del problema.
3. Para contribuciones, sigue las guías estándar de GitHub.

## Autor

Desarrollado por code by apc para gestor de tareas.