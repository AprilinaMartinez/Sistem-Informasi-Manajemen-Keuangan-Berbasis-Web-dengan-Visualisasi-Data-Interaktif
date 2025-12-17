<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'kategori';
$pageTitle = 'Edit Kategori';
$userId = getUserId();

$error = '';
$success = '';
$kategoriId = $_GET['id'] ?? 0;

// Get kategori data
$stmt = $pdo->prepare("SELECT * FROM tbl_kategori WHERE kategori_id = ? AND user_id = ?");
$stmt->execute([$kategoriId, $userId]);
$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $jenis_kategori = $_POST['jenis_kategori'] ?? '';
    
    if (empty($nama_kategori) || empty($jenis_kategori)) {
        $error = 'Semua field harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tbl_kategori SET nama_kategori = ?, jenis_kategori = ? WHERE kategori_id = ? AND user_id = ?");
            $stmt->execute([$nama_kategori, $jenis_kategori, $kategoriId, $userId]);
            
            $success = 'Kategori berhasil diupdate!';
            
            // Redirect after success
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal mengupdate kategori: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-edit me-2"></i>Edit Kategori</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Edit Kategori</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" required 
                               value="<?= htmlspecialchars($_POST['nama_kategori'] ?? $kategori['nama_kategori']) ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Jenis Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_kategori" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Pemasukan" <?= ($_POST['jenis_kategori'] ?? $kategori['jenis_kategori']) === 'Pemasukan' ? 'selected' : '' ?>>
                                Pemasukan
                            </option>
                            <option value="Pengeluaran" <?= ($_POST['jenis_kategori'] ?? $kategori['jenis_kategori']) === 'Pengeluaran' ? 'selected' : '' ?>>
                                Pengeluaran
                            </option>
                        </select>
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
