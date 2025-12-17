<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$userId = getUserId();
$piutangId = $_GET['id'] ?? 0;
$error = '';

$stmt = $pdo->prepare("SELECT * FROM tbl_piutang WHERE piutang_id = ? AND user_id = ?");
$stmt->execute([$piutangId, $userId]);
$piutang = $stmt->fetch();

if (!$piutang) { header('Location: ../hutang/index.php?tab=piutang'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_peminjam = trim($_POST['nama_peminjam'] ?? '');
    $jumlah = $_POST['jumlah_piutang'] ?? 0;
    $sisa = $_POST['sisa_piutang'] ?? 0;
    $tanggal = $_POST['tanggal_piutang'] ?? '';
    $jatuh_tempo = $_POST['jatuh_tempo'] ?? null;
    $status = $_POST['status'] ?? 'Belum Lunas';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if (empty($nama_peminjam) || $jumlah <= 0 || $sisa < 0 || empty($tanggal)) {
        $error = 'Field wajib harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tbl_piutang SET nama_peminjam = ?, jumlah_piutang = ?, sisa_piutang = ?, tanggal_piutang = ?, jatuh_tempo = ?, status = ?, keterangan = ? WHERE piutang_id = ? AND user_id = ?");
            $stmt->execute([$nama_peminjam, $jumlah, $sisa, $tanggal, $jatuh_tempo ?: null, $status, $keterangan, $piutangId, $userId]);
            header('Location: ../hutang/index.php?tab=piutang');
            exit;
        } catch (PDOException $e) {
            $error = 'Gagal mengupdate: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $piutang;
?>

<div class="row mb-4"><div class="col-12"><h2><i class="fas fa-edit me-2"></i>Edit Piutang</h2></div></div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Edit Piutang</h5></div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Peminjam *</label>
                            <input type="text" class="form-control" name="nama_peminjam" required value="<?= htmlspecialchars($data['nama_peminjam'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Piutang *</label>
                            <input type="date" class="form-control" name="tanggal_piutang" required value="<?= $data['tanggal_piutang'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jumlah (Rp) *</label>
                            <input type="number" class="form-control" name="jumlah_piutang" required min="1" value="<?= $data['jumlah_piutang'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sisa (Rp) *</label>
                            <input type="number" class="form-control" name="sisa_piutang" required min="0" value="<?= $data['sisa_piutang'] ?? '' ?>">
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
                        <a href="../hutang/index.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
