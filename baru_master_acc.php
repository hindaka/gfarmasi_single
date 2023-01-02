<?php
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
// echo '<pre>' . print_r($_POST, 1) . '</pre>';
$namaobat = isset($_POST['namaobat']) ? $_POST['namaobat'] : '';
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$kadar = isset($_POST['kadar']) ? $_POST['kadar'] : '';
$satuan_kadar = isset($_POST['satuan_kadar']) ? $_POST['satuan_kadar'] : '';
$satuan_jual = isset($_POST['satuan_jual']) ? $_POST['satuan_jual'] : '';
$bentuk_sediaan = isset($_POST['bentuk_sediaan']) ? $_POST['bentuk_sediaan'] : '';
$kemasan = isset($_POST['kemasan']) ? $_POST['kemasan'] : '';
$spesifikasi = isset($_POST['spesifikasi']) ? $_POST['spesifikasi'] : '';
$fornas = isset($_POST['fornas']) ? $_POST['fornas'] : '';
$formularium = isset($_POST['formularium']) ? $_POST['formularium'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
$flag_single_id = "new";
$stmt = $db->prepare("INSERT INTO `gobat`(`kategori`, `nama`, `kadar`, `satuan_kadar`, `bentuk_sediaan`, `satuan_jual`, `kemasan`, `satuan`,`fornas_app`, `fornas`,`keterangan`,`flag_single_id`) VALUES (:kategori,:nama,:kadar,:satuan_kadar,:bentuk_sediaan,:satuan_jual,:kemasan,:satuan,:fornas_app,:fornas,:keterangan,:flag_single_id)");
$stmt->bindParam(":kategori", $kategori, PDO::PARAM_STR);
$stmt->bindParam(":nama", $namaobat, PDO::PARAM_STR);
$stmt->bindParam(":kadar", $kadar, PDO::PARAM_STR);
$stmt->bindParam(":satuan_kadar", $satuan_kadar, PDO::PARAM_STR);
$stmt->bindParam(":bentuk_sediaan", $bentuk_sediaan, PDO::PARAM_STR);
$stmt->bindParam(":satuan_jual", $satuan_jual, PDO::PARAM_STR);
$stmt->bindParam(":kemasan", $kemasan, PDO::PARAM_STR);
$stmt->bindParam(":satuan", $satuan_jual, PDO::PARAM_STR);
$stmt->bindParam(":fornas_app", $formularium, PDO::PARAM_STR);
$stmt->bindParam(":fornas", $fornas, PDO::PARAM_STR);
$stmt->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
$stmt->bindParam(":flag_single_id", $flag_single_id, PDO::PARAM_STR);
$stmt->execute();
if ($stmt) {
    header("location: master_obat_single.php");
} else {
    header("location: baru_master.php?status=3");
}
