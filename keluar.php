<?php
ob_start();
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
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
$hariini = date("d/m/Y");
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$tipe = isset($_GET['type']) ? trim($_GET['type']) : '';
//cek data sudah posting atau belum
$cek_data_parent = $db->prepare("SELECT * FROM obatkeluar_parent p WHERE id_obatkeluar_parent=:id");
$cek_data_parent->bindParam(":id", $id_parent, PDO::PARAM_INT);
$cek_data_parent->execute();
$check = $cek_data_parent->fetch(PDO::FETCH_ASSOC);
if ($check['status_keluar'] == 'posting') {
    header("location: list_keluar_draft.php?status=5");
    exit('redirect to homepage');
}
//tampilkan data obat keluar
$h3 = $db->prepare("SELECT * FROM obatkeluar_parent p INNER JOIN warehouse w ON(w.id_warehouse=p.id_warehouse) WHERE id_obatkeluar_parent=:id");
$h3->bindParam(":id", $id_parent, PDO::PARAM_INT);
$h3->execute();
$parent = $h3->fetch(PDO::FETCH_ASSOC);
//rincian obat keluar
$list_obat_keluar = $db->prepare("SELECT ob.*,k.harga_beli,k.no_batch,k.expired,k.merk FROM obatkeluar ob INNER JOIN kartu_stok_gobat k ON(k.id_kartu=ob.id_kartu) WHERE ob.id_parent=:id");
$list_obat_keluar->bindParam(":id", $id_parent, PDO::PARAM_INT);
$list_obat_keluar->execute();
$data3 = $list_obat_keluar->fetchAll(PDO::FETCH_ASSOC);
$total_rincian = $list_obat_keluar->rowCount();

