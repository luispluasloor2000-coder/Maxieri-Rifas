<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$numberValue = (int) ($_GET['number'] ?? 0);
$raffle = raffle_public_find($raffleId);
$number = $raffle ? raffle_number_find($raffleId, $numberValue) : null;

if (!$raffle || !$number) {
    http_response_code(404);
    exit('Número no encontrado.');
}

$audits = [];
$stmt = db()->prepare('SELECT created_at, action FROM number_audits WHERE raffle_number_id = ? ORDER BY created_at DESC LIMIT 5');
$stmt->execute([(int) $number['id']]);
$audits = $stmt->fetchAll();

$pageTitle = 'Número ' . $numberValue;
require __DIR__ . '/../components/header.php';
?>
<section class="section narrow">
    <article class="verification-card">
        <span class="status-pill <?= e($number['status']) ?>"><?= e(status_label($number['status'])) ?></span>
        <h1>Número <?= (int) $number['number_value'] ?></h1>
        <p>Enlace público verificable para confirmar la existencia y estado del número.</p>
        <img class="qr-preview" src="<?= e(qr_url(public_number_url($raffleId, $numberValue))) ?>" alt="QR del número">
        <dl class="meta-list">
            <div><dt>Rifa</dt><dd><?= e($raffle['name']) ?></dd></div>
            <div><dt>Comprador</dt><dd><?= e($number['buyer_name'] ?: 'Sin comprador registrado') ?></dd></div>
            <div><dt>Premio</dt><dd><?= e($raffle['prize']) ?></dd></div>
            <div><dt>Fecha sorteo</dt><dd><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></dd></div>
            <div><dt>Registrado</dt><dd><?= $number['registered_at'] ? e(date('d/m/Y H:i', strtotime($number['registered_at']))) : 'Sin registro' ?></dd></div>
        </dl>
    </article>
    <section class="timeline">
        <h2>Historial público</h2>
        <?php if ($audits): ?>
            <?php foreach ($audits as $audit): ?>
                <p><strong><?= e(date('d/m/Y H:i', strtotime($audit['created_at']))) ?></strong> <?= e($audit['action']) ?></p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Este número aún no registra modificaciones administrativas.</p>
        <?php endif; ?>
    </section>
</section>
<?php require __DIR__ . '/../components/footer.php'; ?>

