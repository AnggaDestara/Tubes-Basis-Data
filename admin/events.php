<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $pdo->prepare("DELETE FROM events WHERE id=?")->execute([(int)($_POST['id'] ?? 0)]);
    flash('Event dihapus.');
    redirect('/admin/events.php');
}

$events = $pdo->query("SELECT e.*, c.name AS category, v.name AS venue,
    (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status='registered') AS terdaftar
    FROM events e JOIN categories c ON c.id=e.category_id JOIN venues v ON v.id=e.venue_id
    ORDER BY e.event_date DESC")->fetchAll();

$title = 'Kelola Event';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<h2>Kelola Event <a class="btn sm" href="event_form.php">+ Tambah</a></h2>
<table>
  <tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Peserta</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($events as $ev): ?>
  <tr>
    <td><?= e($ev['title']) ?></td>
    <td><?= e($ev['category']) ?></td>
    <td><?= tanggal($ev['event_date']) ?></td>
    <td><a href="participants.php?event_id=<?= $ev['id'] ?>"><?= $ev['terdaftar'] ?> / <?= $ev['quota'] ?></a></td>
    <td><span class="badge <?= $ev['status'] === 'published' ? 'green' : ($ev['status'] === 'closed' ? 'red' : 'gray') ?>"><?= e($ev['status']) ?></span></td>
    <td>
      <a class="btn sm gray" href="event_form.php?id=<?= $ev['id'] ?>">Edit</a>
      <form method="post" class="inline" onsubmit="return confirm('Hapus event ini?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $ev['id'] ?>">
        <button class="btn sm danger">Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$events): ?><tr><td colspan="6">Belum ada event.</td></tr><?php endif; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
