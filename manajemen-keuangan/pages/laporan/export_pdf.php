<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$userId = getUserId();

// Note: Untuk implementasi lengkap, install mPDF via Composer:
// composer require mpdf/mpdf

// Karena ini adalah demo, kita akan membuat laporan HTML sederhana
// yang bisa di-print sebagai PDF menggunakan CTRL+P

// Get filter parameters (sama seperti index.php)
$periode = $_GET['periode'] ?? 'monthly';
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-t');

if ($periode === 'daily') {
    $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
    $where = "AND t.tanggal = '$tanggal'";
    $periodLabel = date('d F Y', strtotime($tanggal));
} elseif ($periode === 'weekly') {
    $where = "AND t.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
    $periodLabel = date('d M', strtotime($tanggal_mulai)) . ' - ' . date('d M Y', strtotime($tanggal_selesai));
} elseif ($periode === 'monthly') {
    $where = "AND MONTH(t.tanggal) = '$bulan' AND YEAR(t.tanggal) = '$tahun'";
    $periodLabel = date('F Y', strtotime("$tahun-$bulan-01"));
} else {
    $where = "AND YEAR(t.tanggal) = '$tahun'";
    $periodLabel = "Tahun $tahun";
}

// Get statistics
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN jenis_transaksi = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
    SUM(CASE WHEN jenis_transaksi = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
    FROM tbl_transaksi t WHERE user_id = ? $where");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$totalPemasukan = $stats['total_pemasukan'] ?? 0;
$totalPengeluaran = $stats['total_pengeluaran'] ?? 0;
$saldo = $totalPemasukan - $totalPengeluaran;

// Get transactions
$stmt = $pdo->prepare("SELECT t.*, k.nama_kategori, b.nama_bank
    FROM tbl_transaksi t
    JOIN tbl_kategori k ON t.kategori_id = k.kategori_id
    LEFT JOIN tbl_bank b ON t.bank_id = b.bank_id
    WHERE t.user_id = ? $where
    ORDER BY t.tanggal DESC");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - <?= $periodLabel ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #333; padding-bottom: 10px; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .summary-box { border: 2px solid #ddd; padding: 15px; border-radius: 8px; text-align: center; min-width: 200px; }
        .summary-box.income { background: #d4f4dd; border-color: #b8f3d1; }
        .summary-box.expense { background: #ffd5d5; border-color: #ffb5b5; }
        .summary-box.total { background: #e8eef3; border-color: #c8b6e2; }
       .summary-box h3 { margin: 5px 0; font-size: 24px; }
        .summary-box p { margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan Pribadi</h1>
        <h3>Periode: <?= $periodLabel ?></h3>
        <p>User: <?= htmlspecialchars(getNamaLengkap()) ?></p>
        <p>Tanggal Cetak: <?= date('d F Y H:i') ?> WIB</p>
    </div>
    
    <div class="summary">
        <div class="summary-box income">
            <p>Total Pemasukan</p>
            <h3>Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h3>
        </div>
        <div class="summary-box expense">
            <p>Total Pengeluaran</p>
            <h3>Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h3>
        </div>
        <div class="summary-box total">
            <p>Saldo</p>
            <h3>Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
        </div>
    </div>
    
    <h2>Detail Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="12%">Jenis</th>
                <th width="15%">Kategori</th>
                <th width="15%">Bank</th>
                <th width="15%" class="text-right">Jumlah</th>
                <th width="26%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($transactions as $trans): 
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($trans['tanggal'])) ?></td>
                    <td><?= $trans['jenis_transaksi'] ?></td>
                    <td><?= htmlspecialchars($trans['nama_kategori']) ?></td>
                    <td><?= htmlspecialchars($trans['nama_bank'] ?? '-') ?></td>
                    <td class="text-right"><strong>Rp <?= number_format($trans['jumlah'], 0, ',', '.') ?></strong></td>
                    <td><?= htmlspecialchars($trans['keterangan'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f5f5f5;">
                <td colspan="5" class="text-right">TOTAL PEMASUKAN:</td>
                <td class="text-right" style="color: green;">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></td>
                <td></td>
            </tr>
            <tr style="font-weight: bold; background: #f5f5f5;">
                <td colspan="5" class="text-right">TOTAL PENGELUARAN:</td>
                <td class="text-right" style="color: red;">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></td>
                <td></td>
            </tr>
            <tr style="font-weight: bold; background: #e8eef3;">
                <td colspan="5" class="text-right">SALDO AKHIR:</td>
                <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            🖨️ Print / Save as PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            ✖️ Tutup
        </button>
    </div>
    
    <script>
        // Auto print on load (optional, comment out if not needed)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
