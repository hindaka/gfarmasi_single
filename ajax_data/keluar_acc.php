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
//get var
$id_parent = isset($_POST['parent']) ? $_POST['parent'] : '';
$tipe = isset($_POST['type']) ? $_POST['type'] : '';
$tanggal = isset($_POST["tglkeluar"]) ? $_POST['tglkeluar'] : '';
$id_kartu = isset($_POST["id_kartu"]) ? $_POST['id_kartu'] : '';
$volume_input = isset($_POST["volume"]) ? $_POST['volume'] : '';
$id_warehouse = isset($_POST['id_warehouse']) ? $_POST['id_warehouse'] : '';
$warehouse = isset($_POST['warehouse']) ? $_POST['warehouse'] : '';
$pemesan = isset($_POST['pemesan']) ? $_POST['pemesan'] : '';
$tuslah = isset($_POST['tuslah']) ? $_POST['tuslah'] : '1';
$id_tuslah = isset($_POST['id_tuslah']) ? $_POST['id_tuslah'] : '';
$in_out = "booked";
$keterangan = "Pesanan dari " . $warehouse . " oleh " . $pemesan;
//convert tanggal
$format = explode("-", substr($tanggal, 0, 10));
$new_date = $format[2] . "/" . $format['1'] . "/" . $format[0];
$volume_in = 0;
$feedback = [];

try {
	// get data kartu
	$kartu_persediaan = $db->query("SELECT k.*,g.nama FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.id_kartu='" . $id_kartu . "'");
	$list_masuk = $kartu_persediaan->fetch(PDO::FETCH_ASSOC);
	$volume_kartu_awal = isset($list_masuk['volume_kartu_awal']) ? $list_masuk['volume_kartu_awal'] : 0;
	$volume_akhir = isset($list_masuk['volume_kartu_akhir']) ? $list_masuk['volume_kartu_akhir'] : 0;
	$volume_out = $volume_input;
	$volume_sisa = $volume_akhir - $volume_out;
	$id_obat = isset($list_masuk['id_obat']) ? $list_masuk['id_obat'] : 0;
	$namaobat = isset($list_masuk['nama']) ? $list_masuk['nama'] : '';
	$sumber = isset($list_masuk['sumber_dana']) ? $list_masuk['sumber_dana'] : '';
	$merk = isset($list_masuk['merk']) ? $list_masuk['merk'] : '';
	$jenis = isset($list_masuk['jenis']) ? $list_masuk['jenis'] : '';
	$pabrikan = isset($list_masuk['pabrikan']) ? $list_masuk['pabrikan'] : '';
	$expired = isset($list_masuk['expired']) ? $list_masuk['expired'] : '';
	$no_batch = isset($list_masuk['no_batch']) ? $list_masuk['no_batch'] : '';
	$harga_beli = isset($list_masuk['harga_beli']) ? $list_masuk['harga_beli'] : 0;
	$harga_jual = isset($list_masuk['harga_jual_non_tuslah']) ? $list_masuk['harga_jual_non_tuslah'] : 0;
	//update volume_kartu_akhir berdasarkan data on point
	$update_vol = $db->query("UPDATE kartu_stok_gobat SET volume_kartu_akhir='" . $volume_sisa . "' WHERE id_kartu='" . $id_kartu . "'");
	//insert ke kartu_stok_gobat
	$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_gobat`(`id_obat`, `sumber_dana`,`merk`,`jenis`,`pabrikan`, `volume_kartu_awal`,`volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual_non_tuslah`, `keterangan`, `ref`) VALUES (:id_obat,:sumber,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:keterangan,:ref)");
	$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
	$ins_kartu->bindParam(":sumber", $sumber, PDO::PARAM_STR);
	$ins_kartu->bindParam(":merk", $merk, PDO::PARAM_STR);
	$ins_kartu->bindParam(":jenis", $jenis, PDO::PARAM_STR);
	$ins_kartu->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
	$ins_kartu->bindParam(":volume_kartu_awal", $volume_kartu_awal, PDO::PARAM_INT);
	$ins_kartu->bindParam(":volume_kartu_akhir", $volume_sisa, PDO::PARAM_INT);
	$ins_kartu->bindParam(":volume_sisa", $volume_sisa, PDO::PARAM_INT);
	$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
	$ins_kartu->bindParam(":tujuan", $warehouse, PDO::PARAM_STR);
	$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
	$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
	$ins_kartu->bindParam(":expired", $expired, PDO::PARAM_STR);
	$ins_kartu->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
	$ins_kartu->bindParam(":harga_beli", $harga_beli, PDO::PARAM_INT);
	$ins_kartu->bindParam(":harga_jual", $harga_jual, PDO::PARAM_INT);
	$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
	$ins_kartu->bindParam(":ref", $id_kartu, PDO::PARAM_INT);
	$ins_kartu->execute();
	$id_kartu_gobat = $db->lastInsertId();
	//insert ke obatkeluar
	$ins_keluar = $db->prepare("INSERT INTO `obatkeluar`(`id_obat`, `id_parent`,`id_kartu`, `id_warehouse`, `tanggal`, `namaobat`, `sumber`,`merk`,`jenis`,`pabrikan`, `volume`, `ruang`,`id_tuslah`,`ket_tuslah`,`time`)VALUES (:id_obat,:id_parent,:id_kartu,:id_warehouse,:tanggal,:namaobat,:sumber,:merk,:jenis,:pabrikan,:volume,:ruang,:id_tuslah,:ket_tuslah,:waktu)");
	$ins_keluar->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
	$ins_keluar->bindParam(":id_parent", $id_parent, PDO::PARAM_INT);
	$ins_keluar->bindParam(":id_kartu", $id_kartu_gobat, PDO::PARAM_INT);
	$ins_keluar->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
	$ins_keluar->bindParam(":tanggal", $new_date, PDO::PARAM_STR);
	$ins_keluar->bindParam(":namaobat", $namaobat, PDO::PARAM_STR);
	$ins_keluar->bindParam(":sumber", $sumber, PDO::PARAM_STR);
	$ins_keluar->bindParam(":merk", $merk, PDO::PARAM_STR);
	$ins_keluar->bindParam(":jenis", $jenis, PDO::PARAM_STR);
	$ins_keluar->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
	$ins_keluar->bindParam(":volume", $volume_out, PDO::PARAM_INT);
	$ins_keluar->bindParam(":ruang", $warehouse, PDO::PARAM_STR);
	$ins_keluar->bindParam(":id_tuslah", $id_tuslah, PDO::PARAM_INT);
	$ins_keluar->bindParam(":ket_tuslah", $tuslah, PDO::PARAM_INT);
	$ins_keluar->bindParam(":waktu", $tanggal, PDO::PARAM_STR);
	$ins_keluar->execute();
	$feedback = [
		"status" => 200,
		"title" => "Berhasil",
		"msg" => "Data Berhasil ditambahkan",
		"icon" => "success"
	];
} catch (PDOException $e) {
	$feedback = [
		"status" => 400,
		"title" => "Galat",
		"msg" => $e->getMessage()."onfile : ".$e->getFile()." online : ".$e->getLine(),
		"icon" => "error"
	];
}
echo json_encode($feedback);