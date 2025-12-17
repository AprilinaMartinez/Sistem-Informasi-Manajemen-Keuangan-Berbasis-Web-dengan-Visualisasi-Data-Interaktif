<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'bank';
$pageTitle = 'Detail Rekening';
$userId = getUserId();
$bankId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE bank_id = ? AND user_id = ?");
$stmt->execute([$bankId, $userId]);
$bank = $stmt->fetch();
if (!$bank) { header('Location: index.php'); exit; }

// Get transactions for this bank
$stmt = $pdo->prepare("SELECT t.*, k.nama_kategori 
                       FROM tbl_transaksi t
                       JOIN tbl_kategori k ON t.kategori_id = k.kategori_id
                       WHERE t.bank_id = ? AND t.user_id = ?
                       ORDER BY t.tanggal DESC");
$stmt->execute([$bankId, $userId]);
$transactions = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-university me-2"></i><?= htmlspecialchars($bank['nama_bank']) ?></h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card stat-card bg-total">
            <h5>Saldo Saat Ini</h5>
            <h2>Rp <?= number_format($bank['saldo_saat_ini'], 0, ',', '.') ?></h2>
            <small>Saldo Awal: Rp <?= number_format($bank['saldo_awal'], 0, ',', '.') ?></small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6>Informasi Rekening</h6>
                <p class="mb-1"><strong>Nama Bank:</strong> <?= htmlspecialchars($bank['nama_bank']) ?></p>
                <?php if ($bank['nomor_rekening'] !== '-'): ?>
                    <p class="mb-1"><strong>No. Rekening:</strong> <?= htmlspecialchars($bank['nomor_rekening']) ?></p>
                <?php endif; ?>
                <p class="mb-0"><strong>Total Transaksi:</strong> <?= count($transactions) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactionTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $trans): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $trans['jenis_transaksi'] === 'Pemasukan' ? 'income' : 'expense' ?>">
                                            <?= $trans['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($trans['nama_kategori']) ?></td>
                                    <td><strong>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($trans['keterangan'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Rekening
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    initDataTable('#transactionTable');
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
