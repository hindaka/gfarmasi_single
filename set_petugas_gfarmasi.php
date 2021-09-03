<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-',$tipe);
if ($tipes[0]!='Gfarmasi')
{
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
$check_all = isset($_POST['check_data']) ? $_POST['check_data'] : '';
$instalasi = "GFARMASI";
$split_data = explode(',',$check_all);
if(is_array($split_data)){
		$total_data = count($split_data);
		for ($i=0; $i < $total_data ; $i++) {
			// check petugas sudah terdaftar belum
			$check_petugas = $db->query("SELECT * FROM petugas WHERE id_pegawai='".$split_data[$i]."' AND instalasi='".$instalasi."'");
			$check = $check_petugas->rowCount();
			if($check > 0){
					//skip
			}else{
				$execute = $db->query("INSERT INTO `petugas`(`id_pegawai`, `instalasi`) VALUES ('".$split_data[$i]."','".$instalasi."')");
			}
		}
		echo json_encode("Data Pegawai Berhasil didaftarkan sebagai Petugas Gudang farmasi");
}else{
		echo json_encode("Pengaturan Data Petugas Gudang Farmasi Gagal dilakukan!");
}
