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
$id_warehouse = isset($_GET["warehouse"]) ? $_GET['warehouse'] : '';
$today = date('Y-m-d');
$get_warehouse = $db->query("SELECT * FROM warehouse WHERE id_warehouse='" . $id_warehouse . "'");
$warehouse = $get_warehouse->fetch(PDO::FETCH_ASSOC);
// //tampilkan data tindakan
$h3 = $db->query("SELECT * FROM temp_stok_awal WHERE id_warehouse='" . $id_warehouse . "' AND sync='n'");
$total_data = $h3->rowCount();
$get_sumber = $db->query("SELECT * FROM kelola_sumber_dana WHERE delete_stat=1 ORDER BY nama_sumber ASC");
$data_sumber = $get_sumber->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        .new_data {
            color: blue;
        }

        .old_data {
            color: burlywood;
        }

        .select2-result-obat__namaobat {
            height: 30px;
        }
    </style>
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
                    <small>Stok Awal Warehouse <?php echo $warehouse['nama_ruang']; ?></small>
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
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="namaobat">Nama obat <span style="color:red">*</span></label>
                                        <select class="form-control select_obat" name="id_obat" id="id_obat" style="width:100%;" required>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="volume">Volume <span style="color:red;">*</span></label>
                                        <input type="number" class="form-control" id="volume" name="volume" placeholder="Masukan Jumlah Barang" min="0" required>
                                        <input type="text" name="volume_akhir" id="volume_akhir">
                                    </div>
                                    <div class="form-group">
                                        <label for="harga_beli">Harga Beli + PPN <span style="color:red;">*</span></label>
                                        <input type="number" class="form-control" id="harga_ppn" name="harga_ppn" placeholder="Masukan Harga PPN" min="0" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="nobatch"><br>No. Batch <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="nobatch" name="nobatch" placeholder="No. Batch" autocomplete="off" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="expired">Tanggal Kadaluarsa <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="expired" name="expired" placeholder="Expired Date" autocomplete="off" readonly>
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
                                        <input type="text" name="merk" id="merk" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Pabrikan</label>
                                        <input type="text" name="pabrikan" id="pabrikan" class="form-control" placeholder="Masukan Nama Pabrikan Jika jenis generik">
                                    </div>
                                    <div class="form-group">
                                        <label for="tuslah">Tuslah <span style="color:red;">*</span></label><br>
                                        <input class="minimal-red" type="radio" name="tuslah" id="tuslah1" value="1">&nbsp;Rajal&nbsp;&nbsp;
                                        <input class="minimal-red" type="radio" name="tuslah" id="tuslah2" value="2">&nbsp;Rajal Racik&nbsp;&nbsp;
                                        <input class="minimal-red" type="radio" name="tuslah" id="tuslah3" value="3">&nbsp;Ranap&nbsp;&nbsp;
                                        <input class="minimal-red" type="radio" name="tuslah" id="tuslah4" value="4">&nbsp;Ranap Racik&nbsp;&nbsp;
                                        <input class="minimal-red" type="radio" name="tuslah" id="tuslah5" value="5">&nbsp;Non Tuslah&nbsp;&nbsp;
                                    </div>
                                    <div class="form-group">
                                        <label for="alasan">Alasan <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="alasan" name="alasan" placeholder="Masukan Alasan Set Stok" autocomplete="off" required>
                                    </div>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    <button id="btnTambah" type="button" class="btn btn-primary"><i class="fa fa-plus"></i>Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div><!-- /.left column -->
                    <!-- right column -->
                    <div class="col-md-7">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-users"></i>
                                <h3 class="box-title">Data Stok Awal <?php echo $warehouse['nama_ruang']; ?></h3>
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
                                                <th>Tuslah</th>
                                                <th>Sumber Dana</th>
                                                <th>Merk</th>
                                                <th>Alasan</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <a class="btn btn-app bg-green" href="stok_depo_set_save.php?warehouse=<?php echo $id_warehouse; ?>&today=<?php echo $today; ?>"><i class="fa fa-save"></i>Simpan</a>
                                <a class="btn btn-app bg-red" href="stok_depo_set_cancel.php?warehouse=<?php echo $id_warehouse; ?>&today=<?php echo $today; ?>"><i class="fa fa-trash"></i>Batal</a>
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
    <script src='../plugins/sweetalert/sweetalert.min.js'></script>
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
            // reset selectpicker
            $("#id_obat").val('');
            $("#id_obat").selectpicker("refresh");
            //reset icheck radio button
            $('input[name="tuslah"]').removeAttr('checked').iCheck('update');
        }
        $(function() {
            var master_pegawai = $('#example1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "ajax_data/data_temp_stok2.php?w=<?php echo $id_warehouse; ?>",
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
                        "data": 'tuslah',
                        "render": function(data, type, full, meta) {
                            var tuslah_name;
                            if (data == 1) {
                                tuslah_name = 'Rajal';
                            } else if (data == 2) {
                                tuslah_name = 'Rajal Racik';
                            } else if (data == 3) {
                                tuslah_name = 'Ranap';
                            } else if (data == 4) {
                                tuslah_name = 'Ranap Racik';
                            } else if (data == 5) {
                                tuslah_name = 'Non Tuslah';
                            } else {
                                tuslah_name = 'Unknown';
                            }
                            return tuslah_name;
                        }
                    },
                    {
                        "data": 'sumber_dana',
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
                        "data": 'alasan',
                        "render": function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, full, meta) {
                            return '<button class="btn btn-sm btn-danger" onclick="return peringatan(' + data.id_temp + ')"><i class="fa fa-trash"></i> Hapus</button>';
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
            $(".select_obat").select2({
                ajax: {
                    url: "ajax_data/get_obat_keluar.php",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        console.log(data);
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
                placeholder: 'Pilih Obat',
                allowClear: true,
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });

            function formatRepo(repo) {
                if (repo.loading) {
                    return repo.text;
                }
                let text_name = '';
                if (repo.jenis == 'generik') {
                    text_name += repo.nama_obat + " (<b style='color:blue'>" + repo.merk_pabrik + "</b>)| stok: " + repo.volume_kartu_akhir;
                } else if (repo.jenis == 'non generik') {
                    text_name += repo.nama_obat + " (<b style='color:green'>" + repo.merk_pabrik + "</b>)| stok: " + repo.volume_kartu_akhir;
                } else {
                    text_name += repo.nama_obat + " | stok : " + repo.volume_kartu_akhir;
                }
                var $container = $(
                    "<div class='select2-result-obat clearfix'>" +
                    "<div class='select2-result-obat__namaobat'>" + text_name + "</div>" +
                    "</div>"
                );

                // var $container = $(
                //     "<div class='select2-result-repository clearfix'>" +
                //     "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
                //     "<div class='select2-result-repository__meta'>" +
                //     "<div class='select2-result-repository__title'></div>" +
                //     "<div class='select2-result-repository__description'></div>" +
                //     "<div class='select2-result-repository__statistics'>" +
                //     "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> </div>" +
                //     "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> </div>" +
                //     "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> </div>" +
                //     "</div>" +
                //     "</div>" +
                //     "</div>"
                // );

                // $container.find(".select2-result-repository__title").text(repo.full_name);
                // $container.find(".select2-result-repository__description").text(repo.description);
                // $container.find(".select2-result-repository__forks").append(repo.forks_count + " Forks");
                // $container.find(".select2-result-repository__stargazers").append(repo.stargazers_count + " Stars");
                // $container.find(".select2-result-repository__watchers").append(repo.watchers_count + " Watchers");

                return $container;
            }

            function formatRepoSelection(repo) {
                let text_name = '';
                if (repo.id == '') {
                    text_name = repo.text;
                } else {
                    if (repo.jenis == 'generik') {
                        text_name += repo.nama_obat + " (" + repo.merk_pabrik + ")| stok: " + repo.volume_kartu_akhir;
                    } else if (repo.jenis == 'non generik') {
                        text_name += repo.nama_obat + " (" + repo.merk_pabrik + ")| stok: " + repo.volume_kartu_akhir;
                    } else {
                        text_name += repo.nama_obat + " | stok : " + repo.volume_kartu_akhir;
                    }
                }
                return text_name;
            }
            // $('#id_obat').change(function() {
            //     let selectedItem = $(this).val();
            //     let sp = selectedItem.split("|");
            //     let id_obat = sp[0];
            //     let id_kartu = sp[1];
            //     if (id_kartu != '') {
            //         $.ajax({
            //             type: "POST",
            //             url: "ajax_data/get_item_kartu.php",
            //             data: {
            //                 "id_kartu": id_kartu
            //             },
            //             success: function(response) {
            //                 console.log(response)
            //                 let res = JSON.parse(response);
            //                 $('#volume_akhir').val(res.data['volume_kartu_akhir']);
            //                 $('#harga_ppn').val(res.data['harga_beli']);
            //                 $('#nobatch').val(res.data['no_batch']);
            //                 let expired = res.data['expired'];
            //                 let ex = expired.split("-");
            //                 let new_exp = ex[2] + "/" + ex[1] + "/" + ex[0];
            //                 $('#expired').val(new_exp);
            //                 $('#merk').val(res.data['merk']);
            //                 $('#sumber_dana').val(res.data['sumber_dana']);
            //             },
            //             error: function(err) {
            //                 console.warn(err)
            //             }
            //         });
            //     }
            // });
            $('#btnTambah').click(function(event) {
                event.preventDefault();
                let selectedItem = $('#id_obat').val();
                let sp = selectedItem.split("|");
                let id_obat = sp[0];
                var volume = $("#volume").val();
                var volume_akhir = $('#volume_akhir').val();

                var harga_ppn = $("#harga_ppn").val();
                var nobatch = $("#nobatch").val();
                var expired = $("#expired").val();
                var merk = $('#merk').val();
                var sumber_dana = $('#sumber_dana').val();
                var tuslah = $("input[name='tuslah']:checked").val();
                var alasan = $("#alasan").val();
                if (id_obat == "") {
                    swal('Peringatan', 'Silakan Pilih Obat terlebih dahulu', 'warning');
                    return;
                } else if ((volume == "") || (volume == 0)) {
                    swal('Peringatan', 'Silakan Isi Volume terlebih dahulu dan tidak boleh kurang dari 1', 'warning');
                    return;
                } else if (parseInt(volume) > parseInt(volume_akhir)) {
                    swal('Peringatan', 'Volume yang akan diinput melebihi jumlah stok yang tersedia : ' + volume_akhir, 'warning');
                    return;
                } else if (nobatch == "") {
                    swal('Peringatan', 'Silakan Isi Nomor Batch Terlebih Dahulu', 'warning');
                    return;
                } else if (expired == "") {
                    swal('Peringatan', 'Silakan Isi Tanggal Kadaluarsa Terlebih Dahulu', 'warning');
                    return;
                } else if (tuslah == "") {
                    swal('Peringatan', "Silakan Isi Tuslah Terlebih Dahulu", 'warning');
                    return;
                } else if (alasan == "") {
                    swal('Peringatan', "Silakan Isi Alasan Terlebih Dahulu", 'warning');
                    return;
                } else {
                    //ajax
                    $.ajax({
                        type: "POST",
                        url: "ajax_data/set_temp_stok_awal.php",
                        data: {
                            'id_warehouse': <?php echo $id_warehouse ?>,
                            'id_obat': id_obat,
                            'volume': volume,
                            'harga_ppn': harga_ppn,
                            'nobatch': nobatch,
                            'expired': expired,
                            'sumber_dana': sumber_dana,
                            'merk': merk,
                            'tuslah': tuslah,
                            'alasan': alasan
                        },
                        success: function(respon) {
                            console.log(respon);
                            let res = JSON.parse(respon);
                            swal(res.title, res.msg, res.icon).then((_val) => {
                                // call notification
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