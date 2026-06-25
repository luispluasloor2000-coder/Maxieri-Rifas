<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();
$raffleId = (int) ($_GET['raffle_id'] ?? 0);
$raffle = raffle_find($raffleId);
if (!$raffle) {
    http_response_code(404);
    exit('Rifa no encontrada.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $numberId = (int) ($_POST['number_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'sell_print') {
        $result = sell_raffle_number($raffleId, $numberId, (int) $user['id'], $_POST);
        if ($result['ok']) {
            redirect('tickets/ticket.php?raffle_id=' . $raffleId . '&number=' . $result['number_value'] . '&autoprint=1');
        }
        set_flash('error', $result['message']);
    } else {
        $result = update_raffle_number_admin($raffleId, $numberId, (int) $user['id'], $_POST);
        set_flash($result['ok'] ? 'success' : 'error', $result['ok'] ? 'Número actualizado correctamente.' : $result['message']);
    }
    redirect('admin/numbers.php?raffle_id=' . $raffleId);
}

$numbers = raffle_numbers($raffleId);
$stats = raffle_stats($raffleId);
$flash = get_flash();
$pageTitle = 'Números';
require __DIR__ . '/../components/admin_header.php';
?>
<section class="panel">
    <div class="panel-head">
        <div>
            <h2><?= e($raffle['name']) ?></h2>
            <p><?= e($raffle['prize']) ?> · <?= e(money((float) $raffle['price'])) ?></p>
        </div>
        <div class="actions">
            <a class="btn ghost" href="<?= e(url('public/raffle.php?id=' . $raffleId)) ?>">Ver público</a>
            <a class="btn ghost" href="<?= e(url('admin/export.php?raffle_id=' . $raffleId)) ?>">Exportar CSV</a>
        </div>
    </div>
    <div class="stats-row">
        <div><strong><?= $stats['available'] ?></strong><span>Disponibles</span></div>
        <div><strong><?= $stats['reserved'] ?></strong><span>Reservados</span></div>
        <div><strong><?= $stats['sold'] ?></strong><span>Vendidos</span></div>
        <div><strong><?= $stats['total'] ? round(($stats['sold'] / $stats['total']) * 100) : 0 ?>%</strong><span>Vendido</span></div>
    </div>
    <?php if ($flash): ?><p class="alert <?= $flash['type'] === 'success' ? 'success' : '' ?>"><?= e($flash['message']) ?></p><?php endif; ?>
    <div class="number-board admin-board">
        <?php foreach ($numbers as $number): ?>
            <button class="number-cell <?= e($number['status']) ?>" data-number-id="<?= (int) $number['id'] ?>" data-number='<?= e(json_encode($number, JSON_UNESCAPED_UNICODE)) ?>'>
                <?= (int) $number['number_value'] ?>
            </button>
        <?php endforeach; ?>
    </div>
</section>

<dialog class="modal" id="numberModal">
    <form method="post" class="form-panel">
        <?= csrf_field() ?>
        <input type="hidden" name="number_id" id="modalNumberId">
        <div class="panel-head">
            <h2 id="modalTitle">Número</h2>
            <button type="button" data-close-modal>cerrar</button>
        </div>
        <label>Estado
            <select name="status" id="modalStatus">
                <option value="available">Disponible</option>
                <option value="reserved">Reservado</option>
                <option value="sold">Vendido</option>
            </select>
        </label>
        <div class="form-grid">
            <label>Comprador
                <input name="buyer_name" id="modalBuyerName">
            </label>
            <label>Teléfono
                <input name="buyer_phone" id="modalBuyerPhone">
            </label>
            <label>Ciudad
                <input name="buyer_city" id="modalBuyerCity">
            </label>
        </div>
        <label>Observaciones
            <textarea name="notes" id="modalNotes" rows="3"></textarea>
        </label>
        <div class="actions">
            <button class="btn primary" type="submit" name="action" value="sell_print">Vender e imprimir</button>
            <button class="btn ghost" type="submit" name="action" value="save">Guardar</button>
            <a class="btn ghost" id="ticketLink" href="#">Boleto</a>
            <a class="btn ghost" id="publicLink" href="#">Vista pública</a>
        </div>
    </form>
</dialog>
<script>
window.RAFFLE_ID = <?= (int) $raffleId ?>;
window.APP_BASE = <?= json_encode(rtrim(APP_URL, '/')) ?>;
</script>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>
