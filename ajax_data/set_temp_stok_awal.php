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
$harga_ppn = isset($_POST['harga_ppn']) ? $_POST['harga_ppn'] : '';
$no_batch = isset($_POST['nobatch']) ? $_POST['nobatch'] : '';
$expired = isset($_POST['expired']) ? $_POST['expired'] : '';
$sumber_dana = isset($_POST['sumber_dana']) ? $_POST['sumber_dana'] : '';
$merk = isset($_POST['merk']) ? $_POST['merk'] : '';
$date = explode("/",$expired);
$new_date = $date[2]."-".$date[1]."-".$date[0];
$tuslah = isset($_POST['tuslah']) ? $_POST['tuslah'] : '';
$alasan = isset($_POST['alasan']) ? $_POST['alasan'] : '';
$today = date('Y-m-d');

try {
  // check
  $get_check = $db->query("SELECT COUNT(*) as total_data FROM temp_stok_awal WHERE created_at LIKE '%.$today.%' AND id_warehouse='".$id_warehouse."' AND id_obat='".$id_obat."' AND sync='n'");
  $check = $get_check->fetch(PDO::FETCH_ASSOC);
  $total_data = isset($check['total_data']) ? $check['total_data'] : 0;
  if($total_data>0){
      $feedback=[
          "status"=>400,
          "title"=>"Peringatan",
          "msg"=>"Silakan hapus data yang sudah ada didalam tabel terlebih dahulu",
          "icon"=>"warning"
      ];
  }else{
    $ins= $db->prepare("INSERT INTO `temp_stok_awal`(`id_obat`, `id_warehouse`,`volume`,`harga_beli`, `no_batch`, `expired`,`merk`,`sumber_dana`, `tuslah`,`alasan`) VALUES(:id_obat,:id_warehouse,:volume,:harga_ppn,:no_batch,:expired,:merk,:sumber_dana,:tuslah,:alasan)");
    $ins->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
    $ins->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
    $ins->bindParam(":volume",$volume,PDO::PARAM_INT);
    $ins->bindParam(":harga_ppn",$harga_ppn,PDO::PARAM_INT);
    $ins->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
    $ins->bindParam(":expired",$new_date,PDO::PARAM_STR);
    $ins->bindParam(":merk",$merk,PDO::PARAM_STR);
    $ins->bindParam(":sumber_dana",$sumber_dana,PDO::PARAM_STR);
    $ins->bindParam(":tuslah",$tuslah,PDO::PARAM_INT);
    $ins->bindParam(":alasan",$alasan,PDO::PARAM_STR);
    $ins->execute();
    $feedback=[
        "status"=>200,
        "title"=>"Berhasil!",
        "msg"=>"Data Stok Awal Berhasil disimpan",
        "icon"=>"success"
    ];
  }
} catch (PDOException $e) {
    $feedback=[
        "status"=>400,
        "title"=>"Error",
        "msg"=>$e->getMessage(),
        "icon"=>"error"
    ];
}
echo json_encode($feedback);
