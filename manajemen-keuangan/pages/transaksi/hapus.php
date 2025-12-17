<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$userId = getUserId();
$transaksiId = $_GET['id'] ?? 0;

// Get transaksi data
$stmt = $pdo->prepare("SELECT * FROM tbl_transaksi WHERE transaksi_id = ? AND user_id = ?");
$stmt->execute([$transaksiId, $userId]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Rollback bank balance if applicable
    if ($transaksi['bank_id']) {
        if ($transaksi['jenis_transaksi'] === 'Pemasukan') {
            $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini - ? WHERE bank_id = ? AND user_id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini + ? WHERE bank_id = ? AND user_id = ?");
        }
        $stmt->execute([$transaksi['jumlah'], $transaksi['bank_id'], $userId]);
    }
    
    // Delete transaksi
    $stmt = $pdo->prepare("DELETE FROM tbl_transaksi WHERE transaksi_id = ? AND user_id = ?");
    $stmt->execute([$transaksiId, $userId]);
    
    $pdo->commit();
    
    $_SESSION['success_message'] = 'Transaksi berhasil dihapus dan saldo bank telah diupdate!';
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = 'Gagal menghapus transaksi: ' . $e->getMessage();
}

header('Location: index.php');
exit;
