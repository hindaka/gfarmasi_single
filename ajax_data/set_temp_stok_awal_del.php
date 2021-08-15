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
$id_temp= isset($_POST['id']) ? $_POST['id'] : '';
try {
	$check_del = $db->query("SELECT * FROM temp_stok_awal WHERE id_temp='".$id_temp."'");
	$check = $check_del->rowCount();
	if($check>0){
		$del = $db->query("DELETE FROM temp_stok_awal WHERE id_temp='".$id_temp."'");
		$feedback=[
            "status"=>200,
            "title"=>"Berhasil",
            "msg"=>'Data Sementara Stok Awal Berhasil dihapus',
            "icon"=>"success"
        ];
	}else{
		$feedback=[
            "status"=>400,
            "title"=>"Peringatan",
            "msg"=>'Data Sementara Stok Awal Gagal dihapus',
            "icon"=>"warning"
        ];
	}
} catch (PDOException $e) {
    $feedback=[
        "status"=>400,
        "title"=>"Peringatan",
        "msg"=>$e->getMessage(),
        "icon"=>"warning"
    ];
}
echo json_encode($feedback);