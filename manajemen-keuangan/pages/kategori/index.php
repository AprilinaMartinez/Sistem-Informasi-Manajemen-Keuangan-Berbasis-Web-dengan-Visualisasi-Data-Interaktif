<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'kategori';
$pageTitle = 'Data Kategori';
$userId = getUserId();

// Get all categories
$stmt = $pdo->prepare("SELECT * FROM tbl_kategori WHERE user_id = ? ORDER BY jenis_kategori, nama_kategori");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-tags me-2"></i>Data Kategori</h2>
        <p class="text-muted">Kelola kategori pemasukan dan pengeluaran Anda</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <a href="tambah.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Tambah Kategori
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categoryTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="40%">Nama Kategori</th>
                                <th width="20%">Jenis</th>
                                <th width="20%">Tanggal Dibuat</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($categories as $cat): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($cat['nama_kategori']) ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= $cat['jenis_kategori'] === 'Pemasukan' ? 'income' : 'expense' ?>">
                                            <i class="fas fa-<?= $cat['jenis_kategori'] === 'Pemasukan' ? 'arrow-up' : 'arrow-down' ?>"></i>
                                            <?= $cat['jenis_kategori'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($cat['created_at'])) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $cat['kategori_id'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $cat['kategori_id'] ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" title="Hapus">
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
    initDataTable('#categoryTable');
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
