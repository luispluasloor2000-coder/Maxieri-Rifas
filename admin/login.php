<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = clean_string($_POST['username'] ?? '', 60);
    $password = (string) ($_POST['password'] ?? '');
    $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'last' => 0];
    $blocked = ($attempts['count'] ?? 0) >= 5 && time() - (int) ($attempts['last'] ?? 0) < 300;

    if ($blocked) {
        $error = 'Demasiados intentos. Espera unos minutos e intenta de nuevo.';
    } elseif (login($username, $password)) {
        unset($_SESSION['login_attempts']);
        redirect('admin/index.php');
    } else {
        $_SESSION['login_attempts'] = [
            'count' => (int) ($attempts['count'] ?? 0) + 1,
            'last' => time(),
        ];
        $error = 'Usuario o contraseña incorrectos.';
    }
}

$pageTitle = 'Ingresar';
require __DIR__ . '/../components/header.php';
?>
<section class="section narrow">
    <form class="form-panel login-panel" method="post">
        <?= csrf_field() ?>
        <span class="eyebrow">Acceso seguro</span>
        <h1>Panel de administración</h1>
        <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>
        <label>Usuario
            <input name="username" autocomplete="username" required>
        </label>
        <label>Contraseña
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button class="btn primary full" type="submit">Ingresar</button>
    </form>
</section>
<?php require __DIR__ . '/../components/footer.php'; ?>
