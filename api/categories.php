<?php

include '../config/database.php'; // incluye la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8');

// instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Manejo de diferentes métodos HTTP
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtener todas las categorías
    $stmt = $conn->prepare('SELECT id, name FROM categories');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories, JSON_UNESCAPED_UNICODE);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Crear nueva categoría
    if (isset($_POST['name'])) {
        $name = $_POST['name'];

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
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Actualizar categoría
    parse_str(file_get_contents('php://input'), $_PUT); // Obtener datos del cuerpo de la solicitud PUT

    if (isset($_PUT['id']) && isset($_PUT['name'])) {
        $id = $_PUT['id'];
        $name = $_PUT['name'];

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
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Eliminar categoría
    parse_str(file_get_contents('php://input'), $_DELETE); // Obtener datos del cuerpo de la solicitud DELETE

    if (isset($_DELETE['id'])) {
        $id = $_DELETE['id'];

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
