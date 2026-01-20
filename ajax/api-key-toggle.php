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
elseif(!isset($_POST['key']) or !is_numeric($_POST['key']))
{

    echo json_encode(['error' => 'Key ID is required.']);
    exit;
    
}

$stmt = $mysqli->prepare('
    SELECT id 
    FROM api_key
    WHERE api_id = ? 
    AND key_id = ?
    LIMIT 1
');
$stmt->bind_param('ii', $_POST['api'], $_POST['key']);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0)
{

    $stmt = $mysqli->prepare('
        DELETE FROM api_key 
        WHERE api_id = ? 
        AND key_id = ?
        LIMIT 1
    ');
    $stmt->bind_param('ii', $_POST['api'], $_POST['key']);
    $stmt->execute();

    echo json_encode(['status' => 'disabled']);
    exit;

}
else
{

    $stmt = $mysqli->prepare('
        INSERT INTO api_key (
            api_id, 
            key_id
        ) VALUES (?, ?)
    ');
    $stmt->bind_param('ii', $_POST['api'], $_POST['key']);
    $stmt->execute();

    echo json_encode(['status' => 'enabled']);
    exit;

}
