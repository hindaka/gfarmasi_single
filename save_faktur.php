<?php
//conn
session_start();
include("../inc/pdo.conf.php");
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
//get var
$id_faktur=isset($_GET["faktur"]) ? $_GET['faktur'] : '';
$sumber = isset($_GET['sumber']) ? $_GET['sumber'] : '';
$tgl_bayar="";
$tgl_rbayar="";
try {
	//get all data from faktur
	$list_data = $db->query("SELECT * FROM kartu_stok_gobat WHERE id_faktur='".$id_faktur."' AND aktif='tidak' AND in_out='masuk'");
	$jumlah_data =  $list_data->rowCount();
	if($jumlah_data > 0){
		//get list id obat dan update stoknya
		$data_kartu = $list_data->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data_kartu as $kartu) {
			//get harga obat
			$harga_obat = $db->query("SELECT harga,volume FROM gobat WHERE id_obat='".$kartu['id_obat']."'");
			$before = $harga_obat->fetch(PDO::FETCH_ASSOC);
			$new_volume = $before['volume'] + $kartu['volume_in'];
			// update obat
			$up_obat = $db->prepare("UPDATE gobat SET volume=:volume,harga=:harga,hargalama=:hargalama,expired=:expired,nobatch=:nobatch WHERE id_obat=:id_obat");
			$up_obat->bindParam(":volume",$new_volume,PDO::PARAM_INT);
			$up_obat->bindParam(":harga",$kartu['harga_beli'],PDO::PARAM_INT);
			$up_obat->bindParam(":hargalama",$before['harga'],PDO::PARAM_INT);
			$up_obat->bindParam(":expired",$kartu['expired'],PDO::PARAM_INT);
			$up_obat->bindParam(":nobatch",$kartu['no_batch'],PDO::PARAM_INT);
			$up_obat->bindParam(":id_obat",$kartu['id_obat'],PDO::PARAM_INT);
			$up_obat->execute();
			//update kartu_stok_gobat
			$up_kartu = $db->prepare("UPDATE kartu_stok_gobat SET aktif='ya',keterangan='Sudah ditambahkan ke stok(faktur)' WHERE id_kartu=:id_kartu");
			$up_kartu->bindParam(":id_kartu",$kartu['id_kartu'],PDO::PARAM_INT);
			$up_kartu->execute();
		}
		//hitung pnn & pph
		$get_parent_faktur = $db->query("SELECT perusahaan,pembayaran_tunai FROM faktur WHERE id_faktur='".$id_faktur."'");
		$parent = $get_parent_faktur->fetch(PDO::FETCH_ASSOC);
		$pembayaran_tunai = $parent['pembayaran_tunai'];
		if($pembayaran_tunai=='ya'){
			$status_faktur = "2";
			$tgl_bayar = date('d/m/Y');
			$tgl_rbayar = date('d/m/Y');
		}else{
			$status_faktur = "1";
		}
		// check supplier
		$get_supplier = $db->query("SELECT pajak_all FROM supplier WHERE nama_perusahaan LIKE '".$parent['perusahaan']."'");
		$sup = $get_supplier->fetch(PDO::FETCH_ASSOC);

		$get_faktur = $db->query("SELECT ppn,total FROM itemfaktur WHERE id_faktur='".$id_faktur."'");
		$faktur = $get_faktur->fetchAll(PDO::FETCH_ASSOC);
		$total_ppn = 0;
		$total_faktur = 0;
		$pph = 0;
		$ppn_fix =0;
		$kena_pajak ="tidak";
		foreach ($faktur as $f) {
			$total_ppn = $total_ppn + $f['ppn'];
			$total_faktur = $total_faktur + $f['total'];
		}
		if($sup['pajak_all']=='ya'){
			$pph = $total_ppn * 10 * (1.5/100);
			$ppn_fix = $total_ppn;
			$kena_pajak ="ya";
		}else{
			if($total_faktur>=2000000){
 			 $pph = $total_ppn * 10 * (1.5/100);
 			 $ppn_fix = $total_ppn;
 			 $kena_pajak ="ya";
 		 }else if($total_faktur>=1000000 && $total_faktur<2000000){
 			 $pph = 0;
 			 $ppn_fix = $total_ppn;
 			 $kena_pajak ="ya";
 		 }else{
 			 $pph=0;
 			 $ppn_fix=$total_ppn;
 		 }
		}

		$up_faktur = $db->query("UPDATE faktur SET status='".$status_faktur."',tgl_bayar='".$tgl_bayar."',tgl_rbayar='".$tgl_rbayar."',total_ppn=".$ppn_fix.",total_pph=".$pph.",kena_pajak='".$kena_pajak."' WHERE id_faktur='".$id_faktur."'");
		// redirect ke halaman
		echo "<script language=\"JavaScript\">window.location = \"masuk.php?status=1\"</script>";
	}else{
		echo "<script language=\"JavaScript\">window.location = \"tambah.php?id=$id_faktur&sumber=$sumber&status=5\"</script>";
	}
} catch (PDOException $e) {
	echo "Fail to Save data : Fail ON(".$e->getMessage().")";
}
