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
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/iCheck/all.css">
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

<body class="<?php echo $skin_gfarmasi; ?>">
    <div class="wrapper">

        <?php include("header.php"); ?>
        <?php include "menu_index.php"; ?>
        <div class="content-wrapper">
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Supplier Berhasil ditambahkan
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Supplier Berhasil diubah
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Supplier Sudah Terdaftar, Cek DATA SUPPLIER !
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Terjadi kesalahan pada sistem.<br> Perhatikan Kembali penulisan data inputan.
                    </center>
                </div>
            <?php } ?>
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Pengaturan
                    <small>Data Petugas Farmasi</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-book"></i> Data Pegawai</h3>
                                <span class="pull-right">
                                    <!-- <input type="checkbox" onClick="testing(this)" /> Toggle all on the page -->
                                    <button class="btn btn-sm btn-success" type="button" onclick="getCheck()"><i class="fa fa-send"></i> Daftarkan Sebagai Petugas G.Farmasi</button>
                                </span>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-hover" width="100%">
                                        <thead>
                                            <tr class="info">
                                                <th>#</th>
                                                <th>nama</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-book"></i> Data Petugas Farmasi</h3>
                                <span class="pull-right">
                                    <button class="btn btn-sm btn-warning" type="button" onclick="getBackCheck()"><i class="fa fa-undo"></i> Batalkan Sebagai Petugas G.Farmasi</button>
                                </span>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover" width="100%">
                                        <thead>
                                            <tr class="info">
                                                <th>#</th>
                                                <th>Nama Petugas</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
    <!-- iCheck 1.0.1 -->
    <script src="../plugins/iCheck/icheck.min.js"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        function reloadTable() {
            $('#example1').DataTable().ajax.reload();
            $('#example2').DataTable().ajax.reload();
        }

        function getCheck() {
            /* declare an checkbox array */
            var chkArray = [];

            /* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
            $(".chk:checked").each(function() {
                chkArray.push($(this).val());
            });

            /* we join the array separated by the comma */
            var selected;
            selected = chkArray.join(',');
            /* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
            if (selected.length > 0) {
                // alert("You have selected " + selected);
                // console.log(selected);
                //ajax goes here
                $.ajax({
                    type: "POST",
                    url: "set_petugas_gfarmasi.php",
                    data: {
                        'check_data': selected,
                    },
                    dataType: 'json',
                    success: function(respon) {
                        // console.log("SUCCESS : ", respon);
                        alert(respon);
                        // call notification
                        reloadTable();
                    },
                    error: function(e) {
                        // $("#result").text(e.responseText);
                        console.log("ERROR : ", e.responseText);
                        reloadTable();
                    }
                });
            } else {
                alert("Silakan pilih ceklis satu/lebih petugas terlebih dahulu");
            }
        }

        function getBackCheck() {
            /* declare an checkbox array */
            var chkArray = [];
            /* look for all checkboes that have a class 'chk' attached to it and check if it was checked */
            $(".chk_back:checked").each(function() {
                chkArray.push($(this).val());
            });
            /* we join the array separated by the comma */
            var selected;
            selected = chkArray.join(',');
            /* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
            if (selected.length > 0) {
                // alert("You have selected " + selected);
                console.log(selected);
                //ajax goes here
                $.ajax({
                    type: "POST",
                    url: "set_petugas_gfarmasi_undo.php",
                    data: {
                        'check_data': selected,
                    },
                    dataType: 'json',
                    success: function(respon) {
                        // console.log("SUCCESS : ", respon);
                        alert(respon);
                        // call notification
                        reloadTable();
                    },
                    error: function(e) {
                        // $("#result").text(e.responseText);
                        console.log("ERROR : ", e.responseText);
                        reloadTable();
                    }
                });
            } else {
                alert("Silakan pilih ceklis satu/lebih Data Petugas Farmasi terlebih dahulu");
            }
        }
    </script>
    <script type="text/javascript">
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-green',
            radioClass: 'iradio_minimal-green'
        });
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').css('position', 'relative');

        function testing(source) {
            checkboxes = document.getElementsByName('pilihObat');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
    <script type="text/javascript">
        $(function() {
            var master_pegawai = $('#example1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/data_master_petugas.php",
                "columns": [{
                        "data": "id_pegawai",
                        "render": function(data, type, full, meta) {
                            return '<input class=\"minimal chk\" id=\"pilihObat\" type=\"checkbox\" name=\"pilihObat\" value=\"' + data + '\">';
                        }
                    },
                    {
                        "data": "nama"
                    }
                ],
                "order": [
                    [1, "asc"]
                ]
            });
            var master_petugas = $('#example2').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/data_master_petugas_gfarmasi.php",
                "columns": [{
                        "data": "id_petugas",
                        "render": function(data, type, full, meta) {
                            return '<input class=\"minimal chk_back\" id=\"pilihPetugas\" type=\"checkbox\" name=\"pilihPetugas\" value=\"' + data + '\">';
                        }
                    },
                    {
                        "data": "nama"
                    }
                ],
            });
        });
    </script>
</body>

</html>