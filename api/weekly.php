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
    $result = mysqli_query($conn, "SELECT * FROM weekly_rows WHERE user_id = $userId ORDER BY sort_order ASC");
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($rows);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $id = (int)$data['id'];
        $field = mysqli_real_escape_string($conn, $data['field']);
        $value = mysqli_real_escape_string($conn, $data['value']);

        $allowedFields = ['day', 'time', 'activity', 'status'];
        if (!in_array($field, $allowedFields)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid field']);
            exit;
        }

        mysqli_query($conn, "UPDATE weekly_rows SET `$field` = '$value' WHERE id = $id AND user_id = $userId");
        echo json_encode(['success' => true]);
    } else {
        $maxOrder = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(MAX(sort_order), 0) as m FROM weekly_rows WHERE user_id = $userId"))['m'];
        $newOrder = $maxOrder + 1;
        mysqli_query($conn, "INSERT INTO weekly_rows (user_id, day, time, activity, status, sort_order) VALUES ($userId, 'Senin', '09:00', '', 'none', $newOrder)");
        $newId = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'id' => $newId]);
    }
    exit;
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];
    mysqli_query($conn, "DELETE FROM weekly_rows WHERE id = $id AND user_id = $userId");
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
