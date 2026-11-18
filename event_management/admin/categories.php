<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (($_POST['action'] ?? '') === 'add' && trim($_POST['name'] ?? '') !== '') {
            $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([trim($_POST['name'])]);
            flash('Kategori ditambahkan.');
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]);
            flash('Kategori dihapus.');
        }
    } catch (PDOException $ex) {
        flash('Gagal: nama sudah ada atau kategori masih dipakai event.', 'error');
    }
    redirect('/admin/categories.php');
}
$rows = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$title = 'Kategori';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/_menu.php';
?>
<h2>Kategori</h2>
<form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="action" value="add">
  <div><input type="text" name="name" placeholder="Nama kategori baru" required></div>
  <div style="flex:0"><button class="btn">Tambah</button></div>
</form>
<table>
  <tr><th>Nama</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= e($r['name']) ?></td><td>
    <form method="post" class="inline" onsubmit="return confirm('Hapus kategori?')"><?= csrf_field() ?>
      <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
      <button class="btn sm danger">Hapus</button></form></td></tr>
  <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
