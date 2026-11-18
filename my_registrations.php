<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$stmt = $pdo->prepare("SELECT r.*, e.id AS event_id, e.title, e.event_date, v.name AS venue
    FROM registrations r
    JOIN events e ON e.id=r.event_id
    JOIN venues v ON v.id=e.venue_id
    WHERE r.user_id=? ORDER BY r.registered_at DESC");
$stmt->execute([$_SESSION['user']['id']]);
$rows = $stmt->fetchAll();

$title = 'Pendaftaran Saya';
include __DIR__ . '/includes/header.php';
?>
<h2>Pendaftaran Saya</h2>
<table>
  <tr><th>Event</th><th>Tanggal</th><th>Lokasi</th><th>Status</th><th>Didaftarkan</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><a href="<?= BASE_URL ?>/event_detail.php?id=<?= $r['event_id'] ?>"><?= e($r['title']) ?></a></td>
    <td><?= tanggal($r['event_date']) ?></td>
    <td><?= e($r['venue']) ?></td>
    <td><span class="badge <?= $r['status'] === 'registered' ? 'green' : 'gray' ?>"><?= e($r['status']) ?></span></td>
    <td><?= tanggal($r['registered_at']) ?></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5">Belum ada pendaftaran.</td></tr><?php endif; ?>
</table>
<?php include __DIR__ . '/includes/footer.php'; ?>
