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
try {
    $db->beginTransaction();
    //read all sisa stok
    $all_stok = $db->query("SELECT k.id_obat,SUM(k.volume_kartu_akhir) as sisa_stok,k.no_batch,k.expired,k.harga_beli,k.harga_jual_non_tuslah,k.sumber_dana FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.in_out='masuk' AND k.volume_kartu_akhir>0 AND g.flag_single_id='old' AND g.jenis!='bhp' GROUP BY k.id_obat,k.no_batch,k.expired,k.harga_beli ORDER BY k.id_obat ASC");
    $all = $all_stok->fetchAll(PDO::FETCH_ASSOC);
    $total_data_sisa = $all_stok->rowCount();
    // echo '<pre>' . print_r($all, 1) . '</pre>';
    $total_match_data = 0;
    $total_new_data = 0;
    $terdaftar = 0;
    $total_data_update = 0;
    //find data id_obat_lama yang tersimpan pada kartu_stok_gobat & migrasi_data
    // $migrasi_data = $db->prepare("SELECT id_migrasi,id_obat,id_obat_lama,vol FROM migrasi_obat WHERE id_obat_lama=:id_obat_lama");
    $migrasi_data = $db->prepare("SELECT * FROM migrasi_obat WHERE id_obat_lama=:id_obat_lama AND no_batch=:no_batch AND expired=:expired AND harga_beli=:harga_beli");
    $check_jenis = $db->prepare("SELECT * FROM migrasi_obat WHERE id_obat_lama=:id_obat_lama GROUP BY id_obat_lama");
    foreach ($all as $row) {
        //set variable
        $id_obat_lama = isset($row['id_obat']) ? $row['id_obat'] : 0;
        $sisa_stok = isset($row['sisa_stok']) ? $row['sisa_stok'] : 0;
        $no_batch_baru = isset($row['no_batch']) ? $row['no_batch'] : 0;
        $expired_baru = isset($row['expired']) ? $row['expired'] : 0;
        $sumber_dana_baru = isset($row['sumber_dana']) ? $row['sumber_dana'] : 0;
        $harga_beli_baru = isset($row['harga_beli']) ? $row['harga_beli'] : 0;
        $harga_jual_non_tuslah_baru = isset($row['harga_jual_non_tuslah']) ? $row['harga_jual_non_tuslah'] : 0;
        //set bind value
        $migrasi_data->bindParam(":id_obat_lama", $id_obat_lama, PDO::PARAM_INT);
        $migrasi_data->bindParam(":no_batch", $no_batch_baru, PDO::PARAM_STR);
        $migrasi_data->bindParam(":expired", $expired_baru, PDO::PARAM_STR);
        $migrasi_data->bindParam(":harga_beli", $harga_beli_baru, PDO::PARAM_STR);
        $migrasi_data->execute();
        $mg = $migrasi_data->fetch(PDO::FETCH_ASSOC);
        $total_found = $migrasi_data->rowCount();
        $vol_lama = isset($mg['vol']) ? $mg['vol'] : 0;
        $id_obat_single = isset($mg['id_obat']) ? $mg['id_obat'] : 0;
        $total_kartu_migrasi = 0;
        if ($total_found >= 1) {
            $total_match_data++;
            if ($vol_lama != $sisa_stok) {
                $total_data_update++;
                echo $id_obat_lama . '-' . $sisa_stok . '-' . $no_batch_baru . '-' . $total_found . '<pre>' . print_r($mg, 1) . '</pre>';
                // $update_migrasi = $db->query("UPDATE migrasi_obat SET vol='" . $sisa_stok . "' WHERE id_obat_lama='" . $id_obat_lama . "' AND no_batch='" . $no_batch_baru . "' AND expired='" . $expired_baru . "' AND harga_beli='" . $harga_beli_baru . "'");
                // $update_kartu = $db->query("UPDATE kartu_stok_gobat SET volume_kartu_akhir='" . $sisa_stok . "',volume_in='" . $sisa_stok . "',volume_kartu_awal='" . $sisa_stok . "' WHERE id_obat='" . $id_obat_single . "' AND no_batch='" . $no_batch_baru . "' AND expired='" . $expired_baru . "' AND harga_beli='" . $harga_beli_baru . "' AND keterangan LIKE '(Migrasi Single ID)'");
            } else {
                // sisa stok terakhir sama dengan saat migrasi
            }
            // echo $id_obat_lama . " - " . $total_found . " : sisa_stok : " . $sisa_stok . ", volume lama :" . $vol_lama . " HARUS UPDATE TABEL MIGRASI & KARTU STOK<br>";
            //jika data ditemukan sesuai dengan id, no batch, expired dan harga => replace volume dengan sisa volume terkini
            // //check kartu_stok_gobat
            // $check_kartu = $db->query("SELECT *  FROM `kartu_stok_gobat` k WHERE k.id_obat='" . $id_obat_single . "' AND k.no_batch='" . $no_batch_baru . "' AND k.harga_beli='" . $harga_beli_baru . "' AND k.expired='" . $expired_baru . "' AND k.`keterangan` LIKE '(Migrasi Single ID)'");
            // $total_kartu_migrasi = $check_kartu->rowCount();
            // echo " total kartu : " . $total_kartu_migrasi . "<br>";
            //update data migrasi dan kartu_stok_gobat


        } else {
            //jika data tidak ditemukan insert data baru tersebut kedalam tabel migrasi dan kartu_stok_gobat
            $check_jenis->bindParam(":id_obat_lama", $id_obat_lama, PDO::PARAM_INT);
            $check_jenis->execute();
            $gj = $check_jenis->fetch(PDO::FETCH_ASSOC);
            $id_obat_new = isset($gj['id_obat']) ? $gj['id_obat'] : 0;
            $jenis = isset($gj['jenis']) ? $gj['jenis'] : '';
            $merk = isset($gj['merk']) ? $gj['merk'] : '';
            $pabrikan = isset($gj['pabrikan']) ? $gj['pabrikan'] : '';
            $vol_ref = isset($gj['vol']) ? $gj['vol'] : '';
            if ($check_jenis->rowCount() >= 1) {
                //old-data
                echo $id_obat_lama . "-" . $no_batch_baru . '-' . $sisa_stok . '-' . $harga_beli_baru . ' (' . $expired_baru . ')' . $check_jenis->rowCount() . "<br>";
                echo '<pre>' . print_r($gj, 1) . '</pre>';
                $ins_kartu = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`) VALUES (:id_obat,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol)");
                $ins_kartu->bindParam(":id_obat", $id_obat_new);
                $ins_kartu->bindParam(":id_obat_lama", $id_obat_lama);
                $ins_kartu->bindParam(":jenis", $jenis);
                $ins_kartu->bindParam(":merk", $merk);
                $ins_kartu->bindParam(":pabrikan", $pabrikan);
                $ins_kartu->bindParam(":sumber_dana", $sumber_dana_baru);
                $ins_kartu->bindParam(":harga_beli", $harga_beli_baru);
                $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah_baru);
                $ins_kartu->bindParam(":no_batch", $no_batch_baru);
                $ins_kartu->bindParam(":expired", $expired_baru);
                $ins_kartu->bindParam(":vol", $sisa_stok);
                // $ins_kartu->execute();
            } else {
                // new_data cannot be eksekusi karena belum dimapping
                $total_new_data++;
                echo "new " . $id_obat_lama . "-" . $no_batch_baru . '-' . $sisa_stok . '-' . $check_jenis->rowCount() . "<br>";
                echo '<pre>' . print_r($gj, 1) . '</pre>';
                $ins_kartu = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`) VALUES (:id_obat,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol)");
                $ins_kartu->bindParam(":id_obat", $id_obat_new);
                $ins_kartu->bindParam(":id_obat_lama", $id_obat_lama);
                $ins_kartu->bindParam(":jenis", $jenis);
                $ins_kartu->bindParam(":merk", $merk);
                $ins_kartu->bindParam(":pabrikan", $pabrikan);
                $ins_kartu->bindParam(":sumber_dana", $sumber_dana_baru);
                $ins_kartu->bindParam(":harga_beli", $harga_beli_baru);
                $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah_baru);
                $ins_kartu->bindParam(":no_batch", $no_batch_baru);
                $ins_kartu->bindParam(":expired", $expired_baru);
                $ins_kartu->bindParam(":vol", $sisa_stok);
                // $ins_kartu->execute();
            }

            // if ($check_jenis->rowCount() > 0) {
            //     echo $id_obat_lama . " - " . $check_jenis->rowCount() . " : sisa_stok : " . $sisa_stok . ", volume lama :" . $vol_ref;
            //     // $terdaftar++;
            //     // echo " total kartu : " . $total_kartu_migrasi . " || " . $jenis . " || " . $merk . " || " . $pabrikan . "<br>";
            //     // echo '<pre>' . print_r($gj, 1) . '</pre>';
            //     // insert into migrasi
            //     // insert into kartu
            //     $ins_kartu = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`) VALUES (:id_obat,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol)");
            //     $ins_kartu->bindParam(":id_obat", $id_obat_new);
            //     $ins_kartu->bindParam(":id_obat_lama", $id_obat_lama);
            //     $ins_kartu->bindParam(":jenis", $jenis);
            //     $ins_kartu->bindParam(":merk", $merk);
            //     $ins_kartu->bindParam(":pabrikan", $pabrikan);
            //     $ins_kartu->bindParam(":sumber_dana", $sumber_dana_baru);
            //     $ins_kartu->bindParam(":harga_beli", $harga_beli_baru);
            //     $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah_baru);
            //     $ins_kartu->bindParam(":no_batch", $no_batch_baru);
            //     $ins_kartu->bindParam(":expired", $expired_baru);
            //     $ins_kartu->bindParam(":vol", $sisa_stok);
            //     // $ins_kartu->execute();
            //     echo "<br>";
            // } else {
            //     echo $id_obat_lama . " - " . $check_jenis->rowCount() . " : sisa_stok : " . $sisa_stok . ", volume lama :" . $vol_lama;
            //     // data kartu tidak terdaftar dan ter mapping
            //     echo "<br>";
            //     $ins_kartu = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`) VALUES (:id_obat,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol)");
            //     $ins_kartu->bindParam(":id_obat", $id_obat_new);
            //     $ins_kartu->bindParam(":id_obat_lama", $id_obat_lama);
            //     $ins_kartu->bindParam(":jenis", $jenis);
            //     $ins_kartu->bindParam(":merk", $merk);
            //     $ins_kartu->bindParam(":pabrikan", $pabrikan);
            //     $ins_kartu->bindParam(":sumber_dana", $sumber_dana_baru);
            //     $ins_kartu->bindParam(":harga_beli", $harga_beli_baru);
            //     $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah_baru);
            //     $ins_kartu->bindParam(":no_batch", $no_batch_baru);
            //     $ins_kartu->bindParam(":expired", $expired_baru);
            //     $ins_kartu->bindParam(":vol", $sisa_stok);
            // $ins_kartu->execute();
            // }

        }
    }
    $db->commit();
    echo 'All Data Sisa Stok : ' . $total_data_sisa . "<br>";
    // echo 'Id obat lama terdaftar : ' . $terdaftar . "<br>";
    echo "Match Data : " . $total_match_data . "<br>";
    echo "Data yang harus diupdate : " . $total_data_update . "<br>";
    echo "New Data : " . $total_new_data . "<br>";
} catch (PDOException $th) {
    $db->rollBack();
    echo $th->getMessage() . "<br>";
    echo $th->getLine();
}