$h4 = $db->query("SELECT k.id_kartu,k.id_obat,g.nama,k.sumber_dana,k.merk,k.no_batch,k.volume_kartu_akhir,k.no_batch,k.jenis,k.pabrikan,g.flag_single_id,k.harga_beli FROM kartu_stok_gobat k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.volume_kartu_akhir>0 AND k.in_out='masuk'");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
$tuslah_aktif = $db->query("SELECT * FROM tuslah WHERE aktif='y'");
$tuslah = $tuslah_aktif->fetch(PDO::FETCH_ASSOC);
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
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- BootsrapSelect -->
    <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
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

        <?php include("header.php"); ?>
        <?php include "menu_index.php"; ?>
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
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat berhasil dihapus
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Stok obat tidak mencukupi
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
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
                <div class="alert alert-info">Pengurangan Nilai Stok dilakukan jika sudah mengkonfirmasi pengeluaran dengan menekan tombol SIMPAN.</div>
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-4">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-medkit"></i>
                                <h3 class="box-title">Data Obat</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="keluaracc.php" method="post">
                                <input type="hidden" name="parent" id="parent" value="<?php echo $id_parent; ?>">
                                <input type="hidden" name="type" id="type" value="<?php echo $tipe; ?>">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="tglkeluar">Tanggal</label>
                                        <input type="text" class="form-control" id="tglkeluar" name="tglkeluar" value="<?php echo $parent['tanggal_keluar']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse / Mini Depo</label>
                                        <input type="text" class="form-control" id="warehouse" name="warehouse" value="<?php echo $parent['nama_ruang'] ?>" readonly>
                                        <input type="hidden" class="form-control" id="id_warehouse" name="id_warehouse" value="<?php echo $parent['id_warehouse'] ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="pemesan">Pemesan</label>
                                        <input type="text" name="pemesan" class="form-control" id="pemesan" value="<?php echo $parent['pemesan']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="tuslah">Tuslah</label>
                                        <?php if (($parent['nama_ruang'] == 'Poli Anak') && ($parent['nama_ruang'] == 'Poli Kandungan')) : ?>
                                            <input type="text" class="form-control" id="tuslah_rajal" value="<?php echo number_format($tuslah['rajal'], 0, ',', '.'); ?>" readonly>
                                            <input type="hidden" name="tuslah" value="1">
                                        <?php else : ?>
                                            <input type="text" class="form-control" id="tuslah_ranap" value="<?php echo number_format($tuslah['ranap'], 0, ',', '.'); ?>" readonly>
                                            <input type="hidden" name="tuslah" value="3">
                                        <?php endif; ?>
                                        <input type="hidden" name="id_tuslah" id="id_tuslah" value="<?php echo $tuslah['id_tuslah']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="namaobat">Nama obat <span style="color:red">*</span></label>
                                        <select class="form-control selectpicker" data-live-search="true" name="id_obat" id="id_obat" required>
                                            <option value="">Pilih Obat</option>
                                            <?php
                                            foreach ($data4 as $r4) {   
                                                if($r4['flag_single_id']=='new'){
                                                    $text_name = "(single id)".$r4['nama'] . "(" . $r4['merk'] . ") |" . $r4['volume_kartu_akhir'] . " | " . $r4['sumber_dana'] . " | " . $r4['no_batch'] . " | Rp.".$r4['harga_beli'];
                                                }else{
                                                    $text_name = $r4['nama'] . "(" . $r4['merk'] . ")|" . $r4['volume_kartu_akhir'] . " | " . $r4['sumber_dana'] . " | " . $r4['no_batch'] . " | Rp.".$r4['harga_beli'];
                                                }                                             
                                                echo "<option value='" . $r4['id_kartu'] . "|" . $r4['id_obat'] . "|" . $r4['volume_kartu_akhir'] . "'>".$text_name."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="volume">Volume <span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="volume" name="volume" placeholder="Volume" autocomplete="off" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="tambah">&nbsp;</label>
                                        <button id="btnTambah" type="button" class="btn btn-primary">Tambah Obat Keluar</button>
                                    </div>
                                </div><!-- /.box-body -->

                        </div>
                    </div><!-- /.left column -->
                    <!-- right column -->
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-medkit"></i>
                                <h3 class="box-title">Rincian Obat yang akan keluar ke <?php echo $parent['nama_ruang']; ?></h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <div class="form-group">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="info">
                                                <th>Nama</th>
                                                <th>Sumber</th>
                                                <th>Volume</th>
                                                <th>Harga Satuan</th>
                                                <th>No Batch</th>
                                                <th>Expired</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        foreach ($data3 as $r3) {
                                            $volumeformat = number_format($r3['volume'], 0, ".", ".");
                                            echo "<tr>
                                                    <td>" . $r3['namaobat'] . "<br><span style='font-size:10px'>Merk: ".$r3['merk']."<br>Jenis: ".$r3['jenis']."<br>Pabrikan: ".$r3['pabrikan']."</span></td>
                                                    <td>" . $r3['sumber'] . "</td>
                                                    <td>" . $volumeformat . "</td>
                                                    <td>" . number_format($r3['harga_beli'], 4, ',', '.') . "</td>
                                                    <td>" . $r3['no_batch'] . "</td>
                                                    <td>" . $r3['expired'] . "</td>
                                                    <td><a class='btn btn-sm btn-danger' href='hapuskeluar.php?parent=" . $id_parent . "&id=" . $r3['id_obatkeluar'] . "&kartu=" . $r3['id_kartu'] . "&type=" . $tipe . "'><i class='fa fa-trash'></i> Hapus</a></td>
                                                </tr>";
                                        }
                                        ?>
                                    </table>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <?php
                                if ($total_rincian > 0) {
                                    echo "<a class=\"btn btn-app bg-blue\" href=\"save_keluar.php?parent=" . $id_parent . "&mode=draft\"><i class=\"fa fa-save\"></i> Draft</a>";
                                    echo "<a class=\"btn btn-app bg-green\" href=\"save_keluar.php?parent=" . $id_parent . "&mode=save\"><i class=\"fa fa-save\"></i> Simpan</a>";
                                    // echo "<a class=\"btn btn-app\" href=\"cancel_keluar.php?parent=".$id_parent."\"><i class=\"fa fa-trash\"></i> Batal</a>";
                                } else {
                                    echo "<a class=\"btn btn-app bg-red\" href=\"cancel_keluar.php?parent=" . $id_parent . "\"><i class=\"fa fa-trash\"></i> Batalkan</a>";
                                }
                                ?>
                            </div>
                        </div>
                    </div><!-- /.right column -->
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
    <!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- BootsrapSelect -->
    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <script src='../plugins/sweetalert/sweetalert.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        $(function() {
            $("#example1").dataTable();
            $('#btnTambah').click(function(ev) {
                ev.preventDefault();
                let parent = $('#parent').val();
                let type = $('#type').val();
                let tglkeluar = $('#tglkeluar').val();
                let warehouse = $('#warehouse').val();
                let id_warehouse = $('#id_warehouse').val();
                let pemesan = $('#pemesan').val();
                let tuslah = $('#tuslah').val();
                let id_tuslah = $('#id_tuslah').val();
                let selectedItem = $('#id_obat').val();
                let sp = selectedItem.split("|");
                let id_kartu = sp[0];
                let id_obat = sp[1];
                let volume_akhir = sp[2];
                let volume = $('#volume').val();
                if (warehouse == '') {
                    swal('Peringatan!', 'Nama Depo/Ruangan pemesan belum dipilih', 'warning');
                    return;
                }
                if (pemesan == '') {
                    swal('Peringatan!', 'Nama pemesan belum dipilih', 'warning');
                    return;
                }
                if (id_kartu == '') {
                    swal('Peringatan!', 'Nama Obat/BMHP belum dipilih', 'warning');
                    return;
                }
                if ((volume == '') || (parseInt(volume) == 0)) {
                    swal('Peringatan!', 'Volume belum diisi', 'warning');
                    return;
                }
                if (parseInt(volume) > parseInt(volume_akhir)) {
                    swal('Peringatan', 'Volume barang yang akan dimutasi tidak boleh lebih dari ' + volume_akhir, 'warning');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "ajax_data/keluar_acc.php",
                    data: {
                        "parent": parent,
                        "type": type,
                        "tglkeluar": tglkeluar,
                        "tuslah": tuslah,
                        "id_tuslah": id_tuslah,
                        "pemesan": pemesan,
                        "warehouse": warehouse,
                        "id_warehouse": id_warehouse,
                        "id_kartu": id_kartu,
                        "volume": volume
                    },
                    success: function(response) {
                        console.log(response);
                        let res = JSON.parse(response);
                        swal(res.title, res.msg, res.icon).then((_val) => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        console.warn(err);
                    }
                });
            });
        });
        //Date range picker
        $('#tglkeluar').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            autoclose: true
        });
    </script>

</body>

</html>