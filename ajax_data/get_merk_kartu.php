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
$q = isset($_GET['q']) ? $_GET['q'] : '';
$id_obat = isset($_GET['id_obat']) ? $_GET['id_obat'] : '';

$stmt = $db->query("SELECT id_obat,merk FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND merk LIKE '%" . $q . "%' GROUP BY merk");
$data4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_data = $stmt->rowCount();
$groups = [];
if ($total_data > 0) {
    foreach ($data4 as $row) {
        $id = $row['merk'];
        $item = [
            "id" => $id,
            "id_obat" => $row['id_obat'],
            "flag"=>"old",
            "nama" => $row['merk']
        ];
        array_push($groups, $item);
    }
} else {
    $id = trim($q);
    $nama = trim($q);
    $item = [
        "id" => $id,
        "id_obat" => $id_obat,
        "flag"=>"new",
        "nama" => $nama
    ];
    array_push($groups, $item);
}
$feedback = [
    "total_count" => $total_data,
    "incomplete_results" => false,
    "items" => $groups
];
echo json_encode($feedback);
