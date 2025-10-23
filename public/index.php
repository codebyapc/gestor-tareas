<?php
// Punto de entrada principal para TaskFlow
// Inicia la sesión y enruta las solicitudes

session_start();

require_once '../config.php';

// Aquí irá la lógica de enrutamiento básica
// Por simplicidad, redirigir a login si no está autenticado

if (!isset($_SESSION['user_id'])) {
    header('Location: ../app/views/login.php');
    exit;
} else {
    header('Location: ../app/views/dashboard.php');
    exit;
}
?>