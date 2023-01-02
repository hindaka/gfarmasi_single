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
$id_obat = isset($_GET['o']) ? $_GET['o'] : 0;
$get_gobat = $db->query("SELECT * FROM gobat WHERE id_obat='" . $id_obat . "'");
$data = $get_gobat->fetch(PDO::FETCH_ASSOC);
$nama = isset($data['nama']) ? $data['nama'] : '';
$kategori = isset($data['kategori']) ? $data['kategori'] : '';
$kadar = isset($data['kadar']) ? $data['kadar'] : '';
$satuan_kadar = isset($data['satuan_kadar']) ? $data['satuan_kadar'] : '';
$satuan_jual = isset($data['satuan_jual']) ? $data['satuan_jual'] : '';
$bentuk_sediaan = isset($data['bentuk_sediaan']) ? $data['bentuk_sediaan'] : '';
$kemasan = isset($data['kemasan']) ? $data['kemasan'] : '';
$spesifikasi_obat = isset($data['spesifikasi_obat']) ? $data['spesifikasi_obat'] : '';
$keterangan = isset($data['keterangan']) ? $data['keterangan'] : '';
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
    <!-- daterange picker -->
    <link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
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
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Obat telah diproses
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data Obat gagal diupdate
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <section class="content-header">
                <h1>
                    Edit
                    <small>data obat</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Edit</li>
                    <li class="active">Data Obat</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="alert alert-info">Field yang bertandakan <span style="color:red">*</span> <b>WAJIB</b> diisi dengan <b>BAIK & BENAR</b></div>
                <div class="box">
                    <div class="box-header">
                        <i class="fa fa-user"></i>
                        <h3 class="box-title">Input data obat</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="edit_master_single_acc.php" method="post">
                        <input type="hidden" id="id_obat" name="id_obat" value="<?php echo $id_obat; ?>">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="namaobat">NAMA OBAT <span style="color:red;">* (Nama generik + Kadar + Satuan kadar + Bentuk Sediaan + Kemasan)</span></label>
                                <input type="text" class="form-control" id="namaobat" name="namaobat" placeholder="Nama Obat" value="<?= $nama; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="jenis">JENIS/KATEGORI <span style="color:red;">*</span></label>
                                <select class="form-control" name="kategori" id="kategori" required>
                                    <?php
                                    if ($kategori == 'obat') {
                                        echo '<option value="">--Pilih Kategori--</option>
                                            <option value="Obat" selected>OBAT</option>
                                            <option value="BMHP">BHP</option>';
                                    } else if ($kategori == 'bmhp') {
                                        echo '<option value="">--Pilih Kategori--</option>
                                            <option value="Obat">OBAT</option>
                                            <option value="BMHP" selected>BHP</option>';
                                    } else {
                                        echo '<option value="">--Pilih Kategori--</option>
                                            <option value="Obat">OBAT</option>
                                            <option value="BMHP">BHP</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kemasan">KADAR <span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="kadar" name="kadar" placeholder="Masukan Kadar" value="<?= $kadar; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kemasan">SATUAN KADAR <span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="satuan_kadar" name="satuan_kadar" placeholder="satuan kadar" value="<?= $satuan_kadar; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="satuan">SATUAN JUAL <span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="satuan_jual" name="satuan_jual" placeholder="Satuan jual" value="<?= $satuan_jual; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="bentuk">BENTUK SEDIAAN <span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="bentuk" name="bentuk" placeholder="Bentuk sediaan" value="<?= $bentuk_sediaan; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="kemasan">KEMASAN <span style="color:red;">*</span></label>
                                <input type="text" class="form-control" id="kemasan" name="kemasan" placeholder="Kemasan" value="<?= $kemasan; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="spesifikasi">SPESIFIKASI OBAT <span style="color:red;">*</span></label>
                                <textarea name="spesifikasi" rows="3" class="form-control" required><?= $spesifikasi_obat; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="fornas">FORNAS <span style="color:red;">*</span></label>
                                <select class="form-control" name="fornas" required>
                                    <option value="">Pilih Fornas</option>
                                    <option value="-">Bukan FORNAS</option>
                                    <option value="GF">Generik Formularium</option>
                                    <option value="GNF">Generik Non Formularium</option>
                                    <option value="NGF">Non Generik Formularium</option>
                                    <option value="NGNF">Non Generik Non Formularium</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="formularium">FORMULARIUM RS <span style="color:red;">*</span></label>
                                <select class="form-control" name="formularium" required>
                                    <option value="">Pilih FORMULARIUM RS</option>
                                    <option value="ya">Ya</option>
                                    <option value="tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">KETERANGAN / CATATAN TAMBAHAN <span style="color:red;">*</span></label>
                                <textarea name="keterangan" rows="3" class="form-control"><?= $keterangan; ?></textarea>
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
        $(function() {
            $("#example1").dataTable();
            $('#example2').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "bInfo": true,
                "bAutoWidth": false
            });
        });
        //Date range picker
        $('#tanggalf').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true
        });
    </script>

</body>

</html>