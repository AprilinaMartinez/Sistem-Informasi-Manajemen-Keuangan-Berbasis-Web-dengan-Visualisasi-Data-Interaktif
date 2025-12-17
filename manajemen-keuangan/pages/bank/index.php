<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();
$currentPage = 'bank';
$pageTitle = 'Rekening Bank';
$userId = getUserId();

// Get all bank accounts
$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE user_id = ? ORDER BY nama_bank");
$stmt->execute([$userId]);
$banks = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-university me-2"></i>Rekening Bank</h2>
        <p class="text-muted">Kelola rekening bank dan saldo Anda</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <a href="tambah.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Rekening
        </a>
    </div>
</div>

<div class="row">
    <?php foreach ($banks as $bank): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-university text-primary me-2"></i>
                            <?= htmlspecialchars($bank['nama_bank']) ?>
                        </h5>
                    </div>
                    
                    <?php if ($bank['nomor_rekening'] !== '-'): ?>
                        <p class="text-muted mb-2">
                            <small>No. Rek: <?= htmlspecialchars($bank['nomor_rekening']) ?></small>
                        </p>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <small class="text-muted">Saldo Awal</small>
                        <h6>Rp <?= number_format($bank['saldo_awal'], 0, ',', '.') ?></h6>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Saldo Saat Ini</small>
                        <h4 class="text-primary">Rp <?= number_format($bank['saldo_saat_ini'], 0, ',', '.') ?></h4>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="detail.php?id=<?= $bank['bank_id'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="edit.php?id=<?= $bank['bank_id'] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="hapus.php?id=<?= $bank['bank_id'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirm('Hapus rekening ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (count($banks) === 0): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Belum ada rekening bank. Silakan tambahkan rekening baru.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
