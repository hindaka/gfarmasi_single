<?php
//conn
session_start();
include("../../inc/pdo.conf.php");
date_default_timezone_set("Asia/Jakarta");
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
$id_obat_new = isset($_POST['id_single']) ? $_POST['id_single'] : 0;
$id_obat_lama = isset($_POST['id_lama']) ? $_POST['id_lama'] : 0;
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : NULL;
$pabrikan = isset($_POST['pabrikan']) ? $_POST['pabrikan'] : NULL;
$merk = isset($_POST['merk']) ? $_POST['merk'] : NULL;
$no_batch = isset($_POST['no_batch']) ? $_POST['no_batch'] : NULL;
$expired = isset($_POST['expired']) ? $_POST['expired'] : NULL;
$vol = isset($_POST['vol']) ? $_POST['vol'] : 0;
$harga_beli = isset($_POST['harga_beli']) ? $_POST['harga_beli'] : 0;
$harga_jual = isset($_POST['harga_jual']) ? $_POST['harga_jual'] : 0;
$sumber_dana = isset($_POST['sumber_dana']) ? $_POST['sumber_dana'] : NULL;
$feedback = [];
try {
    $ins_kartu = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`) VALUES (:id_obat,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol)");
    $ins_kartu->bindParam(":id_obat", $id_obat_new);
    $ins_kartu->bindParam(":id_obat_lama", $id_obat_lama);
    $ins_kartu->bindParam(":jenis", $jenis);
    $ins_kartu->bindParam(":merk", $merk);
    $ins_kartu->bindParam(":pabrikan", $pabrikan);
    $ins_kartu->bindParam(":sumber_dana", $sumber_dana);
    $ins_kartu->bindParam(":harga_beli", $harga_beli);
    $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual);
    $ins_kartu->bindParam(":no_batch", $no_batch);
    $ins_kartu->bindParam(":expired", $expired);
    $ins_kartu->bindParam(":vol", $vol);
    $ins_kartu->execute();
    $feedback = [
        "code" => 200,
        "msg" => "Sukses"
    ];
} catch (PDOException $pd) {
    $feedback = [
        "code" => 201,
        "msg" => $pd->getMessage()
    ];
}
echo json_encode($feedback);
