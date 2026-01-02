<?php
require_once __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/includes/bootstrap.php';

// Router: supports pretty URLs like /page/var/1 => page.php?var=1
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

if ($path === '') 
{

    header('Location: /login');
    exit;

}

$segments = explode('/', $path);
$page = array_shift($segments);

for ($i = 0; $i < count($segments) - 1; $i += 2) 
{
    
    if (isset($segments[$i+1])) $_GET[$segments[$i]] = $segments[$i+1];

}

if ($page && file_exists(__DIR__ . "/$page.php") && $page !== 'index') 
{

    include __DIR__ . "/$page.php";
    exit;

}
elseif ($page && file_exists(__DIR__ . "/action/$page.php") && $page !== 'index') 
{

    include __DIR__ . "/action/$page.php";
    exit;

}
else 
{

    header('HTTP/1.0 404 Not Found');
    include __DIR__ . "/404.php";
    exit;
    
}
