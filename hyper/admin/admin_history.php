<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Search, filter bulan & status
$search = isset($_GET['search']) ? $_GET['search'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT * FROM pengajuan WHERE status!='pending'";

if (!empty($search)) {
    $query .= " AND nama LIKE '%$search%'";
}
if (!empty($bulan)) {
    $query .= " AND MONTH(created_at)='$bulan'";
}
if (!empty($status_filter) && in_array($status_filter,['approved','reject'])) {
    $query .= " AND status='$status_filter'";
}

$query .= " ORDER BY id DESC";

$result = $koneksi->query($query);

// Hitung total nominal dan total bayar untuk yang di-Approved
$total_nominal = 0;
$total_bayar = 0;
while($row = $result->fetch_assoc()){
    if($row['status'] === 'approved'){
        $total_nominal += $row['nominal'];
        $total_bayar += $row['total_bayar'];
    }
}
$result->data_seek(0); // Reset pointer
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>History Transaksi</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { font-family:'Poppins',sans-serif; background:#111; color:white; }
.sidebar { width:220px; background:#1a1a1a; min-height:100vh; position:fixed; top:0; left:0; padding:20px; display:flex; flex-direction:column; gap:15px; }
.sidebar h2 { color:#FF3C3C; margin-bottom:10px; text-align:center; }
.sidebar a { background:#222; padding:12px; border-radius:12px; text-decoration:none; color:white; font-weight:600; transition:0.3s; text-align:center; }
.sidebar a:hover { background:#FF3C3C; }
.main { margin-left:240px; padding:20px; }
.card { background:rgba(20,20,20,0.85); border:1px solid rgba(255,60,60,0.3); padding:20px; border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.6); transition:0.3s; }
.card:hover { transform: translateY(-4px); box-shadow:0 12px 25px rgba(0,0,0,0.7); }
.card h3 { color:#FF3C3C; margin-bottom:15px; font-size:20px; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>HYPER.ID Admin</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_saham.php">Pemegang Saham</a>
    <a href="admin_acc.php">ACC/Tolak</a>
    <a href="admin_history.php">History</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main">
    <div class="card">
        <h3>📑 History Transaksi</h3>

        <!-- Form Filter -->
        <form method="get" class="flex flex-wrap gap-3 mb-4">
            <input type="text" name="search" placeholder="Cari Nama..." value="<?= htmlspecialchars($search) ?>"
                class="px-4 py-2 rounded-lg bg-gray-800 text-white border border-gray-600 focus:outline-none">
            
            <select name="bulan" class="px-4 py-2 rounded-lg bg-gray-800 text-white border border-gray-600 focus:outline-none">
                <option value="">Semua Bulan</option>
                <?php for ($i=1; $i<=12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($bulan==$i?'selected':'') ?>><?= date("F", mktime(0,0,0,$i,1)) ?></option>
                <?php endfor; ?>
            </select>

            <select name="status" class="px-4 py-2 rounded-lg bg-gray-800 text-white border border-gray-600 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="approved" <?= ($status_filter=='approved'?'selected':'') ?>>Approved</option>
                <option value="reject" <?= ($status_filter=='reject'?'selected':'') ?>>Reject</option>
            </select>

            <button type="submit" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg font-semibold transition">
                Filter
            </button>
        </form>

        <!-- Total Approved -->
        <div class="flex gap-6 mb-4 text-white">
            <div class="bg-gray-800 p-4 rounded-lg flex-1 shadow-md">
                <h4 class="text-red-500 font-semibold mb-2">Total Nominal Approved</h4>
                <p>Rp <?= number_format($total_nominal,0,",",".") ?></p>
            </div>
            <div class="bg-gray-800 p-4 rounded-lg flex-1 shadow-md">
                <h4 class="text-red-500 font-semibold mb-2">Total Bayar Approved</h4>
                <p>Rp <?= number_format($total_bayar,0,",",".") ?></p>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-center">
                <thead class="bg-red-600">
    <tr>
        <th class="px-3 py-2">ID</th>
        <th class="px-3 py-2">Nama</th>
        <th class="px-3 py-2">Bank</th>
        <th class="px-3 py-2">Nomor Rekening</th>
        <th class="px-3 py-2">Jenis</th>
        <th class="px-3 py-2">Nominal</th>
        <th class="px-3 py-2">Tempo</th>
        <th class="px-3 py-2">Total Bayar</th>
        <th class="px-3 py-2">Status</th>
        <th class="px-3 py-2">Tanggal</th>
    </tr>
</thead>
<tbody class="divide-y divide-gray-700">
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="hover:bg-gray-800 transition">
            <td class="px-3 py-2"><?= $row['id'] ?></td>
            <td class="px-3 py-2"><?= $row['nama'] ?></td>
            <td class="px-3 py-2"><?= $row['bank'] ?></td>
            <td class="px-3 py-2"><?= $row['norek'] ?></td>
            <td class="px-3 py-2"><?= $row['jenis_transaksi'] ?></td>
            <td class="px-3 py-2"><?= number_format($row['nominal']) ?></td>
            <td class="px-3 py-2"><?= $row['tempo'] ?> bulan</td>
            <td class="px-3 py-2"><?= number_format($row['total_bayar']) ?></td>
            <td class="px-3 py-2 capitalize"><?= $row['status'] ?></td>
            <td class="px-3 py-2"><?= date("d M Y", strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="10" class="py-4">⚠️ Tidak ada data ditemukan.</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
