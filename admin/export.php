<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$raffle = raffle_find($raffleId);
if (!$raffle) {
    http_response_code(404);
    exit('Rifa no encontrada.');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="rifa-' . $raffleId . '-numeros.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['numero', 'estado', 'comprador', 'telefono', 'ciudad', 'observaciones', 'registrado']);
foreach (raffle_numbers($raffleId) as $number) {
    fputcsv($out, [
        $number['number_value'],
        status_label($number['status']),
        csv_safe($number['buyer_name']),
        csv_safe($number['buyer_phone']),
        csv_safe($number['buyer_city']),
        csv_safe($number['notes']),
        $number['registered_at'],
    ]);
}
fclose($out);
