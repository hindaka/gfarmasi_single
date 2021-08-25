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
$id_faktur = isset($_POST['id_faktur']) ? $_POST['id_faktur'] : '';
$stmt = $db->query("SELECT * FROM itemfaktur WHERE id_faktur='".$id_faktur."'");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($data);