<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

// Require login
requireLogin();

$currentPage = 'dashboard';
$pageTitle = 'Dashboard';
$userId = getUserId();

// Get current date info
$today = date('Y-m-d');
$currentMonth = date('Y-m');
$currentYear = date('Y');

// Fungsi untuk mendapatkan total berdasarkan periode
function getTotal($pdo, $userId, $jenis, $periode = 'all', $tanggal = null) {
    $query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM tbl_transaksi 
              WHERE user_id = ? AND jenis_transaksi = ?";
    $params = [$userId, $jenis];
    
    if ($periode === 'day') {
        $query .= " AND tanggal = ?";
        $params[] = $tanggal;
    } elseif ($periode === 'month') {
        $query .= " AND DATE_FORMAT(tanggal, '%Y-%m') = ?";
        $params[] = $tanggal;
    } elseif ($periode === 'year') {
        $query .= " AND YEAR(tanggal) = ?";
        $params[] = $tanggal;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch()['total'];
}

// Hitung semua statistics
$stats = [
    'income_today' => getTotal($pdo, $userId, 'Pemasukan', 'day', $today),
    'income_month' => getTotal($pdo, $userId, 'Pemasukan', 'month', $currentMonth),
    'income_year' => getTotal($pdo, $userId, 'Pemasukan', 'year', $currentYear),
    'income_total' => getTotal($pdo, $userId, 'Pemasukan'),
    
    'expense_today' => getTotal($pdo, $userId, 'Pengeluaran', 'day', $today),
    'expense_month' => getTotal($pdo, $userId, 'Pengeluaran', 'month', $currentMonth),
    'expense_year' => getTotal($pdo, $userId, 'Pengeluaran', 'year', $currentYear),
    'expense_total' => getTotal($pdo, $userId, 'Pengeluaran'),
];

// Data untuk grafik bulanan (12 bulan terakhir)
$monthlyData = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('M Y', strtotime("-$i months"));
    
    $income = getTotal($pdo, $userId, 'Pemasukan', 'month', $month);
    $expense = getTotal($pdo, $userId, 'Pengeluaran', 'month', $month);
    
    $monthlyData[] = [
        'label' => $monthName,
        'income' => $income,
        'expense' => $expense
    ];
}

// Data untuk pie chart (pengeluaran per kategori bulan ini)
$stmt = $pdo->prepare("
    SELECT k.nama_kategori, COALESCE(SUM(t.jumlah), 0) as total
    FROM tbl_kategori k
    LEFT JOIN tbl_transaksi t ON k.kategori_id = t.kategori_id 
        AND t.jenis_transaksi = 'Pengeluaran'
        AND DATE_FORMAT(t.tanggal, '%Y-%m') = ?
        AND t.user_id = ?
    WHERE k.user_id = ? AND k.jenis_kategori = 'Pengeluaran'
    GROUP BY k.kategori_id, k.nama_kategori
    HAVING total > 0
    ORDER BY total DESC
");
$stmt->execute([$currentMonth, $userId, $userId]);
$categoryData = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="row fade-in">
    <div class="col-12 mb-4">
        <h2><i class="fas fa-chart-line me-2"></i>Dashboard Keuangan</h2>
        <p class="text-muted">Ringkasan keuangan Anda hari ini, <?= date('d F Y') ?></p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Pemasukan Hari Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-income">
            <i class="fas fa-arrow-up stat-card-icon"></i>
            <div class="stat-card-title">Pemasukan Hari Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['income_today'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Pemasukan Bulan Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-income">
            <i class="fas fa-calendar-check stat-card-icon"></i>
            <div class="stat-card-title">Pemasukan Bulan Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['income_month'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Pemasukan Tahun Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-income">
            <i class="fas fa-calendar-alt stat-card-icon"></i>
            <div class="stat-card-title">Pemasukan Tahun Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['income_year'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Total Pemasukan -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-total">
            <i class="fas fa-coins stat-card-icon"></i>
            <div class="stat-card-title">Total Pemasukan</div>
            <h3 class="stat-card-value"><?= number_format($stats['income_total'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Pengeluaran Hari Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-expense">
            <i class="fas fa-arrow-down stat-card-icon"></i>
            <div class="stat-card-title">Pengeluaran Hari Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['expense_today'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Pengeluaran Bulan Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-expense">
            <i class="fas fa-calendar-times stat-card-icon"></i>
            <div class="stat-card-title">Pengeluaran Bulan Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['expense_month'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Pengeluaran Tahun Ini -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-expense">
            <i class="fas fa-calendar stat-card-icon"></i>
            <div class="stat-card-title">Pengeluaran Tahun Ini</div>
            <h3 class="stat-card-value"><?= number_format($stats['expense_year'], 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <!-- Total Pengeluaran -->
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-total">
            <i class="fas fa-wallet stat-card-icon"></i>
            <div class="stat-card-title">Total Pengeluaran</div>
            <h3 class="stat-card-value"><?= number_format($stats['expense_total'], 0, ',', '.') ?></h3>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <!-- Monthly Comparison Bar Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <canvas id="monthlyComparisonChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Calendar Widget -->
    <div class="col-lg-4">
        <div class="calendar-widget">
            <div class="calendar-header">
                <div class="d-flex gap-2 w-100">
                    <select id="monthSelect" class="form-select calendar-dropdown shadow-sm" onchange="updateCalendarFromDropdown()"></select>
                    <select id="yearSelect" class="form-select calendar-dropdown shadow-sm" onchange="updateCalendarFromDropdown()"></select>
                </div>
            </div>
            <div class="calendar-days" id="calendarDays"></div>
        </div>
    </div>
</div>

<!-- Trend Line Chart -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <canvas id="trendChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Transaksi Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT t.*, k.nama_kategori
                                FROM tbl_transaksi t
                                JOIN tbl_kategori k ON t.kategori_id = k.kategori_id
                                WHERE t.user_id = ?
                                ORDER BY t.tanggal DESC, t.created_at DESC
                                LIMIT 10
                            ");
                            $stmt->execute([$userId]);
                            $recentTransactions = $stmt->fetchAll();
                            
                            if (count($recentTransactions) > 0):
                                foreach ($recentTransactions as $trans):
                            ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $trans['jenis_transaksi'] === 'Pemasukan' ? 'income' : 'expense' ?>">
                                            <?= $trans['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($trans['nama_kategori']) ?></td>
                                    <td><strong>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?></strong></td>
                                    <td><?= htmlspecialchars($trans['keterangan'] ?? '-') ?></td>
                                </tr>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="/manajemen-keuangan/pages/transaksi/index.php" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>Lihat Semua Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/manajemen-keuangan/assets/js/dashboard.js"></script>
<script>
// Prepare data for charts
const monthlyLabels = <?= json_encode(array_column($monthlyData, 'label')) ?>;
const monthlyIncome = <?= json_encode(array_column($monthlyData, 'income')) ?>;
const monthlyExpense = <?= json_encode(array_column($monthlyData, 'expense')) ?>;

const categoryLabels = <?= json_encode(array_column($categoryData, 'nama_kategori')) ?>;
const categoryValues = <?= json_encode(array_column($categoryData, 'total')) ?>;

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initMonthlyComparisonChart(monthlyLabels, monthlyIncome, monthlyExpense);
    initTrendChart(monthlyLabels, monthlyIncome, monthlyExpense);
    
    if (categoryLabels.length > 0) {
        initCategoryPieChart(categoryLabels, categoryValues);
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
