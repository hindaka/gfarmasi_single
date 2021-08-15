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
$id_item=isset($_GET["id"]) ? $_GET['id'] : '';
$id_faktur=isset($_GET["faktur"]) ? $_GET['faktur'] : '';
$sumber_dana = isset($_GET['sumber']) ? $_GET['sumber'] : '';

try {
	//ambil value obat
	$h2=$db->query("SELECT * FROM itemfaktur WHERE id_item='$id_item'");
	$r2=$h2->fetch(PDO::FETCH_ASSOC);
	$namaobat=$r2["namaobat"];
	$volume=$r2["volume"];
	$sumber=$r2["sumber"];
	//ambil value obat
	$h3=$db->query("SELECT * FROM gobat WHERE nama='$namaobat' AND sumber='$sumber'");
	$r3=$h3->fetch(PDO::FETCH_ASSOC);
	$volumelama=$r3["volume"];
	$id_obat = $r3['id_obat'];
	$total=$volumelama-$volume;
	//update
	// $result = $db->query("UPDATE gobat SET volume='$total' WHERE nama='$namaobat' AND sumber='$sumber'");
	//insert
	$result2 = $db->query("DELETE FROM itemfaktur WHERE id_item='$id_item'");
	//delete list item kartu_stok_gobat
	$kartu_stok = $db->prepare("DELETE FROM kartu_stok_gobat WHERE id_obat=:obat AND id_faktur=:faktur");
	$kartu_stok->bindParam(":obat",$id_obat,PDO::PARAM_INT);
	$kartu_stok->bindParam(":faktur",$id_faktur,PDO::PARAM_INT);
	$kartu_stok->execute();
	//action
	if ($kartu_stok) {
	echo "<script language=\"JavaScript\">window.location = \"tambah.php?id=".$id_faktur."&sumber=".$sumber_dana."\"</script>";
	} else {
	echo "gagal";
	}
} catch (PDOException $e) {
	echo "Fail to delete data : Fail ON(".$e->getMessage().")";
}
