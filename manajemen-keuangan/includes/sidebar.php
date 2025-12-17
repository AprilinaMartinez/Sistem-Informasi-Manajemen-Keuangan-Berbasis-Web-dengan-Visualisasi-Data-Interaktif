    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <h4>Keuangan Saya</h4>
        </div>
        
        <nav class="sidebar-menu">
            <a href="/manajemen-keuangan/dashboard.php" class="menu-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            
            <a href="/manajemen-keuangan/pages/kategori/index.php" class="menu-item <?= ($currentPage ?? '') === 'kategori' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> Data Kategori
            </a>
            
            <a href="/manajemen-keuangan/pages/transaksi/index.php" class="menu-item <?= ($currentPage ?? '') === 'transaksi' ? 'active' : '' ?>">
                <i class="fas fa-exchange-alt"></i> Transaksi
            </a>
            
            <a href="/manajemen-keuangan/pages/hutang/index.php" class="menu-item <?= ($currentPage ?? '') === 'hutang' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-usd"></i> Hutang Piutang
            </a>
            
            <a href="/manajemen-keuangan/pages/bank/index.php" class="menu-item <?= ($currentPage ?? '') === 'bank' ? 'active' : '' ?>">
                <i class="fas fa-university"></i> Rekening Bank
            </a>
            
            <a href="/manajemen-keuangan/pages/laporan/index.php" class="menu-item <?= ($currentPage ?? '') === 'laporan' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
            
            <a href="/manajemen-keuangan/pages/profile/change_password.php" class="menu-item <?= ($currentPage ?? '') === 'password' ? 'active' : '' ?>">
                <i class="fas fa-key"></i> Ganti Password
            </a>
            
            <a href="#" onclick="confirmLogout(event)" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div>
                <h5><?= $pageTitle ?? 'Dashboard' ?></h5>
            </div>
            
            <div class="user-info">
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars(getNamaLengkap()) ?></span>
                    <span class="user-role">User</span>
                </div>
                <div class="user-avatar">
                    <?= strtoupper(substr(getNamaLengkap(), 0, 1)) ?>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="content-wrapper">
