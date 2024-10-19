<?php

include '../config/database.php'; // incluye la conexión a la base de datos

// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: token, Content-Type');
header('Access-Control-Max-Age: 1728000');
header('Content-Type: application/json; charset=utf-8');

// Manejo de solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Content-Length: 0');
    header('Content-Type: text/plain');
    exit();
}

// instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Iniciar sesión para manejar la autenticación
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el cuerpo de la solicitud y decodificar JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email']) && isset($data['password'])) {
        $email = $data['email'];
        $password = $data['password'];

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
                            "success" => true,
                            'id' => $user['id'],
                            'name' => $user['name'],
                        ],
                    ], JSON_UNESCAPED_UNICODE);
            } else {
                // Respuesta de error de contraseña
                echo json_encode([
                    "success" => false,
                    "message" => "Contraseña incorrecta"
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            // Respuesta de error de usuario no encontrado
            echo json_encode([
                "success" => false,
                "message" => "Usuario no encontrado"
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // Respuesta de error si faltan parámetros
        echo json_encode([
            "success" => false,
            "message" => "Email y contraseña son requeridos"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Respuesta de método no permitido
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ], JSON_UNESCAPED_UNICODE);
}
