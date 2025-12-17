<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$validToken = false;

// Validate Token
if (empty($token)) {
    $error = 'Token tidak valid.';
} else {
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch();
    
    if ($resetRequest) {
        $validToken = true;
    } else {
        $error = 'Token tidak valid atau sudah kadaluarsa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Get user email from reset request
            $email = $resetRequest['email'];
            
            // Update User Password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);
            
            // Delete Token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            $pdo->commit();
            $success = 'Password berhasil diubah! Silakan login dengan password baru.';
            $validToken = false; // Disable form
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Gagal mengubah password: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Manajemen Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --blue-pastel-1: #A8D8EA;
            --blue-pastel-2: #C3E5F0;
            --blue-pastel-3: #D4EBF5;
            --blue-pastel-4: #B8E1F0;
            --blue-pastel-5: #93C9E0;
        }
        
        body {
            background: linear-gradient(135deg, var(--blue-pastel-2) 0%, var(--blue-pastel-4) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-custom {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-5) 100%);
            padding: 30px;
            text-align: center;
            color: #333;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-5) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: #333;
            transition: all 0.3s;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #000;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-key fa-3x mb-3"></i>
            <h4 class="mb-0">Reset Password</h4>
        </div>
        <div class="p-4">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-custom w-100">Login Sekarang</a>
                </div>
            <?php elseif ($validToken): ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" required minlength="6 placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required placeholder="Ulangi password">
                    </div>
                    
                    <button type="submit" class="btn btn-custom w-100">
                        <i class="fas fa-save me-2"></i> Simpan Password
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="btn btn-link text-decoration-none">Kirim Ulang Link Reset</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
