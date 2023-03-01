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
                    DATA MIGRASI
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">DATA MIGRASI</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-tasks"></i>
                                <h3 class="box-title">Data Migrasi</h3>
                                <!-- <div class="btn-group pull-right">
                                    <button onclick="window.location.href='baru_master.php'" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah obat</button>
                                    <button onclick="window.location.href='export_single.php'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export Data</button>
                                </div> -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-condensed table-striped" width="100%">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th>#</th>
                                                <th>tgl migrasi</th>
                                                <th>id lama</th>
                                                <th>Nama Baru</th>
                                                <th>jenis</th>
                                                <th>merk</th>
                                                <th>pabrikan</th>
                                                <th>no batch</th>
                                                <th>expired</th>
                                                <th>vol</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                        <!-- Modal -->
                        <div id="myModal" class="modal fade" role="dialog">
                            <div class="modal-dialog modal-sm">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header bg-purple">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Data Referensi Obat</h4>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="reff_id" name="reff_id">
                                        <div class="form-group">
                                            <label for="my-input">Referensi ID Lama</label>
                                            <input id="reff_old_id" name="reff_old_id" class="form-control" type="text">
                                            <small style="color:red">Gunakan Penanda ";" untuk pemisah antar ID OBAT</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="submitData" type=" button" class="btn bg-purple">Update</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>
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
    <script src="../plugins/datatables2/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables2/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        function loadForm(ele) {
            let id = ele.id;
            let id_obat = $('#' + id).data('id_obat');
            let reff_old_id = $('#' + id).data('reff');
            $('#myModal').modal().find('.modal-body #reff_id').val(id_obat);
            $('#myModal').modal().find('.modal-body #reff_old_id').val(reff_old_id);
            $('#myModal').modal();
        }
        $(function() {
            $('#submitData').on('click', function(e) {
                e.preventDefault();
                let reff_id = $('#reff_id').val();
                let reff_old_id = $('#reff_old_id').val();
                $.ajax({
                    type: "POST",
                    url: "ajax_data/update_reff_old_id.php",
                    data: {
                        "reff_id": reff_id,
                        "reff_old_id": reff_old_id
                    },
                    success: function(response) {
                        let res = JSON.parse(response);
                        alert(res.msg);
                        location.reload();
                    },
                    error: function(err) {
                        console.warn(err);
                    }
                });
            });
            var master_obat = $('#example1').DataTable({
                "processing": true,
                "language": {
                    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                },
                "serverSide": true,
                "ajax": "ajax_data/data_katalog_migrasi.php",
                "columns": [{
                        "searchable": false,
                        "data": "id_migrasi",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": "created_at",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": "id_obat_lama",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": "nama",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": "jenis",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": "merk",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": "pabrikan",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": true,
                        "data": "no_batch",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": "expired",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "searchable": false,
                        "data": "vol",
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                ],
                "order": [
                    [0, 'asc']
                ],
            });
        });
    </script>

</body>

</html>