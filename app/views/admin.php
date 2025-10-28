<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir configuración
require_once dirname(__DIR__, 2) . '/config.php';

// Verificar que las constantes estén definidas
if (!defined('APP_URL')) {
    die('Error: APP_URL no está definido. Verifica la configuración.');
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - TaskFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/styles.css">    
</head>
<body>
    <div class="container">
        <header>
            <h1>TaskFlow</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo $_SESSION['user_name']; ?> (Admin)</span>
                <button id="theme-toggle" class="theme-toggle" title="Cambiar tema">🌙</button>
                <a href="<?php echo APP_URL; ?>/dashboard" >Dashboard</a>
                <a href="<?php echo APP_URL; ?>/public/index.php?action=logout">Logout</a>
            </div>
        </header>

        <div class="dashboard">
            <div class="sidebar projects-sidebar">
                <!-- Barra lateral izquierda vacía -->
            </div>

            <div class="main-content">
                <div class="main-header">
                    <h2>Panel de Administración</h2>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <div class="tabs" style="margin-bottom: 2rem;">
                    <button class="tab-btn active" onclick="showTab('pending')">Usuarios Pendientes</button>
                    <button class="tab-btn" onclick="showTab('all')">Todos los Usuarios</button>
                </div>

                <div id="pending-tab" class="tab-content active">
                    <h3>Usuarios Pendientes de Aprobación</h3>
                    <?php
                    require_once __DIR__ . '/../controllers/AdminController.php';
                    $adminController = new AdminController();
                    $pendientes = $adminController->usuarioModel->obtenerPendientes();
                    ?>
                    <?php if (empty($pendientes)): ?>
                        <p>No hay usuarios pendientes de aprobación.</p>
                    <?php else: ?>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Fecha de Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendientes as $usuario): ?>
                                    <tr>
                                        <td><?php echo $usuario['id']; ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></td>
                                        <td><span class="status-badge status-pending">Pendiente</span></td>
                                        <td>
                                            <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?action=aprobar_usuario" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $adminController->getCsrfToken(); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                                <button type="submit" class="action-btn btn-approve">✅ Aprobar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div id="all-tab" class="tab-content">
                    <h3>Todos los Usuarios</h3>
                    <?php
                    $todos = $adminController->usuarioModel->obtenerTodos();
                    ?>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Fecha de Registro</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todos as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></td>
                                    <td>
                                        <?php if ($usuario['activo'] == 1): ?>
                                            <span class="status-badge status-active">Activo</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($usuario['activo'] == 1): ?>
                                            <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?action=desactivar_usuario" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $adminController->getCsrfToken(); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                                <button type="submit" class="action-btn btn-deactivate">🚫 Desactivar</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?php echo APP_URL; ?>/public/index.php?action=aprobar_usuario" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $adminController->getCsrfToken(); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                                                <button type="submit" class="action-btn btn-approve">✅ Aprobar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="sidebar statistics-sidebar"></div>

    <style>
        /* Estilos específicos para la tabla de usuarios en admin */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: var(--surface-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .user-table th,
        .user-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .user-table th {
            background: var(--background-color);
            font-weight: 600;
            color: var(--text-primary);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .user-table tbody tr {
            transition: var(--transition);
        }

        .user-table tbody tr:hover {
            background: var(--background-color);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            margin-right: 0.5rem;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-deactivate {
            background: #ef4444;
            color: white;
        }

        .btn-deactivate:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: var(--transition);
            font-weight: 500;
        }

        .tab-btn:hover {
            background: var(--background-color);
            color: var(--text-primary);
        }

        .tab-btn.active {
            background: var(--primary-color);
            color: white;
            border-bottom: 2px solid var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .main-header h2 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .tab-content h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .user-table th,
            .user-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }

            .action-btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
            }

            .tabs {
                flex-direction: column;
                gap: 0;
            }

            .tab-btn {
                border-radius: 0;
                border-bottom: 1px solid var(--border-color);
            }
        }
    </style>

    <!-- Toast notifications -->
    <div id="toast-container"></div>

    <!-- Variables de configuración para JavaScript -->
    <script>
        window.APP_URL = '<?php echo APP_URL; ?>';
        window.API_URL = '<?php echo API_URL; ?>';
    </script>

    <script>
        function showTab(tabName) {
            // Ocultar todos los tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Mostrar el tab seleccionado
            document.getElementById(tabName + '-tab').classList.add('active');

            // Actualizar botones de tab
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        // Función para cambiar el tema (igual que en dashboard)
        function toggleTheme() {
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');

            if (html.hasAttribute('data-theme')) {
                html.removeAttribute('data-theme');
                themeToggle.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeToggle.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Cargar tema guardado
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const themeToggle = document.getElementById('theme-toggle');

            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                themeToggle.textContent = '☀️';
            } else {
                themeToggle.textContent = '🌙';
            }

            // Agregar event listener al botón de tema
            themeToggle.addEventListener('click', toggleTheme);
        });
    </script>
</body>
</html>