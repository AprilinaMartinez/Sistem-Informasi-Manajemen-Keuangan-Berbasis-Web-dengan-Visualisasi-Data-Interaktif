<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';

requireLogin();

$currentPage = 'kategori';
$pageTitle = 'Tambah Kategori';
$userId = getUserId();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $jenis_kategori = $_POST['jenis_kategori'] ?? '';
    
    if (empty($nama_kategori) || empty($jenis_kategori)) {
        $error = 'Semua field harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_kategori (user_id, nama_kategori, jenis_kategori) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $nama_kategori, $jenis_kategori]);
            
            $success = 'Kategori berhasil ditambahkan!';
            
            // Redirect after success
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan kategori: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-plus me-2"></i>Tambah Kategori</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah Kategori</h5>
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
                               placeholder="Contoh: Gaji, Makanan, Transport, dll"
                               value="<?= htmlspecialchars($_POST['nama_kategori'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Jenis Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_kategori" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Pemasukan" <?= ($_POST['jenis_kategori'] ?? '') === 'Pemasukan' ? 'selected' : '' ?>>
                                Pemasukan
                            </option>
                            <option value="Pengeluaran" <?= ($_POST['jenis_kategori'] ?? '') === 'Pengeluaran' ? 'selected' : '' ?>>
                                Pengeluaran
                            </option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Simpan
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
