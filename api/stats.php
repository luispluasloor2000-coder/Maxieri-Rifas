<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$raffle = raffle_public_find($raffleId);
if (!$raffle && current_user()) {
    $raffle = raffle_find($raffleId);
}
if (!$raffle) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Rifa no encontrada']);
    exit;
}

echo json_encode(['ok' => true, 'stats' => raffle_stats($raffleId)], JSON_UNESCAPED_UNICODE);
