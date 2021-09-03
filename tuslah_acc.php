<?php
session_start();
include("../inc/pdo.conf.php");
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

$today = date('Y-m-d H:i:s');
$rajal = isset($_POST['rajal']) ? $_POST['rajal'] : '';
$rajal_racik = isset($_POST['rajal_racik']) ? $_POST['rajal_racik'] : '';
$ranap = isset($_POST['ranap']) ? $_POST['ranap'] : '';
$ranap_racik = isset($_POST['ranap_racik']) ? $_POST['ranap_racik'] : '';
$ranap_presiden = isset($_POST['ranap_presiden']) ? $_POST['ranap_presiden'] : '';
$ranap_presiden_racik = isset($_POST['ranap_presiden_racik']) ? $_POST['ranap_presiden_racik'] : '';
$ranap_suju = isset($_POST['ranap_suju']) ? $_POST['ranap_suju'] : '';
$ranap_suju_racik = isset($_POST['ranap_suju_racik']) ? $_POST['ranap_suju_racik'] : '';
$ranap_vip = isset($_POST['ranap_vip']) ? $_POST['ranap_vip'] : '';
$ranap_vip_racik = isset($_POST['ranap_vip_racik']) ? $_POST['ranap_vip_racik'] : '';
$aktif = 'y';
try {
    //update all tuslah aktif menjadi n
    $update_all = $db->query("UPDATE tuslah SET aktif='n'");
    $ins_tuslah = $db->prepare("INSERT INTO `tuslah`(`rajal`, `rajal_racik`, `ranap`, `ranap_racik`,`ranap_presiden`,`ranap_racik_presiden`,`ranap_suju`,`ranap_racik_suju`,`ranap_vip`,`ranap_racik_vip`,`aktif`)VALUES (:rajal,:rajal_racik,:ranap,:ranap_racik,:ranap_presiden,:ranap_presiden_racik,:ranap_suju,:ranap_suju_racik,:ranap_vip,:ranap_vip_racik,:aktif)");
    $ins_tuslah->bindParam(":rajal", $rajal, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":rajal_racik", $rajal_racik, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap", $ranap, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_racik", $ranap_racik, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_presiden", $ranap_presiden, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_presiden_racik", $ranap_presiden_racik, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_suju", $ranap_suju, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_suju_racik", $ranap_suju_racik, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_vip", $ranap_vip, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":ranap_vip_racik", $ranap_vip_racik, PDO::PARAM_INT);
    $ins_tuslah->bindParam(":aktif", $aktif, PDO::PARAM_STR);
    $ins_tuslah->execute();
    echo "<script language=\"JavaScript\">window.location = \"tuslah.php?status=1\"</script>";
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}
