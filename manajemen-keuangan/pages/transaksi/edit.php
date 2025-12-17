<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'transaksi';
$pageTitle = 'Edit Transaksi';
$userId = getUserId();

$error = '';
$transaksiId = $_GET['id'] ?? 0;

// Get transaksi data
$stmt = $pdo->prepare("SELECT * FROM tbl_transaksi WHERE transaksi_id = ? AND user_id = ?");
$stmt->execute([$transaksiId, $userId]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    header('Location: index.php');
    exit;
}

// Get categories
$stmt = $pdo->prepare("SELECT * FROM tbl_kategori WHERE user_id = ? ORDER BY jenis_kategori, nama_kategori");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll();

// Get bank accounts
$stmt = $pdo->prepare("SELECT * FROM tbl_bank WHERE user_id = ? ORDER BY nama_bank");
$stmt->execute([$userId]);
$banks = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    // $jenis_transaksi determined by category
    $kategori_id = $_POST['kategori_id'] ?? '';
    $bank_id = $_POST['bank_id'] ?? null;
    $jumlah = $_POST['jumlah'] ?? 0;
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if (empty($tanggal) || empty($kategori_id) || $jumlah <= 0) {
        $error = 'Tanggal, kategori, dan jumlah harus diisi dengan benar!';
    } else {
        try {
            // Validate Category and Get Type
            $stmt = $pdo->prepare("SELECT jenis_kategori FROM tbl_kategori WHERE kategori_id = ? AND user_id = ?");
            $stmt->execute([$kategori_id, $userId]);
            $categoryData = $stmt->fetch();
            
            if (!$categoryData) {
                throw new Exception("Kategori tidak valid.");
            }
            
            $jenis_transaksi = $categoryData['jenis_kategori'];
            
            $pdo->beginTransaction();
            
            // Rollback old bank balance (using original transaction data)
            if ($transaksi['bank_id']) {
                if ($transaksi['jenis_transaksi'] === 'Pemasukan') {
                    $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini - ? WHERE bank_id = ? AND user_id = ?");
                } else {
                    $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini + ? WHERE bank_id = ? AND user_id = ?");
                }
                $stmt->execute([$transaksi['jumlah'], $transaksi['bank_id'], $userId]);
            }
            
            // Update transaksi
            $stmt = $pdo->prepare("UPDATE tbl_transaksi 
                                   SET tanggal = ?, jenis_transaksi = ?, kategori_id = ?, bank_id = ?, jumlah = ?, keterangan = ?
                                   WHERE transaksi_id = ? AND user_id = ?");
            $stmt->execute([$tanggal, $jenis_transaksi, $kategori_id, $bank_id ?: null, $jumlah, $keterangan, $transaksiId, $userId]);
            
            // Apply new bank balance (using new derived type)
            if ($bank_id) {
                if ($jenis_transaksi === 'Pemasukan') {
                    $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini + ? WHERE bank_id = ? AND user_id = ?");
                } else {
                    $stmt = $pdo->prepare("UPDATE tbl_bank SET saldo_saat_ini = saldo_saat_ini - ? WHERE bank_id = ? AND user_id = ?");
                }
                $stmt->execute([$jumlah, $bank_id, $userId]);
            }
            
            $pdo->commit();
            
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Gagal mengupdate transaksi: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-edit me-2"></i>Edit Transaksi</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Edit Transaksi</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="formTransaksi">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal" required 
                                       value="<?= $_POST['tanggal'] ?? $transaksi['tanggal'] ?>">
                            </div>
                        </div>
                        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" name="kategori_id" id="kategoriSelect" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['kategori_id'] ?>" 
                                                data-jenis="<?= $cat['jenis_kategori'] ?>"
                                                <?= ($_POST['kategori_id'] ?? $transaksi['kategori_id']) == $cat['kategori_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jenis Transaksi</label>
                                <input type="text" class="form-control" id="jenisTransaksi" name="jenis_transaksi" 
                                       value="<?= $_POST['jenis_transaksi'] ?? $transaksi['jenis_transaksi'] ?>" readonly>
                                <small class="text-muted">Otomatis terisi berdasarkan kategori</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bank/Rekening</label>
                                <select class="form-select" name="bank_id">
                                    <option value="">-- Tidak Menggunakan Bank --</option>
                                    <?php foreach ($banks as $bank): ?>
                                        <option value="<?= $bank['bank_id'] ?>" 
                                                <?= ($_POST['bank_id'] ?? $transaksi['bank_id']) == $bank['bank_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($bank['nama_bank']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah" required min="1" step="0.01"
                               value="<?= $_POST['jumlah'] ?? $transaksi['jumlah'] ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= htmlspecialchars($_POST['keterangan'] ?? $transaksi['keterangan']) ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
// Auto-fill jenis transaksi berdasarkan kategori
$(document).ready(function() {
    $('#kategoriSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const jenis = selectedOption.data('jenis');
        
        if (jenis) {
            $('#jenisTransaksi').val(jenis);
        } else {
            $('#jenisTransaksi').val('-');
        }
    });

    // Trigger on load if value exists
    if ($('#kategoriSelect').val()) {
        $('#kategoriSelect').trigger('change');
    }
});
</script>
