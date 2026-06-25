<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$numberValue = (int) ($_GET['number'] ?? 0);
$raffle = raffle_public_find($raffleId);
$number = $raffle ? raffle_number_find($raffleId, $numberValue) : null;

if (!$raffle || !$number) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Número no encontrado']);
    exit;
}

echo json_encode([
    'ok' => true,
    'number' => [
        'value' => (int) $number['number_value'],
        'status' => $number['status'],
        'buyer_name' => $number['buyer_name'],
        'registered_at' => $number['registered_at'],
    ],
    'raffle' => [
        'id' => (int) $raffle['id'],
        'name' => $raffle['name'],
        'prize' => $raffle['prize'],
        'draw_date' => $raffle['draw_date'],
    ],
], JSON_UNESCAPED_UNICODE);

