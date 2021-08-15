<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors','1');
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
$id_warehouse = isset($_GET['warehouse']) ? $_GET['warehouse'] : '';
$id_obat = isset($_GET['o']) ? $_GET['o'] : '';
$today = isset($_GET['today']) ? $_GET['today'] : '';
$links="master_obat_single.php?status=3";
//check data
$get_check = $db->query("SELECT * FROM temp_stok_awal WHERE id_warehouse='".$id_warehouse."' AND sync='n' AND created_at LIKE '%".$today."%'");
$check = $get_check->rowCount();
if($check>0){
	$del_all = $db->query("DELETE FROM temp_stok_awal WHERE id_warehouse='".$id_warehouse."' AND sync='n' AND created_at LIKE '%".$today."%'");
	echo "<script language=\"JavaScript\">window.location = \"".$links."\"</script>";
}else{
	echo "<script language=\"JavaScript\">window.location = \"".$links."\"</script>";
}
?>
