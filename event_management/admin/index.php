<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$stat = [
  'Total Event'     => $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
  'Event Published' => $pdo->query("SELECT COUNT(*) FROM events WHERE status='published'")->fetchColumn(),
  'Total Peserta'   => $pdo->query("SELECT COUNT(*) FROM registrations WHERE status='registered'")->fetchColumn(),
  'Total User'      => $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
];
$recent = $pdo->query("SELECT r.registered_at, u.name, e.title FROM registrations r
    JOIN users u ON u.id=r.user_id JOIN events e ON e.id=r.event_id
    WHERE r.status='registered' ORDER BY r.registered_at DESC LIMIT 5")->fetchAll();

$title = 'Dashboard Admin';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<h2>Dashboard</h2>
<div class="stats">
<?php foreach ($stat as $label => $val): ?>
  <div class="stat"><b><?= $val ?></b><?= e($label) ?></div>
<?php endforeach; ?>
</div>
<h3>Pendaftaran Terbaru</h3>
<table>
  <tr><th>Nama</th><th>Event</th><th>Waktu</th></tr>
  <?php foreach ($recent as $r): ?>
    <tr><td><?= e($r['name']) ?></td><td><?= e($r['title']) ?></td><td><?= tanggal($r['registered_at']) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$recent): ?><tr><td colspan="3">Belum ada pendaftaran.</td></tr><?php endif; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
