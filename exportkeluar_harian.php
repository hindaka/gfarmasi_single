<?php
session_start();
include("../inc/pdo.conf.php");
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
$tanggal_awal = isset($_GET["tanggal_awal"]) ? $_GET['tanggal_awal'] : '';
$new_awal = str_replace("-", "", $tanggal_awal);
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$new_akhir = str_replace("-", "", $tanggal_akhir);

$sql = "SELECT ob.*,IFNULL(ks.harga_beli,'kosong') as harga_netto, (ob.volume * (IFNULL(ks.harga_beli,0))) as total_biaya,ks.expired,ks.no_batch,g.satuan,ks.jenis,ks.merk,ks.pabrikan  FROM `obatkeluar` ob LEFT JOIN gobat g ON(ob.id_obat=g.id_obat) LEFT JOIN kartu_stok_gobat ks ON(ob.id_kartu=ks.id_kartu) WHERE CAST(CONCAT(SUBSTRING(ob.tanggal,7,4),SUBSTRING(ob.tanggal,4,2),SUBSTRING(ob.tanggal,1,2)) as UNSIGNED)>=" . $new_awal . " AND CAST(CONCAT(SUBSTRING(ob.tanggal,7,4),SUBSTRING(ob.tanggal,4,2),SUBSTRING(ob.tanggal,1,2)) as UNSIGNED)<=" . $new_akhir;
$h2 = $db->query($sql);
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_obat_keluar_" . $new_awal . "_" . $new_akhir . ".xls");
?>
Data rekapitulasi Obat Keluar Gudang Farmasi Tanggal <?php echo $tanggal_awal; ?> s.d <?php echo $tanggal_akhir; ?>
<table id="example1" class="table table-bordered table-striped" border="1">
    <thead>
        <tr>
            <th>Tanggal Input</th>
            <th>Tanggal Keluar</th>
            <th>Nama Obat</th>
            <th>Satuan</th>
            <th>Jenis/Kategori</th>
            <th>Merk/Pabrikan</th>
            <th>Sumber</th>
            <th>Volume</th>
            <th>No. Batch</th>
            <th>Expired</th>
            <th>Ruang / Tujuan</th>
            <th>Harga Beli</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data2 as $r2) {
            $jenis = isset($r2['jenis']) ? $r2['jenis'] : '';
            if ($jenis == 'generik') {
                $merk_pabrikan = isset($r2['pabrikan']) ? $r2['pabrikan'] : '';
            } else if ($jenis == 'non generik') {
                $merk_pabrikan = isset($r2['merk']) ? $r2['merk'] : '';
            } else {
                $merk_pabrikan = isset($r2['pabrikan']) ? $r2['pabrikan'] : '';
            }
            echo "<tr>
					<td>" . $r2['time'] . "</td>
					<td>" . $r2['tanggal'] . "</td>
					<td>" . $r2['namaobat'] . "</td>
					<td>" . $r2['satuan'] . "</td>
					<td>" . $jenis . "</td>
					<td>" . $merk_pabrikan . "</td>
					<td>" . $r2['sumber'] . "</td>
					<td>" . $r2['volume'] . "</td>
					<td>" . $r2['no_batch'] . "</td>
					<td>" . $r2['expired'] . "</td>
					<td>" . $r2['ruang'] . "</td>
					<td>" . $r2['harga_netto'] . "</td>
				</tr>";
        }
        ?>
    </tbody>
</table>