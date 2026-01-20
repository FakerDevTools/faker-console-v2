<?php

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{

    echo json_encode(['error' => 'Invalid request method.']);
    exit;

}
elseif(!isset($_POST['ip']) or !is_numeric($_POST['ip']))
{

    echo json_encode(['error' => 'IP ID is required.']);
    exit;

}
elseif(!isset($_POST['key']) or !is_numeric($_POST['key']))
{

    echo json_encode(['error' => 'Key ID is required.']);
    exit;
    
}

$stmt = $mysqli->prepare('
    SELECT id, type
    FROM ips
    WHERE id = ? 
    AND key_id = ?
    LIMIT 1
');
$stmt->bind_param('ii', $_POST['ip'], $_POST['key']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $type);

if($stmt->num_rows > 0)
{

    $stmt->fetch();

    $type = ( $type == 'allow' ? 'block' : 'allow' );

    $stmt = $mysqli->prepare('
        UPDATE ips 
        SET type = ?
        WHERE id = ? 
        AND key_id = ?
        LIMIT 1
    ');
    $stmt->bind_param('sii', $type, $_POST['ip'], $_POST['key']);
    $stmt->execute();

    echo json_encode(['status' => $type]);
    exit;

}
else
{

    echo json_encode(['error' => 'API key association not found.']);
    exit;

}
