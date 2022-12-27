<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/set_gfarmasi.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'Gfarmasi') {
    unset($_SESSION['tipe']);
    unset($_SESSION['namauser']);
    unset($_SESSION['password']);
    header("location:../index.php?status=2");
    exit;
}
include "../inc/anggota_check.php";
$sumber = isset($_GET["sumber"]) ? $_GET['sumber'] : 'APBD';
$hariini = date("d/m/Y");
//mysql data pasien
$h2 = $db->query("SELECT kg.id_obat,g.nama,g.kadar,g.satuan,g.satuan_jual,g.kemasan,kg.jenis,kg.merk,kg.pabrikan,SUM(kg.volume_kartu_akhir) as stok,kg.harga_beli,kg.no_batch,kg.expired,g.flag_single_id,g.old_id_ref FROM kartu_stok_gobat kg INNER JOIN gobat g ON(kg.id_obat=g.id_obat) WHERE in_out='masuk' GROUP BY kg.id_obat,kg.no_batch,kg.harga_beli ORDER BY g.nama ASC");
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
$total_data = $h2->rowCount();
//EXCEL
// header("Content-type: application/vnd.ms-excel");
// header("Content-Disposition: attachment; filename=stok-gudangfarmasi-" . $hariini . ".xls");
?>
Data stok Obat Gudang Farmasi, per <?php echo $hariini; ?>
<table id="example1" class="table table-bordered table-striped" border="1">
    <thead>
        <tr>
            <th>No. ID</th>
            <th>Nama Obat</th>
            <th>Jenis</th>
            <th>Volume</th>
            <th>Satuan</th>
            <th>HNA + PPN</th>
            <th>No Batch</th>
            <th>Tanggal Kadaluarsa</th>
            <th>Single ID</th>
            <th>Ref ID Lama</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($total_data > 0) {
            foreach ($data2 as $r2) {
                $nama = isset($r2['nama']) ? $r2['nama'] : '';
                $kadar = isset($r2['kadar']) ? $r2['kadar'] : '';
                $satuan_kadar = isset($r2['satuan_kadar']) ? $r2['satuan_kadar'] : '';
                $satuan_jual = isset($r2['satuan_jual']) ? $r2['satuan_jual'] : '';
                $kemasan = isset($r2['kemasan']) ? $r2['kemasan'] : '';
                $jenis = isset($r2['jenis']) ? $r2['jenis'] : '';
                $merk = isset($r2['merk']) ? $r2['merk'] : '';
                $pabrikan = isset($r2['pabrikan']) ? $r2['pabrikan'] : '';
                $nama_text = viewNamaBarang($nama, $kadar, $satuan_kadar, $satuan_jual, $kemasan, $jenis, $pabrikan, $merk);
                echo "<tr>
                        <td>" . $r2['id_obat'] . "</td>
                        <td>" . $nama_text . "</td>
                        <td>" . $jenis . "</td>
                        <td>" . $r2['stok'] . "</td>
                        <td>" . $r2['satuan_jual'] . "</td>
                        <td>" . $r2['harga_beli'] . "</td>
                        <td>" . $r2['no_batch'] . "</td>
                        <td>" . $r2['expired'] . "</td>
                        <td>" . $r2['flag_single_id'] . "</td>
                        <td>" . $r2['old_id_ref'] . "</td>
                    </tr>";
            }
        }
        ?>
    </tbody>
</table>
<?php echo $total_data; ?>