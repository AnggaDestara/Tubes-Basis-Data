<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$event = ['title'=>'','category_id'=>'','venue_id'=>'','description'=>'','event_date'=>'','quota'=>50,'price'=>0,'status'=>'draft'];
if ($id) {
    $s = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $s->execute([$id]);
    $event = $s->fetch();
    if (!$event) die('Event tidak ditemukan.');
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$venues     = $pdo->query("SELECT * FROM venues ORDER BY name")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'category_id' => (int)($_POST['category_id'] ?? 0),
        'venue_id'    => (int)($_POST['venue_id'] ?? 0),
        'description' => trim($_POST['description'] ?? ''),
        'event_date'  => str_replace('T', ' ', $_POST['event_date'] ?? ''),
        'quota'       => (int)($_POST['quota'] ?? 0),
        'price'       => (float)($_POST['price'] ?? 0),
        'status'      => $_POST['status'] ?? 'draft',
    ];
    if ($data['title'] === '') $errors[] = 'Judul wajib diisi.';
    if (!$data['category_id'] || !$data['venue_id']) $errors[] = 'Kategori dan lokasi wajib dipilih.';
    if ($data['event_date'] === '') $errors[] = 'Tanggal wajib diisi.';
    if ($data['quota'] < 1) $errors[] = 'Kuota minimal 1.';
    if (!in_array($data['status'], ['draft','published','closed'])) $errors[] = 'Status tidak valid.';

    if (!$errors) {
        if ($id) {
            $pdo->prepare("UPDATE events SET title=?, category_id=?, venue_id=?, description=?, event_date=?, quota=?, price=?, status=? WHERE id=?")
                ->execute([$data['title'],$data['category_id'],$data['venue_id'],$data['description'],$data['event_date'],$data['quota'],$data['price'],$data['status'],$id]);
            flash('Event diperbarui.');
        } else {
            $pdo->prepare("INSERT INTO events (title,category_id,venue_id,description,event_date,quota,price,status,created_by) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$data['title'],$data['category_id'],$data['venue_id'],$data['description'],$data['event_date'],$data['quota'],$data['price'],$data['status'],$_SESSION['user']['id']]);
            flash('Event ditambahkan.');
        }
        redirect('/admin/events.php');
    }
    $event = array_merge($event, $data);
}

$tglValue = $event['event_date'] ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '';
$title = $id ? 'Edit Event' : 'Tambah Event';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<div class="card">
  <h2><?= e($title) ?></h2>
  <?php foreach ($errors as $er): ?><div class="alert error"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post"><?= csrf_field() ?>
    <label>Judul</label><input type="text" name="title" value="<?= e($event['title']) ?>" required>
    <div class="row">
      <div><label>Kategori</label>
        <select name="category_id">
          <option value="">- pilih -</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $event['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select></div>
      <div><label>Lokasi</label>
        <select name="venue_id">
          <option value="">- pilih -</option>
          <?php foreach ($venues as $v): ?>
            <option value="<?= $v['id'] ?>" <?= $event['venue_id'] == $v['id'] ? 'selected' : '' ?>><?= e($v['name']) ?></option>
          <?php endforeach; ?>
        </select></div>
    </div>
    <label>Deskripsi</label><textarea name="description" rows="4"><?= e($event['description']) ?></textarea>
    <div class="row">
      <div><label>Tanggal &amp; Waktu</label><input type="datetime-local" name="event_date" value="<?= e($tglValue) ?>" required></div>
      <div><label>Kuota</label><input type="number" name="quota" min="1" value="<?= e($event['quota']) ?>"></div>
      <div><label>Harga (Rp)</label><input type="number" name="price" min="0" step="1000" value="<?= e($event['price']) ?>"></div>
    </div>
    <label>Status</label>
    <select name="status">
      <?php foreach (['draft','published','closed'] as $st): ?>
        <option value="<?= $st ?>" <?= $event['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn">Simpan</button> <a class="btn gray" href="events.php">Batal</a>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
