<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$bln1 = base64_decode($_GET["awal"]);
$awal = str_replace("-", "", $bln1);
$bln2 = base64_decode($_GET["akhir"]);
$akhir = str_replace("-", "", $bln2);

$sql = "SELECT f.id_faktur,CONCAT(SUBSTRING(f.tgl_faktur,7,4),'-',SUBSTRING(f.tgl_faktur,4,2),'-',SUBSTRING(f.tgl_faktur,1,2)) as 'tanggal_faktur',f.tgl_faktur,f.no_faktur,f.ppn_persen,f.ekatalog,f.perusahaan,f.tgl_bayar,f.tgl_rbayar,f.cara_bayar,f.pembayaran_tunai,im.namaobat,g.jenis,g.satuan,g.fornas,im.volume,im.harga,im.diskon,im.total,im.nobatch,im.expired,im.sumber,im.ppn_text,im.time,f.keterangan,f.sumber_pelunasan FROM `faktur` f INNER JOIN itemfaktur im ON(f.id_faktur=im.id_faktur) INNER JOIN gobat g ON(im.namaobat=g.nama AND im.sumber=g.sumber) WHERE CAST(CONCAT(SUBSTRING(f.tgl_faktur,7,4),SUBSTRING(f.tgl_faktur,4,2),SUBSTRING(f.tgl_faktur,1,2)) as UNSIGNED) >= :awal AND CAST(CONCAT(SUBSTRING(f.tgl_faktur,7,4),SUBSTRING(f.tgl_faktur,4,2),SUBSTRING(f.tgl_faktur,1,2)) as UNSIGNED) <= :akhir ORDER BY tanggal_faktur,no_faktur";
$h2 = $db->prepare($sql);
$h2->bindParam(":awal", $awal);
$h2->bindParam(":akhir", $akhir);
$h2->execute();
$list_faktur = $h2->fetchAll(PDO::FETCH_ASSOC);
$total_data = $h2->rowCount();

//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap-faktur-" . $bln1 . "-" . $bln2 . ".xls");
?>
Data rekapitulasi Faktur Gudang Farmasi <?php echo $bln1 . " - " . $bln2; ?>
<table border=1>
    <thead>
        <tr>
            <th>No.Register</th>
            <th>Tanggal Faktur</th>
            <th>Tanggal Bayar</th>
            <th>Cara Bayar</th>
            <th>Pembayaran Tunai</th>
            <th>No. Faktur</th>
            <th>PPN(%)</th>
            <th>E-katalog</th>
            <th>Perusahaan</th>
            <th>No.Batch</th>
            <th>Expired</th>
            <th>Nama Obat</th>
            <th>Jenis</th>
            <th>Satuan</th>
            <th>Fornas</th>
            <th>Volume</th>
            <th>HNA</th>
            <th>diskon (%)</th>
            <th>Total</th>
            <th>HNA+PPN</th>
            <th>Sumber</th>
            <th>Dana Pelunasan</th>
            <th>Keterangan</th>
            <th>Waktu Input</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_harga = 0;
        foreach ($list_faktur as $row) {
            $harga = isset($row['harga']) ? $row['harga'] : 0;
            $diskon = isset($row['diskon']) ? $row['diskon'] : 0;
            if ($diskon > 0) {
                $hitungdiskon = ($harga * $diskon) / 100;
            } else {
                $hitungdiskon = 0;
            }
            $hargafinal = $harga - $hitungdiskon;
            if ($row['ppn_text'] == '11') {
                $hnappn = $hargafinal + ($hargafinal * 0.11);
            } else {
                $hnappn = $hargafinal + ($hargafinal * 0.1);
            }
            $total_harga += $row['total'];
            echo "<tr>
					<td>" . $row['id_faktur'] . "</td>
					<td>" . $row['tanggal_faktur'] . "</td>
					<td>" . $row['tgl_bayar'] . "</td>
					<td>" . $row['cara_bayar'] . "</td>
					<td>" . $row['pembayaran_tunai'] . "</td>
					<td>'" . $row['no_faktur'] . "</td>
					<td>" . $row['ppn_persen'] . "</td>
					<td>" . $row['ekatalog'] . "</td>
					<td>" . $row['perusahaan'] . "</td>
					<td>'" . $row['nobatch'] . "</td>
					<td>" . $row['expired'] . "</td>
					<td>" . $row['namaobat'] . "</td>
					<td>" . $row['jenis'] . "</td>
					<td>" . $row['satuan'] . "</td>
					<td>" . $row['fornas'] . "</td>
					<td>" . $row['volume'] . "</td>
					<td>" . $row['harga_satuan'] . "</td>
					<td>" . $row['diskon'] . "</td>
					<td>" . $row['total'] . "</td>
					<td>" . $hnappn . "</td>
					<td>" . $row['sumber'] . "</td>
					<td>" . $row['sumber_pelunasan'] . "</td>
					<td>" . $row['keterangan'] . "</td>
					<td>" . $row['time'] . "</td>
			</tr>";
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="15"></td>
            <td><?php echo $total_harga; ?></td>
        </tr>
    </tfoot>
</table>