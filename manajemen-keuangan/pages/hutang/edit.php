<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();
$currentPage = 'hutang';
$pageTitle = 'Edit Hutang';
$userId = getUserId();
$hutangId = $_GET['id'] ?? 0;
$error = '';

$stmt = $pdo->prepare("SELECT * FROM tbl_hutang WHERE hutang_id = ? AND user_id = ?");
$stmt->execute([$hutangId, $userId]);
$hutang = $stmt->fetch();

if (!$hutang) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemberi = trim($_POST['nama_pemberi_hutang'] ?? '');
    $jumlah = $_POST['jumlah_hutang'] ?? 0;
    $sisa = $_POST['sisa_hutang'] ?? 0;
    $tanggal = $_POST['tanggal_hutang'] ?? '';
    $jatuh_tempo = $_POST['jatuh_tempo'] ?? null;
    $status = $_POST['status'] ?? 'Belum Lunas';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if (empty($nama_pemberi) || $jumlah <= 0 || $sisa < 0 || empty($tanggal)) {
        $error = 'Field wajib harus diisi dengan benar!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tbl_hutang SET nama_pemberi_hutang = ?, jumlah_hutang = ?, sisa_hutang = ?, tanggal_hutang = ?, jatuh_tempo = ?, status = ?, keterangan = ? WHERE hutang_id = ? AND user_id = ?");
            $stmt->execute([$nama_pemberi, $jumlah, $sisa, $tanggal, $jatuh_tempo ?: null, $status, $keterangan, $hutangId, $userId]);
            
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal mengupdate hutang: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $hutang;
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-edit me-2"></i>Edit Hutang</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Edit Hutang</h5></div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pemberi Hutang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_pemberi_hutang" required value="<?= htmlspecialchars($data['nama_pemberi_hutang'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Hutang <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_hutang" required value="<?= $data['tanggal_hutang'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jumlah Hutang (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_hutang" required min="1" value="<?= $data['jumlah_hutang'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sisa Hutang (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="sisa_hutang" required min="0" value="<?= $data['sisa_hutang'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" name="jatuh_tempo" value="<?= $data['jatuh_tempo'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="Belum Lunas" <?= ($data['status'] ?? '') === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="Lunas" <?= ($data['status'] ?? '') === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Update</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
