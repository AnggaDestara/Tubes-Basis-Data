<?php
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT e.*, c.name AS category, v.name AS venue, v.address,
    (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id AND r.status='registered') AS terdaftar
    FROM events e
    JOIN categories c ON c.id=e.category_id
    JOIN venues v ON v.id=e.venue_id
    WHERE e.id=? AND e.status<>'draft'");
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) { http_response_code(404); die('Event tidak ditemukan.'); }

$myReg = null;
if (is_login()) {
    $s = $pdo->prepare("SELECT * FROM registrations WHERE event_id=? AND user_id=?");
    $s->execute([$id, $_SESSION['user']['id']]);
    $myReg = $s->fetch();
}
$already = $myReg && $myReg['status'] === 'registered';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    csrf_check();
    $uid = $_SESSION['user']['id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        if ($already) {
            flash('Anda sudah terdaftar di event ini.', 'error');
        } elseif ($event['status'] !== 'published') {
            flash('Pendaftaran sudah ditutup.', 'error');
        } elseif ($event['terdaftar'] >= $event['quota']) {
            flash('Maaf, kuota sudah penuh.', 'error');
        } else {
            $pdo->prepare("INSERT INTO registrations (event_id,user_id,status) VALUES (?,?,'registered')
                           ON DUPLICATE KEY UPDATE status='registered', registered_at=NOW()")
                ->execute([$id, $uid]);
            flash('Pendaftaran berhasil!');
        }
    } elseif ($action === 'cancel' && $already) {
        $pdo->prepare("UPDATE registrations SET status='cancelled' WHERE event_id=? AND user_id=?")
            ->execute([$id, $uid]);
        flash('Pendaftaran dibatalkan.');
    }
    redirect('/event_detail.php?id=' . $id);
}

$sisa = $event['quota'] - $event['terdaftar'];
$title = $event['title'];
include __DIR__ . '/includes/header.php';
?>
<div class="card">
  <span class="badge"><?= e($event['category']) ?></span>
  <h2><?= e($event['title']) ?></h2>
  <p> <?= tanggal($event['event_date']) ?><br>
      <?= e($event['venue']) ?> - <?= e($event['address']) ?><br>
      <?= rupiah($event['price']) ?><br>
      Terdaftar: <?= $event['terdaftar'] ?> / <?= $event['quota'] ?></p>
  <p><?= nl2br(e($event['description'])) ?></p>

  <?php if ($already): ?>
    <form method="post"><?= csrf_field() ?>
      <input type="hidden" name="action" value="cancel">
      <span class="badge green">Anda sudah terdaftar</span>
      <button class="btn danger sm" onclick="return confirm('Batalkan pendaftaran?')">Batalkan</button>
    </form>
  <?php elseif ($event['status'] !== 'published'): ?>
    <span class="badge gray">Pendaftaran ditutup</span>
  <?php elseif ($sisa <= 0): ?>
    <span class="badge red">Kuota penuh</span>
  <?php elseif (!is_login()): ?>
    <a class="btn" href="<?= BASE_URL ?>/login.php">Login untuk mendaftar</a>
  <?php else: ?>
    <form method="post"><?= csrf_field() ?>
      <input type="hidden" name="action" value="register">
      <button class="btn">Daftar Sekarang</button>
    </form>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
