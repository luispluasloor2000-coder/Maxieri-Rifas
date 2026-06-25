<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        $stmt = db()->prepare('DELETE FROM raffles WHERE id = ?');
        $stmt->execute([$id]);
    }

    if ($action === 'duplicate' && $id > 0) {
        $raffle = raffle_find($id);
        if ($raffle) {
            $stmt = db()->prepare(
                'INSERT INTO raffles (name, description, prize, draw_date, numbers_quantity, price, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $raffle['name'] . ' copia',
                $raffle['description'],
                $raffle['prize'],
                $raffle['draw_date'],
                $raffle['numbers_quantity'],
                $raffle['price'],
                'hidden',
            ]);
            ensure_numbers_for_raffle((int) db()->lastInsertId(), (int) $raffle['numbers_quantity']);
        }
    }
    redirect('admin/raffles.php');
}

$raffles = db()->query('SELECT * FROM raffles ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Rifas';
require __DIR__ . '/../components/admin_header.php';
?>
<section class="panel">
    <div class="panel-head">
        <h2>Administrar rifas</h2>
        <a class="btn primary" href="<?= e(url('admin/raffle_form.php')) ?>">Nueva rifa</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Nombre</th><th>Premio</th><th>Números</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($raffles as $raffle): ?>
                <tr>
                    <td><?= (int) $raffle['id'] ?></td>
                    <td><?= e($raffle['name']) ?></td>
                    <td><?= e($raffle['prize']) ?></td>
                    <td><?= (int) $raffle['numbers_quantity'] ?></td>
                    <td><?= e(money((float) $raffle['price'])) ?></td>
                    <td><?= e(status_label($raffle['status'])) ?></td>
                    <td class="actions">
                        <a href="<?= e(url('admin/raffle_form.php?id=' . $raffle['id'])) ?>">Editar</a>
                        <a href="<?= e(url('admin/numbers.php?raffle_id=' . $raffle['id'])) ?>">Números</a>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $raffle['id'] ?>">
                            <button name="action" value="duplicate">Duplicar</button>
                            <button name="action" value="delete" data-confirm="Eliminar esta rifa y sus números?">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>

