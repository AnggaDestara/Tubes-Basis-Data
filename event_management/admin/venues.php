<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (($_POST['action'] ?? '') === 'add' && trim($_POST['name'] ?? '') !== '') {
            $pdo->prepare("INSERT INTO venues (name,address,capacity) VALUES (?,?,?)")
                ->execute([trim($_POST['name']), trim($_POST['address'] ?? ''), (int)($_POST['capacity'] ?? 0)]);
            flash('Lokasi ditambahkan.');
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $pdo->prepare("DELETE FROM venues WHERE id=?")->execute([(int)$_POST['id']]);
            flash('Lokasi dihapus.');
        }
    } catch (PDOException $ex) {
        flash('Gagal: lokasi masih dipakai event.', 'error');
    }
    redirect('/admin/venues.php');
}
$rows = $pdo->query("SELECT * FROM venues ORDER BY name")->fetchAll();

$title = 'Lokasi';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<h2>Lokasi / Venue</h2>
<form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="action" value="add">
  <div><input type="text" name="name" placeholder="Nama lokasi" required></div>
  <div><input type="text" name="address" placeholder="Alamat" required></div>
  <div><input type="number" name="capacity" placeholder="Kapasitas" min="0"></div>
  <div style="flex:0"><button class="btn">Tambah</button></div>
</form>
<table>
  <tr><th>Nama</th><th>Alamat</th><th>Kapasitas</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['name']) ?></td><td><?= e($r['address']) ?></td><td><?= $r['capacity'] ?></td><td>
    <form method="post" class="inline" onsubmit="return confirm('Hapus lokasi?')"><?= csrf_field() ?>
      <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
      <button class="btn sm danger">Hapus</button></form></td></tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
