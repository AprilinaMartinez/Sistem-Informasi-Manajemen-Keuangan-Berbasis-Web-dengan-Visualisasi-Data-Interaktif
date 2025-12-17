<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email harus diisi!';
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT user_id, nama_lengkap FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$email, $token, $expires]);
                
                // Construct reset link
                // Assuming localhost structure, adjust if needed
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $path = dirname($_SERVER['PHP_SELF']);
                $resetLink = "$protocol://$host$path/reset_password.php?token=$token";
                
                // Simulate email sending (Log to file for XAMPP testing)
                $logMessage = "[" . date('Y-m-d H:i:s') . "] Reset Token for $email: $resetLink" . PHP_EOL;
                file_put_contents('reset_log.txt', $logMessage, FILE_APPEND);
                
                // In production, use mail() or a library like PHPMailer
                // mail($email, "Reset Password", "Klik link ini: $resetLink");
            }
            
            // Always show success message for security (prevent enumeration)
            $message = 'Jika email terdaftar, kami telah mengirimkan link reset password ke email Anda.';
            
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem.';
            // Log real error for admin: $e->getMessage()
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Manajemen Keuangan</title>
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
        
        .form-control:focus {
            border-color: var(--blue-pastel-1);
            box-shadow: 0 0 0 0.2rem rgba(168, 216, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-lock fa-3x mb-3"></i>
            <h4 class="mb-0">Lupa Password?</h4>
            <p class="mb-0 mt-2 small">Masukkan email Anda untuk reset password</p>
        </div>
        <div class="p-4">
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php" class="btn btn-link text-decoration-none">Kembali ke Login</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required placeholder="nama@email.com">
                    </div>
                    
                    <button type="submit" class="btn btn-custom w-100 mb-3">
                        <i class="fas fa-paper-plane me-2"></i> Kirim Link Reset
                    </button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none text-muted small">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
