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
$id_obat = isset($_POST["id_obat"]) ? $_POST['id_obat'] : '';
$jenis = isset($_POST["jenis"]) ? $_POST['jenis'] : '';
$merk_pabrikan = isset($_POST["merk_pabrikan"]) ? $_POST['merk_pabrikan'] : '';
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
if ($jenis == 'generik') {
	$flag = "k.pabrikan LIKE '" . $merk_pabrikan . "'";
} else if ($jenis == 'non generik') {
	$flag = "k.merk LIKE '" . $merk_pabrikan . "'";
} else {
	$flag = "k.merk LIKE '" . $merk_pabrikan . "'";
}
try {
	$stmt = $db->query("SELECT k.*,g.nama FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.in_out='masuk' AND k.volume_kartu_akhir>0 AND k.id_obat='" . $id_obat . "' AND k.jenis='" . $jenis . "' AND $flag ORDER BY k.expired ASC");
	$data_kartu = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$i = 0;
	$finish = false;
	$volume_out = $volume_input;
	while ($finish == false) {
		$id_kartu = isset($data_kartu[$i]['id_kartu']) ? $data_kartu[$i]['id_kartu'] : '';
		$volume_kartu_awal = isset($data_kartu[$i]['volume_kartu_awal']) ? $data_kartu[$i]['volume_kartu_awal'] : '';
		$volume_kartu_akhir = isset($data_kartu[$i]['volume_kartu_akhir']) ? $data_kartu[$i]['volume_kartu_akhir'] : '';
		$jenis = isset($data_kartu[$i]['jenis']) ? $data_kartu[$i]['jenis'] : '';
		$merk = isset($data_kartu[$i]['merk']) ? $data_kartu[$i]['merk'] : '';
		$pabrikan = isset($data_kartu[$i]['pabrikan']) ? $data_kartu[$i]['pabrikan'] : '';
		$expired = isset($data_kartu[$i]['expired']) ? $data_kartu[$i]['expired'] : '';
		$no_batch = isset($data_kartu[$i]['no_batch']) ? $data_kartu[$i]['no_batch'] : '';
		$harga_beli = isset($data_kartu[$i]['harga_beli']) ? $data_kartu[$i]['harga_beli'] : '';
		$harga_jual = isset($data_kartu[$i]['harga_jual_non_tuslah']) ? $data_kartu[$i]['harga_jual_non_tuslah'] : '';
		$sumber_dana = isset($data_kartu[$i]['sumber_dana']) ? $data_kartu[$i]['sumber_dana'] : '';
		$e_kat = isset($data_kartu[$i]['e_kat']) ? $data_kartu[$i]['e_kat'] : '';
		$namaobat = isset($data_kartu[$i]['nama']) ? $data_kartu[$i]['nama'] : '';

		$in_out_ruangan = "masuk";
		$tujuan_ruangan = $warehouse;
		$volume_akhir = 0;
		$volume_out_ruangan = 0;
		$created_at_ruangan = date('Y-m-d H:i:s');
		$keterangan_ruangan = "Barang dari gudang";

		if ($volume_kartu_akhir >= $volume_out) {
			//stok tercukupi
			$volume_kartu_akhir = $volume_kartu_akhir - $volume_out;
			$sisa_keluar = 0;
			$volume_in_ruangan = $volume_out;
			$volume_kartu_awal = $volume_out;
			$volume_sisa = $volume_kartu_akhir - $volume_out;
		} else {
			// stok tidak tercukupi
			$sisa_keluar = $volume_out - $volume_kartu_akhir;
			$volume_in_ruangan = $volume_kartu_akhir;
			if ($volume_out > $volume_kartu_akhir) {
				$volume_sisa = $volume_out - $volume_kartu_akhir;
			}
			$volume = $volume_kartu_akhir;
			$volume_out = $volume_kartu_akhir;
			$volume_kartu_awal = $volume_kartu_akhir;
		}
		//update volume_kartu_akhir berdasarkan data on point
		$update_vol = $db->query("UPDATE kartu_stok_gobat SET volume_kartu_akhir='" . $volume_kartu_akhir . "' WHERE id_kartu='" . $id_kartu . "'");
		//insert ke kartu_stok_gobat
		$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_gobat`(`id_obat`, `sumber_dana`,`e_kat`,`jenis`,`merk`,`pabrikan`, `volume_kartu_awal`,`volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual_non_tuslah`, `keterangan`, `ref`)
	VALUES (:id_obat,:sumber,:e_kat,:jenis,:merk,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:keterangan,:ref)");
		$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$ins_kartu->bindParam(":sumber", $sumber_dana, PDO::PARAM_STR);
		$ins_kartu->bindParam(":e_kat", $e_kat, PDO::PARAM_STR);
		$ins_kartu->bindParam(":jenis", $jenis, PDO::PARAM_STR);
		$ins_kartu->bindParam(":merk", $merk, PDO::PARAM_STR);
		$ins_kartu->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_kartu_awal", $volume_kartu_awal, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_kartu_akhir", $volume_akhir, PDO::PARAM_INT);
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
		$id_kartu_lasts = $db->lastInsertId();
		//insert ke obatkeluar
		$ins_keluar = $db->prepare("INSERT INTO `obatkeluar`(`id_obat`, `id_parent`,`id_kartu`, `id_warehouse`, `tanggal`, `namaobat`, `sumber`,`e_kat`,`merk`,`jenis`,`pabrikan`, `volume`, `ruang`,`id_tuslah`,`ket_tuslah`,`time`)VALUES (:id_obat,:id_parent,:id_kartu,:id_warehouse,:tanggal,:namaobat,:sumber,:e_kat,:merk,:jenis,:pabrikan,:volume,:ruang,:id_tuslah,:ket_tuslah,:waktu)");
		$ins_keluar->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$ins_keluar->bindParam(":id_parent", $id_parent, PDO::PARAM_INT);
		$ins_keluar->bindParam(":id_kartu", $id_kartu_lasts, PDO::PARAM_INT);
		$ins_keluar->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
		$ins_keluar->bindParam(":tanggal", $new_date, PDO::PARAM_STR);
		$ins_keluar->bindParam(":namaobat", $namaobat, PDO::PARAM_STR);
		$ins_keluar->bindParam(":sumber", $sumber_dana, PDO::PARAM_STR);
		$ins_keluar->bindParam(":e_kat", $e_kat, PDO::PARAM_STR);
		$ins_keluar->bindParam(":merk", $merk, PDO::PARAM_STR);
		$ins_keluar->bindParam(":jenis", $jenis, PDO::PARAM_STR);
		$ins_keluar->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
		$ins_keluar->bindParam(":volume", $volume_out, PDO::PARAM_INT);
		$ins_keluar->bindParam(":ruang", $warehouse, PDO::PARAM_STR);
		$ins_keluar->bindParam(":id_tuslah", $id_tuslah, PDO::PARAM_INT);
		$ins_keluar->bindParam(":ket_tuslah", $tuslah, PDO::PARAM_INT);
		$ins_keluar->bindParam(":waktu", $tanggal, PDO::PARAM_STR);
		$ins_keluar->execute();
		if ($sisa_keluar == 0) {
			//end loop
			$finish = true;
		} else {
			// next data
			$i++;
			//sisa yang belum keluar menjadi volume yang akan dicari
			$volume_out = $sisa_keluar;
		}
	}
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
		"msg" => $e->getMessage() . "onfile : " . $e->getFile() . " online : " . $e->getLine(),
		"icon" => "error"
	];
}
echo json_encode($feedback);
