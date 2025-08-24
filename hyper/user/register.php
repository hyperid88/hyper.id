<?php
// register.php
include '../koneksi.php';
session_start();

// Jika user sudah login, redirect ke dashboard
if(isset($_SESSION['id_nasabah'])){
    header("Location: dashboard.php");
    exit();
}

// Proses form submit
$message = '';
if(isset($_POST['id_nasabah'], $_POST['nama'], $_POST['password'])){
    $id_nasabah = trim($_POST['id_nasabah']);
    $nama       = trim($_POST['nama']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek duplicate
    $cek = $koneksi->prepare("SELECT id_nasabah FROM users WHERE id_nasabah=?");
    $cek->bind_param("s", $id_nasabah);
    $cek->execute();
    $cek->store_result();

    if($cek->num_rows > 0){
        $message = "ID Nasabah sudah terdaftar, silakan pilih ID lain.";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO users (id_nasabah, nama, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id_nasabah, $nama, $password);
        if($stmt->execute()){
            $message = "Registrasi berhasil! <a href='login.php' class='text-red-400 underline'>Login di sini</a>";
        } else {
            $message = "Gagal registrasi. Coba lagi.";
        }
        $stmt->close();
    }

    $cek->close();
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - HYPER.ID</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#000000,#8B0000);
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}
.card {
    background: rgba(0,0,0,0.7);
    padding: 40px;
    border-radius: 20px;
    width: 350px;
    text-align:center;
    box-shadow: 0 0 25px rgba(255,0,0,0.3);
}
.card h1 {
    font-size:2rem;
    margin-bottom:20px;
}
.card input {
    width:100%;
    padding:12px;
    margin:12px 0;
    border:none;
    border-radius:10px;
    background:#1a1a1a;
    color:white;
    font-size:14px;
}
.card button {
    width:100%;
    padding:15px;
    margin-top:15px;
    border:none;
    border-radius:12px;
    background:#FF3C3C;
    font-weight:bold;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}
.card button:hover { background:#e60000; }
.message {
    margin-top:15px;
    font-size:0.95rem;
    color:#00ffb3;
}
.message.error { color:#ff4d4d; }
a { color:#FF3C3C; text-decoration:none; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="card">
    <h1>Register HYPER.ID</h1>
    <?php if($message != ''): ?>
        <div class="message <?php echo (strpos($message,'sudah')!==false)?'error':''; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="id_nasabah" placeholder="ID Nasabah" required>
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
    <p class="mt-4 text-gray-300 text-sm">Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</body>
</html>
