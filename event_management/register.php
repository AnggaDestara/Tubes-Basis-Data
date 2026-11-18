<?php
require_once __DIR__ . '/includes/functions.php';
if (is_login()) redirect('/index.php');

$errors = [];
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if (strlen($pass) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($pass !== $pass2) $errors[] = 'Konfirmasi password tidak sama.';

    if (!$errors) {
        $cek = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'user')")
                ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('Registrasi berhasil, silakan login.');
            redirect('/login.php');
        }
    }
}

$title = 'Daftar Akun';
include __DIR__ . '/includes/header.php';
?>
<div class="card form-box">
  <h2>Daftar Akun</h2>
  <?php foreach ($errors as $er): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post"><?= csrf_field() ?>
    <label>Nama</label><input type="text" name="name" value="<?= e($name) ?>" required>
    <label>Email</label><input type="email" name="email" value="<?= e($email) ?>" required>
    <label>Password</label><input type="password" name="password" required>
    <label>Konfirmasi Password</label><input type="password" name="password2" required>
    <button class="btn">Daftar</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
