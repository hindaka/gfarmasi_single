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
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>SIMRS <?php echo $version_gfarmasi; ?> | <?php echo $r1["tipe"]; ?></title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- Bootstrap 3.3.2 -->
	<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- Font Awesome Icons -->
	<link href="../plugins/font-awesome/4.3.0/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons -->
	<link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
	<!-- DATA TABLES -->
	<link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
	<link href="../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	<style media="screen">
		.dataTables_wrapper .dataTables_processing {
			position: absolute;
			top: 30%;
			left: 50%;
			width: 30%;
			height: 40px;
			margin-left: -20%;
			margin-top: -25px;
			padding-top: 20px;
			text-align: center;
			font-size: 1.2em;
			background: none;
		}
	</style>
</head>

<body class="<?php echo $skin_gfarmasi; ?>">
	<div class="wrapper">
		<?php
		include("header.php");
		include "menu_index.php"; ?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<!-- pesan feedback -->
			<?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diupdate
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Pengaturan Stok Awal Obat Berhasil disimpan
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil!</h4>Pengaturan Stok Awal Obat Berhasil dibatalkan
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Stok Obat Berhasil disinkronisasi
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data Kartu Persediaan tidak ditemukan, Sinkronisasi Gagal
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					DAFTAR OBAT & BMHP
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">DAFTAR OBAT & BMHP</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-tasks"></i>
								<h3 class="box-title">Data Obat Rumah Sakit yang ada di gudang</h3>
								<div class="btn-group pull-right">
									<button onclick="window.location.href='baru.php'" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah obat</button>
									<button onclick="window.location.href='export_single.php'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export Data</button>
								</div>
							</div><!-- /.box-header -->
							<div class="box-body">
								<div class="table-responsive">
									<table id="example1" class="table table-condensed table-striped" width="100%">
										<thead>
											<tr class="bg-blue">
												<th>#</th>
												<th>No. ID</th>
												<th>Reff ID Lama</th>
												<th>Kategori</th>
												<th>Nama Obat / Bmhp</th>
												<th>Nama Fornas</th>
												<th>Kandungan</th>
												<th>Bentuk Sediaan</th>
												<th>Satuan Jual</th>
												<th>kemasan</th>
												<th>Action</th>
											</tr>
										</thead>
									</table>
								</div>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div><!-- /.row -->
			</section><!-- /.content -->
		</div><!-- /.content-wrapper -->
		<!-- static footer -->
		<?php include "footer.php"; ?>
		<!-- /.static footer -->
	</div><!-- ./wrapper -->

	<!-- jQuery 2.1.3 -->
	<script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<!-- DATA TABES SCRIPT -->
	<script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		$(function() {
			var master_obat = $('#example1').DataTable({
				"processing": true,
				"language": {
					"processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
				},
				"serverSide": true,
				"ajax": "ajax_data/data_obat_master_single.php",
				"columns": [{
						"searchable": false,
						"data": null,
						"render": function(data, type, full, meta) {
							var small_btn;
							small_btn = '<a class="btn btn-xs btn-warning" href=\"edit_master_single.php?o=' + data.id_obat + '\" title=\"Ubah Data obat ' + data.nama + '\"><i class="fa fa-pencil"></i></a> ';
							return small_btn;
						}
					}, {
						"searchable": true,
						"data": 'id_obat'
					}, {
						"searchable": true,
						"data": 'old_id_ref'
					},
					{
						"searchable": true,
						"data": 'kategori'
					},
					{
						"searchable": true,
						"data": 'nama',
						"render": function(data, type, full, meta) {
							var nama_obat = '<span style="font-size:16px;">' + data + '</span><br>';
							return nama_obat;
						}
					},
					{
						"searchable": true,
						"data": 'nama_fornas',
						"render": function(data, type, full, meta) {
							let fornas = "";
							if (data != '') {
								fornas += '<span style="font-size:12px;" class="label label-default"><i> ' + data + '</i></span>';
							}
							return fornas;
						}
					},
					{
						"searchable": false,
						"data": 'kandungan',
						"render": function(data, type, full, meta) {
							let kandungan = '';
							kandungan = data.split("|").join("; ");
							return kandungan;
						}
					},
					{
						"searchable": false,
						"data": 'bentuk_sediaan'
					},
					{
						"searchable": false,
						"data": 'satuan'
					},
					{
						"searchable": false,
						"data": 'kemasan'
					},
					{
						"searchable": false,
						"data": null,
						"render": function(data, type, full, meta) {
							var btn = "";
							// btn = '<a class="btn btn-xs btn-block bg-purple" href=\"sync_stok_sisa.php?id=' + data.id_obat + '&sumber=' + data.sumber + '\"><i class="fa fa-list"></i> Sinkron Sisa Stok</a>';
							// // btn +='<a class="btn btn-xs btn-block btn-info" href=\"sync_stok.php?id='+data.id_obat+'&sumber='+data.sumber+'\"><i class="fa fa-gear"></i> Sinkronisasi</a>';
							btn += '<a class="btn btn-xs btn-block btn-warning" href=\"set_stok_gudang.php?o=' + data.id_obat + '\"><i class="fa fa-gear"></i> Pengaturan Stok Awal</a>';
							// btn += '<a class="btn btn-xs btn-block btn-success" href=\"histori_harga.php?obat=' + data.nama + '&sumber=' + data.sumber + '\"><i class="fa fa-money"></i> Riwayat Harga</a>';
							btn += '<a class="btn btn-xs btn-block btn-primary" href=\"kartu_stok.php?o=' + data.id_obat + '\"><i class="fa fa-book"></i> Kartu Stok</a>';
							// btn += '<a target="_blank" class="btn btn-xs btn-block btn-default" href=\"cetakbarcode.php?o=' + data.id_obat + '\"><i class="fa fa-list"></i> Cetak Barcode</a>';
							return btn;
						}
					},
				],
				"order": [
					[2, 'asc']
				],
			});
		});
	</script>

</body>

</html>