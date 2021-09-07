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
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
if ($jenis == 'generik') {
	$groupBy = 'GROUP BY pabrikan';
} else if ($jenis == 'non generik') {
	$groupBy = 'GROUP BY merk';
} else if ($jenis == 'bmhp') {
	$groupBy = 'GROUP BY pabrikan';
} else {
	$groupBy = '';
}
$stmt = $db->query("SELECT jenis,merk,pabrikan FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND jenis='" . $jenis . "' " . $groupBy);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_data = $stmt->rowCount();
$feedback_data = [
	"total_data" => 0,
	"content" => []
];
if ($total_data > 0) {
	foreach ($data as $d) {
		$item = [
			"jenis" => $d['jenis'],
			"merk" => $d['merk'],
			"pabrikan" => $d['pabrikan']
		];
		array_push($feedback_data['content'], $item);
	}
	$feedback_data['total_data'] = $total_data;
}
echo json_encode($feedback_data);
