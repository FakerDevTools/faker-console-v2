<?php

// Page requirements validation
if(!isset($_GET['token']) || empty($_GET['token'])) 
{

    message_set('Invalid or missing password reset token.', 'error');
    header_redirect('/login');

}

$stmt = $mysqli->prepare('
    SELECT id 
    FROM users
    WHERE verify_token = ?
    LIMIT 1
');

$stmt->bind_param('s', $_GET['token']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) 
{

    message_set('Invalid token provided.', 'error');
    header_redirect('/login');

}

$stmt = $mysqli->prepare('
    UPDATE users SET 
    is_verified = 1,
    updated_at = NOW()
    WHERE verify_token = ?
');
$stmt->bind_param('s', $_GET['token']);

message_set('Account has been successfully verified! You can now login.', 'success');
header_redirect('/login');
        
