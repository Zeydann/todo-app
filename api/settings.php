<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../config.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM settings");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['key']] = $row['value'];
    }
    echo json_encode($data);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $key = mysqli_real_escape_string($conn, $data['key']);
    $value = mysqli_real_escape_string($conn, $data['value']);

    $allowedKeys = ['quote', 'notes'];
    if (!in_array($key, $allowedKeys)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid key']);
        exit;
    }

    mysqli_query($conn, "INSERT INTO settings (`key`, `value`) VALUES ('$key', '$value')
        ON DUPLICATE KEY UPDATE `value` = '$value'");
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
