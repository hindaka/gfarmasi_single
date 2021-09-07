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
$id_faktur_parent = isset($_GET['cetak']) ? trim($_GET['cetak']) : '';
//faktur_parent
$get_faktur_parent = $db->query("SELECT * FROM faktur_parent fp INNER JOIN faktur f ON(fp.id_faktur=f.id_faktur) WHERE fp.id_faktur_parent='" . $id_faktur_parent . "'");
$fp = $get_faktur_parent->fetch(PDO::FETCH_ASSOC);
$split = explode('/', $fp['tgl_faktur']);
$new_date = $split[2] . "-" . $split[1] . "-" . $split[0];
$tgl_faktur = date('d F Y', strtotime($new_date));
$new_tgl = strtotime($new_date);
$hari = date('l', strtotime($new_date));
function hariToInd($day)
{
    switch ($day) {
        case 'Sunday':
            $hari = "Minggu";
            break;
        case 'Monday':
            $hari = "Senin";
            break;
        case 'Tuesday':
            $hari = "Selasa";
            break;
        case 'Wednesday':
            $hari = "Rabu";
            break;
        case 'Thursday':
            $hari = "Kamis";
            break;
        case 'Friday':
            $hari = "Jumat";
            break;
        case 'Saturday':
            $hari = "Sabtu";
            break;
        default:
            $hari = "unknown";
            break;
    }
    return $hari;
}
function konversi($bln)
{
    switch ($bln) {
        case '01':
            $bulan = "Januari";
            break;
        case '02':
            $bulan = "Februari";
            break;
        case '03':
            $bulan = "Maret";
            break;
        case '04':
            $bulan = "April";
            break;
        case '05':
            $bulan = "Mei";
            break;
        case '06':
            $bulan = "Juni";
            break;
        case '07':
            $bulan = "Juli";
            break;
        case '08':
            $bulan = "Agustus";
            break;
        case '09':
            $bulan = "September";
            break;
        case '10':
            $bulan = "Oktober";
            break;
        case '11':
            $bulan = "November";
            break;
        case '12':
            $bulan = "Desember";
            break;
        default:
            $bulan = "unknown";
            break;
    }
    return $bulan;
}
$bulan = konversi($split[1]);
//get_rincian
$get_rincian = $db->query("SELECT i.*,g.satuan,g.kadar,g.satuan_kadar,g.satuan_jual,g.kemasan FROM itemfaktur i INNER JOIN faktur f ON(f.id_faktur=i.id_faktur) LEFT JOIN gobat g ON(i.id_obat=g.id_obat) WHERE i.id_faktur='" . $fp['id_faktur'] . "'");
$rincian = $get_rincian->fetchAll(PDO::FETCH_ASSOC);
$limit_sk = strtotime("2020-08-25");
//ttd
$get_ttd = $db->query("SELECT * FROM pegawai WHERE nama LIKE '%TAAT TAGORE%' LIMIT 1");
$ttd = $get_ttd->fetch(PDO::FETCH_ASSOC);
$url_ttd = $ttd['url_ttd'];
$ttd_dirut = $ttd['ttd_scan'];
if ($new_tgl >= $limit_sk) {
    //gunakan SK terbaru
    $nama_penyerah = "Taufik Ismail";
    $ttd_penyerah = "197605072010011003";
    $nip_penyerah = "19760507 201001 1 003";
    $jabatan_penyerah = "Penata Muda";
    $nama_gudang = "DIAN DWIYANTI";
    $jabatan_gudang = "Pengatur Tingkat I";
    $ttd_gudang = "197403242009022001";
    $nip_gudang = "19740324 200902 2 001";
} else {
    $nama_penyerah = "Wagimin";
    $jabatan_penyerah = "Penata Muda Tingkat I";
    $ttd_penyerah = "196205091983031007";
    $nip_penyerah = "196205091983031007";
    $nama_gudang = "Taufik Ismail";
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
    <title>Berita Acara Serah Terima</title>
    <style>
        body {
            font-size: 14px;
        }

        .bottom {
            border-bottom: 5px double black;
        }
    </style>
</head>

<body onload="loadPrint()">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class='bottom'><img src="clip_image002.png" /></td>
            <td class='bottom'>
                <div align="center">
                    <span style="font-size:18px;font-weight:bold;">PEMERINTAH KOTA BANDUNG<br>RUMAH SAKIT KHUSUS IBU DAN ANAK</span><br>
                    Jl. KH. Wahid Hasyim (Kopo) Nomor. 311 Tlp. (022) 86037777 IGD. (022) 5200505 Bandung<br />
                    Email : sekretariat@rskiakotabandung.com <br />
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div align="center"><br />
                    BERITA ACARA SERAH TERIMA BARANG PERSEDIAAN <br />
                    NOMOR : <?php echo $fp['no_bast']; ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">Pada hari ini <?php echo hariToInd($hari); ?> tanggal <?php echo $split[0]; ?> bulan <?php echo $bulan; ?> tahun <?php echo $split[2]; ?> yang bertanda tangan dibawah ini :</td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;" align="center">
                <table border="0" cellspacing="0" cellpadding="0" width="60%">
                    <tr>
                        <td valign="top">1.</td>
                        <td width="81" valign="top">Nama<br />
                            Nip<br />
                            Jabatan</td>
                        <td width="18" valign="top">:<br />
                            : <br />
                            : </td>
                        <td valign="top">dr.Taat Tagore Diah Rangkuti, M.KKK<br />
                            19621010 199011 1 003<br />
                            Pengguna Barang Selanjutnya disebut PIHAK KESATU<br /></td>
                    </tr>
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top">2.</td>
                        <td valign="top">Nama<br />
                            Nip<br />
                            Jabatan</td>
                        <td valign="top">:<br />
                            :<br />
                            :</td>
                        <td valign="top">Iwan Setiawan<br />
                            19650929 198803 1 008<br />
                            Pejabat Penatausahaan Pengguna Barang<br /></td>
                    </tr>
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                        <td valign="top">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top">3.</td>
                        <td valign="top">Nama<br />
                            Nip<br />
                            Jabatan</td>
                        <td valign="top">:<br />
                            :<br />
                            :</td>
                        <td valign="top"><?php echo $nama_penyerah; ?><br />
                            <?php echo $nip_penyerah; ?><br />
                            Pengurus Barang Pengguna</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left:20px;" align="justify">
                Pejabat Penatausahaan Pengguna Barang dan Pengurus Barang Pengguna selanjutnya disebut PIHAK KEDUA. PIHAK KESATU telah menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK KESATU, berupa :
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px">
                <div align="center">
                    <table border="1" cellspacing="0" cellpadding="5">
                        <thead>
                            <tr align="center">
                                <th>No</th>
                                <th>Uraian</th>
                                <th colspan="2">
                                    <p align="center">Jumlah</p>
                                </th>
                                <th>Harga Beli</th>
                                <th>Diskon (%)</th>
                                <th>Total Harga</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <?php
                        $i = 1;
                        foreach ($rincian as $row) {
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
                                    <td>" . $i++ . "</td>
                                    <td align='left'>" . $nama_view . "</td>
                                    <td align='right'>" . $row['volume'] . "</td>
                                    <td align='center'>" . $row['satuan'] . "</td>
                                    <td align='right'>Rp " . number_format($row['harga'], $digit_akhir, ',', '.') . "</td>
                                    <td align='center'>" . $row['diskon'] . "</td>
                                    <td align='right'>Rp " . number_format($row['total'], $digit_akhir, ',', '.') . "</td>
                                    <td>Reg. " . $row['id_faktur'] . "</td>
                                </tr>";
                        }
                        ?>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="padding-top:20px">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="center">Bandung, <?php echo date('d F Y', strtotime($tgl_faktur)); ?></td>
                    </tr>
                    <tr>
                        <td align="center">Direktur Rumah Sakit<br> Khusus Ibu dan Anak Kota Bandung</td>
                        <td>&nbsp;</td>
                        <td align="center">Kepala Sub Bagian Tata Usaha</td>
                    </tr>
                    <tr>
                        <td align="center">Selaku</td>
                        <td>&nbsp;</td>
                        <td align="center">Selaku</td>
                    </tr>
                    <tr>
                        <td align="center">Pengguna Barang</td>
                        <td>&nbsp;</td>
                        <td align="center">Pejabat Penatausahaan Pengguna Barang</td>
                    </tr>
                    <tr>
                        <td align="center">
                            <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_dirut . "_c.png"; ?>" height="75px" width="100px" alt="(tidak ada tanda tangan)">
                        </td>
                        <td></td>
                        <td align="center">
                            <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_kasubag . "_c.png"; ?>" height="75px" width="100px" alt="(tidak ada tanda tangan)">
                        </td>
                    </tr>
                    <tr>
                        <td align="center"><u>dr.Taat Tagore Diah Rangkuti, M.KKK</u></td>
                        <td>&nbsp;</td>
                        <td align="center"><u>Iwan Setiawan</u></td>
                    </tr>
                    <tr>
                        <td align="center">Pembina Tingkat I</td>
                        <td>&nbsp;</td>
                        <td align="center">Penata Tingkat I</td>
                    </tr>
                    <tr>
                        <td align="center">NIP. 19621010 199011 1 003</td>
                        <td>&nbsp;</td>
                        <td align="center">NIP. 19650929 198803 1 008</td>
                    </tr>
                    <tr>
                        <td width="338" valign="top">
                            <p align="center">&nbsp;</p>
                        </td>
                        <td width="19" valign="top">
                            <p align="center">&nbsp;</p>
                        </td>
                        <td width="321" valign="top">
                            <p align="center">Pengurus Barang</p>
                            <p align="center">
                                <img style="z-index:1000" src="<?php echo $url_ttd . "warna/" . $ttd_penyerah . "_c.png"; ?>" height="75px" width="75px" alt="(tidak ada tanda tangan)">
                            </p>
                            <p align="center"><u><?php echo $nama_penyerah; ?></u><br />
                                <?php echo $jabatan_penyerah; ?><br />
                                NIP. <?php echo $nip_penyerah; ?></p>
                        </td>
                    </tr>
                </table>
            </td>
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