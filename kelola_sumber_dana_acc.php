<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set('Asia/Jakarta');
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
$nama_sumber = isset($_POST['nama_sumber']) ? $_POST['nama_sumber'] : '';
$policy_stat = 1;
$delete_stat = 1;
//check data
$check_data = $db->query("SELECT COUNT(*) as total_data FROM `kelola_sumber_dana` WHERE nama_sumber LIKE '%".$nama_sumber."%' AND delete_stat='1'");
$check = $check_data->fetch(PDO::FETCH_ASSOC);
$total_data = isset($check['total_data']) ? $check['total_data'] : 0;
if($total_data==0){
    $stmt = $db->prepare("INSERT INTO `kelola_sumber_dana`(`nama_sumber`, `policy_stat`, `delete_stat`) VALUES (:nama_sumber,:policy_stat,:delete_stat)");
    $stmt->bindParam(":nama_sumber",$nama_sumber,PDO::PARAM_STR);
    $stmt->bindParam(":policy_stat",$policy_stat,PDO::PARAM_INT);
    $stmt->bindParam(":delete_stat",$delete_stat,PDO::PARAM_INT);
    $stmt->execute();
}
header("location: kelola_sumber_dana.php?status=1");