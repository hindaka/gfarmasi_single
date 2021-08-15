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

$h4 = $db->query("SELECT id_warehouse,nama_ruang FROM warehouse");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
//get data pegawai
$data_pegawai = $db->query("SELECT id_pegawai,nama,nip FROM pegawai ORDER BY nama ASC");
$pegawai = $data_pegawai->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- daterange picker -->
    <link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- BootsrapSelect -->
    <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
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
</head>

<body class="<?php echo $skin_gfarmasi; ?>">
    <div class="wrapper">
        <?php 
        include "header.php";
        include "menu_index.php"; ?>
        <div class="content-wrapper">
            <!-- pesan feedback -->
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diinput
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data obat tidak ditemukan
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Stok obat tidak mencukupi
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Peringatan!</h4>Transaksi Obat Keluar Berhasil dibatalkan
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Transaksi
                    <small>obat keluar</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Transaksi</li>
                    <li class="active">Obat Keluar</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="alert alert-info">field yang bertandakan <span style="color:red">*</span> Wajib diisi / dipilih</div>

                <!-- left column -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-user"></i>
                        <h3 class="box-title">Pilih Warehouse / Mini Depo Tujuan Pengeluaran Barang</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="obat_keluar_acc.php" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="tanggal">Tanggal Pengeluaran Barang <span style="color:red">*</span></label>
                                <input type="datetime-local" class="form-control" name="tanggal_keluar" id="tanggal_keluar" required>
                            </div>
                            <div class="form-group">
                                <label for="warehouse">Warehouse <span style="color:red">*</span></label>
                                <select class="form-control selectpicker" data-live-search="true" name="warehouse" required>
                                    <option value="">Pilih Warehouse</option>
                                    <?php
                                    foreach ($data4 as $r4) {
                                        $nama_ruang = $r4["nama_ruang"];
                                        echo "<option value='" . $r4['id_warehouse'] . "'>" . $nama_ruang . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sumber_dana">Sumber Dana <span style="color:red">*</span></label>
                                <select class="form-control" name="sumber_dana" required>
                                    <option value="">---Pilih Sumber Dana Obat---</option>
                                    <option value="APBD">APBD</option>
                                    <option value="BLUD">BLUD</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="pemesan">Nama Pemesan <span style="color:red">*</span></label>
                                <!-- <input type="text" class="form-control" name="pemesan" placeholder="Masukan Nama Pemesan disini." required> -->
                                <select class="form-control selectpicker" name="pemesan" data-live-search="true" required>
                                    <option value="">---Pilih Nama Pemesan---</option>
                                    <?php
                                    foreach ($pegawai as $p) {
                                        echo "<option value='" . $p['id_pegawai'] . "'>" . $p['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="jabatan" placeholder="Masukan Jabatan Pemesan" required>
                            </div>
                            <!-- <div class="form-group">
						  <label for="nip">NIP / NIK</label>
						  <input type="text" class="form-control" name="nip" placeholder="Masukan NIP/NIK pemesan" required>
						</div> -->
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit" class="btn btn-md btn-primary">Lanjutkan</button>
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
    <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- BootsrapSelect -->
    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        $(function() {
            $("#example1").dataTable();
        });
        //Date range picker
        $('#expired').datepicker({
            format: 'dd/mm/yyyy',
            startView: 2,
            autoclose: true
        });
    </script>

</body>

</html>