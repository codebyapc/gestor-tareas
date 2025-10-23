# TaskFlow

Un gestor de tareas colaborativo tipo Trello, desarrollado con PHP 8, MySQL y JavaScript. Permite a los usuarios crear proyectos, asignar tareas y gestionar su progreso en tiempo real mediante AJAX.

## Características

- **Autenticación segura**: Registro y login con contraseñas encriptadas (bcrypt).
- **Gestión de proyectos**: CRUD completo para proyectos.
- **Gestión de tareas**: CRUD completo para tareas con estados (pendiente, en progreso, completada).
- **Dashboard interactivo**: Estadísticas y gráficos en tiempo real.
- **Interfaz responsive**: Diseño limpio con CSS Grid y Flexbox.
- **Operaciones AJAX**: Sin recargas de página para una experiencia fluida.

## Tecnologías

- **Backend**: PHP 8 con arquitectura MVC, PDO para MySQL.
- **Frontend**: HTML5, CSS3, JavaScript (Fetch API).
- **Base de datos**: MySQL.
- **Control de versiones**: Git.

## Instalación y Configuración

### Requisitos

- PHP 8.0 o superior.
- MySQL 5.7 o superior.
- Servidor web (Apache recomendado, como XAMPP).

### Instalación con XAMPP (Recomendado para desarrollo local)

1. **Instalar XAMPP**:
   - Descarga e instala XAMPP desde [apachefriends.org](https://www.apachefriends.org/).
   - Inicia los módulos Apache y MySQL.

2. **Configurar la base de datos**:
   - Abre phpMyAdmin (http://localhost/phpmyadmin).
   - Crea una nueva base de datos llamada `taskflow`.
   - Ejecuta el script SQL desde `database.sql` para crear las tablas.

3. **Configurar el proyecto**:
   - Clona o descarga el proyecto en `htdocs` de XAMPP (ej: `C:\xampp\htdocs\taskflow`).
   - Ajusta `config.php` si es necesario (por defecto: host=localhost, db=taskflow, user=root, pass=).

4. **Ejecutar**:
   - Accede a `http://localhost/taskflow/public/` en tu navegador.
   - Regístrate e inicia sesión para usar la aplicación.

### Instalación con Docker (Para entornos de producción o desarrollo avanzado)

1. **Requisitos**: Docker y Docker Compose instalados.

2. **Configurar**:
   - Clona el proyecto.
   - Ajusta `config.php` para Docker (ej: host=mysql, db=taskflow, user=root, pass=password).

3. **Ejecutar**:
   ```bash
   docker-compose up -d
   ```
   - Accede a `http://localhost:8080/public/`.

4. **Base de datos**:
   - Ejecuta `database.sql` en el contenedor MySQL.

## Estructura del Proyecto

```
taskflow/
├── app/
│   ├── controllers/     # Lógica de controladores
│   ├── models/          # Modelos de datos
│   └── views/           # Vistas HTML
├── public/              # Archivos públicos (CSS, JS, API)
│   ├── css/
│   ├── js/
│   ├── index.php        # Punto de entrada
│   └── api.php          # API para AJAX
├── config.php           # Configuración de DB
├── database.sql         # Script de base de datos
└── README.md
```

## Uso

1. **Registro/Login**: Crea una cuenta o inicia sesión.
2. **Proyectos**: Crea proyectos y gestiona tareas.
3. **Tareas**: Asigna, actualiza estados y elimina tareas.
4. **Dashboard**: Visualiza estadísticas y progreso.

## Contribución

1. Fork el proyecto.
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcion`).
3. Commit tus cambios (`git commit -am 'Agrega nueva funcion'`).
4. Push a la rama (`git push origin feature/nueva-funcion`).
5. Abre un Pull Request.

## Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## Autor

Desarrollado por code by apc para gestor de tareas.