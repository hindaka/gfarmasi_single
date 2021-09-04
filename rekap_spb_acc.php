<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
include("../inc/set_gfarmasi.php");
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
$bulanawal = isset($_POST["bulanawal"]) ? $_POST['bulanawal'] : '';
$bulanakhir = isset($_POST["bulanakhir"]) ? $_POST['bulanakhir'] : '';
$tahun = isset($_POST["tahun"]) ? $_POST['tahun'] : '';
$gabung1 = $tahun . $bulanawal;
$gabung2 = $tahun . $bulanakhir;

$sql = "SELECT op.*,o.id_kartu,o.namaobat,g.satuan,o.volume,o.sumber,o.ruang as tujuan FROM `obatkeluar_parent` op INNER JOIN obatkeluar o ON(o.id_parent=op.id_obatkeluar_parent) LEFT JOIN gobat g ON(o.namaobat=g.nama AND o.sumber=g.sumber) WHERE CAST(CONCAT(SUBSTRING(o.tanggal,7,4),SUBSTRING(o.tanggal,4,2)) as UNSIGNED) >= '$gabung1' AND CAST(CONCAT(SUBSTRING(o.tanggal,7,4),SUBSTRING(o.tanggal,4,2)) as UNSIGNED) <= '$gabung2'";
$h2 = $db->query($sql);
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version_gfarmasi; ?> | Modul Gudang Farmasi</title>
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
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diupdate
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
                                <h3 class="box-title">Data Rekapitulasi Obat Keluar</h3>
                                <button onclick="window.location.href='export_spb.php?tgl1=<?php echo $gabung1; ?>&tgl2=<?php echo $gabung2; ?>&bln1=<?php echo $bulanawal ?>&bln2=<?php echo $bulanakhir ?>'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export To Excel</button>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th>Tanggal Keluar</th>
                                                <th>Nomor Nota Dinas</th>
                                                <th>Nomor SPB</th>
                                                <th>Nomor SPBB</th>
                                                <th>Nama Obat</th>
                                                <th>Satuan</th>
                                                <th>Jenis/Kategori</th>
                                                <th>Merk/Pabrikan</th>
                                                <th>Sumber</th>
                                                <th>Volume</th>
                                                <th>Ruang / Tujuan</th>
                                                <th>Harga Beli</th>
                                                <th>No. Batch</th>
                                                <th>Expired</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($data2 as $r2) {
                                                $h3 = $db->query("SELECT harga_beli,no_batch,expired,jenis,merk,pabrikan FROM kartu_stok_gobat WHERE id_kartu='" . $r2['id_kartu'] . "'");
                                                $r3 = $h3->fetch(PDO::FETCH_ASSOC);
                                                $harga_beli = isset($r3['harga_beli']) ? $r3['harga_beli'] : '';
                                                $batch = isset($r3["no_batch"]) ? $r3["no_batch"] : '';
                                                $exp = isset($r3["expired"]) ? $r3["expired"] : '';
                                                $jenis = isset($r3['jenis']) ? $r3['jenis'] : '';
                                                if ($jenis == 'generik') {
                                                    $merk_pabrikan = isset($r3['pabrikan']) ? $r3['pabrikan'] : '';
                                                } else if ($jenis == 'non generik') {
                                                    $merk_pabrikan = isset($r3['merk']) ? $r3['merk'] : '';
                                                } else {
                                                    $merk_pabrikan = isset($r3['pabrikan']) ? $r3['pabrikan'] : '';
                                                }

                                                echo "<tr>
                                                        <td>" . $r2['tanggal_keluar'] . "</td>
                                                        <td>" . $r2['nomor'] . "</td>
                                                        <td>" . $r2['no_spb'] . "</td>
                                                        <td>" . $r2['no_spbb'] . "</td>
                                                        <td>" . $r2['namaobat'] . "</td>
                                                        <td>" . $r2['satuan'] . "</td>
                                                        <td>" . $jenis . "</td>
                                                        <td>" . $merk_pabrikan . "</td>
                                                        <td>" . $r2['sumber'] . "</td>
                                                        <td>" . $r2['volume'] . "</td>
                                                        <td>" . $r2['tujuan'] . "</td>
                                                        <td>Rp." . number_format($harga_beli, $digit_akhir, ',', '.') . "</td>
                                                        <td>" . $batch . "</td>
                                                        <td>" . $exp . "</td>
                                                    </tr>";
                                            }

                                            ?>
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
    <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
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
            $("#example1").dataTable();
        });
    </script>

</body>

</html>