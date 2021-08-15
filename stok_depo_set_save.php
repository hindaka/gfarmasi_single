<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors','1');
ini_set('max_execution_time', 3000);
date_default_timezone_set('Asia/Jakarta');
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-',$tipe);
if ($tipes[0]!='Gfarmasi')
{
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
$id_warehouse = isset($_GET['warehouse']) ? $_GET['warehouse'] : '';
$today = isset($_GET['today']) ? $_GET['today'] : '';
$full_date = date('Y-m-d H:i:s');
$id_kartu_gobat= 0;
$in_out ="masuk";
$keterangan = "Penyesuaian Stok untuk Awal Penggunaan";
$tujuan = "-";
//get tuslah yg aktif
$get_tuslah = $db->query("SELECT * FROM tuslah WHERE aktif='y'");
$item_tuslah = $get_tuslah->fetch(PDO::FETCH_ASSOC);
//check data
$get_check = $db->query("SELECT t.* FROM temp_stok_awal t INNER JOIN gobat g ON(t.id_obat=g.id_obat) WHERE t.id_warehouse='".$id_warehouse."' AND t.sync='n' AND t.created_at LIKE '%".$today."%' ORDER BY expired ASC");
$check = $get_check->rowCount();
if($check>0){
	$get_all = $get_check->fetchAll(PDO::FETCH_ASSOC);
	foreach ($get_all as $row) {
		//insert into warehouse, kartu stok ruangan
		//hitung ppn (diinput user langsung hargabeli + ppn)
		$merk = isset($row['merk']) ? $row['merk'] : '';
		$volume = isset($row['volume']) ? $row['volume'] : 0;
		$harga_beli = isset($row['harga_beli']) ? $row['harga_beli'] : 0;
		$hargappn=$harga_beli;
		//harga jual
		$harga_jual = $hargappn * 1.2;
		//hitung harga final
		$totalharga= $volume * $hargappn;
		$volume_in = $volume;
		$volume_out = 0;
		$aktif='ya';
		$keterangan = isset($row['alasan']) ? $row['alasan'] : '';
		$sumber_dana = isset($row['sumber_dana']) ? $row['sumber_dana'] : '';
		$expired = isset($row['expired']) ? $row['expired'] : '';
		$no_batch = isset($row['no_batch']) ? $row['no_batch'] : '';
		$id_obat = isset($row['id_obat']) ? $row['id_obat'] : '';
		
		$ref =0;
		//check in warehouse_stok is already have a stok
		$check_warehouse = $db->query("SELECT * FROM warehouse_stok WHERE id_warehouse='".$id_warehouse."' AND id_obat='".$row['id_obat']."' ORDER BY id_warehouse_stok ASC LIMIT 1");
		$total_ware = $check_warehouse->rowCount();
		if($total_ware>0){
			$ware = $check_warehouse->fetch(PDO::FETCH_ASSOC);
			//update stok jika data sudah ada
			$up = $db->query("UPDATE warehouse_stok SET stok=stok+".$row['volume']." WHERE id_warehouse_stok='".$ware['id_warehouse_stok']."'");
			//insert into kartustok_ruangan
			$stok_ruangan = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`,`merk`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `aktif`, `created_at`, `keterangan`, `ref`, `mem_id`) VALUES (:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:merk,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:aktif,:created_at,:keterangan,:ref,:mem_id)");
			$stok_ruangan->bindParam(":id_kartu_gobat",$id_kartu_gobat,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":sumber_dana",$sumber_dana,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":merk",$merk,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":volume_kartu_awal",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_kartu_akhir",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_sisa",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":in_out",$in_out,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":tujuan",$tujuan,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":volume_in",$volume_in,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_out",$volume_out,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":expired",$expired,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":harga_beli",$hargappn);
			$stok_ruangan->bindParam(":harga_jual",$harga_jual);
			$stok_ruangan->bindParam(":id_tuslah",$item_tuslah['id_tuslah'],PDO::PARAM_INT);
			$stok_ruangan->bindParam(":ket_tuslah",$row['tuslah'],PDO::PARAM_INT);
			$stok_ruangan->bindParam(":aktif",$aktif,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":created_at",$full_date,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":ref",$ref,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":mem_id",$r1['mem_id'],PDO::PARAM_INT);
			$stok_ruangan->execute();
		}else{
			//insert into warehouse_stok
			$ins = $db->prepare("INSERT INTO `warehouse_stok`(`id_warehouse`, `id_obat`, `stok`, `expired`, `no_batch`, `created_at`) VALUES (:id_warehouse,:id_obat,:stok,:expired,:no_batch,:created_at)");
			$ins->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
			$ins->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
			$ins->bindParam(":stok",$volume,PDO::PARAM_INT);
			$ins->bindParam(":expired",$expired,PDO::PARAM_STR);
			$ins->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
			$ins->bindParam(":created_at",$full_date,PDO::PARAM_STR);
			$ins->execute();

			//insert into kartustok_ruangan
			$stok_ruangan = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`,`merk`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `aktif`, `created_at`, `keterangan`, `ref`, `mem_id`) VALUES (:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:merk,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:aktif,:created_at,:keterangan,:ref,:mem_id)");
			$stok_ruangan->bindParam(":id_kartu_gobat",$id_kartu_gobat,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":sumber_dana",$sumber_dana,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":merk",$merk,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":volume_kartu_awal",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_kartu_akhir",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_sisa",$volume,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":in_out",$in_out,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":tujuan",$tujuan,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":volume_in",$volume_in,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":volume_out",$volume_out,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":expired",$expired,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":harga_beli",$hargappn);
			$stok_ruangan->bindParam(":harga_jual",$harga_jual);
			$stok_ruangan->bindParam(":id_tuslah",$item_tuslah['id_tuslah'],PDO::PARAM_INT);
			$stok_ruangan->bindParam(":ket_tuslah",$row['tuslah'],PDO::PARAM_INT);
			$stok_ruangan->bindParam(":aktif",$aktif,PDO::PARAM_INT);
			$stok_ruangan->bindParam(":created_at",$full_date,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":ref",$ref,PDO::PARAM_STR);
			$stok_ruangan->bindParam(":mem_id",$r1['mem_id'],PDO::PARAM_INT);
			$stok_ruangan->execute();
		}
		//update sync menjadi y
		$up_sync = $db->query("UPDATE temp_stok_awal SET sync='y' WHERE id_temp='".$row['id_temp']."'");
	} //end foreach
	echo "<script language=\"JavaScript\">window.location = \"stok_depo.php?status=1\"</script>";
}else{
	echo "<script language=\"JavaScript\">window.location = \"stok_depo_set.php?warehouse=".$id_warehouse."&status=1\"</script>";
}
?>
