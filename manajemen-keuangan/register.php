<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi
    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        try {
            // Cek apakah username sudah ada
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username sudah digunakan!';
            } else {
                // Cek apakah email sudah ada
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email sudah digunakan!';
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user baru
                    $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, username, email, password) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nama_lengkap, $username, $email, $hashed_password]);
                    
                    $new_user_id = $pdo->lastInsertId();
                    
                    // Buat kategori default untuk user baru
                    $default_categories = [
                        ['Gaji', 'Pemasukan'],
                        ['Bonus', 'Pemasukan'],
                        ['Investasi', 'Pemasukan'],
                        ['Lain-lain', 'Pemasukan'],
                        ['Makanan & Minuman', 'Pengeluaran'],
                        ['Transportasi', 'Pengeluaran'],
                        ['Belanja', 'Pengeluaran'],
                        ['Tagihan', 'Pengeluaran'],
                        ['Hiburan', 'Pengeluaran'],
                        ['Kesehatan', 'Pengeluaran'],
                        ['Pendidikan', 'Pengeluaran'],
                        ['Lain-lain', 'Pengeluaran']
                    ];
                    
                    $stmt = $pdo->prepare("INSERT INTO tbl_kategori (user_id, nama_kategori, jenis_kategori) VALUES (?, ?, ?)");
                    foreach ($default_categories as $cat) {
                        $stmt->execute([$new_user_id, $cat[0], $cat[1]]);
                    }
                    
                    // Buat akun Cash default
                    $stmt = $pdo->prepare("INSERT INTO tbl_bank (user_id, nama_bank, nomor_rekening, saldo_awal, saldo_saat_ini) VALUES (?, 'Cash', '-', 0, 0)");
                    $stmt->execute([$new_user_id]);
                    
                    // Redirect to login after 2 seconds
                    $success = 'Registrasi berhasil! Mengalihkan ke halaman login...';
                    header('Refresh: 2; URL=login.php');

                }
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
    <title>Registrasi - Sistem Manajemen Keuangan</title>
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
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-3) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-4) 100%);
            padding: 30px;
            text-align: center;
            color: #333;
        }
        
        .register-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .register-body {
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
        
        .btn-register {
            background: linear-gradient(135deg, var(--blue-pastel-1) 0%, var(--blue-pastel-4) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: #333;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
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
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h3 class="mb-0">Registrasi Akun</h3>
            <p class="mb-0 mt-2">Sistem Manajemen Keuangan Pribadi</p>
        </div>
        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="nama_lengkap" required 
                               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="text" class="form-control" name="username" required 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" id="password" required 
                               minlength="6">
                        <span class="input-group-text password-toggle" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required 
                               minlength="6">
                        <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register w-100">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </button>
                
                <div class="text-center mt-3">
                    <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
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
