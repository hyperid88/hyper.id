<?php
session_start();
include "../koneksi.php";
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Filter bulan
$filter_month = isset($_GET['month']) ? $_GET['month'] : 'all';

// Ambil data nasabah ACC
$sql = "SELECT * FROM pengajuan WHERE status='approved'";
if($filter_month != 'all'){
    $sql .= " AND MONTH(created_at)='$filter_month'";
}
$nasabah_result = $koneksi->query($sql);

// Hitung stats
$stats = [
    'kasbon_rupiah'=>0, 'kasbon_rupiah_profit'=>0,
    'kasbon_dollar'=>0, 'kasbon_dollar_profit'=>0,
    'gadai_hp'=>0, 'gadai_hp_profit'=>0,
    'gadai_motor'=>0, 'gadai_motor_profit'=>0,
    'gadai_emas'=>0, 'gadai_emas_profit'=>0,
    'total_nominal'=>0,
    'total_profit'=>0,
    'total_orang'=>0
];

while($row = $nasabah_result->fetch_assoc()){
    $stats['total_nominal'] += $row['nominal'];
    $profit = $row['total_bayar'] - $row['nominal'];
    $stats['total_profit'] += $profit;
    $stats['total_orang']++;

    $jenis = strtolower(trim($row['jenis_transaksi']));
    switch($jenis){
        case 'kasbon rupiah':
            $stats['kasbon_rupiah']++;
            $stats['kasbon_rupiah_profit'] += $profit;
            break;
        case 'kasbon dollar':
            $stats['kasbon_dollar']++;
            $stats['kasbon_dollar_profit'] += $profit;
            break;
        case 'gadai hp':
            $stats['gadai_hp']++;
            $stats['gadai_hp_profit'] += $profit;
            break;
        case 'gadai motor':
            $stats['gadai_motor']++;
            $stats['gadai_motor_profit'] += $profit;
            break;
        case 'gadai emas':
            $stats['gadai_emas']++;
            $stats['gadai_emas_profit'] += $profit;
            break;
    }
}

// Chart data
$chart_labels = ['Kasbon Rupiah','Kasbon Dollar','Gadai HP','Gadai Motor','Gadai Emas'];
$chart_data = [
    $stats['kasbon_rupiah_profit'],
    $stats['kasbon_dollar_profit'],
    $stats['gadai_hp_profit'],
    $stats['gadai_motor_profit'],
    $stats['gadai_emas_profit']
];

// Fungsi nama bulan
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
<title>Dashboard Admin HYPER.ID</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { font-family:'Poppins',sans-serif; background:#111; color:white; }
.sidebar { width:220px; background:#1a1a1a; min-height:100vh; position:fixed; top:0; left:0; padding:20px; display:flex; flex-direction:column; gap:15px; }
.sidebar h2 { color:#FF3C3C; margin-bottom:10px; text-align:center; font-size:24px; }
.sidebar a { background:#222; padding:12px; border-radius:12px; text-decoration:none; color:white; font-weight:600; transition:0.3s; text-align:center; }
.sidebar a:hover { background:#FF3C3C; }
.main { margin-left:240px; padding:20px; }
.card { background:rgba(20,20,20,0.85); border:1px solid rgba(255,60,60,0.3); padding:20px; border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.6); transition:0.3s; }
.card:hover { transform: translateY(-4px); box-shadow:0 12px 25px rgba(0,0,0,0.7); }
.card h3 { color:#FF3C3C; margin-bottom:10px; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>HYPER.ID Admin</h2>
    <a href="admin_saham.php">Pemegang Saham</a>
    <a href="admin_acc.php">ACC/Tolak</a>
    <a href="admin_history.php">History</a>
    <a href="admin_logout.php">Logout</a>
</div>

<div class="main">

    <!-- Sambutan Dashboard -->
    <div class="card mb-6 text-center">
        <h2 class="text-2xl text-red-500 font-bold mb-2">📊 Selamat Datang di Dashboard HYPER.ID!</h2>
        <p class="text-gray-300">Pantau transaksi nasabah, total pinjaman, dan profit setiap jenis transaksi dengan mudah dan cepat.</p>
    </div>

    <!-- Filter Bulan -->
    <div class="card mb-6">
        <form method="get" class="flex items-center gap-3 flex-wrap">
            <label for="month" class="font-semibold">Filter Bulan:</label>
            <select name="month" id="month" class="bg-gray-800 px-3 py-1 rounded text-white">
                <option value="all" <?= $filter_month=='all'?'selected':'' ?>>Semua Bulan</option>
                <?php for($m=1;$m<=12;$m++):
                    $mStr = str_pad($m,2,'0',STR_PAD_LEFT);
                ?>
                <option value="<?= $mStr ?>" <?= $filter_month==$mStr?'selected':'' ?>><?= monthName($mStr) ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded font-semibold">Terapkan</button>
        </form>
    </div>

    <!-- Stats Global -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card text-center">
            <h3>Total Nasabah</h3>
            <p class="text-xl"><?= $stats['total_orang'] ?></p>
        </div>
        <div class="card text-center">
            <h3>Total Nominal Pinjaman</h3>
            <p class="text-xl">Rp<?= number_format($stats['total_nominal'],0,",",".") ?></p>
        </div>
        <div class="card text-center">
            <h3>Total Profit</h3>
            <p class="text-xl">Rp<?= number_format($stats['total_profit'],0,",",".") ?></p>
        </div>
    </div>

    <!-- Detail Jenis Transaksi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php
        $transaksi = [
            ['label'=>'Kasbon Rupiah','count'=>$stats['kasbon_rupiah'],'profit'=>$stats['kasbon_rupiah_profit']],
            ['label'=>'Kasbon Dollar','count'=>$stats['kasbon_dollar'],'profit'=>$stats['kasbon_dollar_profit']],
            ['label'=>'Gadai HP','count'=>$stats['gadai_hp'],'profit'=>$stats['gadai_hp_profit']],
            ['label'=>'Gadai Motor','count'=>$stats['gadai_motor'],'profit'=>$stats['gadai_motor_profit']],
            ['label'=>'Gadai Emas','count'=>$stats['gadai_emas'],'profit'=>$stats['gadai_emas_profit']]
        ];
        foreach($transaksi as $t):
        ?>
        <div class="card text-center">
            <h3><?= $t['label'] ?></h3>
            <p>Transaksi: <?= $t['count'] ?></p>
            <p>Profit: Rp<?= number_format($t['profit'],0,",",".") ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Chart Total Profit -->
    <div class="card">
        <h3>Total Profit per Jenis Transaksi</h3>
        <canvas id="profitChart"></canvas>
    </div>

</div>

<script>
const ctx = document.getElementById('profitChart').getContext('2d');
new Chart(ctx,{
    type:'bar',
    data:{
        labels: <?= json_encode($chart_labels) ?>,
        datasets:[{
            label:'Profit (Rp)',
            data: <?= json_encode($chart_data) ?>,
            backgroundColor:[
                'rgba(255,60,60,0.7)',
                'rgba(255,100,100,0.7)',
                'rgba(180,0,0,0.7)',
                'rgba(139,0,0,0.7)',
                'rgba(200,50,50,0.7)'
            ],
            borderColor:[
                'rgba(255,60,60,1)',
                'rgba(255,100,100,1)',
                'rgba(180,0,0,1)',
                'rgba(139,0,0,1)',
                'rgba(200,50,50,1)'
            ],
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
