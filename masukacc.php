<?php
session_start();
include("../inc/pdo.conf.php");
ini_set('display_errors','1');
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
$tanggalf=isset($_POST["tanggalf"]) ? $_POST['tanggalf'] : '';
$nofaktur=isset($_POST["nofaktur"]) ? $_POST['nofaktur'] : '';
$perusahaan=isset($_POST["perusahaan"]) ? $_POST['perusahaan'] : '';
$ekatalog = isset($_POST['ekatalog']) ? $_POST['ekatalog'] : 'tidak';
$petugas = isset($_POST['petugas']) ? $_POST['petugas'] : 0;
$sumber = isset($_POST['sumber']) ? $_POST['sumber'] : '';
$jatuh_tempo_list = isset($_POST['jatuh_tempo']) ? $_POST['jatuh_tempo'] : '';
$tempo = explode("/",$jatuh_tempo_list);
$jatuh_tempo = $tempo['2']."-".$tempo['1']."-".$tempo[0];
$dana_pembayaran = isset($_POST['dana_pembayaran']) ? $_POST['dana_pembayaran'] : 'tidak';
$cara_bayar = isset($_POST['cara_bayar']) ? $_POST['cara_bayar'] : '';
$pembelian = isset($_POST['pembelian']) ? $_POST['pembelian'] : 'Dalam';
$pembayaran_tunai = isset($_POST['pembayaran_tunai']) ? $_POST['pembayaran_tunai'] : 'tidak';
$jenis_faktur = isset($_POST['jenis_faktur']) ? $_POST['jenis_faktur'] : '-';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
$status = "1";
$kode_dokumen="";
$split_date = explode("/",$tanggalf);
function getRomawi($bln){
      switch ($bln){
          case '01':
              return "I";
              break;
          case '02':
              return "II";
              break;
          case '03':
              return "III";
              break;
          case '04':
              return "IV";
              break;
          case '05':
              return "V";
              break;
          case '06':
              return "VI";
              break;
          case '07':
              return "VII";
              break;
          case '08':
              return "VIII";
              break;
          case '09':
              return "IX";
              break;
          case '10':
              return "X";
              break;
          case '11':
              return "XI";
              break;
          case '12':
              return "XII";
              break;
      }
}
// check apakah nama perusahaan terdaftar
$check = $db->query("SELECT * FROM supplier WHERE nama_perusahaan LIKE '".$perusahaan."'");
$row = $check->rowCount();
if($row==1){
	//found
	//check no_faktur
	$get_check = $db->query("SELECT * FROM faktur WHERE no_faktur='".$nofaktur."' AND status='1'");
	$total_faktur = $get_check->rowCount();
	if($total_faktur > 0){
		//redirect to tambah
		$check = $get_check->fetch(PDO::FETCH_ASSOC);
		$id_faktur = $check['id_faktur'];
		echo "<script language=\"JavaScript\">window.location = \"tambah.php?id=".$id_faktur."&sumber=".$sumber."\"</script>";
	}else{
		// format nomor 027/001/IV/APBD-FAR/BASTBP/RSKIA/2018
		$bulan = $split_date[1];
		$bulan_romawi = getRomawi($bulan);
		// if($sumber=='APBD'){
		// 	$dana = "APBD-FAR/BASTBP";
		// }else if($sumber=='BLUD'){
		// 	$dana = "BLUD-FAR/BASTBP";
		// }else{
		// 	$dana = "KOSONG-FAR/BASTBP";
		// }
		$dana = "FARMASI/BASTBP";
		
		$rs = "RSKIA";
		$tahun = $split_date[2];
		if($dana_pembayaran=='apbd'){
			$dana_p ="ya";
		}else{
			$dana_p ="tidak";
		}
		//create a transaction
		//get list dokumen in faktur parent
		$get_dok = $db->query("SELECT CAST(IFNULL(MAX(SUBSTRING(no_bast,1,4)),'0000') as UNSIGNED) as urutan FROM `faktur_parent` WHERE sumber_dana='".$sumber."' AND MONTH(created_at)='".$bulan."' AND YEAR(created_at)='".$tahun."'");
		$no = $get_dok->fetch(PDO::FETCH_ASSOC);
		$urutan = $no['urutan']+1;
		$format_urutan = sprintf("%04s",$urutan);
		$kode_dokumen .=$format_urutan."/".$bulan_romawi."/".$dana."/".$rs."/".$tahun;
		//insert
		$result = $db->prepare("INSERT INTO faktur(id_petugas,tgl_faktur,no_faktur,perusahaan,status,ekatalog,jatuh_tempo,bayar_apbd,keterangan,jenis_faktur) VALUES(:id_petugas,:tgl,:nofaktur,:perusahaan,:status,:ekatalog,:jatuh_tempo,:bayar_apbd,:keterangan,:jenis_faktur)");
		$result->bindParam(":id_petugas",$petugas,PDO::PARAM_INT);
		$result->bindParam(":tgl",$tanggalf,PDO::PARAM_STR);
		$result->bindParam(":nofaktur",$nofaktur,PDO::PARAM_STR);
		$result->bindParam(":perusahaan",$perusahaan,PDO::PARAM_STR);
		$result->bindParam(":status",$status,PDO::PARAM_STR);
		$result->bindParam(":ekatalog",$ekatalog,PDO::PARAM_STR);
		$result->bindParam(":jatuh_tempo",$jatuh_tempo,PDO::PARAM_STR);
		$result->bindParam(":bayar_apbd",$dana_p,PDO::PARAM_STR);
		$result->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
		$result->bindParam(":jenis_faktur",$jenis_faktur,PDO::PARAM_STR);
		$result->execute();
		$id_faktur=$db->lastInsertId();
		// create faktur_parent
		$ins = $db->prepare("INSERT INTO `faktur_parent`(`no_bast`, `id_faktur`,`sumber_dana`, `mem_id`) VALUES (:no_bast,:id_faktur,:sumber_dana,:mem_id)");
		$ins->bindParam(":no_bast",$kode_dokumen,PDO::PARAM_STR);
		$ins->bindParam(":id_faktur",$id_faktur,PDO::PARAM_INT);
		$ins->bindParam(":sumber_dana",$sumber,PDO::PARAM_STR);
		$ins->bindParam(":mem_id",$r1['mem_id'],PDO::PARAM_INT);
		$ins->execute();
		//action
		if ($result) {
		echo "<script language=\"JavaScript\">window.location = \"tambah.php?id=".$id_faktur."&sumber=".$sumber."\"</script>";
		} else {
		echo "gagal";
		}
	}
}else{
	// redirect to masuk.php, supplier not found
	echo "<script language=\"JavaScript\">window.location = \"masuk.php?status=2\"</script>";
}
?>
