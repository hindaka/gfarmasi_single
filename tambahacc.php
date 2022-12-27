<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$mem_id = isset($r1['mem_id']) ? $r1['mem_id'] : NULL;
//get var
$id_faktur = isset($_GET["id"]) ? $_GET['id'] : '';
$sumber_dana = isset($_GET['sumber']) ? $_GET['sumber'] : '';
$id_obat = isset($_POST["id_obat"]) ? $_POST['id_obat'] : '';
$merk = isset($_POST["merk"]) ? $_POST['merk'] : '';
$jenis = isset($_POST["jenis"]) ? $_POST['jenis'] : '';
$pabrikan = isset($_POST["pabrikan"]) ? $_POST['pabrikan'] : '';
$volume = isset($_POST["volume"]) ? $_POST['volume'] : '';
$harga = isset($_POST["harga"]) ? trim($_POST['harga']) : '';
$harga = str_replace(",", ".", $harga);
$diskon = isset($_POST["diskon"]) ? $_POST['diskon'] : '';
$nobatch = isset($_POST["nobatch"]) ? $_POST['nobatch'] : '';
$expired = isset($_POST["expired"]) ? $_POST['expired'] : '';
$ppn = isset($_POST['ppn']) ? $_POST['ppn'] : '0';
$e_kat = isset($_POST['e_kat']) ? $_POST['e_kat'] : 'tidak';

$exp = explode("/", $expired);
$expired_stok = $exp[2] . "/" . $exp[1] . "/" . $exp[0];
$hariini = date("d/m/Y");
$in_out = "masuk";
$tujuan = "-";
$volume_in = $volume;
$volume_out = 0;
$created_at = date('Y-m-d H:i:s');
$aktif = 'tidak';
$keterangan = '-';
//ambil value obat
$h3 = $db->query("SELECT * FROM gobat WHERE id_obat='" . $id_obat . "'");
$r3 = $h3->fetch(PDO::FETCH_ASSOC);
$cek = $h3->rowCount();
if ($cek == 0) {
	header("location:tambah.php?id=$id_faktur&status=2");
} else {
	$id_obat = isset($r3['id_obat']) ? $r3['id_obat'] : '';
	$namaobat = isset($r3['nama']) ? $r3['nama'] : '';
	$volumelama = isset($r3["volume"]) ? $r3['volume'] : '';
	$hargalama = isset($r3["harga"]) ? $r3['harga'] : '';
	$total = $volumelama + $volume;
	$volume_sisa = $volumelama + $volume;
	$volume_kartu_awal = $volume;
	$volume_kartu_akhir = $volume;
	//hitung diskon
	if ($diskon > 0) {
		$hitungdiskon = ($harga * $diskon) / 100;
	} else {
		$hitungdiskon = 0;
	}
	$hargafinal = $harga - $hitungdiskon;
	$harga_beli = $hargafinal;
	//hitung harga final
	$totalharga = $volume * $harga_beli;
	if ($ppn == '10') {
		$hargappn = $totalharga * 0.1;
		$inc_ppn = $harga_beli * 0.1;
	} else if ($ppn == '11') {
		$hargappn = $totalharga * 0.11;
		$inc_ppn = $harga_beli * 0.11;
	} else if ($ppn == '12') {
		$hargappn = $totalharga * 0.12;
		$inc_ppn = $harga_beli * 0.12;
	} else {
		$hargappn = 0;
		$inc_ppn = 0;
	}
	$harga_ppn_inc = $harga_beli + $inc_ppn;
	$totalharga_ppn = $totalharga + $hargappn;
	$totalharga_jual = $totalharga_ppn + ($totalharga_ppn * 0.2);
	$harga_jual = ($harga_beli + $inc_ppn) + ($harga_beli + $inc_ppn) * 0.2;
	try {
		$db->beginTransaction();
		//update
		// $result = $db->query("UPDATE gobat SET volume='$total',harga='$hargappn',hargalama='$hargalama',nobatch='$nobatch',expired='$expired',terbaru='$hariini' WHERE id_obat='$id_obat'");
		//insert
		$result2 = $db->query("INSERT INTO itemfaktur(id_faktur,tanggal,namaobat,id_obat,volume,harga,diskon,ppn,ppn_text,total,harga_satuan,nobatch,expired,sumber,e_kat,merk,jenis,pabrikan) VALUES ('$id_faktur','$hariini','$namaobat','$id_obat','$volume','$harga','$diskon','$hargappn','$ppn','$totalharga_ppn','$harga_ppn_inc','$nobatch','$expired','$sumber_dana','$e_kat','$merk','$jenis','$pabrikan')");
		//db hasil
		$result3 = $db->prepare("INSERT INTO `kartu_stok_gobat`(`id_obat`, `id_faktur`,`e_kat`, `sumber_dana`,`merk`,`jenis`,`pabrikan`, `volume_kartu_awal`,`volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`,`ppn_tipe`, `harga_beli`, `harga_jual_non_tuslah`, `aktif`, `created_at`, `keterangan`,`mem_id`)VALUES (:id_obat,:id_faktur,:e_kat,:sumber,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:ppn_tipe,:harga_beli,:harga_jual,:aktif,:created_at,:keterangan,:mem_id)");
		$result3->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$result3->bindParam(":id_faktur", $id_faktur, PDO::PARAM_INT);
		$result3->bindParam(":e_kat", $e_kat, PDO::PARAM_STR);
		$result3->bindParam(":sumber", $sumber_dana, PDO::PARAM_STR);
		$result3->bindParam(":merk", $merk, PDO::PARAM_STR);
		$result3->bindParam(":jenis", $jenis, PDO::PARAM_STR);
		$result3->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
		$result3->bindParam(":volume_kartu_awal", $volume_kartu_awal, PDO::PARAM_INT);
		$result3->bindParam(":volume_kartu_akhir", $volume_kartu_akhir, PDO::PARAM_INT);
		$result3->bindParam(":volume_sisa", $volume_sisa, PDO::PARAM_INT);
		$result3->bindParam(":in_out", $in_out, PDO::PARAM_STR);
		$result3->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
		$result3->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
		$result3->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
		$result3->bindParam(":expired", $expired_stok, PDO::PARAM_STR);
		$result3->bindParam(":no_batch", $nobatch, PDO::PARAM_STR);
		$result3->bindParam(":ppn_tipe", $ppn, PDO::PARAM_STR);
		$result3->bindParam(":harga_beli", $harga_ppn_inc);
		$result3->bindParam(":harga_jual", $harga_jual);
		$result3->bindParam(":aktif", $aktif, PDO::PARAM_STR);
		$result3->bindParam(":created_at", $created_at, PDO::PARAM_STR);
		$result3->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$result3->bindParam(":mem_id", $mem_id, PDO::PARAM_STR);
		$result3->execute();
		$db->commit();
		// action
		if ($result3) {
			echo "<script language=\"JavaScript\">window.location = \"tambah.php?id=" . $id_faktur . "&sumber=" . $sumber_dana . "\"</script>";
		} else {
			echo "gagal";
		}
	} catch (PDOException $e) {
		$db->rollBack();
		echo "Fail to update barang masuk : Fail ON (" . $e->getMessage() . ")";
	}
}
