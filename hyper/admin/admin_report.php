<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$result = $koneksi->query("SELECT * FROM pengajuan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengajuan</title>
    <style>
        body { font-family: Arial; background:#111; color:white; }
        h1 { text-align:center; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #444; padding:10px; text-align:center; }
        th { background:red; }
    </style>
</head>
<body>
    <h1>Laporan Pengajuan</h1>
    <a href="admin_dashboard.php">← Kembali</a>
    <table>
        <tr>
            <th>ID</th><th>Nama</th><th>Bank</th><th>Jenis</th><th>Nominal</th>
            <th>Tempo</th><th>Total Bayar</th><th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['bank'] ?></td>
            <td><?= $row['jenis_transaksi'] ?></td>
            <td><?= number_format($row['nominal']) ?></td>
            <td><?= $row['tempo'] ?> bulan</td>
            <td><?= number_format($row['total_bayar']) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
