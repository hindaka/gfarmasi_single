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
$all_stok = $db->query("SELECT k.id_obat,SUM(k.volume_kartu_akhir) as sisa_stok,k.no_batch,k.expired,k.harga_beli,k.harga_jual_non_tuslah,k.sumber_dana FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.in_out='masuk' AND k.volume_kartu_akhir>0 AND g.flag_single_id='old' AND g.jenis!='bhp' GROUP BY k.id_obat,k.no_batch,k.expired,k.harga_beli ORDER BY k.id_obat ASC");
$all = $all_stok->fetchAll(PDO::FETCH_ASSOC);
$total_data_sisa = $all_stok->rowCount();
//get distinct pabrikan
$get_pabrikan = $db->query("SELECT DISTINCT(pabrikan) as pabrik FROM migrasi_obat");
$list_pabrikan = $get_pabrikan->fetchAll(PDO::FETCH_ASSOC);
//get distinct merk
$get_merk = $db->query("SELECT DISTINCT(merk) as merk FROM migrasi_obat");
$list_merk = $get_merk->fetchAll(PDO::FETCH_ASSOC);
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
                    Daftar Obat yang belum migrasi
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
                                <h3 class="box-title">Daftar Obat yang belum migrasi</h3>
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
                                                <th>ID LAMA</th>
                                                <th>LINK ID SINGLE</th>
                                                <th>Nama Obat / Bmhp</th>
                                                <th>Jenis</th>
                                                <th>Merk</th>
                                                <th>Pabrikan</th>
                                                <th>Harga Beli</th>
                                                <th>No Batch</th>
                                                <th>Expired</th>
                                                <th>Jumlah</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($total_data_sisa > 0) {
                                                $migrasi_data = $db->prepare("SELECT * FROM migrasi_obat WHERE id_obat_lama=:id_obat_lama AND no_batch=:no_batch AND expired=:expired AND harga_beli=:harga_beli");
                                                $check_jenis = $db->prepare("SELECT * FROM migrasi_obat WHERE id_obat_lama=:id_obat_lama GROUP BY id_obat_lama");
                                                $i = 1;
                                                foreach ($all as $row) {
                                                    //set variable
                                                    $id_obat_lama = isset($row['id_obat']) ? $row['id_obat'] : 0;
                                                    $sisa_stok = isset($row['sisa_stok']) ? $row['sisa_stok'] : 0;
                                                    $no_batch_baru = isset($row['no_batch']) ? $row['no_batch'] : 0;
                                                    $expired_baru = isset($row['expired']) ? $row['expired'] : 0;
                                                    $sumber_dana_baru = isset($row['sumber_dana']) ? $row['sumber_dana'] : 0;
                                                    $harga_beli_baru = isset($row['harga_beli']) ? $row['harga_beli'] : 0;
                                                    $harga_jual_non_tuslah_baru = isset($row['harga_jual_non_tuslah']) ? $row['harga_jual_non_tuslah'] : 0;
                                                    //set bind value
                                                    $migrasi_data->bindParam(":id_obat_lama", $id_obat_lama, PDO::PARAM_INT);
                                                    $migrasi_data->bindParam(":no_batch", $no_batch_baru, PDO::PARAM_STR);
                                                    $migrasi_data->bindParam(":expired", $expired_baru, PDO::PARAM_STR);
                                                    $migrasi_data->bindParam(":harga_beli", $harga_beli_baru, PDO::PARAM_STR);
                                                    $migrasi_data->execute();
                                                    $mg = $migrasi_data->fetch(PDO::FETCH_ASSOC);
                                                    $total_found = $migrasi_data->rowCount();
                                                    $vol_lama = isset($mg['vol']) ? $mg['vol'] : 0;
                                                    $id_obat_single = isset($mg['id_obat']) ? $mg['id_obat'] : 0;
                                                    $total_kartu_migrasi = 0;
                                                    if ($total_found == 0) {
                                                        $check_jenis->bindParam(":id_obat_lama", $id_obat_lama, PDO::PARAM_INT);
                                                        $check_jenis->execute();
                                                        $gj = $check_jenis->fetch(PDO::FETCH_ASSOC);
                                                        $id_obat_new = isset($gj['id_obat']) ? $gj['id_obat'] : 0;
                                                        $jenis = isset($gj['jenis']) ? $gj['jenis'] : '';
                                                        $merk = isset($gj['merk']) ? $gj['merk'] : '';
                                                        $pabrikan = isset($gj['pabrikan']) ? $gj['pabrikan'] : '';
                                                        $vol_ref = isset($gj['vol']) ? $gj['vol'] : '';
                                                        //get nama obat
                                                        $get_nama = $db->query("SELECT id_obat,nama FROM gobat WHERE old_id_ref LIKE '%" . $id_obat_lama . "%' LIMIT 1");
                                                        $total_id_baru = $get_nama->rowCount();
                                                        if ($total_id_baru > 0) {
                                                            $n = $get_nama->fetch(PDO::FETCH_ASSOC);
                                                            $nama_obat_single = isset($n['nama']) ? $n['nama'] : '';
                                                            $id_obat_single = isset($n['id_obat']) ? $n['id_obat'] : '';
                                                            $text_single = $id_obat_single;
                                                        } else {
                                                            $text_single = "Setting di ref obat single!!";
                                                            $id_obat_single = 0;
                                                        }

                                                        $get_nama_lama = $db->query("SELECT nama FROM gobat WHERE id_obat='" . $id_obat_lama . "' LIMIT 1");
                                                        $n_lama = $get_nama_lama->fetch(PDO::FETCH_ASSOC);
                                                        $nama_lama = isset($n_lama['nama']) ? $n_lama['nama'] : '-';
                                                        $tr = '';
                                                        if ($check_jenis->rowCount() == 0) {
                                                            $tr .= '<tr>
                                                                <td>#</td>
                                                                <td>' . $id_obat_lama . '</td>
                                                                <td>' . $id_obat_single . '</td>
                                                                <td>' . $nama_lama . '</td>
                                                                <td>
                                                                    <select id="jenis' . $i . '" name="jenis' . $i . '" class="form-control">
                                                                    <option value="">--pilih jenis--</option>
                                                                    <option value="generik">Generik</option>
                                                                    <option value="non generik">Non Generik</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select id="merk' . $i . '" name="merk' . $i . '" class="form-control select2">
                                                                    <option value="">--pilih merk--</option>';
                                                            foreach ($list_merk as $m) {
                                                                $tr .= '<option value="' . $m['merk'] . '">' . $m['merk'] . '</option>';
                                                            }
                                                            $tr .= '</select>
                                                                </td>
                                                                <td>
                                                                    <select id="pabrikan' . $i . '" name="pabrikan' . $i . '" class="form-control select2">
                                                                    <option value="">--pilih pabrikan--</option>';
                                                            foreach ($list_pabrikan as $p) {
                                                                $tr .= '<option value="' . $p['pabrik'] . '">' . $p['pabrik'] . '</option>';
                                                            }
                                                            $tr .= '</select>
                                                                </td>
                                                                <td>' . $harga_beli_baru . '</td>
                                                                <td>' . $no_batch_baru . '</td>
                                                                <td>' . $expired_baru . '</td>
                                                                <td>' . $sisa_stok . '</td>
                                                                <td><button id="btn_' . $i . '" onclick="saveData(this)" data-id_single="' . $id_obat_single . '" data-no_batch="' . $no_batch_baru . '" data-expired="' . $expired_baru . '" data-harga_beli="' . $harga_beli_baru . '" data-harga_jual="' . $harga_jual_non_tuslah_baru . '" data-id_lama="' . $id_obat_lama . '" data-vol="' . $sisa_stok . '" data-sumber_dana="' . $sumber_dana_baru . '" class="btn btn-sm btn-success"><i class="fa fa-check"></i></button></td>
                                                            </tr>';
                                                            echo $tr;
                                                            $i++;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
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
    <script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
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

        function saveData(ele) {
            console.log(ele);
            let id = ele.id;
            let id_single = $('#' + id).data('id_single');
            if (id_single == 0) {
                alert("Silakan Setting Ref pada id obat single terlebih dahulu ");
                return;
            }
            let id_lama = $('#' + id).data('id_lama');
            let no_batch = $('#' + id).data('no_batch');
            let expired = $('#' + id).data('expired');
            let vol = $('#' + id).data('vol');
            let harga_beli = $('#' + id).data('harga_beli');
            let harga_jual = $('#' + id).data('harga_jual');
            let sumber_dana = $('#' + id).data('sumber_dana');
            let sp = id.split("_");
            let nomor = sp[1];
            let jenis = $('#jenis' + nomor).val();
            if (jenis == "") {
                alert("Jenis Baris " + nomor + " Belum dipilih!");
                return;
            }
            let merk = $('#merk' + nomor).val();
            if (jenis == 'non generik' && merk == '') {
                alert("Merk Wajib diisi karena tipe jenis yang dipilih Non Generik");
                return;
            }
            let pabrikan = $('#pabrikan' + nomor).val();
            if (jenis == 'generik' && pabrikan == '') {
                alert("Pabrikan Wajib diisi karena tipe jenis yang dipilih Generik");
                return;
            }
            $.ajax({
                type: "POST",
                url: "ajax_data/mapping_single_obat.php",
                data: {
                    "id_single": id_single,
                    "id_lama": id_lama,
                    "jenis": jenis,
                    "pabrikan": pabrikan,
                    "merk": merk,
                    "no_batch": no_batch,
                    "expired": expired,
                    "vol": vol,
                    "harga_beli": harga_beli,
                    "harga_jual": harga_jual,
                    "sumber_dana": sumber_dana,
                },
                success: function(response) {
                    console.log(response);
                    let res = JSON.parse(response);
                    if (res.code == 200) {
                        // sukses
                        swal('Berhasil', res.msg, 'success').then((_val) => {
                            location.reload();
                        });
                    } else {
                        // error
                        swal('ERROR', res.msg, 'error');
                    }
                },
                error: function(err) {
                    console.warn(err);
                }
            });
        }
        $(function() {
            $('.select2').select2({
                placeholder: "Pilih/Inputkan Nama Baru",
                tags: true,
                allowClear: true
            });
            // $('#submitData').on('click', function(e) {
            //     e.preventDefault();
            //     let reff_id = $('#reff_id').val();
            //     let reff_old_id = $('#reff_old_id').val();
            //     $.ajax({
            //         type: "POST",
            //         url: "ajax_data/update_reff_old_id.php",
            //         data: {
            //             "reff_id": reff_id,
            //             "reff_old_id": reff_old_id
            //         },
            //         success: function(response) {
            //             let res = JSON.parse(response);
            //             alert(res.msg);
            //             location.reload();
            //         },
            //         error: function(err) {
            //             console.warn(err);
            //         }
            //     });
            // });
            var master_obat = $('#example1').DataTable();
        });
    </script>

</body>

</html>