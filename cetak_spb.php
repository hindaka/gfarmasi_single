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
$spb = $db->query("SELECT ob.pemesan,ob.jabatan,ob.nip,ob.no_spb,w.nama_ruang,ob.created_at as tanggal_order FROM obatkeluar_parent ob INNER JOIN warehouse w ON(w.id_warehouse=ob.id_warehouse) WHERE ob.id_obatkeluar_parent='" . $id_parent . "'");
$head = $spb->fetch(PDO::FETCH_ASSOC);
$pemesan = $head['pemesan'];
$jabatan = $head['jabatan'];
$nip = $head['nip'];
$ruang = $head['nama_ruang'];
$no_spb = $head['no_spb'];
$tanggal_order = substr($head['tanggal_order'], 0, 10);
$date = new DateTime($tanggal_order);
// echo $date->format('d.m.Y'); // 31.07.2012
// echo $date->format('d-m-Y'); // 31-07-2012
// echo $date->format('d F Y'); // 31-07-2012
$limit_sk = strtotime("2020-08-25");
//get data spb
$cetak_spb = $db->query("SELECT ob.*,kg.harga_beli,kg.expired,kg.no_batch,g.satuan,g.kadar,g.satuan_kadar,g.satuan_jual,g.kemasan FROM obatkeluar ob INNER JOIN kartu_stok_gobat kg ON(kg.id_kartu=ob.id_kartu) INNER JOIN gobat g ON(g.id_obat=kg.id_obat) WHERE id_parent='" . $id_parent . "' ORDER BY ob.namaobat ASC");
$cetak = $cetak_spb->fetchAll(PDO::FETCH_ASSOC);
//ttd
$get_ttd = $db->query("SELECT * FROM pegawai WHERE nip LIKE '" . $nip . "'");
$ttd = $get_ttd->fetch(PDO::FETCH_ASSOC);
$url_ttd = $ttd['url_ttd'];
$ttd_scan = $ttd['ttd_scan'];
if (strtotime($tanggal_order) >= $limit_sk) {
    //gunakan SK terbaru
    $nama_penyerah = "TAUFIK ISMAIL";
    $ttd_penyerah = "197605072010011003";
    $nip_penyerah = "19760507 201001 1 003";
    $jabatan_penyerah = "Penata Muda";
    $nama_gudang = "SHERLY DONNA VALENCIA";
    $jabatan_gudang = "Pengatur Tingkat I";
    $ttd_gudang = "198811022010012002";
    $nip_gudang = "19881102 201001 2 002";
} else {
    $nama_penyerah = "WAGIMIN";
    $jabatan_penyerah = "Penata Muda Tingkat I";
    $ttd_penyerah = "196205091983031007";
    $nip_penyerah = "196205091983031007";
    $nama_gudang = "TAUFIK ISMAIL";
    $jabatan_gudang = "Penata Muda";
    $ttd_gudang = "197605072010011003";
    $nip_gudang = "19760507 201001 1 003";
}
$nip_kasubag = "19650929 198803 1 008";
$ttd_kasubag = "196509291988031008";
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cetak SPB</title>
</head>

<body onload="loadPrint();">
    <table width="864" border="0" cellspacing="0">
        <tr>
            <td width="93"><img src="clip_image002.png" width="140" height="89" /></td>
            <td width="758">
                <div align="center">PEMERINTAH KOTA BANDUNG<br />
                    <strong>RUMAH SAKIT UMUM DAERAH BANDUNG KIWARI</strong><br />
                    Jl. KH. Wahid Hasyim (Kopo) Nomor. 311 Tlp. (022) 86037777 IGD. (022) 5200505 Bandung<br />
                    Email : sekretariat@rsudbandungkiwari.or.id <br />
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom:5px double black;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;padding-top:10px;padding-bottom:10px;">
                <b>SURAT PENGELUARAN BARANG</b><br />
                Nomor : <?php echo $no_spb; ?>
            </td>
        </tr>
        <tr>
            <td style="padding-left:10px;">
                Diminta oleh
            </td>
            <td>: <?php echo $ruang; ?></td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Dari</td>
            <td>: Pembantu Pengurus Barang Farmasi</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom:20px;padding-top:20px;">
                <div align="center">
                    <table cellspacing="0" cellpadding="1" border="1">
                        <thead>
                            <tr align="center">
                                <td width="28">No</td>
                                <td width="220">Nama Barang</td>
                                <td width="100">Expired</td>
                                <td width="100">No Batch</td>
                                <td width="134">Jumlah</td>
                                <td width="192">Keterangan</td>
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
                                        <td align='center'>" . substr($row['expired'], 0, 10) . "</td>
                                        <td align='center'>" . $row['no_batch'] . "</td>
                                        <td align='center'>" . $row['volume'] . "</td>
                                        <td align='center'>-</td>
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
            <td width="338" valign="top" align="center" style="padding-top:10px;">Yang Menerima</td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center" style="padding-top:10px;">Yang Menyerahkan</td>
        </tr>
        <tr>
            <td width="338" valign="top"></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">Pengurus Barang Pengguna</td>
        </tr>
        <tr>
            <td align="center">
                <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_scan . "_c.png"; ?>" height="75px" width="100px" alt="(tidak ada tanda tangan)">
            </td>
            <td width="19" height="100px"></td>
            <td align="center">
                <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_penyerah . "_c.png"; ?>" height="100px" width="75px" alt="(tidak ada tanda tangan)">
            </td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"><?php echo ucwords($pemesan); ?></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center"><?php echo $nama_penyerah; ?></td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"><?php echo $jabatan; ?></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center"><?php echo $jabatan_penyerah; ?></td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"><?php echo $nip; ?></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">NIP. <?php echo $nip_penyerah; ?></td>
        </tr>

        <tr>
            <td valign="top">&nbsp;</td>
            <td valign="top">&nbsp;</td>
            <td valign="top">
                <p align="center">Pembantu Pengurus Barang Farmasi<br />
                    <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_gudang . "_c.png"; ?>" height="75px" width="75px" alt="(tidak ada tanda tangan)">
                    <br />
                    <?php echo $nama_gudang; ?><br />
                    <?php echo $jabatan_gudang; ?><br />
                    NIP.
                    <?php echo $nip_gudang; ?>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="3" valign="top">
                <p align="center"><br />
                    Mengetahui,<br />
                    Kepala Sub Bagian Umum dan Kepegawaian<br />
                    Selaku Pejabat Penatausahaan Barang<br />
                    <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_kasubag . "_c.png"; ?>" height="100px" width="100px" alt="(tidak ada tanda tangan)">
                    <br />
                    IWAN SETIAWAN<br />
                    Penata Tingkat I<br />
                    NIP. 19650929 198803 1 008
                </p>
            </td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
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