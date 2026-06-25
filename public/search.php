<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$raffles = db()->query("SELECT id, name FROM raffles WHERE status IN ('active','finished') ORDER BY draw_date ASC")->fetchAll();
$result = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['number'])) {
    $raffleId = (int) ($_GET['raffle_id'] ?? 0);
    $number = (int) ($_GET['number'] ?? 0);

    if ($raffleId <= 0 || $number <= 0) {
        $error = 'Selecciona una rifa y escribe un número válido.';
    } else {
        $raffle = raffle_public_find($raffleId);
        $item = raffle_number_find($raffleId, $number);
        if ($raffle && $item) {
            $result = ['raffle' => $raffle, 'number' => $item];
        } else {
            $error = 'No encontramos ese número en una rifa pública.';
        }
    }
}

$pageTitle = 'Verificar número';
require __DIR__ . '/../components/header.php';
?>
<section class="section narrow">
    <div class="section-heading">
        <span class="eyebrow">Consulta pública</span>
        <h1>Verifica tu número</h1>
        <p>El sistema muestra solo información pública para proteger los datos del comprador.</p>
    </div>
    <form class="form-panel" method="get">
        <label>Rifa
            <select name="raffle_id" required>
                <option value="">Seleccionar</option>
                <?php foreach ($raffles as $raffle): ?>
                    <option value="<?= (int) $raffle['id'] ?>" <?= ((int) ($_GET['raffle_id'] ?? 0) === (int) $raffle['id']) ? 'selected' : '' ?>>
                        <?= e($raffle['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Número
            <input type="number" name="number" min="1" value="<?= e($_GET['number'] ?? '') ?>" required>
        </label>
        <button class="btn primary" type="submit">Consultar</button>
    </form>

    <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>

    <?php if ($result): ?>
        <?php $item = $result['number']; $raffle = $result['raffle']; ?>
        <article class="verification-card">
            <span class="status-pill <?= e($item['status']) ?>"><?= e(status_label($item['status'])) ?></span>
            <h2>Número <?= (int) $item['number_value'] ?></h2>
            <dl class="meta-list">
                <div><dt>Rifa</dt><dd><?= e($raffle['name']) ?></dd></div>
                <div><dt>Comprador</dt><dd><?= e($item['buyer_name'] ?: 'Sin comprador registrado') ?></dd></div>
                <div><dt>Premio</dt><dd><?= e($raffle['prize']) ?></dd></div>
                <div><dt>Fecha del sorteo</dt><dd><?= e(date('d/m/Y H:i', strtotime($raffle['draw_date']))) ?></dd></div>
                <div><dt>Registro</dt><dd><?= $item['registered_at'] ? e(date('d/m/Y H:i', strtotime($item['registered_at']))) : 'Sin registro' ?></dd></div>
            </dl>
            <a class="btn ghost" href="<?= e(public_number_url((int) $raffle['id'], (int) $item['number_value'])) ?>">Abrir enlace verificable</a>
        </article>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../components/footer.php'; ?>

