<?php
session_start();
if(!isset($_SESSION['id_nasabah'])){
    header("Location: login.php");
    exit();
}
include('../koneksi.php');

$success = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_nasabah = $_SESSION['id_nasabah'];
    $nama = $_SESSION['nama'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $nominal_input = preg_replace('/\D/','',$_POST['nominal']); // input user
    $tempo = (int)$_POST['tempo'];
    $spesifikasi = $_POST['spesifikasi'];
    $bunga = 0.15;

    $rate = ($jenis_transaksi=="Kasbon Dollar" || $jenis_transaksi=="Tukar Dollar ke Rupiah") ? 16500 : 1;

    // Nominal & total bayar untuk DB selalu dalam Rupiah
    $nominal_db = $nominal_input * $rate;
    $total_bayar_db = round($nominal_db * (1 + $bunga * $tempo));

    // Norek & bank optional untuk dollar
    $norek = ($jenis_transaksi=="Kasbon Dollar"||$jenis_transaksi=="Tukar Dollar ke Rupiah") ? '' : $_POST['norek'];
    $bank  = ($jenis_transaksi=="Kasbon Dollar"||$jenis_transaksi=="Tukar Dollar ke Rupiah") ? '' : $_POST['bank'];

    $sql = "INSERT INTO pengajuan (id_nasabah, nama, norek, bank, jenis_transaksi, nominal, tempo, total_bayar, spesifikasi)
            VALUES ('$id_nasabah','$nama','$norek','$bank','$jenis_transaksi','$nominal_db','$tempo','$total_bayar_db','$spesifikasi')";

    if(mysqli_query($koneksi,$sql)){
        $success = true;
    } else {
        echo "Error: ".mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ajukan Pinjaman - HYPER.ID</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body { font-family: Poppins, sans-serif; background: linear-gradient(135deg,#000,#8B0000); color:white; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
.container { max-width:600px; width:100%; background:rgba(255,255,255,0.08); backdrop-filter:blur(10px); border-radius:16px; padding:30px; box-shadow:0 8px 20px rgba(0,0,0,0.3); text-align:center; }
h2 { margin-bottom:20px; font-size:1.8rem; }
input, select, textarea { width:100%; padding:12px; margin-top:8px; margin-bottom:15px; border:none; border-radius:10px; background:#1a1a1a; color:white; font-size:14px; }
button { background:#FF3C3C; color:white; font-weight:bold; padding:12px; width:100%; border-radius:10px; cursor:pointer; transition:0.3s; font-size:16px; }
button:hover { background:#e60000; }
#hasil { font-weight:bold; color:#00ffb3; text-align:center; margin-bottom:15px; }
a.back { background:#222; padding:8px 12px; border-radius:8px; text-decoration:none; color:white; display:inline-block; margin-bottom:15px; }
a.back:hover { background:#333; }
</style>
</head>
<body>
<div class="container">
<a href="dashboard.php" class="back">← Kembali ke Dashboard</a>
<h2>Form Pengajuan HYPER.ID</h2>

<?php if($success): ?>
    <div id="hasil">✅ Pengajuan berhasil! Total bayar otomatis sudah tersimpan dalam Rupiah.</div>
<?php endif; ?>

<form id="form-transaksi" method="POST">
    <label>Nama Lengkap:</label>
    <input type="text" name="nama" value="<?= $_SESSION['nama']; ?>" readonly>

    <label>Nomor Rekening:</label>
    <input type="text" name="norek" id="norekInput" required>

    <label>Pilih Bank:</label>
    <select name="bank" id="bankSelect" required>
        <option value="">-- Pilih Bank --</option>
        <option value="BCA">BCA</option>
        <option value="BRI">BRI</option>
        <option value="MANDIRI">Mandiri</option>
        <option value="OVO">OVO</option>
        <option value="GOPAY">GOPAY</option>
        <option value="SEABANK">SEABANK</option>
    </select>

    <label>Jenis Transaksi:</label>
    <select name="jenis_transaksi" id="jenis_transaksi" required>
        <option value="">-- Pilih Transaksi --</option>
        <option value="Kasbon Rupiah">Kasbon Rupiah</option>
        <option value="Kasbon Dollar">Kasbon Dollar</option>
        <option value="Gadai HP">Gadai HP</option>
        <option value="Gadai Motor">Gadai Motor</option>
        <option value="Gadai Emas">Gadai Emas</option>
        <option value="Tukar Dollar ke Rupiah">Tukar Dollar ke Rupiah</option>
    </select>

    <label>Nominal Pengajuan:</label>
    <input type="text" name="nominal" id="nominal" placeholder="Contoh: 100$ / 1.000.000" required>

    <label>Tempo Bayar (bulan):</label>
    <select name="tempo" id="tempo" required>
        <option value="">-- Pilih Tempo --</option>
        <?php for($i=1;$i<=12;$i++){ echo "<option value='$i'>$i Bulan</option>"; } ?>
    </select>

    <label>Spesifikasi (jika Gadai Barang):</label>
    <textarea name="spesifikasi" rows="3"></textarea>

    <div class="mb-4">
      <input type="checkbox" id="agree" required>
      <label for="agree">Saya sudah membaca dan menyetujui semua ketentuan HYPER.ID</label>
    </div>

    <div id="hasil"></div>
    <input type="hidden" name="total_bayar" id="total_bayar">
    <button type="submit">Ajukan Sekarang</button>
</form>
</div>

<script>
const nominalInput = document.getElementById('nominal');
const tempoSelect = document.getElementById('tempo');
const hasilDiv = document.getElementById('hasil');
const totalBayarInput = document.getElementById('total_bayar');
const jenisSelect = document.getElementById('jenis_transaksi');
const norekInput = document.getElementById('norekInput');
const bankSelect = document.getElementById('bankSelect');

function parseNominal(str){ return parseInt(str.replace(/\D/g,'')) || 0; }
function formatRupiah(angka){ return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g,"."); }

function hitungTotal(){
    const nominal = parseNominal(nominalInput.value);
    const tempo = parseInt(tempoSelect.value) || 0;
    const jenis = jenisSelect.value;

    let symbol = "Rp";
    const bunga = 0.15;
    let total = 0;

    if(nominal>0 && tempo>0){
        if(jenis==="Kasbon Dollar" || jenis==="Tukar Dollar ke Rupiah"){
            symbol = "$";
            total = nominal * (1 + bunga*tempo); // tampil tetap $ untuk user
            norekInput.value = '';
            norekInput.required = false;
            bankSelect.selectedIndex = 0;
            bankSelect.required = false;
        } else {
            total = nominal * (1 + bunga*tempo);
            norekInput.required = true;
            bankSelect.required = true;
        }
        hasilDiv.innerText = `Total yang harus dibayar: ${symbol} ${formatRupiah(Math.round(total))}`;
        totalBayarInput.value = Math.round(total); // frontend
    } else {
        hasilDiv.innerText = '';
        totalBayarInput.value = '';
    }
}

nominalInput.addEventListener('input', function(){
    let raw = this.value.replace(/\D/g,'');
    this.value = raw.length>0 ? formatRupiah(raw) : '';
    hitungTotal();
});
tempoSelect.addEventListener('change', hitungTotal);
jenisSelect.addEventListener('change', hitungTotal);

document.getElementById('form-transaksi').addEventListener('submit', function(e){
    if(!document.getElementById('agree').checked){
        e.preventDefault();
        alert("Silakan centang persetujuan terlebih dahulu!");
        return false;
    }
});
</script>
</body>
</html>
