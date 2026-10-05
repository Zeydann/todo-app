<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$userId = (int)$_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM todos WHERE user_id = $userId ORDER BY sort_order ASC");
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && isset($data['field'])) {
        $id = (int)$data['id'];
        $field = $data['field'];
        $allowedFields = ['text', 'done'];
        if (!in_array($field, $allowedFields)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid field']);
            exit;
        }

        if ($field === 'done') {
            $value = (int)$data['value'];
            mysqli_query($conn, "UPDATE todos SET done = $value WHERE id = $id AND user_id = $userId");
        } else {
            $value = mysqli_real_escape_string($conn, $data['value']);
            mysqli_query($conn, "UPDATE todos SET text = '$value' WHERE id = $id AND user_id = $userId");
        }
        echo json_encode(['success' => true]);
    } else {
        $maxOrder = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(sort_order), 0) as m FROM todos WHERE user_id = $userId"))['m'];
        $newOrder = $maxOrder + 1;
        mysqli_query($conn, "INSERT INTO todos (user_id, text, done, sort_order) VALUES ($userId, '', FALSE, $newOrder)");
        $newId = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'id' => $newId]);
    }
    exit;
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];
    mysqli_query($conn, "DELETE FROM todos WHERE id = $id AND user_id = $userId");
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
