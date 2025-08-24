<?php
session_start();
include "../koneksi.php";

// Debug koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM admin WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Password MD5 legacy
        if ($row['password'] === md5($password)) {
            $_SESSION['admin'] = $username;

            // Debug session
            // echo "Sukses login, session tersimpan: " . $_SESSION['admin'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm bg-gray-800 p-8 rounded-xl shadow-lg animate-fadeIn">
        <h2 class="text-2xl font-bold text-red-500 mb-6 text-center">Admin Login</h2>
        <form method="post" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required
                   class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
            <input type="password" name="password" placeholder="Password" required
                   class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
            <button type="submit" name="login"
                    class="w-full bg-red-600 hover:bg-red-700 transition-colors py-2 rounded-lg font-semibold text-white">
                Login
            </button>
        </form>
        <?php if (!empty($error)) : ?>
            <p class="mt-4 text-center text-red-500 font-medium"><?= $error ?></p>
        <?php endif; ?>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
    </style>
</body>
</html>
