<?php
//conn
session_start();
include("../inc/pdo.conf.php");
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
//get var
$id_obatkeluar = isset($_GET["id"]) ? $_GET['id'] : '';
$id_kartu = isset($_GET['kartu']) ? $_GET['kartu'] : '';
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$tipe = isset($_GET['type']) ? $_GET['type'] : '';
try {
	$db->beginTransaction();
	//get reference
	$reference = $db->query("SELECT id_obat,ref,volume_out FROM kartu_stok_gobat WHERE id_kartu='" . $id_kartu . "'");
	$ref = $reference->fetch(PDO::FETCH_ASSOC);
	$ref_id = isset($ref['ref']) ? $ref['ref'] : 0;
	$volume_return = isset($ref['volume_out']) ? $ref['volume_out'] : 0;
	// get sisa stok
	$get_sisa_stok = $db->query("SELECT volume_kartu_akhir FROM kartu_stok_gobat WHERE id_kartu='" . $ref_id . "' LIMIT 1");
	$sisa = $get_sisa_stok->fetch(PDO::FETCH_ASSOC);
	$sisa_stok = isset($sisa['volume_kartu_akhir']) ? $sisa['volume_kartu_akhir'] : 0;
	$return_stok = $sisa_stok + $volume_return;
	//update stok data
	$up_stok = $db->query("UPDATE kartu_stok_gobat SET volume_kartu_akhir=$return_stok WHERE id_kartu='" . $ref_id . "'");
	//delete data di kartu_stok_gobat & obatkeluar
	$del_kartu = $db->prepare("DELETE FROM kartu_stok_gobat WHERE id_kartu=:id_kartu");
	$del_kartu->bindParam(":id_kartu", $id_kartu, PDO::PARAM_INT);
	$del_kartu->execute();
	//delete data obat keluar
	$del_keluar = $db->prepare("DELETE FROM obatkeluar WHERE id_obatkeluar=:id_obatkeluar");
	$del_keluar->bindParam(":id_obatkeluar", $id_obatkeluar, PDO::PARAM_INT);
	$del_keluar->execute();
	$db->commit();
	echo "<script language=\"JavaScript\">window.location = \"keluar.php?parent=" . $id_parent . "&type=" . $tipe . "&status=2\"</script>";
} catch (PDOException $e) {
	$db->rollBack();
	echo "Fail to delete data : Fail ON(" . $e->getMessage() . ")";
}
