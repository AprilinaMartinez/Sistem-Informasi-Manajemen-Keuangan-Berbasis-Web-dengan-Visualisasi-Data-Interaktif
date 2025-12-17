<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();
$userId = getUserId();
$hutangId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM tbl_hutang WHERE hutang_id = ? AND user_id = ?");
$stmt->execute([$hutangId, $userId]);
$hutang = $stmt->fetch();

if (!$hutang) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM tbl_hutang WHERE hutang_id = ? AND user_id = ?");
    $stmt->execute([$hutangId, $userId]);
    $_SESSION['success_message'] = 'Hutang berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Gagal menghapus hutang: ' . $e->getMessage();
}

header('Location: index.php');
exit;
