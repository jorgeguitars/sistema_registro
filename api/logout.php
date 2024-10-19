<?php
// Iniciar la sesión
session_start();

// Establecer el encabezado para JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // Eliminar todas las variables de sesión
    session_unset(); // Libera todas las variables de sesión
    session_destroy(); // Destruye la sesión

    // Eliminar la cookie de sesión (si existe)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Respuesta de éxito
    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada exitosamente',
    ]);
} else {
    // Si no hay sesión activa, retornar un mensaje de error
    echo json_encode([
        'success' => false,
        'message' => 'No hay sesión activa',
    ]);
}
?>
