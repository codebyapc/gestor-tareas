<?php
session_start();

// Incluir configuración para pasar variables a JavaScript
require_once dirname(__DIR__, 2) . '/config.php';

// Verificar que las constantes estén definidas
if (!defined('APP_URL')) {
    die('Error: APP_URL no está definido. Verifica la configuración.');
}

if (!isset($_SESSION['user_id'])) {
    header('Location: http://localhost/gestor-tareas/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TaskFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo str_replace('/app/views', '', dirname($_SERVER['SCRIPT_NAME'])); ?>/public/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>TaskFlow</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo $_SESSION['user_name']; ?></span>
                <button id="theme-toggle" class="theme-toggle" title="Cambiar tema">🌙</button>
                <?php if ($_SESSION['user_id'] == 1): // Solo mostrar para admin ?>
                    <a href="<?php echo APP_URL; ?>/admin">Admin Panel</a>
                <?php endif; ?>
                <a href="<?php echo APP_URL; ?>/public/index.php?action=logout">Logout</a>
            </div>
        </header>
        
        <div class="dashboard">
            <div class="sidebar projects-sidebar">
                <div class="sidebar-header">
                    <h2>Proyectos</h2>
                    <button id="new-project-btn" class="btn btn-primary">➕ Nuevo</button>
                </div>
                <ul id="projects-list">
                    <!-- Proyectos se cargarán aquí -->
                </ul>
            </div>

            <div class="main-content">
                <div class="main-header">
                    <h2>Tareas</h2>
                    <div class="search-container">
                        <input type="text" id="task-search" placeholder="🔍 Buscar tareas..." class="search-input">
                    </div>
                </div>
                <div id="tasks-container" class="tasks-list">
                    <!-- Tareas se cargarán aquí -->
                </div>
            </div>

            <div class="sidebar statistics-sidebar">
                <div class="statistics-center">
                    <h3>Estadísticas</h3>
                    <canvas id="stats-chart" width="300" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para nuevo proyecto -->
    <div id="project-modal" class="modal">
        <div class="modal-content">
            <span class="close">✕</span>
            <h2>Nuevo Proyecto</h2>
            <form id="project-form">
                <label for="project-name">Nombre:</label>
                <input type="text" id="project-name" required>
                
                <label for="project-desc">Descripción:</label>
                <textarea id="project-desc"></textarea>
                
                <button type="button" onclick="createProject()" class="btn btn-primary">✅ Crear</button>
            </form>
        </div>
    </div>
    
    <!-- Modal para nueva tarea -->
    <div id="task-modal" class="modal">
        <div class="modal-content">
            <span class="close">✕</span>
            <h2>Nueva Tarea</h2>
            <form id="task-form">
                <label for="task-title">Título:</label>
                <input type="text" id="task-title" required>
                
                <label for="task-desc">Descripción:</label>
                <textarea id="task-desc"></textarea>
                
                <label for="task-state">Estado:</label>
                <select id="task-state">
                    <option value="pendiente">Pendiente</option>
                    <option value="en progreso">En Progreso</option>
                    <option value="completada">Completada</option>
                </select>
                
                <label for="task-due-date">Fecha de Vencimiento:</label>
                <input type="date" id="task-due-date">
                
                <button type="submit" class="btn btn-primary">✅ Crear Tarea</button>
            </form>
        </div>
    </div>
    
    <!-- Toast notifications -->
    <div id="toast-container"></div>

    <!-- Variables de configuración para JavaScript -->
    <script>
        window.APP_URL = '<?php echo APP_URL; ?>';
        window.API_URL = '<?php echo API_URL; ?>';
    </script>

    <script src="<?php echo APP_URL; ?>/public/js/dashboard.js"></script>
</body>
</html>