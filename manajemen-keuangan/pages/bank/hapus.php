<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$userId = getUserId();
$bankId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE bank_id = ? AND user_id = ?");
$stmt->execute([$bankId, $userId]);
if (!$stmt->fetch()) { header('Location: index.php'); exit; }

// Check if bank is used in transactions
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tbl_transaksi WHERE bank_id = ?");
$stmt->execute([$bankId]);
$result = $stmt->fetch();

if ($result['count'] > 0) {
    $_SESSION['error_message'] = 'Rekening tidak dapat dihapus karena masih digunakan dalam ' . $result['count'] . ' transaksi!';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM tbl_bank WHERE bank_id = ? AND user_id = ?");
    $stmt->execute([$bankId, $userId]);
    $_SESSION['success_message'] = 'Rekening berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Gagal menghapus: ' . $e->getMessage();
}

header('Location: index.php');
exit;
