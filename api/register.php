<?php
//register
include '../config/database.php'; // Asegúrate de que la ruta sea correcta

header('Content-Type: application/json; charset=utf-8');

// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Responder a las solicitudes OPTIONS
    exit(0); // Termina la ejecución para solicitudes OPTIONS
}


// Crear una instancia de la clase Database
$database = new Database();
$conn = $database->getConnection(); // Obtener la conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica si los datos están presentes
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Preparar la consulta
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');

        if ($stmt) {
            $stmt->execute([$name, $email, $password]);
            echo json_encode(['message' => 'Usuario registrado con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'No se pudo preparar la consulta'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['error' => 'Faltan parámetros en la solicitud'], JSON_UNESCAPED_UNICODE);
    }
}
?>
