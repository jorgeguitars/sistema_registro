<?php

include '../config/database.php'; // incluye la conexión a la base de datos

// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

header('Content-Type: application/json; charset=utf-8');

// instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Manejo de diferentes métodos HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todas las categorías
    $stmt = $conn->prepare('SELECT id, name FROM categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories, JSON_UNESCAPED_UNICODE);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear nueva categoría
    $data = json_decode(file_get_contents('php://input'), true); // Obtener datos del cuerpo de la solicitud POST

    if (isset($data['name'])) {
        $name = $data['name'];

        $stmt = $conn->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->bindParam(1, $name);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Categoría creada con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['message' => 'Error al crear la categoría'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'Nombre de categoría es requerido'], JSON_UNESCAPED_UNICODE);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Actualizar categoría
    $data = json_decode(file_get_contents('php://input'), true); // Obtener datos del cuerpo de la solicitud PUT

    if (isset($data['id']) && isset($data['name'])) {
        $id = $data['id'];
        $name = $data['name'];

        $stmt = $conn->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Categoría actualizada con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['message' => 'Error al actualizar la categoría'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'ID y nombre de categoría son requeridos'], JSON_UNESCAPED_UNICODE);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Eliminar categoría
    $data = json_decode(file_get_contents('php://input'), true); // Obtener datos del cuerpo de la solicitud DELETE

    if (isset($data['id'])) {
        $id = $data['id'];

        $stmt = $conn->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Categoría eliminada con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['message' => 'Error al eliminar la categoría'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'ID de categoría es requerido'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
