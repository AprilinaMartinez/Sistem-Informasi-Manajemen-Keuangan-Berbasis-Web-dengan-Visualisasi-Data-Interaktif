<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'transaksi';
$pageTitle = 'Transaksi';
$userId = getUserId();

// Get filter parameters
$filterJenis = $_GET['jenis'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';
$filterTanggalMulai = $_GET['tanggal_mulai'] ?? '';
$filterTanggalSelesai = $_GET['tanggal_selesai'] ?? '';

// Build query
$query = "SELECT t.*, k.nama_kategori, k.jenis_kategori, b.nama_bank
          FROM tbl_transaksi t
          JOIN tbl_kategori k ON t.kategori_id = k.kategori_id
          LEFT JOIN tbl_bank b ON t.bank_id = b.bank_id
          WHERE t.user_id = ?";
$params = [$userId];

if ($filterJenis) {
    $query .= " AND t.jenis_transaksi = ?";
    $params[] = $filterJenis;
}

if ($filterKategori) {
    $query .= " AND t.kategori_id = ?";
    $params[] = $filterKategori;
}

if ($filterTanggalMulai) {
    $query .= " AND t.tanggal >= ?";
    $params[] = $filterTanggalMulai;
}

if ($filterTanggalSelesai) {
    $query .= " AND t.tanggal <= ?";
    $params[] = $filterTanggalSelesai;
}

$query .= " ORDER BY t.tanggal DESC, t.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Get categories for filter
$stmt = $pdo->prepare("SELECT * FROM tbl_kategori WHERE user_id = ? ORDER BY jenis_kategori, nama_kategori");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-exchange-alt me-2"></i>Manajemen Transaksi</h2>
        <p class="text-muted">Kelola semua transaksi pemasukan dan pengeluaran Anda</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <a href="tambah.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Transaksi
        </a>
    </div>
</div>

<!-- Filter Panel -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Transaksi</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <select class="form-select" name="jenis">
                            <option value="">Semua</option>
                            <option value="Pemasukan" <?= $filterJenis === 'Pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                            <option value="Pengeluaran" <?= $filterJenis === 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori">
                            <option value="">Semua</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['kategori_id'] ?>" <?= $filterKategori == $cat['kategori_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nama_kategori']) ?> (<?= $cat['jenis_kategori'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= htmlspecialchars($filterTanggalMulai) ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= htmlspecialchars($filterTanggalSelesai) ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactionTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%">Jenis</th>
                                <th width="15%">Kategori</th>
                                <th width="15%">Bank/Rekening</th>
                                <th width="15%">Jumlah</th>
                                <th width="20%">Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $totalPemasukan = 0;
                            $totalPengeluaran = 0;
                            
                            foreach ($transactions as $trans): 
                                if ($trans['jenis_transaksi'] === 'Pemasukan') {
                                    $totalPemasukan += $trans['jumlah'];
                                } else {
                                    $totalPengeluaran += $trans['jumlah'];
                                }
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $trans['jenis_transaksi'] === 'Pemasukan' ? 'income' : 'expense' ?>">
                                            <i class="fas fa-<?= $trans['jenis_transaksi'] === 'Pemasukan' ? 'arrow-up' : 'arrow-down' ?>"></i>
                                            <?= $trans['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($trans['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($trans['nama_bank'] ?? 'Cash') ?></td>
                                    <td><strong>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($trans['keterangan'] ?? '-') ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="edit.php?id=<?= $trans['transaksi_id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="hapus.php?id=<?= $trans['transaksi_id'] ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end">TOTAL PEMASUKAN:</td>
                                <td colspan="3" class="text-success">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end">TOTAL PENGELUARAN:</td>
                                <td colspan="3" class="text-danger">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end">SALDO:</td>
                                <td colspan="3">Rp <?= number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
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
