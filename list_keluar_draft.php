<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version_gfarmasi; ?> | <?php echo $r1["nama"]; ?></title>
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
    <style>
        .dataTables_processing {
            left: 50%;
            position: absolute;
            top: 50%;
            z-index: 100;
        }
    </style>
</head>

<body class="<?php echo $skin_gfarmasi; ?>">
    <div class="wrapper">

        <?php include "header.php"; ?>
        <?php include "menu_index.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <!-- pesan feedback -->
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Barang keluar telah diupdate
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Barang Keluar Berhasil dikeluarkan
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Keluar Berhasil dihapus
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Barang Keluar Berhasil tersimpan sebagai Draft
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-warning"></i>Peringatan!!</h4>Data Barang Keluar tersebut sudah diposting
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <section class="content-header">
                <h1>
                    Data Draft Barang Keluar Depo
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Data Draft Barang Keluar Depo</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-tasks"></i>
                                <h3 class="box-title">Data Draft Barang Keluar Depo per <?php echo date("d/m/Y h:i:s"); ?></h3>
                                <div class="btn-group btn-group-sm pull-right">
                                    <button onclick="window.location.href='obat_keluar.php'" class="btn btn-primary"><i class="fa fa-plus"></i> Buat Transaksi</button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped table-hover" width="100%">
                                    <thead>
                                        <tr class="info">
                                            <th>Tanggal Pencatatan</th>
                                            <th>Instalasi</th>
                                            <th>Pemesan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
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
            $("#example1").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/list_draft_keluar.php",
                "language": {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                },
                "columns": [{
                        "data": "tanggal_keluar"
                    },
                    {
                        "data": "nama_ruang"
                    },
                    {
                        "data": "pemesan"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            var btn = '';
                            btn += '<a href="keluar.php?parent=' + data.id_obatkeluar_parent + '&type=' + data.sumber_dana + '" class="btn btn-xs btn-block btn-warning"><i class="fa fa-pencil"></i> Edit Data Keluar</a>';
                            return btn;
                        }
                    },
                ]
            });
        });
    </script>

</body>

</html>