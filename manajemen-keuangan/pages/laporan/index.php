<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'laporan';
$pageTitle = 'Laporan Keuangan';
$userId = getUserId();

// Filter parameters
$periode = $_GET['periode'] ?? 'monthly';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-t');

// Build query based on period
if ($periode === 'daily') {
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $where = "AND t.tanggal = '$tanggal'";
    $periodLabel = date('d F Y', strtotime($tanggal));
} elseif ($periode === 'weekly') {
    $where = "AND t.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
    $periodLabel = date('d M', strtotime($tanggal_mulai)) . ' - ' . date('d M Y', strtotime($tanggal_selesai));
} elseif ($periode === 'monthly') {
    $where = "AND MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'";
    $periodLabel = date('F Y', strtotime("$tahun-$bulan-01"));
} else { // yearly
    $where = "AND YEAR(t.tanggal) = '$tahun'";
    $periodLabel = "Tahun $tahun";
}

// Get statistics
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN jenis_transaksi = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
    SUM(CASE WHEN jenis_transaksi = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
    FROM tbl_transaksi t WHERE user_id = ? $where");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$totalPemasukan = $stats['total_pemasukan'] ?? 0;
$totalPengeluaran = $stats['total_pengeluaran'] ?? 0;
$saldo = $totalPemasukan - $totalPengeluaran;

// Get transactions
$stmt = $pdo->prepare("SELECT t.*, k.nama_kategori, b.nama_bank
    FROM tbl_transaksi t
    JOIN tbl_kategori k ON t.kategori_id = k.kategori_id
    LEFT JOIN tbl_bank b ON t.bank_id = b.bank_id
    WHERE t.user_id = ? $where
    ORDER BY t.tanggal DESC");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-chart-bar me-2"></i>Laporan Keuangan</h2>
        <p class="text-muted">Laporan ringkasan keuangan berdasarkan periode</p>
    </div>
</div>

<!-- Filter -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Periode</label>
                        <select class="form-select" name="periode" id="periodeSelect">
                            <option value="daily" <?= $periode === 'daily' ? 'selected' : '' ?>>Harian</option>
                            <option value="weekly" <?= $periode === 'weekly' ? 'selected' : '' ?>>Mingguan</option>
                            <option value="monthly" <?= $periode === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="yearly" <?= $periode === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 filter-daily" style="display:<?= $periode === 'daily' ? 'block' : 'none' ?>">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="<?= $_GET['tanggal'] ?? date('Y-m-d') ?>">
                    </div>
                    
                    <div class="col-md-2 filter-weekly" style="display:<?= $periode === 'weekly' ? 'block' : 'none' ?>">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= $tanggal_mulai ?>">
                    </div>
                    <div class="col-md-2 filter-weekly" style="display:<?= $periode === 'weekly' ? 'block' : 'none' ?>">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= $tanggal_selesai ?>">
                    </div>
                    
                    <div class="col-md-2 filter-monthly" style="display:<?= $periode === 'monthly' ? 'block' : 'none' ?>">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" name="bulan">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= sprintf('%02d', $m) ?>" <?= $bulan == sprintf('%02d', $m) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 filter-monthly filter-yearly" style="display:<?= in_array($periode, ['monthly', 'yearly']) ? 'block' : 'none' ?>">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" name="tahun">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-income">
            <i class="fas fa-arrow-up stat-card-icon"></i>
            <div class="stat-card-title">Total Pemasukan</div>
            <h3 class="stat-card-value">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h3>
            <small><?= $periodLabel ?></small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-expense">
            <i class="fas fa-arrow-down stat-card-icon"></i>
            <div class="stat-card-title">Total Pengeluaran</div>
            <h3 class="stat-card-value">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h3>
            <small><?= $periodLabel ?></small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card bg-total">
            <i class="fas fa-wallet stat-card-icon"></i>
            <div class="stat-card-title">Saldo</div>
            <h3 class="stat-card-value">Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
            <small><?= $periodLabel ?></small>
        </div>
    </div>
</div>

<!-- Transaction Detail Table -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Transaksi</h5>
                <a href="export_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reportTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Bank</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($transactions as $trans): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $trans['jenis_transaksi'] === 'Pemasukan' ? 'income' : 'expense' ?>">
                                            <?= $trans['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($trans['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($trans['nama_bank'] ?? '-') ?></td>
                                    <td><strong>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($trans['keterangan'] ?? '-') ?></td>
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
    initDataTable('#reportTable');
    
    $('#periodeSelect').on('change', function() {
        const periode = $(this).val();
        $('.filter-daily, .filter-weekly, .filter-monthly, .filter-yearly').hide();
        
        if (periode === 'daily') {
            $('.filter-daily').show();
        } else if (periode === 'weekly') {
            $('.filter-weekly').show();
        } else if (periode === 'monthly') {
            $('.filter-monthly, .filter-yearly').show();
        } else if (periode === 'yearly') {
            $('.filter-yearly').show();
        }
    });
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
