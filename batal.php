<?php
//conn
session_start();
include("../inc/pdo.conf.php");
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
//var
$id_faktur=$_GET["faktur"];
//delete
$result2 = $db->query("DELETE FROM itemfaktur WHERE id_faktur='".$id_faktur."'");
$result = $db->query("DELETE FROM faktur WHERE id_faktur='".$id_faktur."'");
//action
if ($result) {
echo "<script language=\"JavaScript\">window.location = \"masuk.php?status=1\"</script>";
} else {
echo "gagal";
}
