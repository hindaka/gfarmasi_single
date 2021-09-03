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
//get data tuslah
$list_tuslah = $db->query("SELECT * FROM tuslah WHERE aktif='y'");
$find = $list_tuslah->rowCount();
if ($find > 0) {
    $data = $list_tuslah->fetch(PDO::FETCH_ASSOC);
    $rajal = $data['rajal'];
    $rajal_racik = $data['rajal_racik'];
    $ranap = $data['ranap'];
    $ranap_racik = $data['ranap_racik'];
    $ranap_presiden = $data['ranap_presiden'];
    $ranap_presiden_racik = $data['ranap_racik_presiden'];
    $ranap_suju = $data['ranap_suju'];
    $ranap_suju_racik = $data['ranap_racik_suju'];
    $ranap_vip = $data['ranap_vip'];
    $ranap_vip_racik = $data['ranap_racik_vip'];
} else {
    $rajal = 0;
    $rajal_racik = 0;
    $ranap = 0;
    $ranap_racik = 0;
    $ranap_presiden = 0;
    $ranap_presiden_racik = 0;
    $ranap_suju = 0;
    $ranap_suju_racik = 0;
    $ranap_vip = 0;
    $ranap_vip_racik = 0;
}
//get all_data
$tus = $db->query("SELECT * FROM tuslah ORDER BY created_at DESC");
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

        <!-- static header -->
        <header class="main-header">
            <a href="index.php" class="logo"><b>SIMRS</b><?php echo $version_gfarmasi; ?></a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li>
                                            <!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                                                </div>
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li><!-- end message -->
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../dist/img/user3-128x128.jpg" class="img-circle" alt="user image" />
                                                </div>
                                                <h4>
                                                    AdminLTE Design Team
                                                    <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../dist/img/user4-128x128.jpg" class="img-circle" alt="user image" />
                                                </div>
                                                <h4>
                                                    Developers
                                                    <small><i class="fa fa-clock-o"></i> Today</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../dist/img/user3-128x128.jpg" class="img-circle" alt="user image" />
                                                </div>
                                                <h4>
                                                    Sales Department
                                                    <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../dist/img/user4-128x128.jpg" class="img-circle" alt="user image" />
                                                </div>
                                                <h4>
                                                    Reviewers
                                                    <small><i class="fa fa-clock-o"></i> 2 days</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">10</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 10 notifications</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the page and may cause design problems
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-users text-red"></i> 5 new members joined
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#">
                                                <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-user text-red"></i> You changed your username
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">View all</a></li>
                            </ul>
                        </li>
                        <!-- Tasks: style can be found in dropdown.less -->
                        <li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag-o"></i>
                                <span class="label label-danger">9</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 9 tasks</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li>
                                            <!-- Task item -->
                                            <a href="#">
                                                <h3>
                                                    Design some buttons
                                                    <small class="pull-right">20%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">20% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
                                        <li>
                                            <!-- Task item -->
                                            <a href="#">
                                                <h3>
                                                    Create a nice theme
                                                    <small class="pull-right">40%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">40% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
                                        <li>
                                            <!-- Task item -->
                                            <a href="#">
                                                <h3>
                                                    Some task I need to do
                                                    <small class="pull-right">60%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">60% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
                                        <li>
                                            <!-- Task item -->
                                            <a href="#">
                                                <h3>
                                                    Make beautiful transitions
                                                    <small class="pull-right">80%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">80% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="#">View all tasks</a>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
                                <span class="hidden-xs"><?php echo $r1["nama"]; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                                    <p>
                                        <?php echo $r1["nama"]; ?> - <?php echo $r1["tipe"]; ?>
                                        <small>Member sejak <?php echo $r1["tanggal"]; ?></small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="../logout.php" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header><!-- ./static header -->
        <?php include "menu_index.php"; ?>
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Pengaturan Tuslah
                    <small>Rawat Jalan & Rawat Inap</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header">
                                <h3 class="box-title">Pengaturan Tuslah</h3>
                            </div>
                            <form action="tuslah_acc.php" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="rajal">Rajal</label>
                                                <input type="number" name="rajal" class="form-control" id="rajal" name="rajal" placeholder="Masukan biaya tuslah Rawat Jalan" value="<?php echo $rajal; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="rajal_racik">Rajal Racik</label>
                                                <input type="number" name="rajal_racik" class="form-control" id="rajal_racik" placeholder="Masukan biaya tuslah Rawat Jalan Racik" value="<?php echo $rajal_racik; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap">Ranap</label>
                                                <input type="number" name="ranap" class="form-control" id="ranap" placeholder="Masukan biaya tuslah Rawat Inap" value="<?php echo $ranap; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap Racik</label>
                                                <input type="number" name="ranap_racik" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Racik" value="<?php echo $ranap_racik; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap Presiden</label>
                                                <input type="number" name="ranap_presiden" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_presiden; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap Presiden Racik</label>
                                                <input type="number" name="ranap_presiden_racik" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_presiden_racik; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap Suju</label>
                                                <input type="number" name="ranap_suju" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_suju; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap Suju Racik</label>
                                                <input type="number" name="ranap_suju_racik" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_suju_racik; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap VIP</label>
                                                <input type="number" name="ranap_vip" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_vip; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ranap_racik">Ranap VIP Racik</label>
                                                <input type="number" name="ranap_vip_racik" class="form-control" id="ranap_racik" placeholder="Masukan Biaya tuslah Rawat Inap Presiden" value="<?php echo $ranap_vip_racik; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="sumbit" class="btn btn-success btn-md" name="button"><i class="fa fa-save"></i> Simpan</button>
                                </div>
                            </form>
                        </div><!-- /.box -->
                    </div>
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">History Tuslah</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Rajal</th>
                                                <th>Rajal Racik</th>
                                                <th>Ranap</th>
                                                <th>Ranap Racik</th>
                                                <th>Ranap Presiden</th>
                                                <th>Ranap Racik Presiden</th>
                                                <th>Ranap Suju</th>
                                                <th>Ranap Racik Suju</th>
                                                <th>Ranap VIP</th>
                                                <th>Ranap Racik VIP</th>
                                                <th>Aktif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($data as $row) {
                                                if ($row['aktif'] == 'y') {
                                                    $stat = "<i class='fa fa-check'></i>";
                                                } else {
                                                    $stat = "<i class='fa fa-ban'></i>";
                                                }
                                                echo "<tr>
															<td>" . $row['created_at'] . "</td>
															<td>" . $row['rajal'] . "</td>
															<td>" . $row['rajal_racik'] . "</td>
															<td>" . $row['ranap'] . "</td>
                                                            <td>" . $row['ranap_racik'] . "</td>
                                                            <td>" . $row['ranap_presiden'] . "</td>
                                                            <td>" . $row['ranap_racik_presiden'] . "</td>
                                                            <td>" . $row['ranap_suju'] . "</td>
                                                            <td>" . $row['ranap_racik_suju'] . "</td>
                                                            <td>" . $row['ranap_vip'] . "</td>
                                                            <td>" . $row['ranap_racik_vip'] . "</td>
															<td>" . $stat . "</td>
														</tr>";
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