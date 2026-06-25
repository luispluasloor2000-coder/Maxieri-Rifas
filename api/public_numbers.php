<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$raffle = raffle_public_find($raffleId);

if (!$raffle) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Rifa no encontrada']);
    exit;
}

$numbers = array_map(static function (array $number): array {
    return [
        'number_value' => (int) $number['number_value'],
        'status' => $number['status'],
    ];
}, raffle_numbers($raffleId));

echo json_encode([
    'ok' => true,
    'numbers' => $numbers,
    'stats' => raffle_stats($raffleId),
], JSON_UNESCAPED_UNICODE);
