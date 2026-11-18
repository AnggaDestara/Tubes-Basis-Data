<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['event_id'] ?? 0);
$s = $pdo->prepare("SELECT title FROM events WHERE id=?");
$s->execute([$id]);
$event = $s->fetch();
if (!$event) die('Event tidak ditemukan.');

$s = $pdo->prepare("SELECT u.name, u.email, r.status, r.registered_at FROM registrations r
    JOIN users u ON u.id=r.user_id WHERE r.event_id=? ORDER BY r.registered_at");
$s->execute([$id]);
$rows = $s->fetchAll();

$title = 'Peserta Event';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<h2>Peserta: <?= e($event['title']) ?></h2>
<table>
  <tr><th>#</th><th>Nama</th><th>Email</th><th>Status</th><th>Waktu daftar</th></tr>
  <?php foreach ($rows as $i => $r): ?>
  <tr><td><?= $i + 1 ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td>
      <td><span class="badge <?= $r['status'] === 'registered' ? 'green' : 'gray' ?>"><?= e($r['status']) ?></span></td>
      <td><?= tanggal($r['registered_at']) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5">Belum ada peserta.</td></tr><?php endif; ?>
</table>
<p><a href="events.php">&larr; Kembali</a></p>
<?php include __DIR__ . '/../includes/footer.php'; ?>
