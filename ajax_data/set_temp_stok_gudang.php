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
$id_warehouse = isset($_POST['id_warehouse']) ? $_POST['id_warehouse'] : '';
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$volume = isset($_POST['volume']) ? $_POST['volume'] : '';
$harga_beli = isset($_POST['harga_beli']) ? $_POST['harga_beli'] : '';
$no_batch = isset($_POST['nobatch']) ? $_POST['nobatch'] : '';
$expired = isset($_POST['expired']) ? $_POST['expired'] : '';
$merk = isset($_POST['merk']) ? $_POST['merk'] : '';
$sumber_dana = isset($_POST['sumber_dana']) ? $_POST['sumber_dana'] : '';
$date = explode("/",$expired);
$new_date = $date[2]."-".$date[1]."-".$date[0];
$today = date('Y-m-d');
$feedback=[];
try {
  // check
  $get_check = $db->query("SELECT COUNT(*) as total_data FROM temp_stok_awal WHERE created_at LIKE '%.$today.%' AND id_warehouse='".$id_warehouse."' AND id_obat='".$id_obat."' AND sync='n'");
  $check = $get_check->fetch(PDO::FETCH_ASSOC);
  $total_data = isset($check['total_data']) ? $check['total_data'] : '';
  if($total_data>0){
      $feedback=[
          "status"=>400,
          "title"=>"Peringatan",
          "msg"=>'Silakan hapus data yang sudah ada didalam tabel terlebih dahulu',
          "icon"=>"warning"
      ];
  }else{
    $ins= $db->prepare("INSERT INTO `temp_stok_awal`(`id_obat`, `id_warehouse`,`volume`,`harga_beli`, `no_batch`, `expired`,`merk`,`sumber_dana`) VALUES(:id_obat,:id_warehouse,:volume,:harga_beli,:no_batch,:expired,:merk,:sumber_dana)");
    $ins->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
    $ins->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
    $ins->bindParam(":volume",$volume,PDO::PARAM_INT);
    $ins->bindParam(":harga_beli",$harga_beli,PDO::PARAM_INT);
    $ins->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
    $ins->bindParam(":expired",$new_date,PDO::PARAM_STR);
    $ins->bindParam(":merk",$merk,PDO::PARAM_STR);
    $ins->bindParam(":sumber_dana",$sumber_dana,PDO::PARAM_STR);
    $ins->execute();
    $feedback=[
        "status"=>200,
        "title"=>"Berhasil",
        "msg"=>'Data Sementara Stok Awal Berhasil disimpan',
        "icon"=>"success"
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
