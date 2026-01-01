<?php

// Router: supports pretty URLs like /page/var/1 => page.php?var=1
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
if ($path === '') {
    header('Location: /login');
    exit;
}
$segments = explode('/', $path);
$page = array_shift($segments);
if ($page && file_exists(__DIR__ . "/$page.php") && $page !== 'index') {
    // Convert /page/var/1 to $_GET['var'] = 1
    for ($i = 0; $i < count($segments) - 1; $i += 2) {
        if (isset($segments[$i+1])) {
            $_GET[$segments[$i]] = $segments[$i+1];
        }
    }
    include __DIR__ . "/$page.php";
    exit;
}

