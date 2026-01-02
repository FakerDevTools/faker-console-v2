<?php

session_start();

include __DIR__ . '/functions.php';

// Load environment variables from .env file
$env = parse_ini_file(__DIR__ . '/../.env');

foreach ($env as $key => $value) 
{
    define($key, $value);
}

// Connect to database
try 
{
    $mysqli = new mysqli(
        DATABASE_HOST,
        DATABASE_USER,
        DATABASE_PASSWORD,
        DATABASE_NAME
    );
} 
catch (Exception $e)
{
    $error = 'Database connection failed.';
    include(__DIR__ . '/../error.php');
    exit();
}
