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

$tanggal_awal = isset($_GET['tanggal_awal']) ? base64_encode($_GET['tanggal_awal']) : base64_encode(date('Y-m-d H:i:s'));
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? base64_encode($_GET['tanggal_akhir']) :  base64_encode(date('Y-m-d H:i:s'));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version; ?> | <?php echo $r1["tipe"]; ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="../plugins/font-awesome/4.3.0/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Ion Slider -->
    <link rel="stylesheet" href="../plugins/ionslider/ion.rangeSlider.css">
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
</head>

<body class="skin-black">
    <div class="wrapper">
        <?php
        include "header.php";
        include "menu_index.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <!-- pesan feedback -->
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data faktur telah diupdate
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <section class="content-header">
                <h1>
                    Daftar
                    <small>rekapitulasi</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Daftar rekapitulasi</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-user"></i>
                                <h3 class="box-title">Data Rekapitulasi Faktur</h3>
                                <button onclick="window.location.href='exportfakturraw.php?awal=<?php echo $tanggal_awal; ?>&akhir=<?php echo $tanggal_akhir; ?>'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export Data</button>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th>No.Register</th>
                                                <th>Tanggal Faktur</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Cara Bayar</th>
                                                <th>Pembayaran Tunai</th>
                                                <th>No. Faktur</th>
                                                <th>PPN(%)</th>
                                                <th>E-Katalog</th>
                                                <th>Perusahaan</th>
                                                <th>No.Batch</th>
                                                <th>Expired</th>
                                                <th>Nama Obat</th>
                                                <th>Jenis</th>
                                                <th>Satuan</th>
                                                <th>fornas</th>
                                                <th>Volume</th>
                                                <th>HNA</th>
                                                <th>diskon</th>
                                                <th>HNA + PPN</th>
                                                <th>Sumber</th>
                                                <th>Dana Pelunasan</th>
                                                <th>Keterangan</th>
                                                <th>Waktu Input</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
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
            var raw_faktur = $('#example1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/data_raw_faktur.php?awal=<?php echo $tanggal_awal; ?>&akhir=<?php echo $tanggal_akhir; ?>",
                "columns": [{
                        "searchable": true,
                        "data": 'id_faktur'
                    },
                    {
                        "searchable": true,
                        "data": 'tgl_faktur'
                    },
                    {
                        "searchable": true,
                        "data": 'tgl_bayar'
                    },
                    {
                        "searchable": true,
                        "data": 'cara_bayar',
                        "render": function(data, type, full, meta) {
                            var cabar;
                            if (data == 'LS') {
                                cabar = '<span class="label bg-primary">LS</span>';
                            } else {
                                cabar = '<span class="label bg-purple">GU</span>';
                            }
                            return cabar;
                        }
                    },
                    {
                        "searchable": true,
                        "data": 'pembayaran_tunai',
                        "render": function(data, type, full, meta) {
                            var tunai;
                            if (data == 'ya') {
                                tunai = '<span class="label label-success">YA</span>';
                            } else {
                                tunai = '<span class="label label-danger">TIDAK</span>';
                            }
                            return tunai;
                        }
                    },
                    {
                        "searchable": true,
                        "data": 'no_faktur'
                    },
                    {
                        "searchable": true,
                        "data": 'ppn_persen'
                    },
                    {
                        "searchable": true,
                        "data": null,
                        "render": function(data, type, full, meta) {
                            var kat;
                            if (data.ekatalog == 'ya') {
                                kat = 'E-Katalog';
                            } else {
                                kat = 'Non E-Katalog';
                            }
                            return kat;
                        }
                    },
                    {
                        "searchable": true,
                        "data": 'perusahaan'
                    },
                    {
                        "searchable": true,
                        "data": 'nobatch'
                    },
                    {
                        "searchable": true,
                        "data": 'expired'
                    },
                    {
                        "searchable": true,
                        "data": 'namaobat'
                    },
                    {
                        "searchable": true,
                        "data": 'jenis'
                    },
                    {
                        "searchable": true,
                        "data": 'satuan'
                    },
                    {
                        "searchable": true,
                        "data": 'fornas'
                    },
                    {
                        "searchable": true,
                        "data": 'volume'
                    },
                    {
                        "searchable": true,
                        "data": 'harga_satuan',
                        "render": function(data, type, full, meta) {
                            return 'Rp ' + data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": 'diskon'
                    },
                    {
                        "searchable": false,
                        "data": 'total',
                        "render": function(data, type, full, meta) {
                            return 'Rp ' + data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": 'sumber'
                    },
                    {
                        "searchable": true,
                        "data": 'sumber_pelunasan',
                        "render": function(data, type, full, meta) {
                            var block_data = "";
                            if (data == 'BLUD') {
                                block_data = "<span class='label label-warning'>BLUD</span>";
                            } else if (data == 'APBD') {
                                block_data = "<span class='label label-info'>APBD</span>";
                            } else {
                                block_data = "<span class='label label-default'>-</span>";
                            }
                            return block_data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": 'keterangan'
                    },
                    {
                        "searchable": false,
                        "data": 'time'
                    },
                ],
                "order": [
                    [0, 'asc']
                ],
            });
            $('#example2').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "bInfo": true,
                "bAutoWidth": false
            });
        });
    </script>


</body>

</html>