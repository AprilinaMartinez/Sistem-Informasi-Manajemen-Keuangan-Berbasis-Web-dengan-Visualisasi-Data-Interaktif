<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['foto_profil'] = $user['foto_profil'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Keuangan</title>
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
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-5) 100%);
            padding: 40px;
            text-align: center;
            color: #333;
        }
        
        .login-header i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--blue-pastel-1);
            box-shadow: 0 0 0 0.2rem rgba(168, 216, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-5) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: #333;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: #000;
        }
        
        .input-group-text {
            background: var(--blue-pastel-3);
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .password-toggle {
            cursor: pointer;
            background: var(--blue-pastel-3);
            border: 2px solid #e0e0e0;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-wallet"></i>
            <h3 class="mb-0">Selamat Datang</h3>
            <p class="mb-0 mt-2">Sistem Manajemen Keuangan Pribadi</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" required 
                               placeholder="Masukkan username atau email"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" id="password" required 
                               placeholder="Masukkan password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="text-end mt-2">
                        <a href="forgot_password.php" class="text-decoration-none small">Lupa Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
                
                <div class="text-center mt-3">
                    <p class="mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId, iconElement) {
            const field = document.getElementById(fieldId);
            const icon = iconElement.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
