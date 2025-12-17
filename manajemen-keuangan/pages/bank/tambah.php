<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'bank';
$pageTitle = 'Tambah Rekening Bank';
$userId = getUserId();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank = trim($_POST['nama_bank'] ?? '');
    $nomor_rekening = trim($_POST['nomor_rekening'] ?? '-');
    $saldo_awal = $_POST['saldo_awal'] ?? 0;
    
    if (empty($nama_bank)) {
        $error = 'Nama bank harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_bank (user_id, nama_bank, nomor_rekening, saldo_awal, saldo_saat_ini) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $nama_bank, $nomor_rekening, $saldo_awal, $saldo_awal]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan rekening: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12"><h2><i class="fas fa-plus me-2"></i>Tambah Rekening Bank</h2></div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Tambah Rekening</h5></div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank/Rekening <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_bank" required 
                               placeholder="Contoh: Bank Mandiri, BCA, Cash, E-Wallet" 
                               value="<?= htmlspecialchars($_POST['nama_bank'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="nomor_rekening" 
                               placeholder="Opsional, kosongkan jika tidak ada"
                               value="<?= htmlspecialchars($_POST['nomor_rekening'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Saldo Awal (Rp)</label>
                        <input type="number" class="form-control" name="saldo_awal" min="0" step="0.01"
                               placeholder="0"
                               value="<?= $_POST['saldo_awal'] ?? 0 ?>">
                        <small class="text-muted">Saldo saat ini akan diupdate otomatis berdasarkan transaksi</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
