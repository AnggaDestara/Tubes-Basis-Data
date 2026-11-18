<?php
require_once __DIR__ . '/functions.php';
$flash = flash();
$u = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'EventKu') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="<?= BASE_URL ?>/index.php">EventKu<small>Sistem Manajemen Event</small></a>
  <div class="links">
    <a href="<?= BASE_URL ?>/index.php">Event</a>
    <?php if ($u): ?>
      <?php if ($u['role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/admin/index.php">Dashboard Admin</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/my_registrations.php">Pendaftaran Saya</a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/logout.php">Logout (<?= e($u['name']) ?>)</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login.php">Login</a>
      <a href="<?= BASE_URL ?>/register.php">Daftar</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
<?php if ($flash): ?>
  <div class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>
