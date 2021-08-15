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
$id_faktur = isset($_GET["id"]) ? $_GET['id'] : '';
$sumber = isset($_GET['sumber']) ? $_GET['sumber'] : '';
//tampilkan data tindakan
$h3 = $db->query("SELECT * FROM itemfaktur WHERE id_faktur='$id_faktur'");
$data3 = $h3->fetchAll(PDO::FETCH_ASSOC);
$total_data = $h3->rowCount();
$h4 = $db->query("SELECT id_obat,nama,jenis,satuan FROM gobat WHERE flag_single_id='new'");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
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
            <?php } ?>
            <!-- end pesan -->
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Data
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
                <div class="alert alert-info">klik Tombol Simpan untuk menyimpan seluruh data faktur. Data tidak akan tersimpan jika tombol simpan tidak diklik</div>
                <!-- general form elements -->
                <!-- general form elements -->
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-4">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-user"></i>
                                <h3 class="box-title">Data Obat</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="tambahacc.php?id=<?php echo $id_faktur; ?>&sumber=<?php echo $sumber; ?>" method="post">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="namaobat">Nama obat</label>
                                        <select class="form-control selectpicker" name="id_obat" id="id_obat" style="width:100%" required>
                                            <option value=""></option>
                                            <?php
                                            foreach ($data4 as $r4) {
                                                echo "<option value='" . $r4['id_obat'] . "'>" . $r4['nama'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="hargalama">Harga Lama</label>
                                                <input type="text" class="form-control" id="hargalama" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="satuan">Satuan</label>
                                                <input type="text" class="form-control" id="satuan" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Merk</label>
                                        <select name="merk" id="merk" class="form-control select_merk" style="width:100%;">
                                            <option value="">--Pilih Merk--</option>
                                            <?php
                                            if ($total_data > 0) {
                                                for ($i = 0; $i < $total_data; $i++) {
                                                    echo '<option value="' . $split[$i] . '">' . $split[$i] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="volume">Volume terkecil <span style="color:red">*</span></label>
                                                <input type="number" class="form-control" id="volume" name="volume" placeholder="Volume" autocomplete="off" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="harga">Harga Beli <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="harga" name="harga" placeholder="Harga" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="diskon">Diskon (%) <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="diskon" name="diskon" placeholder="Diskon" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="">PPN <span style="color:red">*</span></label>
                                                <select name="ppn" id="ppn" class="form-control" required>
                                                    <option value=""> Pilih PPN </option>
                                                    <option value="10">10%</option>
                                                    <option value="0">Tidak Kena PPN</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nobatch">No. Batch <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="nobatch" name="nobatch" placeholder="No. Batch" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="expired">Expired Date <span style="color:red">*</span></label>
                                                <input type="text" class="form-control" id="expired" name="expired" placeholder="Expired Date" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary"> <i class="fa fa-plus"></i> Tambah</button>
                                </div>
                        </div>
                    </div><!-- /.left column -->
                    <!-- right column -->
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-users"></i>
                                <h3 class="box-title">Data Obat</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr class="bg-blue">
                                                <th>Nama</th>
                                                <th>Merk</th>
                                                <th>Volume</th>
                                                <th>Harga Beli</th>
                                                <th>No Batch</th>
                                                <th>Expired</th>
                                                <th>Harga Netto</th>
                                                <th>Diskon</th>
                                                <th>Harga Setelah Diskon</th>
                                                <th>PPN</th>
                                                <th>Total</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $subtot = 0;
                                        $total = 0;
                                        foreach ($data3 as $r3) {
                                            $volumeformat = number_format($r3['volume'], 0, ".", ".");
                                            $hargaformat = number_format($r3['harga'], 4, ",", ".");
                                            $totalformat = number_format($r3['total'], 4, ",", ".");
                                            $ppnformat = number_format($r3['ppn'], 0, ",", ".");
                                            $total = $r3['volume'] * $r3['harga'];
                                            $hitungdiskon = $total * $r3['diskon'] / 100;
                                            $final = $total - $hitungdiskon;
                                            echo "<tr>
                                                    <td>" . $r3['namaobat'] . "</td>
                                                    <td>" . $r3['merk'] . "</td>
                                                    <td>" . $volumeformat . "</td>
                                                    <td>" . $hargaformat . "</td>
                                                    <td>" . $r3['nobatch'] . "</td>
                                                    <td>" . $r3['expired'] . "</td>
                                                    <td>" . number_format($total, 0, '.', '.') . "</td>
                                                    <td>" . $r3['diskon'] . " %</td>
                                                    <td>" . number_format($final, 0, '.', '.') . "</td>
                                                    <td>" . $ppnformat . "</td>
                                                    <td>" . $totalformat . "</td>
                                                    <td><a class='btn btn-sm btn-danger' href='hapus.php?id=" . $r3['id_item'] . "&faktur=" . $id_faktur . "&sumber=" . $sumber . "'><i class='fa fa-trash'></i> Hapus</a></td>
                                            </tr>";
                                            $subtot += $r3['total'];
                                        }
                                        ?>
                                        <tfoot>
                                            <tr class="success">
                                                <th colspan="9" align="right">Total</th>
                                                <th colspan="2">Rp. <?php echo number_format($subtot, 2, ".", "."); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <?php if ($total_data > 0) : ?>
                                    <a class="btn btn-app bg-green" href="save_faktur.php?faktur=<?php echo $id_faktur; ?>&sumber=<?php echo $sumber; ?>"><i class="fa fa-save"></i>Simpan</a>
                                    <!-- <a class="btn btn-app bg-red" href="batal.php?faktur=<?php echo $id_faktur; ?>"><i class="fa fa-trash"></i>Batal</a> -->
                                <?php else : ?>
                                    <a class="btn btn-app bg-red" href="batal.php?faktur=<?php echo $id_faktur; ?>"><i class="fa fa-trash"></i>Batal</a>
                                <?php endif; ?>
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
    <script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
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
            $('.selectpicker').select2({
                placeholder: "Pilih Obat/Bmhp",
                width : "resolve",
                allowClear: true
            });
            $('.select_merk').select2({
                placeholder: "Pilih Merk",
                allowClear: true,
                width: "resolve"
            });
            $('#id_obat').change(function(event) {
                var id_obat = $('#id_obat').val();
                $.ajax({
                    type: "POST",
                    url: "get_data_obat.php",
                    data: {
                        'id_obat': id_obat,
                    },
                    dataType: 'json',
                    success: function(respon) {
                        var hargalama;
                        var satuan = respon['satuan'];
                        if (respon == 0) {
                            hargalama = "Rp. 0";
                        } else {
                            hargalama = "Rp. " + respon['harga_baru'];
                        }
                        document.getElementById('hargalama').value = hargalama;
                        document.getElementById('satuan').value = satuan;
                        $('.select_merk').val(null).trigger('change');
                        let merk = respon['merk'];
                        if (merk != '') {
                            let sp = merk.split("|");
                            sp.forEach(function(data) {
                                var option = new Option(data, data, true, true);
                                $('#merk').append(option).trigger('change');
                            });
                            $('#merk').val('');
                            $('#merk').trigger('change');
                        }
                    },
                    error: function(e) {
                        // $("#result").text(e.responseText);
                        console.log("ERROR : ", e.responseText);
                    }
                });
            });
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