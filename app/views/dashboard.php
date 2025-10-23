<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TaskFlow</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>TaskFlow</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo $_SESSION['user_name']; ?></span>
                <a href="../controllers/AuthController.php?action=logout">Logout</a>
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
    
    <script src="../js/dashboard.js"></script>
</body>
</html>