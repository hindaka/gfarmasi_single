<?php
//conn
session_start();
include("../../inc/pdo.conf.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'Gfarmasi') {
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$id_faktur = isset($_POST['id_faktur']) ? $_POST['id_faktur'] : '';
$stmt = $db->query("SELECT id_obat,namaobat,nobatch,expired,merk,jenis,pabrikan,harga_satuan FROM itemfaktur WHERE id_faktur='" . $id_faktur . "'");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$html = '<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr class="info">
			<th>Nama Barang</th>
			<th>Jenis</th>
			<th>Merk</th>
			<th>Pabrikan</th>
			<th>No Batch</th>	
			<th>Expired</th>
			<th>Harga Satuan Faktur</th>
			<th>Riwayat Harga Terakhir</th>
		</tr>
	</thead>
	<tbody>';

foreach ($data as $row) {
	$nama_obat = isset($row['namaobat']) ? $row['namaobat'] : '';
	$jenis = isset($row['jenis']) ? $row['jenis'] : '';
	$merk = isset($row['merk']) ? $row['merk'] : '';
	$pabrikan = isset($row['pabrikan']) ? $row['pabrikan'] : '';
	$no_batch = isset($row['nobatch']) ? $row['nobatch'] : '-';
	$expired = isset($row['expired']) ? $row['expired'] : '';
	$id_obat = isset($row['id_obat']) ? $row['id_obat'] : '';
	$harga_satuan = isset($row['harga_satuan']) ? $row['harga_satuan'] : 0;
	if ($jenis == 'generik') {
		$get_harga = $db->query("SELECT harga_beli FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND jenis='" . $jenis . "' AND pabrikan='" . $pabrikan . "' AND aktif='ya' ORDER BY id_kartu DESC LIMIT 1");
		$data_harga = $get_harga->fetch(PDO::FETCH_ASSOC);
	} else if ($jenis == 'non generik') {
		$get_harga = $db->query("SELECT harga_beli FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND jenis='" . $jenis . "' AND merk='" . $merk . "' AND aktif='ya' ORDER BY id_kartu DESC LIMIT 1");
		$data_harga = $get_harga->fetch(PDO::FETCH_ASSOC);
	} else {
		$get_harga = $db->query("SELECT harga_beli FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND jenis='" . $jenis . "' AND aktif='ya' ORDER BY id_kartu DESC LIMIT 1");
		$data_harga = $get_harga->fetch(PDO::FETCH_ASSOC);
	}
	$html .= '<tr>
		<td>' . $nama_obat . '</td>
		<td>' . $jenis . '</td>
		<td>' . $merk . '</td>
		<td>' . $pabrikan . '</td>
		<td>' . $no_batch . '</td>
		<td>' . $expired . '</td>
		<td class="warning" style="text-align:right;">' . $harga_satuan . '</td>
		<td class="success" style="text-align:right;">' . $data_harga['harga_beli'] . '</td>
	</tr>';
}
$html .= '</tbody>
	</table>';
echo $html;
