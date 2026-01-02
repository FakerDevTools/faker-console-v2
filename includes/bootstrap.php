<?php

session_start();

include __DIR__ . '/functions.php';

// Load DB config from .env
$env = parse_ini_file(__DIR__ . '/../.env');

foreach ($env as $key => $value) {
    define($key, $value);
}

$mysqli = new mysqli(
    DATABASE_HOST,
    DATABASE_USER,
    DATABASE_PASSWORD,
    DATABASE_NAME
);

if ($mysqli->connect_errno) {
    $error = 'Database connection failed.';
}