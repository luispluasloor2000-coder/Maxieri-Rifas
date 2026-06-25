<?php $pageTitle = $pageTitle ?? APP_NAME; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#081a2f">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="manifest" href="<?= e(url('manifest.webmanifest')) ?>">
    <link rel="icon" href="<?= e(url('assets/img/icon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>">
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= e(url()) ?>">
        <span class="brand-mark">M</span>
        <span><strong><?= e(APP_NAME) ?></strong><small><?= e(APP_SLOGAN) ?></small></span>
    </a>
    <nav class="top-nav">
        <a href="<?= e(url()) ?>">Inicio</a>
        <a href="<?= e(url('public/search.php')) ?>">Verificar número</a>
        <a href="<?= e(url('admin/index.php')) ?>">Administración</a>
    </nav>
</header>
<main>
