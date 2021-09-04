      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $r1["nama"]; ?></p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header text-green">TRANSAKSI</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-money"></i> <span>Transaksi</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="masuk.php"><i class="fa fa-circle-o"></i> Obat Masuk</a></li>
                <!-- <li><a href="masuk_retur.php"><i class="fa fa-circle-o"></i> Obat Masuk dari Retur</a></li> -->
                <li><a href="list_keluar_draft.php"><i class="fa fa-circle-o"></i> Obat Keluar</a></li>
              </ul>
            </li>
            <!-- <li><a href="keluar_donasi_list.php" class="text-green"><i class="fa fa-book"></i> Barang Donasi Keluar <span class="label label-success">New</span></a></li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-undo"></i> <span>Retur</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="retur_supplier.php"><i class="fa fa-circle-o"></i> Retur ke Supplier</a></li>
                <li><a href="retur_warehouse.php"><i class="fa fa-circle-o"></i> Retur dari Depo</a></li>
              </ul>
            </li> -->
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-money"></i> <span>Data Order Barang</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="order_obat.php"><i class="fa fa-circle-o"></i> Daftar Permohonan Kebutuhan Barang</a></li>
              </ul>
            </li> -->
            <li class="header text-blue">REKAPAN</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-calendar"></i> <span>Rekap Transaksi</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <!-- <li><a href="rmasuk.php"><i class="fa fa-circle-o"></i> Faktur</a></li>
                <li><a href="raw_masuk.php"><i class="fa fa-circle-o"></i> RAW Faktur</a></li>
                <li><a href="rekap_retur_supplier.php"><i class="fa fa-circle-o"></i> Rekap Retur Ke Supplier</a></li>
                <li><a href="rekap_retur_depo.php"><i class="fa fa-circle-o"></i><span class="label label-success">New</span>Rekap Retur Depo</a></li>
                <li><a href="rekap_set_stok.php"><i class="fa fa-circle-o"></i><span class="label label-success">New</span>Rekap Set Stok</a></li>
                <li><a href="rekap_set_stok_gudang.php"><i class="fa fa-circle-o"></i><span class="label label-success">New</span>Rekap Set Stok Gudang Farmasi</a></li>
                <li><a href="rekap_retur_transaksi.php"><i class="fa fa-circle-o"></i><span class="label label-success">New</span>Rekap Retur Transaksi</a></li>
                <li><a href="faktur.php"><i class="fa fa-circle-o"></i> History Retur Faktur</a></li> -->
                <li><a href="romasuk.php"><i class="fa fa-circle-o"></i> Obat Masuk</a></li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-book"></i> <span>Obat Keluar </span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="rkeluar_hari.php"><i class="fa fa-book"></i> Per Hari</a></li>
                    <li><a href="rkeluar_bulan.php"><i class="fa fa-book"></i> Per Bulan</a></li>
                  </ul>
                </li>
                <!-- <li><a href="retur_warehouse_rekap.php"><i class="fa fa-circle-o"></i> Retur Depo</a></li> -->
                <!-- <li><a href="rekap_bast.php"><i class="fa fa-circle-o"></i> Rekap BAST</a></li> -->
                <li><a href="rekap_spb.php"><i class="fa fa-circle-o"></i> Rekap Nota Dinas / SPBB / SPB</a></li>
                <!-- <li><a href="romasuk_akm.php"><i class="fa fa-circle-o"></i> Penerimaan</a></li>
                <li class="treeview">
                  <a href="#">
                    <i class="fa fa-book"></i> <span>Pengeluaran <span class="label label-warning">Update</span></span> <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                    <li><a href="laporan_pengeluaran_farmasi_hari.php"><i class="fa fa-book"></i> Per Hari</a></li>
                    <li><a href="laporan_pengeluaran_farmasi.php"><i class="fa fa-book"></i> Per Bulan</a></li>
                  </ul>
                </li> -->
              </ul>
            </li>
            <li class="header text-yellow">DOKUMEN</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i> <span>Cetak Dokumen <span class="label label-warning">Update</span></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="bast.php"><i class="fa fa-circle-o"></i> Berita Acara Serah Terima</a></li>
                <li><a href="spb.php"><i class="fa fa-circle-o"></i> Pengeluran Barang</a></li>
                <!-- <li><a href="spb_donasi.php" class="text-green"><i class="fa fa-circle-o"></i> Pengeluran Barang Donasi <span class="label label-success">New</span></a></li> -->
              </ul>
            </li>
            <!-- <li class="header text-yellow">PELAPORAN RS ONLINE V2</li>
            <li class="treeview">
              <li><a href="laporan_obat_covid.php"><i class="fa fa-list"></i> Pelaporan Obat Covid</a></li>
            </li> -->
            <li class="header text-orange">PENGELOLAAN STOK</li>
            <li><a href="master_obat_single.php"><i class="fa fa-circle-o"></i> MASTER OBAT <span class="label label-success">New</span></a></li>
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i> <span>Data Stok Obat</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="master_apbd.php"><i class="fa fa-circle-o"></i> Sumber Dana APBD</a></li>
                <li><a href="master_blud.php"><i class="fa fa-circle-o"></i> Sumber Dana BLUD</a></li>
              </ul>
            </li> -->
            <!-- <li><a href="master_donasi.php" class="text-green"><i class="fa fa-book"></i> Data Stok Barang Donasi <span class="label label-success">New</span></a></li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i> <span>Stock Opname</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="stock_opname_gudang.php"><i class="fa fa-circle-o"></i> Stock Opname Gudang</a></li>
                <li><a href="stock_opname.php"><i class="fa fa-circle-o"></i> Stock Opname Apotek</a></li>
                <li><a href="stock_opname_depo.php"><i class="fa fa-circle-o"></i> Stock Opname Depo</a></li>
                <li><a href="rekap_stock_opname.php"><i class="fa fa-circle-o"></i> Rekap Stock Opname</a></li>
              </ul>
            </li> -->
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i> <span>Stok Kadaluarsa</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="obat_kadaluarsa.php"><i class="fa fa-bell"></i> Monitoring Obat Kadaluarsa</a></li>
                <li><a href="obat_kadaluarsa_depo.php"><i class="fa fa-bell"></i> Monitoring Obat Kadaluarsa Depo</a></li>
                <li><a href="move_kadaluarsa.php"><i class="fa fa-list"></i> Pemindahan Stok Obat Kadaluarsa</a></li>
                <li><a href="obat_kadaluarsa_list.php"><i class="fa fa-list"></i> Stok Obat Kadaluarsa</a></li>
              </ul>
            </li> -->
            <!-- <li><a href="cut_off_depo.php"><i class="fa fa-bell"></i> CUT OFF</a></li> -->
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-search"></i> <span>Hitung Stok</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="count_stock_all.php"><i class="fa fa-circle-o"></i> Stok Keseluruhan Depo</a></li>
                <li><a href="count_half_stock.php"><i class="fa fa-circle-o"></i> Stok Setiap Depo</a></li>
                <li><a href="count_keluar_harian_depo.php"><i class="fa fa-circle-o"></i> Pengeluaran Harian Depo</a></li>
                <li><a href="count_keluar_bulanan_depo.php"><i class="fa fa-circle-o"></i> Pengeluaran Bulanan Depo</a></li>
                <li><a href="count_cycle.php"><i class="fa fa-circle-o"></i> Form SO</a></li>
              </ul>
            </li> -->
            <li class="header text-maroon">PENGATURAN</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-gear"></i> <span>Pengaturan</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="stok_depo.php"><i class="fa fa-circle-o"></i> Data Stok Awal Depo</a></li>
                <li><a href="kelola_sumber_dana.php"><i class="fa fa-circle-o"></i> Pengaturan Sumber Dana</a></li>
                <!-- <li><a href="supplier.php" class="text-green"><i class="fa fa-circle-o"></i> Data Supplier <span class="label label-warning">Update</a></li> -->
                <li><a href="data_petugas.php"><i class="fa fa-circle-o"></i> Data Petugas</a></li>
                <li><a href="warehouse.php"><i class="fa fa-circle-o"></i> Depo / Mini Depo</a></li>
                <li><a href="tuslah.php"><i class="fa fa-circle-o"></i> Tuslah</a></li>
                <!-- <li><a href="fornas.php"><i class="fa fa-circle-o"></i> Data Formularium Nasional</a></li> -->
                <!-- <li><a href="bentuk_sediaan.php"><i class="fa fa-circle-o"></i> Data Bentuk Sediaan</a></li> -->
                <li><a href="kadaluarsa_conf.php"><i class="fa fa-circle-o"></i> Waktu Kontrol Kadaluarsa</a></li>
                <!-- <li><a href="master_pabrik.php"><i class="fa fa-circle-o"></i> Data Pabrik/Principle</a></li> -->
                <!-- <li><a href="setting_kelas_terapi.php"><i class="fa fa-circle-o"></i> Data Kelas Terapi</a></li> -->
              </ul>
            </li>
            <!-- <li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i> <span>Laporan Farmasi</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="rekap_rtt.php"><i class="fa fa-circle-o"></i> Resep Tidak Terlayani</a></li>
              </ul>
            </li> -->
            <li><a href="../logout.php"><i class="fa fa-lock"></i> Logout</a></li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>