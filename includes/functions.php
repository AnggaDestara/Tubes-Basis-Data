<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

// Sesuaikan dengan nama folder proyek di Laragon (C:\laragon\www\event_management)
define('BASE_URL', '/event_management');

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect($path) { header('Location: ' . BASE_URL . $path); exit; }
function is_login() { return isset($_SESSION['user']); }
function require_login() { if (!is_login()) redirect('/login.php'); }
function require_admin() {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') { http_response_code(403); die('Akses ditolak.'); }
}
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); die('Token CSRF tidak valid.');
    }
}
function rupiah($n) { return $n > 0 ? 'Rp ' . number_format($n, 0, ',', '.') : 'Gratis'; }
function tanggal($d) { return date('d M Y, H:i', strtotime($d)); }
