<?php
// register
include '../config/database.php'; // Asegúrate de que la ruta sea correcta

// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejo de solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); // Responder a las solicitudes OPTIONS
    exit();
}

// Crear una instancia de la clase Database
$database = new Database();
$conn = $database->getConnection(); // Obtener la conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el cuerpo de la solicitud y decodificar JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica si los datos están presentes
    if (isset($data['name']) && isset($data['email']) && isset($data['password'])) {
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_BCRYPT); // Hash de la contraseña

        // Preparar la consulta
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');

        // Verificar si la consulta se preparó correctamente
        if ($stmt) {
            // Ejecutar la consulta
            $stmt->execute([$name, $email, $password]);
            echo json_encode(['message' => 'Usuario registrado con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'No se pudo preparar la consulta'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['error' => 'Faltan parámetros en la solicitud'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
?>
