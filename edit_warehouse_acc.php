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
$id_warehouse = isset($_POST['ware']) ? $_POST['ware'] : '';
$nama_ruang = isset($_POST['nama_ruang']) ? $_POST['nama_ruang'] : '';
$lokasi = isset($_POST['lokasi']) ? $_POST['lokasi'] : '';
$tipe_depo = isset($_POST['tipe_depo']) ? trim($_POST['tipe_depo']) : '';
try {
    $db->beginTransaction();
	if ($tipe_depo == 'depo_set') {
		$depo_set = 'y';
		$troli_set = 'n';
		$kit_set = 'n';
	} else if ($tipe_depo == 'trolly_emg') {
		$depo_set = 'n';
		$troli_set = 'y';
		$kit_set = 'n';
	} else if ($tipe_depo == 'kit_emg') {
		$depo_set = 'n';
		$troli_set = 'n';
		$kit_set = 'y';
	} else {
		$depo_set = 'n';
		$troli_set = 'n';
		$kit_set = 'n';
	}
	//check duplcate
	$check_duplicate = $db->prepare("SELECT COUNT(*) as total_data FROM warehouse WHERE nama_ruang=:nama_ruang AND id_warehouse<>:id_warehouse");
	$check_duplicate->bindParam(":nama_ruang", $nama_ruang, PDO::PARAM_STR);
	$check_duplicate->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
	$check_duplicate->execute();
	$check = $check_duplicate->fetch(PDO::FETCH_ASSOC);
	if ($check['total_data'] == 0) {
		$up = $db->prepare("UPDATE warehouse SET nama_ruang=:nama_ruang,lokasi=:lokasi,depo_set=:depo_set,trolly_emg=:trolly_emg,kit_emg=:kit_emg WHERE id_warehouse=:id");
		$up->bindParam(":nama_ruang", $nama_ruang, PDO::PARAM_STR);
		$up->bindParam(":lokasi", $lokasi, PDO::PARAM_STR);
		$up->bindParam(":depo_set", $depo_set, PDO::PARAM_STR);
		$up->bindParam(":trolly_emg", $troli_set, PDO::PARAM_STR);
		$up->bindParam(":kit_emg", $kit_set, PDO::PARAM_STR);
		$up->bindParam(":id", $id_warehouse, PDO::PARAM_INT);
		$up->execute();
		echo "<script language=\"JavaScript\">window.location = \"warehouse.php?status=2\"</script>";
	} else {
		echo "<script language=\"JavaScript\">window.location = \"warehouse.php?status=3\"</script>";
	}
    $db->commit();
} catch (PDOException $e) {
    $db->rollBack();
	echo "Error : " . $e->getMessage();
}
