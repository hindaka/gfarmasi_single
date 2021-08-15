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

$h3 = $db->query("SELECT nama_perusahaan FROM supplier ORDER BY nama_perusahaan ASC");
$supplier = $h3->fetchAll(PDO::FETCH_ASSOC);
$get_petugas = $db->query("SELECT p.id_petugas,peg.nama FROM petugas p INNER JOIN pegawai peg ON(p.id_pegawai=peg.id_pegawai) WHERE p.instalasi='GFARMASI'");
$total_peg = $get_petugas->rowCount();
$petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);
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
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Content Header (Page header) -->
            <!-- pesan feedback -->
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diinput
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Perusahaan tidak terdaftar didalam Data Supplier.<br>Silakan daftarkan terlebih dahulu pada menu pengaturan &raquo; supplier.
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi berhasil dibatalkan
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <section class="content-header">
                <h1>
                    Transaksi
                    <small>obat masuk</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Transaksi</li>
                    <li class="active">Obat Masuk</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-user"></i>
                        <h3 class="box-title">Input data faktur</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="masukacc.php" method="post">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tanggalf">Tanggal faktur <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="tanggalf" name="tanggalf" placeholder="Tanggal" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nofaktur">Nomor faktur <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="nofaktur" name="nofaktur" placeholder="No. Faktur" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jatuh_tempo">Tanggal Jatuh Tempo <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal Jatuh tempo" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sumber">Sumber Dana <span style="color:red">*</span></label>
                                        <select class="form-control" name="sumber" required>
                                            <option value="">---Pilih Sumber Dana---</option>
                                            <option value="APBD">APBD</option>
                                            <option value="BLUD">BLUD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Dana Pembayaran <span style="color:red">*</span></label>
                                        <select name="dana_pembayaran" id="dana_pembayaran" class="form-control" required>
                                            <option value="">---Pilih Dana Pembayaran ---</option>
                                            <option value="apbd">APBD</option>
                                            <option value="blud">BLUD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">E-Katalog <span style="color:red">*</span></label>
                                        <select name="ekatalog" id="ekatalog" class="form-control" required>
                                            <option value="">--- Pilih Salah Satu ---</option>
                                            <option value="ya">Ya</option>
                                            <option value="tidak">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Cara Bayar <span style="color:red">*</span></label>
                                        <select name="cara_bayar" id="cara_bayar" class="form-control" required>
                                            <option value="">--- Cara Bayar ---</option>
                                            <option value="LS">LS</option>
                                            <option value="GU">GU</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-md-4">
												<div class="form-group">
												  <label for="">Pembelian <span style="color:red">*</span></label>
												  <select name="pembelian" id="pembelian" class="form-control" required>
												  	<option value="">--- Pilih Pembelian ---</option>
														<option value="Dalam">Dalam</option>
														<option value="Luar">Luar</option>
												  </select>
												</div>
											</div> -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Pembayaran Tunai <span style="color:red">*</span></label>
                                        <select name="pembayaran_tunai" id="pembayaran_tunai" class="form-control" required>
                                            <option value="">--- Pilih Pembayaran ---</option>
                                            <option value="ya">Ya</option>
                                            <option value="tidak">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Jenis Faktur <span style="color:red">*</span></label>
                                        <select name="jenis_faktur" id="jenis_faktur" class="form-control" required>
                                            <option value="">--- Pilih Jenis Faktur ---</option>
                                            <option value="obat">OBAT</option>
                                            <option value="bmhp">BMHP</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="perusahaan">Perusahaan <span style="color:red">*</span></label>
                                <select class=" form-control selectpicker" data-live-search="true" name="perusahaan" required>
                                    <option value="">---Pilih Supplier---</option>
                                    <?php
                                    foreach ($supplier as $s) {
                                        echo "<option value='" . $s['nama_perusahaan'] . "'>" . $s['nama_perusahaan'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="petugas">Petugas Penerima Faktur <span style="color:red">*</span></label>
                                <select class="form-control selectpicker" data-live-search="true" name="petugas" required>
                                    <option value="">---Pilih Petugas---</option>
                                    <?php
                                    foreach ($petugas as $p) {
                                        echo "<option value='" . $p['id_petugas'] . "'>" . $p['nama'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control" required>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
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
    <!-- BootsrapSelect -->
    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        //Date range picker
        $('#tanggalf').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true
        });
        $('#jatuh_tempo').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true
        });
    </script>

</body>

</html>