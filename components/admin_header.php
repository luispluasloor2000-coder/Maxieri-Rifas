<?php
$user = require_login();
$pageTitle = $pageTitle ?? 'Panel';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081a2f">
    <title><?= e($pageTitle) ?> | Admin <?= e(APP_NAME) ?></title>
    <link rel="manifest" href="<?= e(url('manifest.webmanifest')) ?>">
    <link rel="icon" href="<?= e(url('assets/img/icon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>">
</head>
<body class="admin-body">
<aside class="sidebar">
    <a class="brand" href="<?= e(url('admin/index.php')) ?>">
        <span class="brand-mark">M</span>
        <span><strong><?= e(APP_NAME) ?></strong><small>Panel seguro</small></span>
    </a>
    <nav class="side-nav">
        <a href="<?= e(url('admin/index.php')) ?>">Dashboard</a>
        <a href="<?= e(url('admin/raffles.php')) ?>">Rifas</a>
        <a href="<?= e(url('admin/reports.php')) ?>">Reportes</a>
        <a href="<?= e(url('admin/draw.php')) ?>">Sorteo</a>
        <a href="<?= e(url('admin/user_password.php')) ?>">Contraseña</a>
        <a href="<?= e(url('admin/logout.php')) ?>">Salir</a>
    </nav>
</aside>
<main class="admin-main">
<div class="admin-topbar">
    <div>
        <h1><?= e($pageTitle) ?></h1>
        <p>Sesión: <?= e($user['name']) ?></p>
    </div>
</div>
