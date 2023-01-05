<?php
session_start();
include("../inc/pdo.conf.php");
date_default_timezone_set("Asia/Jakarta");
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
$id_bentuk = isset($_GET['bentuk']) ? trim($_GET['bentuk']) : '';
$jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : '';
//get
$get_sediaan = $db->query("SELECT * FROM gobat_bentuk_sediaan WHERE id_bentuk_sediaan='".$id_bentuk."'");
$head = $get_sediaan->fetch(PDO::FETCH_ASSOC);
$h4=$db->query("SELECT g.id_obat,g.nama,g.jenis,gb.nama_bentuk,SUM(kg.volume_kartu_akhir) as sisa_stok FROM `gobat` g INNER JOIN kartu_stok_gobat kg ON(kg.id_obat=g.id_obat) INNER JOIN gobat_bentuk_sediaan gb ON(gb.id_bentuk_sediaan=g.id_bentuk) WHERE kg.in_out='masuk' AND g.jenis='".$jenis."' AND g.id_bentuk='".$id_bentuk."' GROUP BY g.id_obat");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
$today = date('Y-m-d');
$full_day = date('d/m/Y H:i:s');
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=form_so_".$today.".xls");
?>
Tanggal Form SO : <?php echo $full_day; ?><br>
Jenis : <?php echo $jenis; ?><br>
Bentuk Sediaan : <?php echo ucwords($head['nama_bentuk']); ?><br>
<table border="1">
	<thead>
		<tr class="info">
			<th>ID Obat</th>
			<th>Nama Obat</th>
			<th>Jenis</th>
			<th>Bentuk Sediaan</th>
			<th>Sisa Stok</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($data4 as $d) {
				echo '<tr>
								<td>'.$d['id_obat'].'</td>
								<td>'.$d['nama'].'</td>
								<td>'.$d['jenis'].'</td>
								<td>'.$d['nama_bentuk'].'</td>
								<td>'.$d['sisa_stok'].'</td>
							</tr>';
			}
		?>
	</tbody>
</table>
