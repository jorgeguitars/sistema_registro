<?php

include '../config/database.php'; // incluye la conexión a la base de datos

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Instancia de la clase Database
$database = new Database();
$conn = $database->getConnection();

// Método para verificar si el usuario está autenticado
function isAuthenticated() {
    session_start(); // Asegúrate de que la sesión esté iniciada
    return isset($_SESSION['user_id']); // Verifica si existe el ID del usuario en la sesión
}

// Obtener publicaciones (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ajusta la consulta para incluir el nombre de la categoría y ordenar por la fecha de creación
    $stmt = $conn->prepare('
        SELECT p.id, p.title, p.content, p.categoryid, c.name AS category, u.name AS author, p.created_at 
        FROM posts p 
        JOIN users u ON p.userid = u.id
        JOIN categories c ON p.categoryid = c.id
        ORDER BY p.created_at DESC  -- Ordenar por fecha de creación más reciente
    ');
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron publicaciones
    if ($posts) {
        echo json_encode($posts, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['message' => 'No se encontraron publicaciones'], JSON_UNESCAPED_UNICODE);
    }
}


// Crear una nueva publicación (POST)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer el cuerpo de la solicitud JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar que las variables no estén vacías
    if (isset($data['title'], $data['content'], $data['category_id'], $data['userid']) && 
        !empty($data['title']) && 
        !empty($data['content']) && 
        !empty($data['category_id'])) {
        
        $title = $data['title'];
        $content = $data['content'];
        $userId = $data['userid']; // Obtiene el ID del usuario desde la solicitud
        $categoryId = $data['category_id']; // ID de la categoría

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
        echo json_encode(['message' => 'Título, contenido, categoría y userid son requeridos y no pueden estar vacíos'], JSON_UNESCAPED_UNICODE);
    }
}

// Actualizar una publicación (PUT)
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (isAuthenticated()) {
        // Leer el cuerpo de la solicitud JSON
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar que las variables no estén vacías
        if (isset($data['id'], $data['title'], $data['content'], $data['category_id']) && 
            !empty($data['id']) && 
            !empty($data['title']) && 
            !empty($data['content']) && 
            !empty($data['category_id'])) {
            
            $id = $data['id'];
            $title = $data['title'];
            $content = $data['content'];
            $categoryId = $data['category_id'];

            $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?, categoryid = ? WHERE id = ?');
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
            echo json_encode(['message' => 'ID, título, contenido y categoría son requeridos y no pueden estar vacíos'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'El usuario no está autenticado'], JSON_UNESCAPED_UNICODE);
    }
} 
// Eliminar una publicación (DELETE)
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isAuthenticated()) {
        // Leer el cuerpo de la solicitud JSON
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar que el ID no esté vacío
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];

            $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
            $stmt->bindParam(1, $id);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Post eliminado con éxito'], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['message' => 'Error al eliminar el post'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['message' => 'ID de post es requerido y no puede estar vacío'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['message' => 'El usuario no está autenticado'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
