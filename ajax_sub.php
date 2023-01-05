<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$post = $_POST['id'];
$data_warehouse = $db->query("SELECT w.nama_ruang,ws.stok as total_stok FROM `warehouse_stok` ws INNER JOIN gobat g ON(g.id_obat=ws.id_obat) INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) WHERE ws.id_obat='".$post."' ORDER BY w.nama_ruang ASC");
$ware = $data_warehouse->fetchAll(PDO::FETCH_ASSOC);
$total_data = $data_warehouse->rowCount();
if($total_data>0){
  $html = "<div style='padding-left:50px;'><table class='table table-striped table-hover table-bordered'>
    <thead>
      <tr class='info'>
        <th>Nama Ruangan</th>
        <th>Stok Ruangan</th>
      </tr>
    </thead>
    <tbody>";
    foreach ($ware as $w) {
      $html.="<tr>
              <td>".$w['nama_ruang']."</td>
              <td>".$w['total_stok']."</td>
            </tr>";
    }
    $html.="</tbody>
  </table></div>";
}else{
    $html = "<span>Data Tidak ditemukan</span>";
}


echo json_encode($html);
?>
