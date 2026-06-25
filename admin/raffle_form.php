<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();
$id = (int) ($_GET['id'] ?? 0);
$raffle = $id ? raffle_find($id) : null;

if ($id && !$raffle) {
    http_response_code(404);
    exit('Rifa no encontrada.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $data = [
        'name' => clean_string($_POST['name'] ?? '', 160),
        'description' => clean_string($_POST['description'] ?? '', 3000),
        'prize' => clean_string($_POST['prize'] ?? '', 180),
        'draw_date' => str_replace('T', ' ', clean_string($_POST['draw_date'] ?? '', 40)),
        'numbers_quantity' => max(1, (int) ($_POST['numbers_quantity'] ?? 1)),
        'price' => max(0, (float) ($_POST['price'] ?? 0)),
        'status' => in_array($_POST['status'] ?? '', ['active', 'finished', 'hidden'], true) ? $_POST['status'] : 'hidden',
    ];

    if ($id) {
        $stmt = db()->prepare(
            'UPDATE raffles SET name=?, description=?, prize=?, draw_date=?, numbers_quantity=?, price=?, status=? WHERE id=?'
        );
        $stmt->execute([$data['name'], $data['description'], $data['prize'], $data['draw_date'], $data['numbers_quantity'], $data['price'], $data['status'], $id]);
        ensure_numbers_for_raffle($id, $data['numbers_quantity']);
    } else {
        $stmt = db()->prepare(
            'INSERT INTO raffles (name, description, prize, draw_date, numbers_quantity, price, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$data['name'], $data['description'], $data['prize'], $data['draw_date'], $data['numbers_quantity'], $data['price'], $data['status']]);
        $id = (int) db()->lastInsertId();
        ensure_numbers_for_raffle($id, $data['numbers_quantity']);
    }
    redirect('admin/numbers.php?raffle_id=' . $id);
}

$pageTitle = $raffle ? 'Editar rifa' : 'Crear rifa';
require __DIR__ . '/../components/admin_header.php';
?>
<form class="form-panel" method="post">
    <?= csrf_field() ?>
    <div class="form-grid">
        <label>Nombre
            <input name="name" value="<?= e($raffle['name'] ?? '') ?>" required>
        </label>
        <label>Premio
            <input name="prize" value="<?= e($raffle['prize'] ?? '') ?>" required>
        </label>
        <label>Fecha y hora
            <input type="datetime-local" name="draw_date" value="<?= $raffle ? e(date('Y-m-d\TH:i', strtotime($raffle['draw_date']))) : '' ?>" required>
        </label>
        <label>Cantidad de números
            <input type="number" name="numbers_quantity" min="1" value="<?= e((string) ($raffle['numbers_quantity'] ?? 100)) ?>" required>
        </label>
        <label>Precio
            <input type="number" step="0.01" min="0" name="price" value="<?= e((string) ($raffle['price'] ?? '1.00')) ?>" required>
        </label>
        <label>Estado
            <select name="status">
                <?php foreach (['active', 'finished', 'hidden'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= (($raffle['status'] ?? 'active') === $status) ? 'selected' : '' ?>><?= e(status_label($status)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <label>Descripción
        <textarea name="description" rows="5"><?= e($raffle['description'] ?? '') ?></textarea>
    </label>
    <button class="btn primary" type="submit">Guardar rifa</button>
</form>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>
