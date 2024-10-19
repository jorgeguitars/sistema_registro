<?php
session_start(); // Inicia la sesión

// Verificar si el usuario tiene una sesión activa
if (isset($_SESSION['user_id'])) {
    // Si hay sesión, enviar los datos del usuario
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name']
        ]
    ]);
} else {
    // Si no hay sesión activa, devolver un error
    echo json_encode([
        'success' => false,
        'message' => 'No hay sesión activa.'
    ]);
}

