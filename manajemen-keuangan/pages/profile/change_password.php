<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$currentPage = 'password';
$pageTitle = 'Ganti Password';
$userId = getUserId();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password baru dan konfirmasi tidak sama!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        try {
            // Verify old password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!password_verify($old_password, $user['password'])) {
                $error = 'Password lama salah!';
            } else {
                // Update password
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->execute([$hashed, $userId]);
                
                $success = 'Password berhasil diubah!';
            }
        } catch (PDOException $e) {
            $error = 'Gagal mengubah password: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-key me-2"></i>Ganti Password</h2>
        <p class="text-muted">Ubah password akun Anda</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Ganti Password</h5></div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Ubah Password</button>
                        <a href="/manajemen-keuangan/dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
