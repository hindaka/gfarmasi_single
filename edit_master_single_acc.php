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
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
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

$stmt = $db->prepare("UPDATE gobat SET kategori=:kategori,kadar=:kadar,satuan_kadar=:satuan_kadar,satuan_jual=:satuan_jual,bentuk_sediaan=:bentuk_sediaan,kemasan=:kemasan,satuan=:satuan,fornas_app=:fornas_app,fornas=:fornas,keterangan=:keterangan WHERE id_obat=:id_obat");
$stmt->bindParam(":kategori", $kategori, PDO::PARAM_STR);
$stmt->bindParam(":kadar", $kadar, PDO::PARAM_STR);
$stmt->bindParam(":satuan_kadar", $satuan_kadar, PDO::PARAM_STR);
$stmt->bindParam(":bentuk_sediaan", $bentuk_sediaan, PDO::PARAM_STR);
$stmt->bindParam(":satuan_jual", $satuan_jual, PDO::PARAM_STR);
$stmt->bindParam(":kemasan", $kemasan, PDO::PARAM_STR);
$stmt->bindParam(":satuan", $satuan_jual, PDO::PARAM_STR);
$stmt->bindParam(":fornas_app", $formularium, PDO::PARAM_STR);
$stmt->bindParam(":fornas", $fornas, PDO::PARAM_STR);
$stmt->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
$stmt->bindParam(":id_obat", $id_obat);
$stmt->execute();
if ($stmt) {
    header("location: master_obat_single.php?status=1");
} else {
    header("location: edit_master_single.php?status=3");
}
