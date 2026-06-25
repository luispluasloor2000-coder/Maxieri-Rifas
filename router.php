<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

if (preg_match('#^/rifa/([0-9]+)/numero/([0-9]+)/?$#', $path, $matches)) {
    $_GET['raffle_id'] = $matches[1];
    $_GET['number'] = $matches[2];
    require __DIR__ . '/public/number.php';
    return true;
}

if (preg_match('#^/rifa/([0-9]+)/?$#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/public/raffle.php';
    return true;
}

require __DIR__ . '/index.php';
return true;
