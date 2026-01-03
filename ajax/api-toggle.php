<?php

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{

    echo json_encode(['error' => 'Invalid request method.']);
    exit;

}
elseif(!isset($_POST['api']) or !is_numeric($_POST['api']))
{

    echo json_encode(['error' => 'API ID is required.']);
    exit;

}
elseif(!isset($_POST['user']) or !is_numeric($_POST['user']))
{

    echo json_encode(['error' => 'User ID is required.']);
    exit;
    
}

$stmt = $mysqli->prepare('
    SELECT id 
    FROM api_user
    WHERE api_id = ? 
    AND user_id = ?
    LIMIT 1
');
$stmt->bind_param('ii', $_POST['api'], $_POST['user']);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0)
{

    $stmt = $mysqli->prepare('
        DELETE FROM api_user 
        WHERE api_id = ? 
        AND user_id = ?
        LIMIT 1
    ');
    $stmt->bind_param('ii', $_POST['api'], $_POST['user']);
    $stmt->execute();

    echo json_encode(['status' => 'disabled']);
    exit;

}
else
{

    $stmt = $mysqli->prepare('
        INSERT INTO api_user (
            api_id, 
            user_id
        ) VALUES (?, ?)
    ');
    $stmt->bind_param('ii', $_POST['api'], $_POST['user']);
    $stmt->execute();

    echo json_encode(['status' => 'enabled']);
    exit;

}
