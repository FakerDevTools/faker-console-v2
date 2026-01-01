<?php

session_start();

include __DIR__ . '/functions.php';

// Load DB config from .env
$env = parse_ini_file(__DIR__ . '/../.env');
$mysqli = new mysqli(
    $env['DATABASE_HOST'],
    $env['DATABASE_USER'],
    $env['DATABASE_PASSWORD'],
    $env['DATABASE_NAME']
);

if ($mysqli->connect_errno) {
    $error = 'Database connection failed.';
}