<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'hutang';
$pageTitle = 'Tambah Piutang';
$userId = getUserId();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_peminjam = trim($_POST['nama_peminjam'] ?? '');
    $jumlah = $_POST['jumlah_piutang'] ?? 0;
    $sisa = $_POST['sisa_piutang'] ?? 0;
    $tanggal = $_POST['tanggal_piutang'] ?? '';
    $jatuh_tempo = $_POST['jatuh_tempo'] ?? null;
    $status = $_POST['status'] ?? 'Belum Lunas';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if (empty($nama_peminjam) || $jumlah <= 0 || $sisa < 0 || empty($tanggal)) {
        $error = 'Field wajib harus diisi dengan benar!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_piutang (user_id, nama_peminjam, jumlah_piutang, sisa_piutang, tanggal_piutang, jatuh_tempo, status, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $nama_peminjam, $jumlah, $sisa, $tanggal, $jatuh_tempo ?: null, $status, $keterangan]);
            header('Location: ../hutang/index.php?tab=piutang');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan piutang: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12"><h2><i class="fas fa-plus me-2"></i>Tambah Piutang</h2></div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Tambah Piutang</h5></div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_peminjam" required value="<?= htmlspecialchars($_POST['nama_peminjam'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Piutang <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_piutang" required value="<?= $_POST['tanggal_piutang'] ?? date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jumlah Piutang (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_piutang" required min="1" value="<?= $_POST['jumlah_piutang'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sisa Piutang (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="sisa_piutang" required min="0" value="<?= $_POST['sisa_piutang'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" name="jatuh_tempo" value="<?= $_POST['jatuh_tempo'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                        <a href="../hutang/index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
