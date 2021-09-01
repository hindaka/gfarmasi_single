<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
ini_set('display_errors', '1');
date_default_timezone_set('Asia/Jakarta');
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
$selectedItem = isset($_POST['id']) ? $_POST['id'] : '';
$split = explode("|", $selectedItem);
$id_obat = isset($split[0]) ? $split[0] : 0;
$jenis = isset($split[1]) ? $split[1] : 1;
$merk_pabrik = isset($split[2]) ? $split[2] : 2;

$feedback = [];
try {
    if($jenis=='generik'){
        $flag = "pabrikan='".$merk_pabrik."'";
    }else if($jenis=='non generik'){
        $flag = "merk='".$merk_pabrik."'";
    }else{
        $flag = "merk='".$merk_pabrik."'";
    }
    $stmt = $db->query("SELECT SUM(volume_kartu_akhir) as stok FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND jenis='".$jenis."' AND $flag GROUP BY id_obat");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $stok = isset($data['stok']) ? $data['stok'] : 0;
    $feedback = [
        "status" => 200,
        "stok" => $stok
    ];
} catch (PDOException $e) {
    $feedback = [
        "status" => 400,
        "stok" => $stok,
        "msg" => $e->getMessage()
    ];
}
echo json_encode($feedback);
