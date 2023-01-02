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
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : 0;
$filter = isset($_POST['jenis']) ? $_POST['jenis'] : '';
//get_parent
$parent = $db->query("SELECT nama FROM gobat WHERE id_obat='" . $id_obat . "'");
$p = $parent->fetch(PDO::FETCH_ASSOC);
$nama_baru = isset($p['nama']) ? $p['nama'] : '';
$ref = isset($_POST['ref']) ? $_POST['ref'] : 0;
$split = explode(";", $ref);
$new_arr = array_unique($split);
$group_list = [];
foreach ($new_arr as $g) {
    $group_list[] = $g;
}
$total_id_lama = count($group_list);
$table = '
<form action="migrasi_data_fix.php" method="POST">
<table class="table">
    <thead>
        <tr>
            <th>Nama Baru</th>
            <th>Nama Lama</th>
            <th>No Batch</th>
            <th>Expired</th>
            <th>Sumber</th>
            <th>Jenis</th>
            <th>Merk</th>
            <th>Pabrikan</th>
            <th>Stok</th>
            <th>HNA+PPN</th>
        </tr>
    </thead>
    <tbody>';
$total_item = 0;
// echo $ref;
if ($total_id_lama == 0) {
} else if ($total_id_lama == 1) {
    $stmt = $db->query("SELECT g.nama,k.no_batch,k.expired,k.sumber_dana,SUM(k.volume_kartu_akhir) as stok,k.harga_beli,k.harga_jual_non_tuslah FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.id_obat='" . $group_list[0] . "' AND k.in_out='masuk' AND k.volume_kartu_akhir>0 GROUP BY k.id_obat,k.no_batch,k.harga_beli ORDER BY expired");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as $row) {
        $table .= '<tr>
        <td>' . $nama_baru . '</td>
        <td>' . $row['nama'] . '(' . $group_list[0] . ')</td>
        <td>
            <input type="hidden" name="id_obat" id="id_obat" value="' . $id_obat . '">
            <input type="hidden" name="id_obat_lama[]" id="id_obat_lama" value="' . $group_list[0] . '">
            <input type="hidden" name="filter" id="filter" value="' . $filter . '">
            <input type="hidden" name="total_data" id="total_data" value="1">
            <input type="text" name="no_batch[]" id="no_batch" value="' . $row['no_batch'] . '" size="5">
        </td>
        <td><input type="date" name="expired[]" id="expired" value="' . $row['expired'] . '" size="5"></td>
        <td><input type="text" name="sumber_dana[]" id="sumber_dana" value="' . $row['sumber_dana'] . '" size="5"></td>
        <td>
            <select id="jenis" name="jenis[]">
                <option value="">--Pilih Jenis--</option>
                <option value="generik">Generik</option>
                <option value="non generik">Non Generik</option>
                <option value="bmhp">BMHP</option>
            </select>
        </td>
        <td><input type="text" name="merk[]" id="merk" size="5"></td>
        <td><input type="text" name="pabrikan[]" id="pabrikan" size="5"></td>
        <td><input type="number" name="vol[]" id="vol" value="' . $row['stok'] . '" size="5"></td>
        <td><input type="text" name="hna[]" id="hna" value="' . $row['harga_beli'] . '" size="5">
        <input type="hidden" name="hna_jual[]" id="hna_jual" value="' . $row['harga_jual_non_tuslah'] . '" size="5"></td>
        </tr>';
    }
    $table .= '<tr>
        <td colspan="9">-</td>
        <td><input type="submit" name="simpan" value="Simpan" class="btn btn-md btn-primary"></td>
    </tr>';
    $total_item++;
} else {
    for ($i = 0; $i < $total_id_lama; $i++) {
        $stmt = $db->query("SELECT g.nama,k.no_batch,k.expired,k.sumber_dana,SUM(k.volume_kartu_akhir) as stok,k.harga_beli,k.harga_jual_non_tuslah FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.id_obat='" . $group_list[$i] . "' AND k.in_out='masuk' AND k.volume_kartu_akhir>0 GROUP BY k.id_obat,k.no_batch,k.harga_beli ORDER BY expired");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $row) {
            $table .= '<tr>
            <td>' . $nama_baru . '</td>
        <td>' . $row['nama'] . '(' . $group_list[$i] . ')</td>
        <td>
            <input type="hidden" name="id_obat" id="id_obat" value="' . $id_obat . '">
            <input type="hidden" name="id_obat_lama[]" id="id_obat_lama" value="' . $group_list[$i] . '">
            <input type="hidden" name="filter" id="filter" value="' . $filter . '">
            <input type="hidden" name="total_data" id="total_data" value="2">
            <input type="text" name="no_batch[]" id="no_batch" value="' . $row['no_batch'] . '" size="5">
        </td>
        <td><input type="date" name="expired[]" id="expired" value="' . $row['expired'] . '" size="5"></td>
        <td><input type="text" name="sumber_dana[]" id="sumber_dana" value="' . $row['sumber_dana'] . '" size="5"></td>
        <td>
            <select id="jenis" name="jenis[]">
                <option value="">--Pilih Jenis--</option>
                <option value="generik">Generik</option>
                <option value="non generik">Non Generik</option>
                <option value="bmhp">BMHP</option>
            </select>
        </td>
        <td><input type="text" name="merk[]" id="merk" size="5"></td>
        <td><input type="text" name="pabrikan[]" id="pabrikan" size="5"></td>
        <td><input type="number" name="vol[]" id="vol" value="' . $row['stok'] . '" size="5"></td>
        <td>
        <input type="text" name="hna[]" id="hna" value="' . $row['harga_beli'] . '" size="5">
        <input type="hidden" name="hna_jual[]" id="hna_jual" value="' . $row['harga_jual_non_tuslah'] . '" size="5">
        </td>
        </tr>';
        }
    }
    $table .= '<tr>
        <td colspan="9">-</td>
        <td><input type="submit" name="simpan" value="Simpan" class="btn btn-md btn-primary"></td>
    </tr>';
    $total_item++;
}

$table .= '</tbody>
</table></form>';
if ($total_item > 0) {
    $feedback = [
        "msg" => $table,
        "alert" => 0,
    ];
} else {
    $feedback = [
        "msg" => $table,
        "alert" => 1,
    ];
}
echo json_encode($feedback);
