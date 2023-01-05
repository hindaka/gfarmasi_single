<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$data_warehouse = $db->query("SELECT ws.id_obat,SUM(ws.stok) as total_stok,g.nama,g.jenis,g.sumber,g.flag_single_id FROM `warehouse_stok` ws INNER JOIN gobat g ON(g.id_obat=ws.id_obat) GROUP BY id_obat");
$ware = $data_warehouse->fetchAll(PDO::FETCH_ASSOC);
$all_data['data'] = array();
foreach ($ware as $w) {
  $item = array(
    "id_obat" => $w['id_obat'],
    "nama" => $w['nama'],
    "jenis" => $w['jenis'],
    "sumber" => $w['sumber'],
    "stok" => $w['total_stok'],
    "flag_single_id" => $w['flag_single_id'],
  );
  array_push($all_data['data'], $item);
}
echo json_encode($all_data);
