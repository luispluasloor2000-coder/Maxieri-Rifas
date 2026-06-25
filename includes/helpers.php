<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $baseUrl = APP_URL;
    if ($baseUrl === '') {
        $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080';
        $baseUrl = $scheme . '://' . $host;
    }

    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function money(float $amount): string
{
    return '$' . number_format($amount, 2, '.', ',');
}

function status_label(string $status): string
{
    return [
        'active' => 'Activa',
        'finished' => 'Finalizada',
        'hidden' => 'Oculta',
        'available' => 'Disponible',
        'reserved' => 'Reservado',
        'sold' => 'Vendido',
    ][$status] ?? ucfirst($status);
}

function public_number_url(int $raffleId, int $number): string
{
    return url('rifa/' . $raffleId . '/numero/' . $number);
}

function qr_url(string $target): string
{
    return 'https://quickchart.io/qr?size=220&text=' . rawurlencode($target);
}

function current_path(): string
{
    return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}
