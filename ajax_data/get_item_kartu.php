<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
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
	header("location:../../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$id_kartu = isset($_POST['id_kartu']) ? $_POST['id_kartu'] : 0;
try {
    $stmt = $db->prepare("SELECT * from kartu_stok_gobat WHERE id_kartu=:id");
    $stmt->bindParam(":id",$id_kartu,PDO::PARAM_INT);
    $stmt->execute();
    $kartu = $stmt->fetch(PDO::FETCH_ASSOC);
    $feedback=[
        "status"=>200,
        "title"=>"Berhasil",
        "msg"=>"Data Berhasil ditemukan",
        "icon"=>"success",
        "data"=>$kartu
    ];
} catch (PDOException $e) {
    $feedback=[
        "status"=>400,
        "title"=>"Galat",
        "msg"=>$e->getMessage(),
        "icon"=>"error",
        "data"=>[]
    ];
}
echo json_encode($feedback);