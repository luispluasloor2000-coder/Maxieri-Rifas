<?php
require_once __DIR__ . '/includes/bootstrap.php';

$stmt = db()->query("SELECT * FROM raffles WHERE status IN ('active','finished') ORDER BY draw_date ASC");
$raffles = $stmt->fetchAll();
$pageTitle = 'Inicio';
require __DIR__ . '/components/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Transparencia verificable</p>
        <h1><?= e(APP_NAME) ?></h1>
        <p><?= e(APP_SLOGAN) ?> Compra, reserva y verifica números en un sistema claro, serio y auditable.</p>
        <div class="hero-actions">
            <a class="btn primary" href="<?= e(url('public/search.php')) ?>">Verificar número</a>
            <a class="btn ghost" href="#rifas">Ver rifas</a>
        </div>
    </div>
</section>

<section id="rifas" class="section">
    <div class="section-heading">
        <span class="eyebrow">Rifas disponibles</span>
        <h2>Participaciones activas y verificables</h2>
    </div>
    <div class="raffle-grid">
        <?php foreach ($raffles as $raffle): ?>
            <?php $stats = raffle_stats((int) $raffle['id']); ?>
            <article class="raffle-card">
                <div>
                    <span class="status-pill <?= e($raffle['status']) ?>"><?= e(status_label($raffle['status'])) ?></span>
                    <h3><?= e($raffle['name']) ?></h3>
                    <p><?= e($raffle['description']) ?></p>
                </div>
                <dl class="meta-list">
                    <div><dt>Premio</dt><dd><?= e($raffle['prize']) ?></dd></div>
                    <div><dt>Fecha</dt><dd><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></dd></div>
                    <div><dt>Precio</dt><dd><?= e(money((float) $raffle['price'])) ?></dd></div>
                    <div><dt>Vendido</dt><dd><?= $stats['sold'] ?> / <?= $stats['total'] ?></dd></div>
                </dl>
                <a class="btn primary full" href="<?= e(url('public/raffle.php?id=' . $raffle['id'])) ?>">Abrir tablero</a>
            </article>
        <?php endforeach; ?>
        <?php if (!$raffles): ?>
            <p class="empty-state">Todavía no hay rifas públicas configuradas.</p>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/components/footer.php'; ?>

