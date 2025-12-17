<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'hutang';
$pageTitle = 'Hutang & Piutang';
$userId = getUserId();

// Get all hutang
$stmt = $pdo->prepare("SELECT * FROM tbl_hutang WHERE user_id = ? ORDER BY tanggal_hutang DESC");
$stmt->execute([$userId]);
$hutangs = $stmt->fetchAll();

// Get all piutang
$stmt = $pdo->prepare("SELECT * FROM tbl_piutang WHERE user_id = ? ORDER BY tanggal_piutang DESC");
$stmt->execute([$userId]);
$piutangs = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';

$activeTab = $_GET['tab'] ?? 'hutang';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-hand-holding-usd me-2"></i>Hutang & Piutang</h2>
        <p class="text-muted">Kelola catatan hutang dan piutang Anda</p>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'hutang' ? 'active' : '' ?>" data-bs-toggle="tab" href="#hutangTab">
            <i class="fas fa-hand-holding-usd me-2"></i>Hutang
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'piutang' ? 'active' : '' ?>" data-bs-toggle="tab" href="#piutangTab">
            <i class="fas fa-money-bill-wave me-2"></i>Piutang
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Hutang Tab -->
    <div class="tab-pane fade <?= $activeTab === 'hutang' ? 'show active' : '' ?>" id="hutangTab">
        <div class="mb-3">
            <a href="tambah.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Hutang
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="hutangTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pemberi Hutang</th>
                                <th>Jumlah</th>
                                <th>Sisa</th>
                                <th>Tanggal</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($hutangs as $hutang): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($hutang['nama_pemberi_hutang']) ?></strong></td>
                                    <td>Rp <?= number_format($hutang['jumlah_hutang'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($hutang['sisa_hutang'], 0, ',', '.') ?></td>
                                    <td><?= date('d/m/Y', strtotime($hutang['tanggal_hutang'])) ?></td>
                                    <td><?= $hutang['jatuh_tempo'] ? date('d/m/Y', strtotime($hutang['jatuh_tempo'])) : '-' ?></td>
                                    <td>
                                        <span class="badge badge-<?= $hutang['status'] === 'Lunas' ? 'success' : 'warning' ?>">
                                            <?= $hutang['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?= $hutang['hutang_id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $hutang['hutang_id'] ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Hapus hutang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Piutang Tab -->
    <div class="tab-pane fade <?= $activeTab === 'piutang' ? 'show active' : '' ?>" id="piutangTab">
        <div class="mb-3">
            <a href="../piutang/tambah.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Piutang
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="piutangTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Peminjam</th>
                                <th>Jumlah</th>
                                <th>Sisa</th>
                                <th>Tanggal</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($piutangs as $piutang): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($piutang['nama_peminjam']) ?></strong></td>
                                    <td>Rp <?= number_format($piutang['jumlah_piutang'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($piutang['sisa_piutang'], 0, ',', '.') ?></td>
                                    <td><?= date('d/m/Y', strtotime($piutang['tanggal_piutang'])) ?></td>
                                    <td><?= $piutang['jatuh_tempo'] ? date('d/m/Y', strtotime($piutang['jatuh_tempo'])) : '-' ?></td>
                                    <td>
                                        <span class="badge badge-<?= $piutang['status'] === 'Lunas' ? 'success' : 'warning' ?>">
                                            <?= $piutang['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="../piutang/edit.php?id=<?= $piutang['piutang_id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../piutang/hapus.php?id=<?= $piutang['piutang_id'] ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Hapus piutang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    initDataTable('#hutangTable');
    initDataTable('#piutangTable');
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
