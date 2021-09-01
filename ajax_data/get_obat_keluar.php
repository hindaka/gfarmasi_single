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
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
// $h4 = $db->query("SELECT k.id_kartu,k.id_obat,g.nama,k.sumber_dana,k.merk,k.no_batch,k.expired,k.volume_kartu_akhir,k.jenis,k.pabrikan,g.flag_single_id,k.harga_beli FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.volume_kartu_akhir>0 AND k.in_out='masuk' AND g.nama LIKE '%".$keyword."%' ORDER BY k.id_obat,k.expired ASC");
// $h4 = $db->query("SELECT k.id_obat,g.nama,SUM(k.volume_kartu_akhir) as stok,k.no_batch,k.expired,k.sumber_dana,k.jenis, CASE WHEN(k.jenis='generik') THEN k.pabrikan WHEN(k.jenis='non generik') THEN k.merk ELSE k.merk END AS 'merk_pabrik' FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.volume_kartu_akhir>0 AND k.in_out='masuk' AND g.nama LIKE '%".$keyword."%' GROUP BY k.id_obat,k.jenis,merk_pabrik ORDER BY g.nama ASC");
$h4 = $db->query("SELECT a.id_obat,a.nama,SUM(a.volume_kartu_akhir) as stok,a.no_batch,a.expired,a.sumber_dana,a.jenis, a.merk_pabrik FROM (SELECT k.id_obat,g.nama,k.volume_kartu_akhir,k.no_batch,k.expired,k.sumber_dana,k.jenis, CASE WHEN(k.jenis='generik') THEN k.pabrikan WHEN(k.jenis='non generik') THEN k.merk ELSE k.merk END AS 'merk_pabrik' FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.volume_kartu_akhir>0 AND k.in_out='masuk') as a WHERE a.nama LIKE '%".$keyword."%' OR a.merk_pabrik LIKE '%".$keyword."%' GROUP BY a.id_obat,a.jenis,a.merk_pabrik ORDER BY a.nama ASC");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
$total_data = $h4->rowCount();
$groups = [];
if ($total_data > 0) {
    foreach ($data4 as $row) {
        $id = $row['id_obat']."|".$row['jenis']."|".$row['merk_pabrik'];
        $item = [
            "id" => $id,
            "id_obat" => $row['id_obat'],
            "nama_obat" => $row['nama'],
            "sumber_dana" => $row['sumber_dana'],
            "jenis" => $row['jenis'],
            "merk_pabrik" => $row['merk_pabrik'],
            "no_batch" => $row['no_batch'],
            "expired" => $row['expired'],
            "volume_kartu_akhir" => $row['stok']
        ];
        array_push($groups, $item);
    }
}
$feedback = [
    "total_count" => $total_data,
    "incomplete_results" => false,
    "items" => $groups
];
echo json_encode($feedback);
