<?php

include '../config/database.php'; // incluye la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Método para verificar si el usuario está autenticado
function isAuthenticated()
{
    session_start(); // Asegúrate de que la sesión esté iniciada
    return isset($_SESSION['user_id']); // Verifica si existe el ID del usuario en la sesión
}

// Obtener publicaciones (GET)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->prepare('SELECT p.id, p.title, p.content, p.categoryid, u.name AS author FROM posts p JOIN users u ON p.userid = u.id');
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron publicaciones
    if (count($posts) > 0) {
        echo json_encode($posts, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['message' => 'No se encontraron publicaciones'], JSON_UNESCAPED_UNICODE);
    }

    // Crear una nueva publicación (POST)
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isAuthenticated() && isset($_POST['title']) && isset($_POST['content']) && isset($_POST['category_id'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $userId = $_SESSION['user_id']; // Obtiene el ID del usuario autenticado desde la sesión
        $categoryId = $_POST['category_id']; // este id lo vamos a obtener desde el frontend

        // Preparar la consulta para insertar el nuevo post
        $stmt = $conn->prepare('INSERT INTO posts (title, content, userid, categoryid) VALUES (?, ?, ?, ?)');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $content);
        $stmt->bindParam(3, $userId);
        $stmt->bindParam(4, $categoryId);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Post creado con éxito'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['message' => 'Error al crear el post'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'Título, contenido y categoría son requeridos o el usuario no está autenticado'], JSON_UNESCAPED_UNICODE);
    }

    // Actualizar una publicación (PUT)
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isAuthenticated()) {
        parse_str(file_get_contents('php://input'), $_PUT); // Obtener datos del cuerpo de la solicitud PUT

        if (isset($_PUT['id']) && isset($_PUT['title']) && isset($_PUT['content']) && isset($_PUT['category_id'])) {
            $id = $_PUT['id'];
            $title = $_PUT['title'];
            $content = $_PUT['content'];
            $categoryId = $_PUT['category_id'];

            $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ?');
            $stmt->bindParam(1, $title);
            $stmt->bindParam(2, $content);
            $stmt->bindParam(3, $categoryId);
            $stmt->bindParam(4, $id);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Post actualizado con éxito'], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['message' => 'Error al actualizar el post'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['message' => 'ID, título, contenido y categoría son requeridos'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'El usuario no está autenticado'], JSON_UNESCAPED_UNICODE);
    }

    // Eliminar una publicación (DELETE)
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isAuthenticated()) {
        parse_str(file_get_contents('php://input'), $_DELETE); // Obtener datos del cuerpo de la solicitud DELETE

        if (isset($_DELETE['id'])) {
            $id = $_DELETE['id'];

            $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
            $stmt->bindParam(1, $id);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Post eliminado con éxito'], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['message' => 'Error al eliminar el post'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['message' => 'ID de post es requerido'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'El usuario no está autenticado'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
