<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$userId = getUserId();
$piutangId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM tbl_piutang WHERE piutang_id = ? AND user_id = ?");
$stmt->execute([$piutangId, $userId]);
if (!$stmt->fetch()) { header('Location: ../hutang/index.php?tab=piutang'); exit; }

try {
    $stmt = $pdo->prepare("DELETE FROM tbl_piutang WHERE piutang_id = ? AND user_id = ?");
    $stmt->execute([$piutangId, $userId]);
    $_SESSION['success_message'] = 'Piutang berhasil dihapus!';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Gagal menghapus: ' . $e->getMessage();
}

header('Location: ../hutang/index.php?tab=piutang');
exit;
