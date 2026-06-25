<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$user = require_login();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current, $row['password_hash'])) {
        $error = 'La contraseña actual no coincide.';
    } elseif (strlen($new) < 8) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($new !== $confirm) {
        $error = 'La confirmación no coincide.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $update = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $update->execute([$hash, (int) $user['id']]);
        $message = 'Contraseña actualizada correctamente.';
    }
}

$pageTitle = 'Contraseña';
require __DIR__ . '/../components/admin_header.php';
?>
<form class="form-panel" method="post">
    <?= csrf_field() ?>
    <?php if ($message): ?><p class="alert success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>
    <label>Contraseña actual
        <input type="password" name="current_password" required>
    </label>
    <label>Nueva contraseña
        <input type="password" name="new_password" minlength="8" required>
    </label>
    <label>Confirmar nueva contraseña
        <input type="password" name="confirm_password" minlength="8" required>
    </label>
    <button class="btn primary" type="submit">Actualizar contraseña</button>
</form>
<?php require __DIR__ . '/../components/admin_footer.php'; ?>

