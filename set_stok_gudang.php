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
$id_obat = isset($_GET['o']) ? $_GET['o'] : '';
$id_warehouse = 0;
$today = date('Y-m-d');
//tampilkan data tindakan
$h3 = $db->query("SELECT * FROM temp_stok_awal WHERE id_warehouse='0' AND id_obat='" . $id_obat . "' AND sync='n'");
$total_data = $h3->rowCount();
//get kelola sumber dana
$get_sumber = $db->query("SELECT * FROM kelola_sumber_dana WHERE delete_stat=1 ORDER BY nama_sumber ASC");
$data_sumber = $get_sumber->fetchAll(PDO::FETCH_ASSOC);
$h4 = $db->prepare("SELECT * FROM gobat WHERE id_obat=:id");
$h4->bindParam(":id", $id_obat, PDO::PARAM_INT);
$h4->execute();
$obat = $h4->fetch(PDO::FETCH_ASSOC);
$data_merk = isset($obat['merk']) ? $obat['merk'] : '';
if ($data_merk == '') {
    $total_data = 0;
} else {
    $split = explode("|", $data_merk);
    $total_data = count($split);
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
    <!-- daterange picker -->
    <link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- BootsrapSelect -->
    <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/iCheck/all.css">
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
            <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Silakan Masukan Obat terlebih dahulu, sebelum menyimpan.
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
                    Pengaturan
                    <small>Stok Awal Gudang</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li>Transaksi</li>
                    <li class="active">Obat Masuk</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="alert alert-info">klik Tombol Simpan untuk menyelesaikan pengaturan obat.</div>
                <!-- general form elements -->
                <!-- general form elements -->
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-5">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-user"></i>
                                <h3 class="box-title">Data Obat</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form id="myForm" role="form" action="#">
                                <input type="hidden" id="id_warehouse" name="id_warehouse" value="<?php echo $id_warehouse; ?>">
                                <input type="hidden" name="id_obat" id="id_obat" value="<?php echo $obat['id_obat']; ?>">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="namaobat">Nama obat <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" value="<?php echo $obat['nama']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="volume">Volume <span style="color:red;">*</span></label>
                                        <input type="number" class="form-control" id="volume" placeholder="Masukan Jumlah Barang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="harga_beli">Harga Beli + PPN <span style="color:red;">*</span></label>
                                        <input type="number" class="form-control" id="harga_ppn" name="harga_ppn" placeholder="Masukan Harga PPN" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nobatch">No. Batch <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="nobatch" name="nobatch" placeholder="No. Batch" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="expired">Tanggal Kadaluarsa <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="expired" name="expired" placeholder="Expired Date" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Sumber Dana <span style="color:red">*</span></label>
                                        <select name="sumber_dana" id="sumber_dana" class="form-control" required>
                                            <option value="">---Pilih Salah Satu---</option>
                                            <?php
                                            foreach ($data_sumber as $ds) {
                                                echo '<option value="' . $ds['nama_sumber'] . '">' . $ds['nama_sumber'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Jenis <span style="color:red">*</span></label>
                                        <select name="jenis" id="jenis" class="form-control" required>
                                            <option value="">---Pilih Salah Satu---</option>
                                            <option value="generik">Generik</option>
                                            <option value="non generik">Non Generik</option>
                                            <option value="bmhp">Bmhp</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Merk</label>
                                        <select name="merk" id="merk" class="form-control">
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
                                    <div class="form-group">
                                        <label for="">Pabrikan</label>
                                        <input type="text" name="pabrikan" id="pabrikan" class="form-control" placeholder="Masukan Nama Pabrikan Jika jenis generik">
                                    </div>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    <button id="btnTambah" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.left column -->
                    <!-- right column -->
                    <div class="col-md-7">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-users"></i>
                                <h3 class="box-title">Data Stok Awal</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-striped table-hover" width="100%">
                                        <thead>
                                            <tr class="info">
                                                <th>Nama Obat</th>
                                                <th>Volume</th>
                                                <th>Harga Beli</th>
                                                <th>No.Batch</th>
                                                <th>Expired</th>
                                                <th>Jenis</th>
                                                <th>Pabrikan</th>
                                                <th>Merk</th>
                                                <th>Sumber Dana</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <a class="btn btn-app bg-green" href="set_stok_gudang_save.php?warehouse=<?php echo $id_warehouse; ?>&o=<?php echo $obat['id_obat']; ?>&today=<?php echo $today; ?>"><i class="fa fa-save"></i>Simpan</a>
                                <a class="btn btn-app bg-red" href="set_stok_gudang_cancel.php?warehouse=<?php echo $id_warehouse; ?>&o=<?php echo $obat['id_obat']; ?>&today=<?php echo $today; ?>"><i class="fa fa-trash"></i>Batal</a>
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
    <script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- BootsrapSelect -->
    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
    <!-- iCheck 1.0.1 -->
    <script src="../plugins/iCheck/icheck.min.js"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        function peringatan(item) {
            swal({
                    title: "Hapus Data?",
                    text: "Apakah Anda yakin akan menghapus data ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    closeOnClickOutside: false,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: "POST",
                            url: "ajax_data/set_temp_stok_awal_del.php",
                            data: {
                                'id': item,
                            },
                            success: function(respon) {
                                // console.log("SUCCESS : ", respon);
                                let res = JSON.parse(respon);
                                swal(res.title, res.msg, res.icon).then((_val) => {
                                    reloadTable();
                                });
                            },
                            error: function(e) {
                                // $("#result").text(e.responseText);
                                alert(e);
                                console.log("ERROR : ", e.responseText);
                                reloadTable();
                            }
                        });
                    }
                });
        }

        function reloadTable() {
            $('#example1').DataTable().ajax.reload();
        }

        function resetForm() {
            // reset input in form
            document.getElementById("myForm").reset();
        }
        $(function() {
            var master_temp = $('#example1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/data_temp_stok.php?w=<?php echo $id_warehouse; ?>&o=<?php echo $id_obat; ?>",
                "columns": [{
                        "data": 'nama',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'volume',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'harga_beli',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'no_batch',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'expired',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'jenis',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'pabrikan',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'merk',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": 'sumber_dana',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, full, meta) {
                            return '<button class="btn btn-sm btn-danger" onclick="peringatan(' + data.id_temp + ')"><i class="fa fa-trash"></i> Hapus</button>';
                        }
                    },
                ],
                "order": [
                    [0, "asc"]
                ]
            });
            //Red color scheme for iCheck
            $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });
            $('#expired').datepicker({
                format: 'dd/mm/yyyy',
                startView: 2,
                autoclose: true
            });
            $('#btnTambah').click(function(event) {
                event.preventDefault();
                var id_obat = document.getElementById("id_obat").value;
                var volume = $("#volume").val();
                var harga_beli = $("#harga_ppn").val();
                var nobatch = $("#nobatch").val();
                var expired = $("#expired").val();
                var id_warehouse = $("#id_warehouse").val();
                var merk = $('#merk').val();
                var sumber_dana = $('#sumber_dana').val();
                var jenis = $('#jenis').val();
                var pabrikan = $('#pabrikan').val();
                if (id_obat == "") {
                    swal('Peringatan', 'Silakan Pilih Obat terlebih dahulu', 'warning');
                    return;
                } else if ((volume == "") || (volume == 0)) {
                    swal('Peringatan', 'Silakan Isi Volume terlebih dahulu dan tidak boleh kurang dari 1', 'warning');
                    return;
                } else if ((harga_beli == "") || (harga_beli == 0)) {
                    swal('Peringatan', 'Silakan Isi Harga Beli Terlebih Dahulu dan tidak boleh 0', 'warning');
                    return;
                } else if (nobatch == "") {
                    swal('Peringatan', 'Silakan Isi Nomor Batch Terlebih Dahulu', 'warning');
                    return;
                } else if (expired == "") {
                    swal('Peringatan', 'Silakan Isi Tanggal Kadaluarsa Terlebih Dahulu', 'warning');
                    return;
                } else if (sumber_dana == "") {
                    swal('Peringatan', 'Silakan Isi Sumber Dana Terlebih Dahulu', 'warning');
                    return;
                } else if (jenis == "") {
                    swal('Peringatan', 'Silakan Isi Jenis Terlebih Dahulu', 'warning');
                    return;
                } else {
                    //ajax
                    $.ajax({
                        type: "POST",
                        url: "ajax_data/set_temp_stok_gudang.php",
                        data: {
                            'id_warehouse': id_warehouse,
                            'id_obat': id_obat,
                            'volume': volume,
                            'harga_beli': harga_beli,
                            'nobatch': nobatch,
                            'expired': expired,
                            'merk': merk,
                            'sumber_dana': sumber_dana,
                            'jenis': jenis,
                            'pabrikan': pabrikan
                        },
                        success: function(respon) {
                            console.log(respon)
                            let res = JSON.parse(respon);
                            swal(res.title, res.msg, res.icon).then((_val) => {
                                reloadTable();
                                resetForm();
                            });
                        },
                        error: function(e) {
                            // $("#result").text(e.responseText);
                            console.log("ERROR : ", e.responseText);
                            reloadTable();
                        }
                    });
                }
            });
        });
    </script>

</body>

</html>