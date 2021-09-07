<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
$id_warehouse = isset($_GET['warehouse']) ? $_GET['warehouse'] : '';
$id_obat = isset($_GET['o']) ? $_GET['o'] : '';
$today = isset($_GET['today']) ? $_GET['today'] : '';
$full_date = date('Y-m-d H:i:s');
$created_at = date('Y-m-d H:i:s');
$in_out = "masuk";
$keterangan = "Penyesuaian Stok untuk Awal Penggunaan";
$tujuan = "-";
$links = "master_obat_single.php?status=1";

//check data
$get_check = $db->query("SELECT t.*FROM temp_stok_awal t INNER JOIN gobat g ON(t.id_obat=g.id_obat) WHERE t.id_warehouse='" . $id_warehouse . "' AND t.id_obat='" . $id_obat . "' AND t.sync='n' AND t.created_at LIKE '%" . $today . "%' ORDER BY expired ASC");
$check = $get_check->rowCount();
if ($check > 0) {
	$get_all = $get_check->fetchAll(PDO::FETCH_ASSOC);
	foreach ($get_all as $row) {
		//insert kartu stok gobat
		$sumber = isset($row['sumber_dana']) ? $row['sumber_dana'] : '';
		$e_kat = isset($row['e_kat']) ? $row['e_kat'] : '';
		// harga yang dinput harus sudah include ppn
		$hargabeli = isset($row['harga_beli']) ? $row['harga_beli'] : '';
		$volume_input = isset($row['volume']) ? $row['volume'] : 0;
		//hitung ppn
		$hargappn = $hargabeli;
		//harga jual
		$harga_jual = $hargappn * 1.2;
		//hitung harga final
		$totalharga = $volume_input * $hargappn;
		$volume_in = $volume_input;
		$volume_out = 0;
		$aktif = 'tidak';
		$ref = 0;
		$volume_kartu_awal = $volume_input;
		$volume_kartu_akhir = $volume_input;
		$expired_stok = isset($row['expired']) ? $row['expired'] : '';
		$nobatch = isset($row['no_batch']) ? $row['no_batch'] : '';
		$merk = isset($row['merk']) ? $row['merk'] : '';
		$jenis = isset($row['jenis']) ? $row['jenis'] : '';
		$pabrikan = isset($row['pabrikan']) ? $row['pabrikan'] : '';
		//get data kartu
		$get_kartu = $db->query("SELECT volume_kartu_akhir FROM kartu_stok_gobat WHERE id_obat='" . $id_obat . "' AND aktif='ya' AND in_out='masuk' AND volume_kartu_akhir<>0");
		$total_kartu = $get_kartu->rowCount();
		if ($total_kartu > 0) {
			$kartu = $get_kartu->fetch(PDO::FETCH_ASSOC);
			$volume_sisa = $kartu['volume_kartu_akhir'] + $volume_in;
		} else {
			$volume_sisa = $row['volume'];
		}
		//db hasil
		$result3 = $db->prepare("INSERT INTO `kartu_stok_gobat`(`id_obat`,`sumber_dana`,`e_kat`,`merk`,`jenis`,`pabrikan`, `volume_kartu_awal`,`volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual_non_tuslah`, `aktif`, `created_at`, `keterangan`)VALUES (:id_obat,:sumber,:e_kat,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:aktif,:created_at,:keterangan)");
		$result3->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$result3->bindParam(":sumber", $sumber, PDO::PARAM_STR);
		$result3->bindParam(":e_kat", $e_kat, PDO::PARAM_STR);
		$result3->bindParam(":merk", $merk, PDO::PARAM_STR);
		$result3->bindParam(":jenis", $jenis, PDO::PARAM_STR);
		$result3->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
		$result3->bindParam(":volume_kartu_awal", $volume_kartu_awal, PDO::PARAM_INT);
		$result3->bindParam(":volume_kartu_akhir", $volume_kartu_akhir, PDO::PARAM_INT);
		$result3->bindParam(":volume_sisa", $volume_sisa, PDO::PARAM_INT);
		$result3->bindParam(":in_out", $in_out, PDO::PARAM_STR);
		$result3->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
		$result3->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
		$result3->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
		$result3->bindParam(":expired", $expired_stok, PDO::PARAM_STR);
		$result3->bindParam(":no_batch", $nobatch, PDO::PARAM_STR);
		$result3->bindParam(":harga_beli", $hargappn);
		$result3->bindParam(":harga_jual", $harga_jual);
		$result3->bindParam(":aktif", $aktif, PDO::PARAM_STR);
		$result3->bindParam(":created_at", $created_at, PDO::PARAM_STR);
		$result3->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$result3->execute();
		$id_kartu = $db->lastInsertId();
		//get harga obat
		$harga_obat = $db->query("SELECT harga,volume FROM gobat WHERE id_obat='" . $id_obat . "'");
		$before = $harga_obat->fetch(PDO::FETCH_ASSOC);
		$harga_lama = isset($before['harga']) ? $before['harga'] : '';
		$new_volume = $volume_sisa;
		// update obat
		$up_obat = $db->prepare("UPDATE gobat SET volume=:volume,harga=:harga,hargalama=:hargalama,expired=:expired,nobatch=:nobatch WHERE id_obat=:id_obat");
		$up_obat->bindParam(":volume", $new_volume, PDO::PARAM_INT);
		$up_obat->bindParam(":harga", $hargappn, PDO::PARAM_INT);
		$up_obat->bindParam(":hargalama", $harga_lama, PDO::PARAM_INT);
		$up_obat->bindParam(":expired", $expired_stok, PDO::PARAM_INT);
		$up_obat->bindParam(":nobatch", $nobatch, PDO::PARAM_INT);
		$up_obat->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$up_obat->execute();
		//update kartu_stok_gobat
		$up_kartu = $db->prepare("UPDATE kartu_stok_gobat SET aktif='ya',keterangan='Sudah ditambahkan ke stok(set stok)' WHERE id_kartu=:id_kartu");
		$up_kartu->bindParam(":id_kartu", $id_kartu, PDO::PARAM_INT);
		$up_kartu->execute();
		//update sync menjadi y
		$up_sync = $db->query("UPDATE temp_stok_awal SET sync='y' WHERE id_temp='" . $row['id_temp'] . "'");
	} //end foreach
	echo "<script language=\"JavaScript\">window.location = \"" . $links . "\"</script>";
} else {
	echo "<script language=\"JavaScript\">window.location = \"" . $links . "\"</script>";
}
