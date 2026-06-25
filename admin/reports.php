<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();
$rows = db()->query(
    "SELECT r.*, 
        COUNT(n.id) total,
        SUM(n.status = 'available') available,
        SUM(n.status = 'reserved') reserved,
        SUM(n.status = 'sold') sold,
        SUM(CASE WHEN n.status = 'sold' THEN r.price ELSE 0 END) income
     FROM raffles r
     LEFT JOIN raffle_numbers n ON n.raffle_id = r.id
     GROUP BY r.id
     ORDER BY r.created_at DESC"
)->fetchAll();
$pageTitle = 'Reportes';
require __DIR__ . '/../components/admin_header.php';
?>
<section class="panel">
    <div class="panel-head"><h2>Estadísticas por rifa</h2></div>
    <div class="report-grid">
        <?php foreach ($rows as $row): ?>
            <?php $percent = (int) $row['total'] ? round(((int) $row['sold'] / (int) $row['total']) * 100) : 0; ?>
            <article class="report-card">
                <h3><?= e($row['name']) ?></h3>
                <div class="progress"><span style="width: <?= $percent ?>%"></span></div>
                <dl class="meta-list">
                    <div><dt>Vendido</dt><dd><?= (int) $row['sold'] ?> / <?= (int) $row['total'] ?> (<?= $percent ?>%)</dd></div>
                    <div><dt>Reservado</dt><dd><?= (int) $row['reserved'] ?></dd></div>
                    <div><dt>Disponible</dt><dd><?= (int) $row['available'] ?></dd></div>
                    <div><dt>Ingresos</dt><dd><?= e(money((float) $row['income'])) ?></dd></div>
                </dl>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>

