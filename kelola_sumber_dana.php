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
$status = isset($_GET['status']) ? $_GET['status'] : 0;
//get all_data
$tus = $db->query("SELECT * FROM kelola_sumber_dana WHERE delete_stat=1 ORDER BY created_at DESC");
$data = $tus->fetchAll(PDO::FETCH_ASSOC);
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
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Pengaturan Sumber Dana
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="box box-success">
                            <div class="box-header">
                                <h3 class="box-title">Pengaturan Sumber Dana</h3>
                            </div>
                            <form action="kelola_sumber_dana_acc.php" method="post">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="">Masukan Sumber Dana <span style="color:red">*</span></label>
                                        <input type="text" name="nama_sumber" id="nama_sumber" class="form-control" placeholder="Masukan sumber dana" required>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="sumbit" class="btn btn-success btn-md" name="button"><i class="fa fa-save"></i> Simpan</button>
                                </div>
                            </form>
                        </div><!-- /.box -->
                    </div>
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Data Sumber Dana</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="info">
                                                <th>Tanggal dibuat</th>
                                                <th>Sumber Dana</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($data as $row) {
                                                $delete_stat = isset($row['delete_stat']) ? $row['delete_stat'] : 0;
                                                $policy_stat = isset($row['policy_stat']) ? $row['policy_stat'] : 0;
                                                $id_button = "id" . $row['id_sumber'];
                                                if ($policy_stat == 1 && $delete_stat == 1) {
                                                    $btnAktif = '<button onclick="deleteData(this)" id="' . $id_button . '" data-id="' . $row['id_sumber'] . '" class="btn btn-sm bg-red"><i class="fa fa-trash"></i></button>';
                                                } else {
                                                    $btnAktif = '<button class="btn disabled">-</a>';
                                                }
                                                echo '<tr>
                                                        <td>' . $row['created_at'] . '</td>
                                                        <td>' . $row['nama_sumber'] . '</td>
                                                        <td>' . $btnAktif . '</td>
                                                    </tr>';
                                            }
                                            
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <script src='../plugins/sweetalert/sweetalert.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        $(function() {
            $("#example1").dataTable();
        });

        function deleteData(ele) {
            let id = ele.id;
            var id_sumber = $('#' + id).data('id');
            swal({
                    title: "Apakah Anda yakin?",
                    text: "Setelah dihapus, data tidak dapat dikembalikan",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location="kelola_sumber_dana_hapus.php?d="+id_sumber;
                    } else {
                        swal("hapus data dibatalkan");
                    }
                });
        }
    </script>

</body>

</html>