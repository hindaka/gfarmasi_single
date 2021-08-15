<?php
session_start();
include("../inc/pdo.conf.php");
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
$id_petugas = $r1['mem_id'];
$id_warehouse = isset($_POST['warehouse']) ? $_POST['warehouse'] : '';
$sumber_dana = isset($_POST['sumber_dana']) ? $_POST['sumber_dana'] : '';
$pemesan = isset($_POST['pemesan']) ? $_POST['pemesan'] : '';
$jabatan = isset($_POST['jabatan']) ? $_POST['jabatan'] : '';
$tanggal_keluar = isset($_POST['tanggal_keluar']) ? $_POST['tanggal_keluar'] : '';
// get nama & nip pegawai
$data_pegawai = $db->query("SELECT * FROM pegawai WHERE id_pegawai='".$pemesan."'");
$pegawai = $data_pegawai->fetch(PDO::FETCH_ASSOC);

$today = date('Y-m-d H:i:s');
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
// format nomor 027/001/IV/APBD-FAR/RSKIA/2018
// 027 = kode dari pemkot
// 001 = urutan list berdasarkan sumber dana ,bulan, & tahun
// IV = bulan dalam romawi
// APBD-FAR = SUMBER Dana + APBD-FAR
// 2018 = tahun berjalan
$kode_dokumen = "";
// 001/V/BLUD-FAR/SPBB/RSKIA/2018
$no_spbb = "";
// 001/V/BLUD-FAR/SPB/RSKIA/2018
$no_spb = "";
$kode_pemkot = "027";
$bulan = substr($tanggal_keluar,5,2);
$bulan_romawi = getRomawi($bulan);
if($sumber_dana=='APBD'){
	$dana = "APBD-FAR";
}else{
	$dana = "BLUD-FAR";
}
$rs = "RSKIA";
$tahun = substr($tanggal_keluar,0,4);

try {
	//get pembuatan kode dokumen
	$get_list = $db->query("SELECT CAST(IFNULL(MAX(SUBSTRING(nomor,5,3)),'000') as UNSIGNED) as urutan FROM obatkeluar_parent WHERE sumber_dana='".$sumber_dana."' AND MONTH(created_at)='".$bulan."' AND YEAR(created_at)='".$tahun."' ORDER BY tanggal_keluar ASC");
	$no = $get_list->fetch(PDO::FETCH_ASSOC);
	$urutan = $no['urutan']+1;
	$format_urutan = sprintf("%03s",$urutan);
	$kode_dokumen .=$kode_pemkot."/".$format_urutan."/".$bulan_romawi."/".$dana."/".$rs."/".$tahun;
	$no_spbb .=$format_urutan."/".$bulan_romawi."/".$dana."/SPBB/".$rs."/".$tahun;
	$no_spb .=$format_urutan."/".$bulan_romawi."/".$dana."/SPB/".$rs."/".$tahun;

	//create obatkeluar_parent untuk keperluan pembuatan nota dinas dll,,
	$ins_parent = $db->prepare("INSERT INTO `obatkeluar_parent`(`id_warehouse`,`nomor`,`no_spbb`,`no_spb`,`sumber_dana`, `tanggal_keluar`, `pemesan`,`jabatan`,`nip`, `approval_petugas`) VALUES (:id_warehouse,:nomor,:no_spbb,:no_spb,:sumber_dana,:tanggal_keluar,:pemesan,:jabatan,:nip,:approval)");
	$ins_parent->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
	$ins_parent->bindParam(":nomor",$kode_dokumen,PDO::PARAM_STR);
	$ins_parent->bindParam(":no_spbb",$no_spbb,PDO::PARAM_STR);
	$ins_parent->bindParam(":no_spb",$no_spb,PDO::PARAM_STR);
	$ins_parent->bindParam(":sumber_dana",$sumber_dana,PDO::PARAM_STR);
	$ins_parent->bindParam(":tanggal_keluar",$tanggal_keluar,PDO::PARAM_STR);
	$ins_parent->bindParam(":pemesan",$pegawai['nama'],PDO::PARAM_STR);
	$ins_parent->bindParam(":jabatan",$jabatan,PDO::PARAM_STR);
	$ins_parent->bindParam(":nip",$pegawai['nip'],PDO::PARAM_STR);
	$ins_parent->bindParam(":approval",$id_petugas,PDO::PARAM_INT);
	$ins_parent->execute();
	$id_parent = $db->lastInsertId();
	echo "<script language=\"JavaScript\">window.location = \"keluar.php?parent=".$id_parent."&type=".$sumber_dana."\"</script>";
} catch (PDOException $e) {
	echo "Fail to create Parent for Obat keluar : FAIL ON ( ".$e->getMessage()." )";
}
?>
