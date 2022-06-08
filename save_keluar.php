<?php
//conn
session_start();
include("../inc/pdo.conf.php");
date_default_timezone_set("Asia/Bangkok");
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
//get var
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'draft';
$aktif = 'ya';
$keterangan = "-";
$created_at = date('Y-m-d H:i:s');
if ($mode == 'draft') {
    header("location: list_keluar_draft.php?status=4");
} else {
    try {
        $db->beginTransaction();
        //get list obatkeluar
        $list_obatkeluar = $db->query("SELECT ob.id_obatkeluar,ob.id_parent,ob.id_kartu,ob.id_obat,ob.id_warehouse,ob.sumber,k.merk,k.jenis,k.volume_out,k.expired,k.no_batch,k.harga_beli,k.harga_jual_non_tuslah,ob.id_tuslah,ob.ket_tuslah FROM obatkeluar ob INNER JOIN warehouse w ON(w.id_warehouse=ob.id_warehouse) INNER JOIN kartu_stok_gobat k ON(k.id_kartu=ob.id_kartu) WHERE ob.id_parent='" . $id_parent . "'");
        $list_keluar = $list_obatkeluar->fetchAll(PDO::FETCH_ASSOC);
        foreach ($list_keluar as $row) {
            $id_warehouse = isset($row['id_warehouse']) ? $row['id_warehouse'] : 0;
            $id_obat = isset($row['id_obat']) ? $row['id_obat'] : 0;
            $expired = isset($row['expired']) ? $row['expired'] : '';
            $no_batch = isset($row['no_batch']) ? $row['no_batch'] : '';
            //update sync
            $obat_keluar = $db->query("UPDATE obatkeluar SET sync='sudah' WHERE id_kartu='" . $row['id_kartu'] . "'");
            // check tersedia gak di warehouse stok
            $check_obat = $db->prepare("SELECT count(*) as total_data FROM warehouse_stok WHERE id_warehouse=:id_warehouse AND id_obat=:id_obat");
            $check_obat->bindParam(":id_warehouse", $id_warehouse);
            $check_obat->bindParam(":id_obat", $id_obat);
            $check_obat->execute();
            $item = $check_obat->fetch(PDO::FETCH_ASSOC);
            $total_data_warehouse = isset($item['total_data']) ? $item['total_data'] : 0;
            if ($total_data_warehouse > 0) {
                //ambil volume terakhir
                $get_w = $db->prepare("SELECT * FROM warehouse_stok WHERE id_warehouse=:id_warehouse AND id_obat=:id_obat");
                $get_w->bindParam(":id_warehouse", $id_warehouse);
                $get_w->bindParam(":id_obat", $id_obat);
                $get_w->execute();
                $w = $get_w->fetch(PDO::FETCH_ASSOC);
                $volume_warehouse = isset($w['stok']) ? $w['stok'] : 0;
                $id_warehouse_stok = isset($w['id_warehouse_stok']) ? $w['id_warehouse_stok'] : 0;
                $volume_out = isset($row['volume_out']) ? $row['volume_out'] : 0;
                $new_warehouse = $volume_warehouse + $volume_out;
                //update stok warehouse
                $warehouse_stok = $db->prepare("UPDATE warehouse_stok SET stok=:stok,expired=:expired,no_batch=:no_batch WHERE id_warehouse_stok=:id_warehouse_stok");
                $warehouse_stok->bindParam(":stok", $new_warehouse, PDO::PARAM_INT);
                $warehouse_stok->bindParam(":expired", $expired, PDO::PARAM_STR);
                $warehouse_stok->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
                $warehouse_stok->bindParam(":id_warehouse_stok", $id_warehouse_stok, PDO::PARAM_INT);
                $warehouse_stok->execute();
            } else {
                $volume_warehouse = 0;
                $volume_out = isset($row['volume_out']) ? $row['volume_out'] : 0;
                $new_warehouse = $volume_warehouse + $volume_out;
                //insert into warehouse stok
                $warehouse_stok = $db->prepare("INSERT INTO `warehouse_stok`(`id_warehouse`, `id_obat`, `stok`,`expired`,`no_batch`,`created_at`) VALUES (:id_warehouse,:id_obat,:stok,:expired,:no_batch,:created_at)");
                $warehouse_stok->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
                $warehouse_stok->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
                $warehouse_stok->bindParam(":stok", $new_warehouse, PDO::PARAM_INT);
                $warehouse_stok->bindParam(":expired", $expired, PDO::PARAM_STR);
                $warehouse_stok->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
                $warehouse_stok->bindParam(":created_at", $created_at, PDO::PARAM_STR);
                $warehouse_stok->execute();
            }
            $volume_in_ruangan = isset($row['volume_out']) ? $row['volume_out'] : 0;
            $volume_sisa = $new_warehouse;
            $in_out_ruangan = "masuk";
            $tujuan_ruangan = "-";
            $volume_out_ruangan = 0;
            $harga_beli = isset($row['harga_beli']) ? $row['harga_beli'] : 0;
            $harga_jual = isset($row['harga_jual_non_tuslah']) ? $row['harga_jual_non_tuslah'] : 0;
            $id_tuslah = isset($row['id_tuslah']) ? $row['id_tuslah'] : 0;
            $tuslah = isset($row['ket_tuslah']) ? $row['ket_tuslah'] : 0;
            $sumber = isset($row['sumber']) ? $row['sumber'] : '';
            $jenis = isset($row['jenis']) ? $row['jenis'] : '';
            $merk = isset($row['merk']) ? $row['merk'] : '';
            $pabrikan = isset($row['pabrikan']) ? $row['pabrikan'] : '';
            $expired = isset($row['expired']) ? $row['expired'] : '';
            $no_batch = isset($row['no_batch']) ? $row['no_batch'] : '';
            $id_kartu_gobat = isset($row['id_kartu']) ? $row['id_kartu'] : '';
            $keterangan_ruangan = "Barang dari gudang";
            // insert ke kartu_stok_ruangan + tuslah / id_tuslah
            $ins_ruangan = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`,`id_warehouse`, `sumber_dana`,`merk`,`jenis`,`pabrikan`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `created_at`, `keterangan`)VALUES (:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:created_at,:keterangan)");
            $ins_ruangan->bindParam(":id_kartu_gobat", $id_kartu_gobat, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":sumber_dana", $sumber, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":merk", $merk, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":jenis", $jenis, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":volume_kartu_awal", $volume_in_ruangan, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":volume_kartu_akhir", $volume_in_ruangan, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":volume_sisa", $volume_sisa, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":in_out", $in_out_ruangan, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":tujuan", $tujuan_ruangan, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":volume_in", $volume_in_ruangan, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":volume_out", $volume_out_ruangan, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":expired", $expired, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":harga_beli", $harga_beli, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":harga_jual", $harga_jual, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":id_tuslah", $id_tuslah, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":ket_tuslah", $tuslah, PDO::PARAM_INT);
            $ins_ruangan->bindParam(":created_at", $created_at_ruangan, PDO::PARAM_STR);
            $ins_ruangan->bindParam(":keterangan", $keterangan_ruangan, PDO::PARAM_STR);
            $ins_ruangan->execute();
            //update kartu_stok_gobat booked menjadi keluar
            $update_kartu = $db->query("UPDATE kartu_stok_gobat SET in_out='keluar' WHERE id_kartu='" . $id_kartu_gobat . "'");
            //update sisa stok
            $check_stok = $db->query("SELECT SUM(volume_kartu_akhir) as sisa FROM kartu_stok_gobat WHERE in_out='masuk' AND id_obat='" . $row['id_obat'] . "' AND volume_kartu_akhir>0");
            $stok = $check_stok->fetch(PDO::FETCH_ASSOC);
            $stok_sisa = isset($stok['sisa']) ? $stok['sisa'] : 0;
            //update gobat
            $up_gobat = $db->query("UPDATE gobat SET volume='" . $stok_sisa . "' WHERE id_obat='" . $id_obat . "'");
            //update as parent set posting
            $up_parent = $db->query("UPDATE obatkeluar_parent SET status_keluar='posting' WHERE id_obatkeluar_parent='" . $id_parent . "'");
        }
        $db->commit();
    } catch (PDOException $e) {
        echo $e->getMessage() . " getLine :" . $e->getLine();
        $db->rollBack();
    }
}
echo "<script language=\"JavaScript\">window.location = \"list_keluar_draft.php?status=1\"</script>";