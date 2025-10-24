<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: http://localhost/gestor-tareas/app/views/login.php');
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
                <a href="http://localhost/gestor-tareas/public/index.php?action=logout">Logout</a>
            </div>
        </header>
        
        <div class="dashboard">
            <div class="sidebar">
                <h2>Proyectos</h2>
                <button id="new-project-btn">Nuevo Proyecto</button>
                <ul id="projects-list">
                    <!-- Proyectos se cargarán aquí -->
                </ul>
            </div>
            
            <div class="main-content">
                <h2>Tareas</h2>
                <div id="tasks-container">
                    <!-- Tareas se cargarán aquí -->
                </div>
                <div id="statistics">
                    <h3>Estadísticas</h3>
                    <canvas id="stats-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para nuevo proyecto -->
    <div id="project-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Nuevo Proyecto</h2>
            <form id="project-form">
                <label for="project-name">Nombre:</label>
                <input type="text" id="project-name" required>
                
                <label for="project-desc">Descripción:</label>
                <textarea id="project-desc"></textarea>
                
                <button type="submit">Crear</button>
            </form>
        </div>
    </div>
    
    <script src="<?php echo str_replace('/app/views', '', dirname($_SERVER['SCRIPT_NAME'])); ?>/public/js/dashboard.js"></script>
</body>
</html>