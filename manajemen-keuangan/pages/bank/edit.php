<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'bank';
$userId = getUserId();
$bankId = $_GET['id'] ?? 0;
$error = '';

$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE bank_id = ? AND user_id = ?");
$stmt->execute([$bankId, $userId]);
$bank = $stmt->fetch();
if (!$bank) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank = trim($_POST['nama_bank'] ?? '');
    $nomor_rekening = trim($_POST['nomor_rekening'] ?? '-');
    $saldo_awal = $_POST['saldo_awal'] ?? 0;
    
    if (empty($nama_bank)) {
        $error = 'Nama bank harus diisi!';
    } else {
        try {
            // Calculate difference
            $diff = $saldo_awal - $bank['saldo_awal'];
            
            $stmt = $pdo->prepare("UPDATE tbl_bank SET nama_bank = ?, nomor_rekening = ?, saldo_awal = ?, saldo_saat_ini = saldo_saat_ini + ? WHERE bank_id = ? AND user_id = ?");
            $stmt->execute([$nama_bank, $nomor_rekening, $saldo_awal, $diff, $bankId, $userId]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal mengupdate: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $bank;
?>

<div class="row mb-4"><div class="col-12"><h2><i class="fas fa-edit me-2"></i>Edit Rekening Bank</h2></div></div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Edit Rekening</h5></div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank *</label>
                        <input type="text" class="form-control" name="nama_bank" required value="<?= htmlspecialchars($data['nama_bank'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="nomor_rekening" value="<?= htmlspecialchars($data['nomor_rekening'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Saldo Awal (Rp)</label>
                        <input type="number" class="form-control" name="saldo_awal" min="0" value="<?= $data['saldo_awal'] ?? 0 ?>">
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Saldo saat ini: <strong>Rp <?= number_format($bank['saldo_saat_ini'], 0, ',', '.') ?></strong>
                        <br><small>Saldo saat ini dihitung otomatis dari transaksi</small>
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
