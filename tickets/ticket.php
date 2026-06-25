<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$numberValue = (int) ($_GET['number'] ?? 0);
$raffle = raffle_find($raffleId);
$number = $raffle ? raffle_number_find($raffleId, $numberValue) : null;

if (!$raffle || !$number) {
    http_response_code(404);
    exit('Boleto no encontrado.');
}

$target = public_number_url($raffleId, $numberValue);
$autoPrint = ($_GET['autoprint'] ?? '') === '1';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boleto <?= (int) $numberValue ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/ticket.css')) ?>">
</head>
<body>
    <article class="ticket">
        <div class="logo">MAXIERI</div>
        <h1><?= e($raffle['name']) ?></h1>
        <p class="prize"><?= e($raffle['prize']) ?></p>
        <div class="ticket-number"><?= (int) $number['number_value'] ?></div>
        <img src="<?= e(qr_url($target)) ?>" alt="QR del boleto">
        <dl>
            <div><dt>Fecha venta</dt><dd><?= $number['registered_at'] ? e(date('d/m/Y H:i', strtotime($number['registered_at']))) : e(date('d/m/Y H:i')) ?></dd></div>
            <div><dt>Sorteo</dt><dd><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></dd></div>
            <div><dt>Precio</dt><dd><?= e(money((float) $raffle['price'])) ?></dd></div>
            <div><dt>Comprador</dt><dd><?= e($number['buyer_name'] ?: 'Sin registrar') ?></dd></div>
            <div><dt>Estado</dt><dd><?= e(status_label($number['status'])) ?></dd></div>
        </dl>
        <small><?= e($target) ?></small>
    </article>
    <button class="print-button" onclick="window.print()">Imprimir</button>
    <?php if ($autoPrint): ?>
        <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 350));
        </script>
    <?php endif; ?>
</body>
</html>
