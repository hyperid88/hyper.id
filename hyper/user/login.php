<?php
session_start();
$message = ""; // <- inisialisasi agar tidak undefined
include('../koneksi.php');

// kode login di sini...
if(isset($_POST['id_nasabah'])){
    $id_nasabah = $_POST['id_nasabah'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE id_nasabah = ? LIMIT 1");
    $stmt->bind_param("s", $id_nasabah);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['id_nasabah'] = $user['id_nasabah'];
            $_SESSION['nama'] = $user['nama'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Password salah!";
        }
    } else {
        $message = "ID Nasabah tidak terdaftar, silahkan register.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - HYPER.ID</title>
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
    <h1>Login HYPER.ID</h1>
    <?php if($message != ''): ?>
        <div class="message <?php echo (strpos($message,'tidak')!==false)?'error':''; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="id_nasabah" placeholder="ID Nasabah" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p class="mt-4 text-gray-300 text-sm">Belum punya akun? <a href="register.php">Register</a></p>
</div>
</body>
</html>
