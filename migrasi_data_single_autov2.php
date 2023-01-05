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
$mem_id = $r1['mem_id'];
$total_migrasi_insert = 0;
$total_migrasi_update = 0;
$total_kartu_insert = 0;
$total_kartu_update = 0;
try {
    $db->beginTransaction();
    $group_id = $db->query("SELECT k.id_obat,g.nama,SUM(k.volume_kartu_akhir) as vol FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.created_at>='2022-12-25' AND k.in_out='masuk' AND g.flag_single_id='old' AND g.jenis!='bhp' GROUP BY k.id_obat ORDER BY k.id_obat ASC");
    $list_id_lama = $group_id->fetchAll(PDO::FETCH_ASSOC);
    $total_id_lama = $group_id->rowCount();
    // echo '<pre>'.print_r($list_id_lama,1).'</pre>';
    $master_migrasi = $db->prepare("SELECT id_obat as id_obat_single,id_obat_lama,jenis,merk,pabrikan FROM migrasi_obat GROUP BY id_obat_lama HAVING id_obat_lama =:id_obat_lama ORDER BY id_obat");
    $arr_not_found = [];
    $all_stok = $db->prepare("SELECT k.id_obat,SUM(k.volume_kartu_akhir) as sisa_stok,k.no_batch,k.expired,k.harga_beli,k.harga_jual_non_tuslah,k.sumber_dana,k.ppn_tipe FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.created_at>='2022-12-25' AND k.in_out='masuk' AND g.flag_single_id='old' AND g.jenis!='bhp' AND k.id_obat=:id_obat GROUP BY k.id_obat,k.no_batch,k.expired,k.harga_beli ORDER BY k.id_obat ASC");
    $total_data_sisa = $all_stok->rowCount();
    echo "Total ID Obat Lama to migration : " . $total_id_lama . "<br>";
    // echo "Total Data to migration : " . $total_data_sisa. "<br>";
    foreach ($list_id_lama as $lama) {
        $id_obat_lama = isset($lama['id_obat']) ? $lama['id_obat'] : 0;
        $namaobat = isset($lama['nama']) ? $lama['nama'] : 0;
        $stok_akhir = isset($lama['vol']) ? $lama['vol'] : 0;
        $all_stok->bindParam(":id_obat", $id_obat_lama);
        $all_stok->execute();
        $all = $all_stok->fetchAll(PDO::FETCH_ASSOC);
        $total_items = $all_stok->rowCount();
        // echo '<pre>' . print_r($all, 1) . '</pre>';
        //check in array migrasi to get jenis, merk,pabrikan
        $master_migrasi->bindParam("id_obat_lama", $id_obat_lama);
        $master_migrasi->execute();
        $found = $master_migrasi->rowCount();
        if ($found >= 1) {
            $migrasi_data = $master_migrasi->fetch(PDO::FETCH_ASSOC);
            $jenis = isset($migrasi_data['jenis']) ? $migrasi_data['jenis'] : NULL;
            $merk = isset($migrasi_data['merk']) ? $migrasi_data['merk'] : NULL;
            $pabrikan = isset($migrasi_data['pabrikan']) ? $migrasi_data['pabrikan'] : NULL;
            $id_obat_single = isset($migrasi_data['id_obat_single']) ? $migrasi_data['id_obat_single'] : NULL;
            $id_migrasi = isset($migrasi_data['id_migrasi']) ? $migrasi_data['id_migrasi'] : NULL;
            if ($total_items > 0) {
                foreach ($all as $sub) {
                    // echo '<pre>' . print_r($sub, 1) . '</pre>';
                    $sisa_stok = isset($sub['sisa_stok']) ? $sub['sisa_stok'] : 0;
                    $no_batch = isset($sub['no_batch']) ? $sub['no_batch'] : 0;
                    $expired = isset($sub['expired']) ? $sub['expired'] : 0;
                    $harga_beli = isset($sub['harga_beli']) ? $sub['harga_beli'] : 0;
                    $harga_jual_non_tuslah = isset($sub['harga_jual_non_tuslah']) ? $sub['harga_jual_non_tuslah'] : 0;
                    $sumber_dana = isset($sub['sumber_dana']) ? $sub['sumber_dana'] : 0;
                    $ppn_tipe = isset($sub['ppn_tipe']) ? $sub['ppn_tipe'] : 10;
                    //update table migrasi
                    //check dulu, terdaftar ngak
                    $check_migrasi = $db->query("SELECT * FROM migrasi_obat WHERE id_obat_lama='{$id_obat_lama}' AND jenis='{$jenis}' AND no_batch='{$no_batch}'");
                    $total_mig = $check_migrasi->rowCount();
                    if ($total_mig >= 1) {
                        echo $id_obat_lama . ' ' . $total_mig . ' found';
                        $up_migrasi = $db->prepare("UPDATE migrasi_obat SET vol=:vol WHERE id_obat_lama=:id_obat_lama AND jenis=:jenis AND merk=:merk AND pabrikan=:pabrikan AND no_batch=:no_batch AND expired=:expired AND harga_beli=:harga_beli");
                        $up_migrasi->bindParam(":vol", $sisa_stok);
                        $up_migrasi->bindParam(":id_obat_lama", $id_obat_lama);
                        $up_migrasi->bindParam(":jenis", $jenis);
                        $up_migrasi->bindParam(":merk", $merk);
                        $up_migrasi->bindParam(":pabrikan", $pabrikan);
                        $up_migrasi->bindParam(":no_batch", $no_batch);
                        $up_migrasi->bindParam(":expired", $expired);
                        $up_migrasi->bindParam(":harga_beli", $harga_beli);
                        $up_migrasi->execute();
                        if ($up_migrasi) {
                            echo '--> Migrasi Updated<br>';
                            $total_migrasi_update++;
                        } else {
                            echo '--> Migrasi Failed Updated<br>';
                        }
                    } else {
                        echo $id_obat_lama . ' not_found (batch : ' . $no_batch . ')';
                        //insert into table migrasi
                        $created_mig = '2023-01-04 10:00:00';
                        $ins_migrasi = $db->prepare("INSERT INTO `migrasi_obat`(`id_obat`, `id_obat_lama`, `jenis`, `merk`, `pabrikan`, `sumber_dana`, `harga_beli`, `harga_jual_non_tuslah`, `no_batch`, `expired`, `vol`, `created_at`) VALUES (:id_obat_single,:id_obat_lama,:jenis,:merk,:pabrikan,:sumber_dana,:harga_beli,:harga_jual_non_tuslah,:no_batch,:expired,:vol,:created_at)");
                        $ins_migrasi->bindParam(":id_obat_single", $id_obat_single);
                        $ins_migrasi->bindParam(":id_obat_lama", $id_obat_lama);
                        $ins_migrasi->bindParam(":jenis", $jenis);
                        $ins_migrasi->bindParam(":merk", $merk);
                        $ins_migrasi->bindParam(":pabrikan", $pabrikan);
                        $ins_migrasi->bindParam(":sumber_dana", $sumber_dana);
                        $ins_migrasi->bindParam(":harga_beli", $harga_beli);
                        $ins_migrasi->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah);
                        $ins_migrasi->bindParam(":no_batch", $no_batch);
                        $ins_migrasi->bindParam(":expired", $expired);
                        $ins_migrasi->bindParam(":vol", $sisa_stok);
                        $ins_migrasi->bindParam(":created_at", $created_mig);
                        $ins_migrasi->execute();
                        if ($ins_migrasi) {
                            echo "--> Migrasi Inserted<br>";
                            $total_migrasi_insert++;
                        } else {
                            echo "--> Migrasi Failed Inserted<br>";
                        }
                    }
                    //check kartu_stok_gobat
                    $check_kartu = $db->query("SELECT * FROM kartu_stok_gobat WHERE id_obat='" . $id_obat_single . "' AND jenis='" . $jenis . "' AND merk='" . $merk . "' AND pabrikan='" . $pabrikan . "' AND no_batch='" . $no_batch . "' AND expired='" . $expired . "' AND harga_beli='" . $harga_beli . "'");
                    $total_kartu = $check_kartu->rowCount();
                    if ($total_kartu >= 1) {
                        echo $id_obat_single . ' ' . $total_mig . ' found';
                        $keterangan = "(Migrasi Single ID Updated)";
                        //update nilai volume
                        $update_kartu = $db->prepare("UPDATE kartu_stok_gobat SET volume_kartu_awal=:volume_kartu_awal,volume_kartu_akhir=:volume_kartu_akhir,volume_in=:volume_in,keterangan=:keterangan WHERE id_obat=:id_obat_single AND jenis=:jenis AND merk=:merk AND pabrikan=:pabrikan AND no_batch=:no_batch AND expired=:expired AND harga_beli=:harga_beli");
                        $update_kartu->bindParam(":volume_kartu_awal", $sisa_stok);
                        $update_kartu->bindParam(":volume_kartu_akhir", $sisa_stok);
                        $update_kartu->bindParam(":volume_in", $sisa_stok);
                        $update_kartu->bindParam(":keterangan", $keterangan);
                        $update_kartu->bindParam(":id_obat_single", $id_obat_single);
                        $update_kartu->bindParam(":jenis", $jenis);
                        $update_kartu->bindParam(":merk", $merk);
                        $update_kartu->bindParam(":pabrikan", $pabrikan);
                        $update_kartu->bindParam(":no_batch", $no_batch);
                        $update_kartu->bindParam(":expired", $expired);
                        $update_kartu->bindParam(":harga_beli", $harga_beli);
                        $update_kartu->execute();
                        if ($update_kartu) {
                            echo ' --> Kartu Updated<br>';
                            $total_kartu_update++;
                        } else {
                            echo ' --> Kartu Failed Updated<br>';
                        }
                    } else {
                        echo $id_obat_single . ' not_found (batch : ' . $no_batch . ' dan harga : ' . $harga_beli . ')';
                        $created_mig = '2023-01-04 10:00:00';
                        $tujuan = "Migrasi Data";
                        $keterangan = "(Migrasi Single ID)";
                        $aktif = "tidak";
                        $in_out = "masuk";
                        // insert into table kartu_stok_gobat
                        $ins_kartu = $db->prepare("INSERT INTO `kartu_stok_gobat`(`id_obat`, `sumber_dana`, `merk`, `jenis`, `pabrikan`, `volume_kartu_awal`, `volume_kartu_akhir`, `in_out`, `tujuan`, `volume_in`,`expired`, `no_batch`, `ppn_tipe`, `harga_beli`, `harga_jual_non_tuslah`, `aktif`, `created_at`, `keterangan`, `mem_id`) VALUES (:id_obat_single,:sumber_dana,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:in_out,:tujuan,:volume_in,:expired,:no_batch,:ppn_tipe,:harga_beli,:harga_jual_non_tuslah,:aktif,:created_at,:keterangan,:mem_id)");
                        $ins_kartu->bindParam(":id_obat_single", $id_obat_single);
                        $ins_kartu->bindParam(":sumber_dana", $sumber_dana);
                        $ins_kartu->bindParam(":merk", $merk);
                        $ins_kartu->bindParam(":jenis", $jenis);
                        $ins_kartu->bindParam(":pabrikan", $pabrikan);
                        $ins_kartu->bindParam(":volume_kartu_awal", $sisa_stok);
                        $ins_kartu->bindParam(":volume_kartu_akhir", $sisa_stok);
                        $ins_kartu->bindParam(":in_out", $in_out);
                        $ins_kartu->bindParam(":tujuan", $tujuan);
                        $ins_kartu->bindParam(":volume_in", $sisa_stok);
                        $ins_kartu->bindParam(":expired", $expired);
                        $ins_kartu->bindParam(":no_batch", $no_batch);
                        $ins_kartu->bindParam(":ppn_tipe", $ppn_tipe);
                        $ins_kartu->bindParam(":harga_beli", $harga_beli);
                        $ins_kartu->bindParam(":harga_jual_non_tuslah", $harga_jual_non_tuslah);
                        $ins_kartu->bindParam(":aktif", $aktif);
                        $ins_kartu->bindParam(":created_at", $created_mig);
                        $ins_kartu->bindParam(":keterangan", $keterangan);
                        $ins_kartu->bindParam(":mem_id", $mem_id);
                        $ins_kartu->execute();
                        if ($ins_kartu) {
                            echo '--> Kartu Inserted <br>';
                            $total_kartu_insert++;
                        } else {
                            echo '--> Kartu Fail Inserted <br>';
                        }
                    }
                }
            }
        } else {
            $arr_not_found[] = $id_obat_lama . " - " . $namaobat . " (stok : " . $stok_akhir . ")";
            // echo $id_obat_lama . "-not found<br>";
        }
    }
    $db->commit();
    echo '<br>Jumlah Data Migrasi Terupdate : ' . $total_migrasi_update;
    echo '<br>Jumlah Data Migrasi Baru : ' . $total_migrasi_insert;
    echo '<br>Jumlah Data Kartu Baru : ' . $total_kartu_insert;
    echo '<br>Jumlah Data Kartu Terupdate : ' . $total_kartu_update;
    echo '<br>OBAT BELUM TERMAPPING ==><br><pre>' . print_r($arr_not_found, 1) . '</pre>';
} catch (PDOException $e) {
    $e->getMessage();
}
