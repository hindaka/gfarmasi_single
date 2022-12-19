<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-',$tipe);
if ($tipes[0]!='Gfarmasi')
{
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
$id_warehouse = isset($_GET['ware']) ? $_GET['ware'] : '';
$warehouse = $db->prepare("SELECT * FROM warehouse WHERE id_warehouse=:id");
$warehouse->bindParam(":id",$id_warehouse,PDO::PARAM_INT);
$warehouse->execute();
$ware = $warehouse->fetch(PDO::FETCH_ASSOC);
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
  <body class="skin-black">
    <div class="wrapper">
	  <?php 
      include "header.php";
    include "menu_index.php"; ?>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Pengaturan
            <small>Data Mini Depo</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
					<div class="alert alert-info">Field yang bertanda <span style="color:red">*</span> <b>WAJIB</b> diisi dengan <b>BAIK & BENAR</b></div>
        <div class="row">
          <div class="col-md-6 col-xs-12">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Form Data Mini Depo</h3>
              </div>
              <form class="" action="edit_warehouse_acc.php" method="post">
                <div class="box-body">
                  <div class="form-group">
                    <label for="nama_ruang">Nama Ruangan <span style="color:red;">*</span></label>
                    <input type="text" name="nama_ruang" class="form-control" id="nama_ruang" placeholder="Masukan Nama Ruang" value="<?php echo $ware['nama_ruang'] ?>" required>
										<input type="hidden" name="ware" id="ware" value="<?php echo $id_warehouse; ?>">
                  </div>
                  <div class="form-group">
                    <label for="">Lokasi <span style="color:red;">*</span></label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Masukin Lokasi/Tempat depo" required>
                  </div>
                  <div class="form-group">
                    <label for="">Tipe Depo/Warehouse <span style="color:red;">*</span></label><br>
                    <?php 
                      $depo_set = isset($ware['depo_set']) ? $ware['depo_set'] : '';
                      $troli_emg = isset($ware['trolly_emg']) ? $ware['trolly_emg'] : '';
                      $kit_emg = isset($ware['kit_emg']) ? $ware['kit_emg'] : '';
                      if($depo_set=='y'){
                        $tipe_depo='depo_set';
                      }else if($troli_emg=='y'){
                        $tipe_depo='trolly_emg';
                      }else if($kit_emg=='y'){
                        $tipe_depo='kit_emg';
                      }else{
                        $tipe_depo="";
                      }
                      if($tipe_depo=='depo_set'){
                        echo '<input type="radio" name="tipe_depo" id="tipe_depo1" value="depo_set" checked required> Kelola Depo
                        <input type="radio" name="tipe_depo" id="tipe_depo2" value="trolly_emg" required> Troli Emergensi
                        <input type="radio" name="tipe_depo" id="tipe_depo3" value="kit_emg" required> Kit Emergensi';
                      }else if($tipe_depo=='trolly_emg'){
                        echo '<input type="radio" name="tipe_depo" id="tipe_depo1" value="depo_set" required> Kelola Depo
                        <input type="radio" name="tipe_depo" id="tipe_depo2" value="trolly_emg" checked required> Troli Emergensi
                        <input type="radio" name="tipe_depo" id="tipe_depo3" value="kit_emg" required> Kit Emergensi';
                      }else if($tipe_depo=='kit_emg'){
                        echo '<input type="radio" name="tipe_depo" id="tipe_depo1" value="depo_set" required> Kelola Depo
                        <input type="radio" name="tipe_depo" id="tipe_depo2" value="trolly_emg" required> Troli Emergensi
                        <input type="radio" name="tipe_depo" id="tipe_depo3" value="kit_emg" checked required> Kit Emergensi';
                      }else{
                        echo '<input type="radio" name="tipe_depo" id="tipe_depo1" value="depo_set" required> Kelola Depo
                        <input type="radio" name="tipe_depo" id="tipe_depo2" value="trolly_emg" required> Troli Emergensi
                        <input type="radio" name="tipe_depo" id="tipe_depo3" value="kit_emg" required> Kit Emergensi';
                      }
                    ?>
                  </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
              </form>
            </div><!-- /.box -->
          </div>
        </div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- static footer -->
	  <?php include "footer.php"; ?><!-- /.static footer -->
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
      $(function () {
        $("#example1").dataTable();
      });
    </script>

  </body>
</html>
