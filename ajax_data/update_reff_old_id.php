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
$reff_id = isset($_POST['reff_id']) ? $_POST['reff_id'] : '';
$reff_old_id = isset($_POST['reff_old_id']) ? $_POST['reff_old_id'] : '';
$feedback = [];
try {
    $up_data = $db->query("UPDATE gobat SET old_id_ref='" . $reff_old_id . "' WHERE id_obat='" . $reff_id . "'");
    $feedback = [
        "status" => 200,
        "title" => "Berhasil",
        "msg" => 'Data Referensi Obat Lama Berhasil disetting',
        "icon" => "success"
    ];
} catch (PDOException $e) {
    $feedback = [
        "status" => 400,
        "title" => "Peringatan",
        "msg" => $e->getMessage(),
        "icon" => "warning"
    ];
}
echo json_encode($feedback);
