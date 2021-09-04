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
// $tgl=$_GET["tgl"];
$hariini = date("d/m/Y");
$gabung1 = isset($_GET["tgl1"]) ? $_GET['tgl1'] : '';
$gabung2 = isset($_GET["tgl2"]) ? $_GET['tgl2'] : '';

$sql = "SELECT op.*,o.id_kartu,o.namaobat,g.satuan,o.volume,o.sumber,o.ruang as tujuan FROM `obatkeluar_parent` op INNER JOIN obatkeluar o ON(o.id_parent=op.id_obatkeluar_parent) LEFT JOIN gobat g ON(o.namaobat=g.nama AND o.sumber=g.sumber) WHERE CAST(CONCAT(SUBSTRING(o.tanggal,7,4),SUBSTRING(o.tanggal,4,2)) as UNSIGNED) >= '$gabung1' AND CAST(CONCAT(SUBSTRING(o.tanggal,7,4),SUBSTRING(o.tanggal,4,2)) as UNSIGNED) <= '$gabung2'";
$h2 = $db->query($sql);
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap-spb-Periode(" . $gabung1 . "-" . $gabung2 . ").xls");
?>
Data Rekap SPB Gudang Farmasi Periode <?php echo $gabung1 . "-" . $gabung2; ?>
<table border="1">
    <thead>
        <tr>
            <th>Tanggal Keluar</th>
            <th>Nomor Nota Dinas</th>
            <th>Nomor SPB</th>
            <th>Nomor SPBB</th>
            <th>Nama Obat</th>
            <th>Satuan</th>
            <th>Jenis/Kategori</th>
            <th>Merk/Pabrikan</th>
            <th>Sumber</th>
            <th>Volume</th>
            <th>Ruang / Tujuan</th>
            <th>Harga Beli</th>
            <th>No. Batch</th>
            <th>Expired</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data2 as $r2) {
            $h3 = $db->query("SELECT harga_beli,no_batch,expired,jenis,merk,pabrikan FROM kartu_stok_gobat WHERE id_kartu='" . $r2['id_kartu'] . "'");
            $r3 = $h3->fetch(PDO::FETCH_ASSOC);
            $harga_beli = isset($r3['harga_beli']) ? $r3['harga_beli'] : '';
            $batch = isset($r3["no_batch"]) ? $r3["no_batch"] : '';
            $exp = isset($r3["expired"]) ? $r3["expired"] : '';
            $jenis = isset($r3['jenis']) ? $r3['jenis'] : '';
            if ($jenis == 'generik') {
                $merk_pabrikan = isset($r3['pabrikan']) ? $r3['pabrikan'] : '';
            } else if ($jenis == 'non generik') {
                $merk_pabrikan = isset($r3['merk']) ? $r3['merk'] : '';
            } else {
                $merk_pabrikan = isset($r3['pabrikan']) ? $r3['pabrikan'] : '';
            }

            echo "<tr>
                    <td>" . $r2['tanggal_keluar'] . "</td>
                    <td>" . $r2['nomor'] . "</td>
                    <td>" . $r2['no_spb'] . "</td>
                    <td>" . $r2['no_spbb'] . "</td>
                    <td>" . $r2['namaobat'] . "</td>
                    <td>" . $r2['satuan'] . "</td>
                    <td>" . $jenis . "</td>
                    <td>" . $merk_pabrikan . "</td>
                    <td>" . $r2['sumber'] . "</td>
                    <td>" . $r2['volume'] . "</td>
                    <td>" . $r2['tujuan'] . "</td>
                    <td>Rp." . number_format($harga_beli, $digit_akhir, ',', '.') . "</td>
                    <td>" . $batch . "</td>
                    <td>" . $exp . "</td>
                </tr>";
        }

        ?>
    </tbody>
</table>