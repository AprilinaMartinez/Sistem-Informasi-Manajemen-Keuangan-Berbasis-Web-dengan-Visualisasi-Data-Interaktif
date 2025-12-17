<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$userId = getUserId();
$kategoriId = $_GET['id'] ?? 0;

// Cek apakah kategori milik user dan ada transaksi yang menggunakan kategori ini
$stmt = $pdo->prepare("SELECT * FROM tbl_kategori WHERE kategori_id = ? AND user_id = ?");
$stmt->execute([$kategoriId, $userId]);
$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: index.php');
    exit;
}

// Cek apakah ada transaksi yang menggunakan kategori ini (foreign key check)
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tbl_transaksi WHERE kategori_id = ?");
$stmt->execute([$kategoriId]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    $_SESSION['error_message'] = 'Kategori tidak dapat dihapus karena masih digunakan dalam ' . $result['count'] . ' transaksi!';
    header('Location: index.php');
    exit;
}

// Hapus kategori
try {
    $stmt = $pdo->prepare("DELETE FROM tbl_kategori WHERE kategori_id = ? AND user_id = ?");
    $stmt->execute([$kategoriId, $userId]);
    
    $_SESSION['success_message'] = 'Kategori berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Gagal menghapus kategori: ' . $e->getMessage();
}

header('Location: index.php');
exit;
