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
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
try {
    $data_obat = $db->query("SELECT * FROM gobat WHERE id_obat='" . $id_obat . "'");
    $obat = $data_obat->fetch(PDO::FETCH_ASSOC);
    $data = array(
        'hargalama' => $obat['hargalama'],
        'satuan' => $obat['satuan'],
        'harga_baru' => $obat['harga'],
        'merk' => $obat['merk']
    );
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(0);
}
