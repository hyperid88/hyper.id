<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Filter bulan
$filter_month = isset($_GET['month']) ? $_GET['month'] : 'all';

// Ambil semua pemegang saham
$shareholders = $koneksi->query("SELECT * FROM pemegang_saham");

// Hitung total profit sesuai filter
$sql = "SELECT total_bayar, nominal, created_at FROM pengajuan WHERE status='approved'";
if($filter_month != 'all'){
    $sql .= " AND MONTH(created_at)='$filter_month'";
}
$result = $koneksi->query($sql);

$total_profit = 0;
while($row = $result->fetch_assoc()){
    $total_profit += ($row['total_bayar'] - $row['nominal']);
}

// Siapkan data chart
$chart_labels = [];
$chart_data = [];
$shareholders->data_seek(0);
while($row = $shareholders->fetch_assoc()){
    $dividen = $total_profit * ($row['saham']/100);
    $chart_labels[] = $row['nama'];
    $chart_data[] = $dividen;
}
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);
$shareholders->data_seek(0);

function monthName($monthNum){
    $months = ["01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni",
               "07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"];
    return $months[$monthNum] ?? "";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pemegang Saham - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
.filter { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px; }
.filter select, .filter button { padding:8px 12px; border-radius:8px; border:none; font-weight:600; }
.filter select { background:#222; color:white; }
.filter button { background:#FF3C3C; color:white; cursor:pointer; transition:0.3s; }
.filter button:hover { background:#e60000; }
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
        <h3>📊 Total Profit</h3>
        <p class="text-xl">Rp<?= number_format($total_profit,0,",",".") ?> <?= $filter_month=='all'?'(Global)':'('.monthName($filter_month).')' ?></p>
    </div>

    <div class="card">
        <h3>Filter Bulan</h3>
        <form method="get" class="filter">
            <select name="month">
                <option value="all" <?= $filter_month=='all'?'selected':'' ?>>Semua Bulan</option>
                <?php for($m=1;$m<=12;$m++):
                    $mStr = str_pad($m,2,'0',STR_PAD_LEFT);
                ?>
                <option value="<?= $mStr ?>" <?= $filter_month==$mStr?'selected':'' ?>><?= monthName($mStr) ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit">Terapkan</button>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <h3>💰 Dividen Pemegang Saham</h3>
        <table class="table min-w-full text-center border-collapse">
            <thead>
                <tr>
                    <th>Pemegang Saham</th>
                    <th>Modal</th>
                    <th>Saham (%)</th>
                    <th>Dividen</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $shareholders->fetch_assoc()): 
                    $dividen = $total_profit * ($row['saham']/100);
                ?>
                <tr>
                    <td><?= $row['nama'] ?></td>
                    <td>Rp<?= number_format($row['modal'],0,",",".") ?></td>
                    <td><?= $row['saham'] ?>%</td>
                    <td>Rp<?= number_format($dividen,0,",",".") ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>📊 Chart Dividen</h3>
        <canvas id="dividenChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('dividenChart').getContext('2d');
new Chart(ctx,{
    type:'bar',
    data:{
        labels: <?= $chart_labels_json ?>,
        datasets:[{
            label:'Dividen (Rp)',
            data: <?= $chart_data_json ?>,
            backgroundColor:'rgba(239,68,68,0.7)',
            borderColor:'rgba(239,68,68,1)',
            borderWidth:1
        }]
    },
    options:{
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
    }
});
</script>

</body>
</html>
