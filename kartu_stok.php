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
$id_obat = isset($_GET['o']) ? $_GET['o'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$total_data = 0;
//header
$header = $db->query("SELECT * FROM gobat WHERE id_obat='" . $id_obat . "'");
$head = $header->fetch(PDO::FETCH_ASSOC);
if ($filter == 'on') {
    $transaksi = isset($_GET['transaksi']) ? $_GET['transaksi'] : '';
    $jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';
    $merk = isset($_GET['merk']) ? $_GET['merk'] : '';
    $pabrikan = isset($_GET['pabrikan']) ? $_GET['pabrikan'] : '';
    if ($transaksi == 'masuk') {
        $qr = "in_out='masuk' AND";
    } else if ($transaksi == 'keluar') {
        $qr = "in_out='keluar' AND";
    } else {
        $qr = "";
    }
    if ($jenis == 'generik') {
        $h2 = $db->prepare("SELECT k.*,IFNULL(a.nama,'-') as petugas FROM kartu_stok_gobat k LEFT JOIN anggota a ON(k.mem_id=a.mem_id) WHERE " . $qr . " id_obat=:id_obat AND jenis=:jenis AND pabrikan LIKE :pabrikan ORDER BY id_kartu ASC");
        $h2->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
        $h2->bindParam(":jenis", $jenis, PDO::PARAM_STR);
        $h2->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
    } else if ($jenis == 'non generik') {
        $h2 = $db->prepare("SELECT k.*,IFNULL(a.nama,'-') as petugas FROM kartu_stok_gobat k LEFT JOIN anggota a ON(k.mem_id=a.mem_id) WHERE " . $qr . " id_obat=:id_obat AND jenis=:jenis AND merk LIKE :merk ORDER BY id_kartu ASC");
        $h2->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
        $h2->bindParam(":jenis", $jenis, PDO::PARAM_STR);
        $h2->bindParam(":merk", $merk, PDO::PARAM_STR);
    } else if ($jenis == 'bmhp') {
        $h2 = $db->prepare("SELECT k.*,IFNULL(a.nama,'-') as petugas FROM kartu_stok_gobat k LEFT JOIN anggota a ON(k.mem_id=a.mem_id) WHERE " . $qr . " id_obat=:id_obat AND jenis=:jenis AND pabrikan LIKE :pabrikan ORDER BY id_kartu ASC");
        $h2->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
        $h2->bindParam(":jenis", $jenis, PDO::PARAM_STR);
        $h2->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
    } else {
        $h2 = $db->prepare("SELECT k.*,IFNULL(a.nama,'-') as petugas FROM kartu_stok_gobat k LEFT JOIN anggota a ON(k.mem_id=a.mem_id) WHERE " . $qr . " id_obat=:id_obat ORDER BY id_kartu ASC");
        $h2->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
    }
    $h2->execute();
    $data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
    $total_data = $h2->rowCount();
}
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
    <link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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
        .select2-result__nama {
            height: 30px;
        }

        .select2-result__nama>span {
            color: green;
            font-weight: bold;
        }
    </style>
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
                    Kartu Stok
                    <small>Persediaan Obat Farmasi</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Kartu Stok</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-tasks"></i>
                                <h3 class="box-title">Kartu Stok Obat <?php echo $head['nama']; ?></h3>
                                <!-- <a onclick="window.location.href='export_kartu.php?o=<?php echo $id_obat; ?>'" class="btn btn-success pull-right" target="_blank"><i class="fa fa-download"></i> Export Data Kartu</a> -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <fieldset>
                                    <legend>Filter Data Kartu Stok :</legend>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <form class="form-inline">
                                                <input type="hidden" id="o" name="o" value="<?php echo $id_obat; ?>">
                                                <input type="hidden" id="filter" name="filter" value="on">
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <select name="transaksi" id="transaksi" class="form-control" required>
                                                        <option value="">--Pilih Transaksi--</option>
                                                        <option value="all">All</option>
                                                        <option value="masuk">Barang Masuk</option>
                                                        <option value="keluar">Barang Keluar</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <select name="jenis" id="jenis" class="form-control" required>
                                                        <option value="">Pilih Jenis</option>
                                                        <option value="all">All</option>
                                                        <option value="generik">Generik</option>
                                                        <option value="non generik">Non Generik</option>
                                                        <option value="bmhp">BMHP</option>
                                                    </select>
                                                </div>
                                                <div id="merk_block" class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <select name="merk" id="merk" class="form-control select_merk">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div id="pabrikan_block" class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <select name="pabrikan" id="pabrikan" class="form-control select_pabrikan">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for=""></label>
                                                    <input type="submit" id="searchBtn" class="btn btn-md btn-primary" value="Cari Data">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" onclick="resetForm()" class="btn btn-md bg-maroon">Reset</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="info">
                                                <th>Tanggal Pencatatan</th>
                                                <th>No Faktur</th>
                                                <th>Perusahaan</th>
                                                <th>Proses</th>
                                                <th>Tujuan</th>
                                                <th>Expired</th>
                                                <th>No Batch</th>
                                                <th>Jenis</th>
                                                <th>Merk</th>
                                                <th>pabrikan</th>
                                                <th>Sumber Dana</th>
                                                <th>Volume Masuk</th>
                                                <th>Volume Keluar</th>
                                                <th>Volume Sisa</th>
                                                <th>Harga Beli (+ ppn)</th>
                                                <th>Harga E-Katalog</th>
                                                <th>Keterangan</th>
                                                <th>Petugas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($total_data > 0) {
                                                foreach ($data2 as $stok) {
                                                    if ($stok['id_faktur'] == 0) {
                                                        $no_faktur = '-';
                                                        $perusahaan = '-';
                                                    } else {
                                                        $get_faktur = $db->query("SELECT * FROM faktur WHERE id_faktur='" . $stok['id_faktur'] . "'");
                                                        $faktur = $get_faktur->fetch(PDO::FETCH_ASSOC);
                                                        $no_faktur = isset($faktur['no_faktur']) ? $faktur['no_faktur'] : '';
                                                        $perusahaan = isset($faktur['perusahaan']) ? $faktur['perusahaan'] : '';
                                                    }
                                                    if ($stok['in_out'] == 'masuk') {
                                                        $bgcolor = "success";
                                                    } else {
                                                        $bgcolor = "warning";
                                                    }
                                                    if (substr($stok['sumber_dana'], 0, 4) == 'APBD') {
                                                        $sd = '<span class="label bg-maroon">' . $stok['sumber_dana'] . '</span>';
                                                    } else if (substr($stok['sumber_dana'], 0, 4) == 'BLUD') {
                                                        $sd = '<span class="label label-primary">' . $stok['sumber_dana'] . '</span>';
                                                    } else {
                                                        $sd = '<span class="label label-default">' . $stok['sumber_dana'] . '</span>';
                                                    }
                                                    $e_kat = isset($stok['e_kat']) ? $stok['e_kat'] : '';
                                                    if ($e_kat == 'ya') {
                                                        $e_kat_label = '<i class="fa fa-check text-green"></i>';
                                                    } else {
                                                        $e_kat_label = '<i class="fa fa-times text-danger"></i>';
                                                    }
                                                    echo "<tr class='" . $bgcolor . "'>
                                                            <td>" . $stok['created_at'] . "</td>
                                                            <td><a href='#'>" . $no_faktur . "</a></td>
                                                            <td>" . $perusahaan . "</td>
                                                            <td><b>" . ucwords($stok['in_out']) . "</b></td>
                                                            <td>" . $stok['tujuan'] . "</td>
                                                            <td>" . $stok['expired'] . "</td>
                                                            <td>" . $stok['no_batch'] . "</td>
                                                            <td>" . $stok['jenis'] . "</td>
                                                            <td>" . $stok['merk'] . "</td>
                                                            <td>" . $stok['pabrikan'] . "</td>
                                                            <td>" . $sd . "</td>
                                                            <td>" . $stok['volume_in'] . "</td>
                                                            <td>" . $stok['volume_out'] . "</td>
                                                            <td>" . $stok['volume_sisa'] . "</td>
                                                            <td>Rp " . number_format($stok['harga_beli'], $digit_akhir, ',', '.') . "</td>
                                                            <td>" . $e_kat_label . "</td>  
                                                            <td>" . $stok['keterangan'] . "</td>
                                                            <td>" . $stok['petugas'] . "</td>
                                                        </tr>";
                                                }
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
    <script src='../plugins/select2/select2.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        function resetForm() {
            let id_obat = $('#o').val();
            window.location = "kartu_stok.php?o=" + id_obat;
        }
        $(function() {
            $("#example1").dataTable();
            let merk_block = $('#merk_block');
            let pabrikan_block = $('#pabrikan_block');
            merk_block.hide();
            pabrikan_block.hide();
            $('#jenis').change(function() {
                let jenis_selected = $(this).val();
                if (jenis_selected == 'generik') {
                    merk_block.hide();
                    pabrikan_block.show();
                } else if (jenis_selected == 'non generik') {
                    merk_block.show();
                    pabrikan_block.hide();
                } else if (jenis_selected == 'bmhp') {
                    merk_block.hide();
                    pabrikan_block.show();
                } else if (jenis_selected == 'all') {
                    merk_block.hide();
                    pabrikan_block.hide();
                } else {
                    merk_block.hide();
                    pabrikan_block.show();
                }
            });
            let id_obat = $('#o').val();
            $(".select_pabrikan").select2({
                ajax: {
                    url: "ajax_data/get_pabrik_kartu.php",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            id_obat: id_obat,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;

                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Masukan Nama Pabrikan yang dicari',
                allowClear: true,
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
            });
            $(".select_merk").select2({
                ajax: {
                    url: "ajax_data/get_merk_kartu.php",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            id_obat: id_obat,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;

                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Masukan Nama Merk yang dicari',
                allowClear: true,
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
            });

            function formatRepo(repo) {
                let text_name = '';
                if (repo.loading) {
                    return repo.text;
                } else {
                    if (repo.flag == 'new') {
                        text_name = "<span>" + repo.nama + "</span> Tambahkan Sebagai Data Baru";
                    } else {
                        text_name = "<span>" + repo.nama + "</span>";
                    }
                    var $container = $(
                        "<div class='select2-result clearfix'>" +
                        "<div class='select2-result__nama'>" + text_name + "</div>" +
                        "</div>"
                    );
                    return $container;
                }
            }

            function formatRepoSelection(repo) {
                let text_name = '';
                if (repo.id == '') {
                    text_name = repo.text;
                } else {
                    text_name = repo.nama;
                }
                return text_name;
            }
        });
    </script>

</body>

</html>