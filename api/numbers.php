<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$raffle = raffle_find($raffleId);

if (!$raffle) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Rifa no encontrada']);
    exit;
}

$numbers = array_map(static function (array $number): array {
    return [
        'id' => (int) $number['id'],
        'raffle_id' => (int) $number['raffle_id'],
        'number_value' => (int) $number['number_value'],
        'status' => $number['status'],
        'buyer_name' => $number['buyer_name'],
        'buyer_phone' => $number['buyer_phone'],
        'buyer_city' => $number['buyer_city'],
        'notes' => $number['notes'],
        'registered_at' => $number['registered_at'],
    ];
}, raffle_numbers($raffleId));

echo json_encode([
    'ok' => true,
    'numbers' => $numbers,
    'stats' => raffle_stats($raffleId),
], JSON_UNESCAPED_UNICODE);
