<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$raffleId = (int) ($_GET['id'] ?? 0);
$raffle = raffle_public_find($raffleId);
if (!$raffle) {
    http_response_code(404);
    exit('Rifa no encontrada.');
}
$numbers = raffle_numbers($raffleId);
$stats = raffle_stats($raffleId);
$pageTitle = $raffle['name'];
require __DIR__ . '/../components/header.php';
?>
<section class="section">
    <div class="raffle-title">
        <div>
            <span class="status-pill <?= e($raffle['status']) ?>"><?= e(status_label($raffle['status'])) ?></span>
            <h1><?= e($raffle['name']) ?></h1>
            <p><?= e($raffle['description']) ?></p>
        </div>
        <div class="draw-box" data-countdown="<?= e($raffle['draw_date']) ?>">
            <span>Sorteo</span>
            <strong><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></strong>
            <small class="countdown-output">Calculando...</small>
        </div>
    </div>
    <div class="stats-row">
        <div><strong><?= $stats['available'] ?></strong><span>Disponibles</span></div>
        <div><strong><?= $stats['reserved'] ?></strong><span>Reservados</span></div>
        <div><strong><?= $stats['sold'] ?></strong><span>Vendidos</span></div>
        <div><strong><?= e(money((float) $raffle['price'])) ?></strong><span>Precio</span></div>
    </div>
    <div class="number-board">
        <?php foreach ($numbers as $number): ?>
            <a class="number-cell <?= e($number['status']) ?>" data-number-value="<?= (int) $number['number_value'] ?>" href="<?= e(public_number_url($raffleId, (int) $number['number_value'])) ?>">
                <?= (int) $number['number_value'] ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<script>
window.RAFFLE_ID = <?= (int) $raffleId ?>;
window.APP_BASE = <?= json_encode(rtrim(APP_URL, '/')) ?>;
</script>
<?php require __DIR__ . '/../components/footer.php'; ?>
