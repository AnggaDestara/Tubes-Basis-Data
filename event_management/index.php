<?php
require_once __DIR__ . '/includes/functions.php';

$q   = trim($_GET['q'] ?? '');
$cat = (int)($_GET['category'] ?? 0);

$sql = "SELECT e.*, c.name AS category, v.name AS venue,
        (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status='registered') AS terdaftar
        FROM events e
        JOIN categories c ON c.id=e.category_id
        JOIN venues v ON v.id=e.venue_id
        WHERE e.status='published' AND e.title LIKE ?";
$params = ["%$q%"];
if ($cat) { $sql .= " AND e.category_id=?"; $params[] = $cat; }
$sql .= " ORDER BY e.event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$title = 'Daftar Event';
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <h1>Temukan &amp; Ikuti Event Kampus</h1>
  <p>Seminar, workshop, lomba, dan berbagai kegiatan menarik. Daftar dengan mudah dan pantau pendaftaran Anda di satu tempat.</p>
</section>
<h2>Event Mendatang</h2>
<form method="get" class="row">
  <div><input type="text" name="q" placeholder="Cari event..." value="<?= e($q) ?>"></div>
  <div>
    <select name="category">
      <option value="0">Semua kategori</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div style="flex:0"><button class="btn">Cari</button></div>
</form>

<div class="grid">
<?php foreach ($events as $ev): $sisa = $ev['quota'] - $ev['terdaftar']; ?>
  <div class="card">
    <span class="badge"><?= e($ev['category']) ?></span>
    <h3><a href="<?= BASE_URL ?>/event_detail.php?id=<?= $ev['id'] ?>"><?= e($ev['title']) ?></a></h3>
    <p> <?= tanggal($ev['event_date']) ?><br> <?= e($ev['venue']) ?><?= rupiah($ev['price']) ?></p>
    <span class="badge <?= $sisa > 0 ? 'green' : 'red' ?>"><?= $sisa > 0 ? "Sisa kuota: $sisa" : 'Kuota penuh' ?></span>
  </div>
<?php endforeach; ?>
<?php if (!$events): ?><p>Belum ada event yang tersedia.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
