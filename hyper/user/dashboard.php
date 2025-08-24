<?php
session_start();
if(!isset($_SESSION['id_nasabah'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Dashboard - HYPER.ID</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#000000,#8B0000);
    color:white;
    margin:0;
}
nav {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 40px;
    background:#8B0000;
    box-shadow:0 4px 10px rgba(0,0,0,0.3);
}
nav a { color:white; font-weight:bold; margin-left:20px; transition:0.3s; }
nav a:hover { color:#FF3C3C; }
h1,h2,h3,p { margin:0; }
.hero {
    text-align:center;
    padding:80px 20px;
}
.hero h1 { font-size:3rem; margin-bottom:20px; }
.hero p { font-size:1.2rem; margin-bottom:40px; color:#ccc; }
button.ajukan {
    background:#FF3C3C;
    color:white;
    font-weight:bold;
    padding:15px 30px;
    border:none;
    border-radius:12px;
    font-size:1.2rem;
    cursor:pointer;
    transition:0.3s;
}
button.ajukan:hover { background:#e60000; }
section {
    padding:60px 20px;
}
.card {
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(10px);
    padding:25px;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(255,0,0,0.3);
    margin-bottom:30px;
}
.grid-3 {
    display:grid;
    gap:20px;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
}
footer { background:#8B0000; text-align:center; padding:20px; margin-top:40px; color:#ccc; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo font-bold text-2xl">HYPER.ID</div>
    <div>
        <a href="#layanan">Layanan</a>
        <a href="#rules">Rules</a>
        <a href="#kontak">Kontak</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<!-- HERO / TOMBOL AJUKAN -->
<section class="hero">
    <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h1>
    <p>Pinjaman Online Masa Kini, Cepat, Aman, Terpercaya</p>
    <a href="ajukan.php"><button class="ajukan">Ajukan Sekarang</button></a>
</section>

<!-- LAYANAN -->
<section id="layanan">
    <h2 class="text-center text-3xl font-bold mb-12">Layanan Kami</h2>
    <div class="grid-3">
        <div class="card">
            <h3 class="font-bold mb-2">🔐 GADAI BARANG (HP | Emas | Motor)</h3>
            <p>Dana cepat untuk kebutuhan darurat, tanpa prosedur rumit. Gadai HP, motor, atau emas Anda.</p>
        </div>
        <div class="card">
            <h3 class="font-bold mb-2">💵 LOAN RUPIAH & LOAN DOLLAR</h3>
            <p>Pinjaman instan berbasis jaminan atau kepercayaan, solusi pinjaman berbasis USD untuk kebutuhan mendesak.</p>
        </div>
        <div class="card">
            <h3 class="font-bold mb-2">💱 CHANGE DOLLAR TO RUPIAH</h3>
            <p>Rate kompetitif, proses cepat dan terpercaya.</p>
        </div>
    </div>
</section>

<!-- RULES -->
<section id="rules" style="background:rgba(0,0,0,0.4);">
    <h2 class="text-center text-3xl font-bold mb-12">Rules Resmi HYPER.MONEY</h2>
    <div class="grid-3">
        <div class="card">
            <h3 class="font-bold text-red-400 mb-2">🔐 GADAI BARANG</h3>
            <ul class="list-disc list-inside text-gray-300">
                <li>Biaya Gadai: 15% per bulan</li>
                <li>Tenggat: 1 bulan + 1 minggu</li>
                <li>Lewat tenggat = barang jadi milik HYPER.ID</li>
                <li>Barang disimpan aman & tertata</li>
                <li>Limit: 30%-50% dari harga pasaran</li>
            </ul>
        </div>
        <div class="card">
            <h3 class="font-bold text-red-400 mb-2">💱 CHANGE DOLLAR</h3>
            <ul class="list-disc list-inside text-gray-300">
                <li>Rate fluktuatif mengikuti pasar</li>
                <li>Cash & ABA Only</li>
                <li>Transfer ke seluruh bank di Indonesia</li>
            </ul>
        </div>
        <div class="card">
            <h3 class="font-bold text-red-400 mb-2">💵 LOAN</h3>
            <ul class="list-disc list-inside text-gray-300">
                <li>Fee: 15% per bulan</li>
                <li>Tempo sesuai kesepakatan</li>
                <li>Keterlambatan dikenakan penalti, fee naik tiap bulan +15%</li>
            </ul>
        </div>
    </div>
</section>

<!-- KONTAK -->
<section id="kontak" class="text-center">
    <h2 class="text-3xl font-bold mb-6">Kontak Kami</h2>
    <p>📲 WhatsApp: +62 812-1015-7626</p>
    <p>📲 Telegram: @HYPERID88</p>
    <p>📍 Alamat: Jakarta, Indonesia</p>
</section>

<!-- FOOTER -->
<footer>
    &copy; 2025 HYPER.ID - All rights reserved.
</footer>

</body>
</html>
