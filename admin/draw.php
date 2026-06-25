<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();
$raffles = db()->query("SELECT id, name, prize, draw_date FROM raffles WHERE status IN ('active','finished') ORDER BY draw_date DESC")->fetchAll();
$winner = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $raffleId = (int) ($_POST['raffle_id'] ?? 0);
    $stmt = db()->prepare("SELECT n.*, r.prize FROM raffle_numbers n JOIN raffles r ON r.id = n.raffle_id WHERE n.raffle_id = ? AND n.status = 'sold' ORDER BY RAND() LIMIT 1");
    $stmt->execute([$raffleId]);
    $winner = $stmt->fetch();
    if ($winner) {
        $insert = db()->prepare(
            'INSERT INTO draw_history (raffle_id, raffle_number_id, prize, winner_name, number_value, drawn_at, drawn_by)
             VALUES (?, ?, ?, ?, ?, NOW(), ?)'
        );
        $insert->execute([$raffleId, $winner['id'], $winner['prize'], $winner['buyer_name'], $winner['number_value'], $user['id']]);
    }
}

$history = db()->query(
    'SELECT h.*, r.name raffle_name FROM draw_history h JOIN raffles r ON r.id = h.raffle_id ORDER BY h.drawn_at DESC LIMIT 20'
)->fetchAll();
$pageTitle = 'Sorteo';
require __DIR__ . '/../components/admin_header.php';
?>
<section class="draw-stage">
    <form method="post" class="form-panel draw-panel">
        <?= csrf_field() ?>
        <span class="eyebrow">Extracción aleatoria</span>
        <h2>Sorteo en pantalla completa</h2>
        <label>Rifa
            <select name="raffle_id" required>
                <option value="">Seleccionar</option>
                <?php foreach ($raffles as $raffle): ?>
                    <option value="<?= (int) $raffle['id'] ?>"><?= e($raffle['name']) ?> · <?= e($raffle['prize']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="btn primary full draw-button" type="submit">Extraer ganador</button>
    </form>
    <?php if ($winner): ?>
        <article class="winner-card">
            <span>Ganador</span>
            <strong><?= (int) $winner['number_value'] ?></strong>
            <p><?= e($winner['buyer_name'] ?: 'Sin nombre') ?></p>
        </article>
    <?php endif; ?>
</section>
<section class="panel">
    <div class="panel-head"><h2>Historial</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Rifa</th><th>Número</th><th>Ganador</th><th>Premio</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach ($history as $item): ?>
                <tr>
                    <td><?= e($item['raffle_name']) ?></td>
                    <td><?= (int) $item['number_value'] ?></td>
                    <td><?= e($item['winner_name']) ?></td>
                    <td><?= e($item['prize']) ?></td>
                    <td><?= e(date('d/m/Y H:i', strtotime($item['drawn_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>

