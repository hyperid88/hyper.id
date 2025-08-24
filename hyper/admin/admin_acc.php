<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Jika form submit
if(isset($_POST['approve'])){
    $id = $_POST['id'];
    $now = date('Y-m-d H:i:s');
    $koneksi->query("UPDATE pengajuan SET status='approved', created_at='$now' WHERE id='$id'");
}

if(isset($_POST['reject'])){
    $id = $_POST['id'];
    $now = date('Y-m-d H:i:s');
    $koneksi->query("UPDATE pengajuan SET status='rejected', created_at='$now' WHERE id='$id'");
}

// Ambil semua pengajuan pending
$result = $koneksi->query("SELECT * FROM pengajuan WHERE status='pending' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>ACC / Tolak - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { font-family:'Poppins',sans-serif; background:#111; color:white; }
.sidebar { width:220px; background:#1a1a1a; min-height:100vh; position:fixed; top:0; left:0; padding:20px; display:flex; flex-direction:column; gap:15px; }
.sidebar h2 { color:#FF3C3C; margin-bottom:10px; text-align:center; font-size:24px; }
.sidebar a { background:#222; padding:12px; border-radius:12px; text-decoration:none; color:white; font-weight:600; transition:0.3s; text-align:center; }
.sidebar a:hover { background:#FF3C3C; }
.main { margin-left:240px; padding:20px; }
.card { background:rgba(20,20,20,0.85); border:1px solid rgba(255,60,60,0.3); padding:20px; border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.6); transition:0.3s; margin-bottom:20px; }
.card:hover { transform: translateY(-4px); box-shadow:0 12px 25px rgba(0,0,0,0.7); }
.card h3 { color:#FF3C3C; margin-bottom:15px; font-size:22px; }
.table th, .table td { padding:12px; }
.table thead { background:#FF3C3C; color:white; }
.table tbody tr:hover { background:rgba(255,60,60,0.2); }
.btn { padding:6px 12px; border-radius:8px; font-semibold; cursor:pointer; transition:0.3s; }
.btn-approve { background:#22c55e; }
.btn-approve:hover { background:#16a34a; }
.btn-reject { background:#ef4444; }
.btn-reject:hover { background:#b91c1c; }
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
    <div class="card overflow-x-auto">
        <h3>📋 Proses ACC / Tolak</h3>
        <table class="table min-w-full text-center border-collapse">
<thead>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Bank</th>
        <th>Nomor Rekening</th>
        <th>Jenis</th>
        <th>Nominal</th>
        <th>Tempo</th>
        <th>Total Bayar</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['bank'] ?></td>
            <td><?= $row['norek'] ?></td>
            <td><?= $row['jenis_transaksi'] ?></td>
            <td>Rp<?= number_format($row['nominal'],0,",",".") ?></td>
            <td><?= $row['tempo'] ?> bulan</td>
            <td>Rp<?= number_format($row['total_bayar'],0,",",".") ?></td>
            <td class="flex justify-center gap-2">
                <form method="post" class="inline">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button name="approve" class="btn btn-approve">ACC</button>
                </form>
                <form method="post" class="inline">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button name="reject" class="btn btn-reject">Tolak</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9" class="py-4">Tidak ada transaksi pending.</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>
</div>

</body>
</html>
