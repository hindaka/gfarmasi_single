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
$hariini = date("d/m/Y");
$gabung1 = isset($_GET["g1"]) ? $_GET['g1'] : '';
$gabung2 = isset($_GET["g2"]) ? $_GET['g2'] : '';

//mysql data pasien
$sql = "SELECT im.*,g.satuan FROM itemfaktur im INNER JOIN gobat g ON(im.id_obat=g.id_obat) WHERE CAST(CONCAT(SUBSTRING(im.tanggal,7,4),SUBSTRING(im.tanggal,4,2)) as UNSIGNED) >= '" . $gabung1 . "' AND CAST(CONCAT(SUBSTRING(im.tanggal,7,4),SUBSTRING(im.tanggal,4,2)) as UNSIGNED) <= '" . $gabung2 . "'";
$h2 = $db->query($sql);
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);

//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap-masuk-periode(" . $gabung1 . "-" . $gabung2 . ").xls");
?>
Data rekapitulasi Obat Masuk Gudang Farmasi Periode <?php echo $gabung1; ?> - <?php echo $gabung2; ?>
<table id="example1" class="table table-bordered table-striped" border="1">
    <thead>
        <tr>
            <th>Tanggal Input</th>
            <th>Tanggal</th>
            <th>Nama obat</th>
            <th>Satuan</th>
            <th>Jenis/Kategori</th>
            <th>Merk/Pabrikan</th>
            <th>Volume</th>
            <th>Harga</th>
            <th>Diskon(%)</th>
            <th>PPN</th>
            <th>Total</th>
            <th>No. Batch</th>
            <th>Expiry date</th>
            <th>Sumber</th>
            <th>Harga E-katalog</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data2 as $r2) {
            $jenis = isset($r2['jenis']) ? $r2['jenis'] : '';
            if ($jenis == 'generik') {
                $merk_pabrik = isset($r2['pabrikan']) ? $r2['pabrikan'] : '';
            } else if ($jenis == 'non generik') {
                $merk_pabrik = isset($r2['merk']) ? $r2['merk'] : '';
            } else {
                $merk_pabrik = isset($r2['pabrikan']) ? $r2['pabrikan'] : '';
            }
            $e_kat = isset($r2['e_kat']) ? $r2['e_kat'] : '';
            if ($e_kat == 'ya') {
                $e_kat_label = "ya";
            } else {
                $e_kat_label = 'tidak';
            }
            echo "<tr>
					<td>" . $r2['time'] . "</td>
					<td>" . $r2['tanggal'] . "</td>
					<td>" . $r2['namaobat'] . "</td>
					<td>" . $r2['satuan'] . "</td>
					<td>" . $jenis . "</td>
					<td>" . $merk_pabrik . "</td>
					<td>" . $r2['volume'] . "</td>
					<td>" . $r2['harga'] . "</td>
					<td>" . $r2['diskon'] . "</td>
					<td>" . $r2['ppn'] . "</td>
					<td>" . $r2['total'] . "</td>
					<td>" . $r2['nobatch'] . "</td>
					<td>" . $r2['expired'] . "</td>
					<td>" . $r2['sumber'] . "</td>
                    <td>".$e_kat_label."</td>
				</tr>";
        }
        ?>
    </tbody>
</table>