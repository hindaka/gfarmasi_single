<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
include("../inc/set_gfarmasi.php");
date_default_timezone_set('Asia/Jakarta');
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
$id_parent = isset($_GET['cetak']) ? $_GET['cetak'] : '';
//get data obatkeluar_parent
$spb = $db->query("SELECT ob.pemesan,ob.jabatan,ob.nip,ob.nomor,w.nama_ruang,ob.created_at as tanggal_order FROM obatkeluar_parent ob INNER JOIN warehouse w ON(w.id_warehouse=ob.id_warehouse) WHERE ob.id_obatkeluar_parent='" . $id_parent . "'");
$head = $spb->fetch(PDO::FETCH_ASSOC);
$pemesan = $head['pemesan'];
$jabatan = $head['jabatan'];
$nip = $head['nip'];
$ruang = $head['nama_ruang'];
$nomor_surat = $head['nomor'];
$tanggal_order = substr($head['tanggal_order'], 0, 10);
$date = new DateTime($tanggal_order);
// echo $date->format('d.m.Y'); // 31.07.2012
// echo $date->format('d-m-Y'); // 31-07-2012
// echo $date->format('d F Y'); // 31-07-2012

//get data spb
$cetak_spb = $db->query("SELECT ob.*,kg.harga_beli,kg.expired,kg.no_batch,g.satuan,g.kadar,g.satuan_kadar,g.satuan_jual,g.kemasan FROM obatkeluar ob INNER JOIN kartu_stok_gobat kg ON(kg.id_kartu=ob.id_kartu) INNER JOIN gobat g ON(g.id_obat=kg.id_obat) WHERE id_parent='" . $id_parent . "'");
$cetak = $cetak_spb->fetchAll(PDO::FETCH_ASSOC);
//ttd
$get_ttd = $db->query("SELECT * FROM pegawai WHERE nip LIKE '" . $nip . "'");
$ttd = $get_ttd->fetch(PDO::FETCH_ASSOC);
$url_ttd = $ttd['url_ttd'];
$ttd_scan = $ttd['ttd_scan'];
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Cetak Nota Dinas</title>
</head>

<body onload="loadPrint()">
  <table width="864" border="0" cellspacing="0">
    <tr>
      <td width="93"><img src="clip_image002.png" width="140" height="89" /></td>
      <td width="758">
        <div align="center">PEMERINTAH KOTA BANDUNG<br />
          <strong>RUMAH SAKIT KHUSUS IBU DAN ANAK</strong><br />
          Jl. KH. Wahid Hasyim (Kopo) Nomor. 311 Tlp. (022) 86037777 IGD. (022) 5200505 Bandung<br />
          Email : sekretariat@rskiakotabandung.com <br />
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="border-bottom:5px double black;">
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;padding-top:10px;padding-bottom:10px;">
        <b>NOTA DINAS</b>
      </td>
    </tr>
    <tr>
      <td style="padding-left:10px;">
        Kepada
      </td>
      <td>: Kepala Sub Bagian Umum dan Kepegawaian selaku Pejabat Penatausahaan barang</td>
    </tr>
    <tr>
      <td style="padding-left:10px;">
        Dari
      </td>
      <td>: <?php echo $ruang; ?></td>
    </tr>
    <tr>
      <td style="padding-left:10px;">
        Nomor
      </td>
      <td>: <?php echo $nomor_surat; ?></td>
    </tr>
    <tr>
      <td style="padding-left:10px;">
        Tanggal
      </td>
      <td>: <?php echo $date->format('d F Y'); ?></td>
    </tr>
    <tr>
      <td style="padding-left:10px;">Sifat</td>
      <td>: Umum</td>
    </tr>
    <tr>
      <td style="padding-left:10px;">Perihal</td>
      <td>: Permohonan Kebutuhan Barang</td>
    </tr>
    <tr>
      <td colspan="2" style="border-bottom:3px double black;"></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;text-indent: 20px;">
        <p>
          Dipermaklumkan dengan hormat, sehubungan dengan kebutuhan sarana dan prasarana di lingkungan Rumah Sakit Khusus Ibu dan Anak Kota Bandung, dengan ini kami mohon untuk disediakan kebutuhan barang sebagai berikut :
        </p>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="padding-bottom:20px;padding-top:20px;">
        <div align="center">
          <table cellspacing="0" cellpadding="1" border="1">
            <thead>
              <tr align="center">
                <td width="28">No</td>
                <td width="220">Nama / Jenis Barang</td>
                <td width="100">Expired</td>
                <td width="100">No Batch</td>
                <td width="134">Banyaknya</td>
                <td width="192">Satuan</td>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              foreach ($cetak as $row) {
                //config view nama_barang
                $namaobat = isset($row['namaobat']) ? $row['namaobat'] : '';
                $kadar = isset($row['kadar']) ? $row['kadar'] : '';
                $satuan_kadar = isset($row['satuan_kadar']) ? $row['satuan_kadar'] : '';
                $satuan_jual = isset($row['satuan_jual']) ? $row['satuan_jual'] : '';
                $kemasan = isset($row['kemasan']) ? $row['kemasan'] : '';
                $jenis = isset($row['jenis']) ? $row['jenis'] : '';
                $merk = isset($row['merk']) ? $row['merk'] : '';
                $pabrikan = isset($row['pabrikan']) ? $row['pabrikan'] : '';
                $nama_view = viewNamaBarang($namaobat,$kadar,$satuan_kadar,$satuan_jual,$kemasan,$jenis,$pabrikan,$merk);
                echo "<tr>
                      <td align='center'>" . $i++ . "</td>
                      <td>" . $nama_view . "</td>
                      <td>" . substr($row['expired'], 0, 10) . "</td>
                      <td align='center'>" . $row['no_batch'] . "</td>
                      <td align='center'>" . $row['volume'] . "</td>
                      <td align='center'>" . $row['satuan'] . "</td>
                    </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </td>
    </tr>
  </table>
  <table width="864" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="338" valign="top"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center">Bandung, <?php echo $date->format('d F Y'); ?></td>
    </tr>
    <tr>
      <td width="338" valign="top" align="center"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center" style="padding-top:10px;">Yang mengajukan Permohonan Kebutuhan Barang</td>
    </tr>
    <tr>
      <td width="338" valign="top"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center"></td>
    </tr>
    <tr>
      <td width="338" height="100px"></td>
      <td width="19" height="100px"></td>
      <td style="text-align:center;">
        <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_scan . "_c.png"; ?>" height="75px" width="100px" alt="(tidak ada tanda tangan)">
      </td>
    </tr>
    <tr>
      <td width="338" valign="top" align="center"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center"><?php echo strtoupper($pemesan); ?></td>
    </tr>
    <tr>
      <td width="338" valign="top" align="center"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center"><?php echo $jabatan; ?></td>
    </tr>
    <tr>
      <td width="338" valign="top" align="center"></td>
      <td width="19" valign="top"></td>
      <td width="321" valign="top" align="center"><?php echo $nip; ?></td>
    </tr>
  </table>
  <script type="text/javascript">
    function loadPrint() {
      window.print();
      setTimeout(function() {
        window.close();
      }, 100);
    }
  </script>
</body>

</html>