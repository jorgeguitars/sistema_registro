<?php

include '../config/database.php'; // incluye la conexión a la base de datos

// establece el encabezado para JSON y codificación UTF-8
header('Content-Type: application/json; charset=utf-8');

// instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Iniciar sesión para manejar la autenticación
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // se valida que las variables sean correctas
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Preparar la consulta para obtener el usuario
        $stmt = $conn->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();

        // Verificar si se encontró el usuario
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Almacenar información del usuario en la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // Respuesta de éxito
                echo json_encode(
                    [
                        'message' => 'Inicio de sesión exitoso',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name'],
                        ],
                    ],
                    JSON_UNESCAPED_UNICODE,
                );
            } else {
                echo json_encode(['message' => 'Contraseña incorrecta'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['message' => 'Usuario no encontrado'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'Email y contraseña son requeridos'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
