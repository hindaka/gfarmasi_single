<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
include("../inc/set_gfarmasi.php");
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
$spb = $db->query("SELECT ob.pemesan,ob.nomor,ob.no_spbb,w.nama_ruang,ob.created_at as tanggal_order FROM obatkeluar_parent ob INNER JOIN warehouse w ON(w.id_warehouse=ob.id_warehouse) WHERE ob.id_obatkeluar_parent='" . $id_parent . "'");
$head = $spb->fetch(PDO::FETCH_ASSOC);
$pemesan = $head['pemesan'];
$ruang = $head['nama_ruang'];
$nomor_surat = $head['nomor'];
$no_spbb = $head['no_spbb'];
$tanggal_order = substr($head['tanggal_order'], 0, 10);
$date = new DateTime($tanggal_order);
// echo $date->format('d.m.Y'); // 31.07.2012
// echo $date->format('d-m-Y'); // 31-07-2012
// echo $date->format('d F Y'); // 31-07-2012

//get data spb
$cetak_spb = $db->query("SELECT ob.*,kg.harga_beli,kg.expired,kg.no_batch,g.satuan FROM obatkeluar ob INNER JOIN kartu_stok_gobat kg ON(kg.id_kartu=ob.id_kartu) INNER JOIN gobat g ON(g.id_obat=kg.id_obat) WHERE id_parent='" . $id_parent . "'");
$cetak = $cetak_spb->fetchAll(PDO::FETCH_ASSOC);
//ttd
$get_ttd = $db->query("SELECT * FROM pegawai WHERE nip LIKE '196509291988031008'");
$ttd = $get_ttd->fetch(PDO::FETCH_ASSOC);
$url_ttd = $ttd['url_ttd'];
$ttd_scan = $ttd['ttd_scan'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cetak SPBB</title>
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
                <b>SURAT PERINTAH PENGELUARAN / PENYALURAN BARANG</b><br />
                Nomor : <?php echo $no_spbb; ?>
            </td>
        </tr>
        <tr>
            <td style="padding-left:10px;">
                Dari
            </td>
            <td>: Pejabat Penatausahaan Barang</td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Kepada</td>
            <td>: Pembantu Pengurus Barang Farmasi</td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Alamat</td>
            <td>: Jl. KH. Wahid Hasyim (Kopo) Nomor. 311</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left:10px;padding-top:15px;padding-bottom:15px;">Harap dikeluarkan dari Gudang dan disalurkan barang tersebut dalam daftar di bawah ini :</td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Untuk</td>
            <td>: <?php echo $ruang; ?></td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Dasar</td>
            <td>: Nota Dinas Permohonan Kebutuhan Barang No.<?php echo $nomor_surat; ?></td>
        </tr>
        <tr>
            <td style="padding-left:10px;">Tanggal</td>
            <td>: <?php echo $tanggal_order; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom:20px;padding-top:20px;">
                <div align="center">
                    <table cellspacing="0" cellpadding="1" border="1">
                        <thead>
                            <tr align="center">
                                <td width="28">No</td>
                                <td width="90">Banyaknya</td>
                                <td width="100">Satuan</td>
                                <td width="220">Nama Barang</td>
                                <td width="125">Harga Satuan</td>
                                <td width="125">Jumlah</td>
                                <td width="75">Ket</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $all_item = 0;
                            foreach ($cetak as $row) {
                                $total = $row['harga_beli'] * $row['volume'];
                                $all_item += $total;
                                echo "<tr>
                                        <td align='center'>" . $i++ . "</td>
                                        <td align='center'>" . $row['volume'] . "</td>
                                        <td align='center'>" . $row['satuan'] . "</td>
                                        <td>" . $row['namaobat'] . "</td>
                                        <td align='right'>" . number_format($row['harga_beli'], $digit_akhir, ',', '.') . "</td>
                                        <td align='right'>" . number_format($total, $digit_akhir, ',', '.') . "</td>
                                        <td align='center'>-</td>
                                    </tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td width="28"></td>
                                <td width="90"></td>
                                <td width="100"></td>
                                <td width="220"></td>
                                <td width="125"></td>
                                <td width="125" align="right"><?php echo number_format($all_item, $digit_akhir, ',', '.'); ?></td>
                                <td width="75"></td>
                            </tr>
                        </tfoot>
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
            <td width="321" valign="top" align="center" style="padding-top:10px;">Kepala Sub Bagian Umum dan Kepegawaian</td>
        </tr>
        <tr>
            <td width="338" valign="top"></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">Selaku Pejabat Penatausahaan Barang</td>
        </tr>
        <tr>
            <td width="338" height="100px"></td>
            <td width="19" height="100px"></td>
            <td align="center">
                <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_scan . "_c.png"; ?>" height="75px" width="100px" alt="(tidak ada tanda tangan)">
            </td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">IWAN SETIAWAN</td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">Penata Tingkat I</td>
        </tr>
        <tr>
            <td width="338" valign="top" align="center"></td>
            <td width="19" valign="top"></td>
            <td width="321" valign="top" align="center">NIP. 19650929 198803 1 008</td>
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