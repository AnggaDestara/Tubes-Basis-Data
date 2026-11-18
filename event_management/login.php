<?php
require_once __DIR__ . '/includes/functions.php';
if (is_login()) redirect('/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([trim($_POST['email'] ?? '')]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']];
        redirect($user['role'] === 'admin' ? '/admin/index.php' : '/index.php');
    }
    $error = 'Email atau password salah.';
}

$title = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="card form-box">
  <h2>Login</h2>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post"><?= csrf_field() ?>
    <label>Email</label><input type="email" name="email" required>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn">Masuk</button>
  </form>
  <p>Belum punya akun? <a href="<?= BASE_URL ?>/register.php">Daftar</a></p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
