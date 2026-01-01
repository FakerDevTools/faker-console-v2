<?php

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['exists' => false, 'error' => 'Invalid email.']);
    exit;
}

if (!isset($mysqli) || $mysqli->connect_errno) {
    echo json_encode(['exists' => false, 'error' => 'Database connection failed.']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();
$mysqli->close();

if ($exists) {
    echo json_encode(['exists' => $exists, 'error' => 'Email already registered.']);
} else {
    echo json_encode(['exists' => $exists]);
}
    
