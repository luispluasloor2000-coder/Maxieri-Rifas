<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pageTitle = 'Dashboard';
require __DIR__ . '/../components/admin_header.php';

$totals = db()->query(
    "SELECT
        COUNT(DISTINCT r.id) raffles,
        COUNT(n.id) numbers_total,
        SUM(n.status = 'sold') sold,
        SUM(n.status = 'reserved') reserved,
        SUM(n.status = 'available') available,
        SUM(CASE WHEN n.status = 'sold' THEN r.price ELSE 0 END) income
     FROM raffles r
     LEFT JOIN raffle_numbers n ON n.raffle_id = r.id"
)->fetch();
$recent = db()->query('SELECT * FROM raffles ORDER BY created_at DESC LIMIT 6')->fetchAll();
?>
<div class="stats-row admin-stats">
    <div><strong><?= (int) $totals['raffles'] ?></strong><span>Rifas</span></div>
    <div><strong><?= (int) $totals['sold'] ?></strong><span>Vendidos</span></div>
    <div><strong><?= (int) $totals['reserved'] ?></strong><span>Reservados</span></div>
    <div><strong><?= e(money((float) $totals['income'])) ?></strong><span>Ingresos</span></div>
</div>
<section class="panel">
    <div class="panel-head">
        <h2>Rifas recientes</h2>
        <a class="btn primary" href="<?= e(url('admin/raffle_form.php')) ?>">Crear rifa</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nombre</th><th>Premio</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recent as $raffle): ?>
                <tr>
                    <td><?= e($raffle['name']) ?></td>
                    <td><?= e($raffle['prize']) ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></td>
                    <td><?= e(status_label($raffle['status'])) ?></td>
                    <td><a href="<?= e(url('admin/numbers.php?raffle_id=' . $raffle['id'])) ?>">Números</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>

