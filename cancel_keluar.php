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
//get var
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$del_parent = $db->query("DELETE FROM obatkeluar_parent WHERE id_obatkeluar_parent='".$id_parent."'");

echo "<script language=\"JavaScript\">window.location = \"list_keluar_draft.php?status=3\"</script>";
?>
