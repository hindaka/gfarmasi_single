<?php
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'faktur';

// Table's primary key
$primaryKey = 'id_faktur';
$tanggal_awal = isset($_GET['awal']) ? base64_decode($_GET['awal']) : date('Y-m-d');
$awal = str_replace("-", "", substr($tanggal_awal, 0, 10));
$tanggal_akhir = isset($_GET['akhir']) ? base64_decode($_GET['akhir']) : date('Y-m-d');
$akhir = str_replace("-", "", substr($tanggal_akhir, 0, 10));
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => '`f`.`id_faktur`', 'dt' => 'id_faktur', 'field' => 'id_faktur', 'as' => 'id_faktur'),
    array('db' => 'CONCAT(SUBSTRING(`f`.`tgl_faktur`,7,4),"-",SUBSTRING(`f`.`tgl_faktur`,4,2),"-",SUBSTRING(`f`.`tgl_faktur`,1,2))', 'dt' => 'tanggal_faktur', 'field' => 'tanggal_faktur', 'as' => 'tanggal_faktur'),
    array('db' => '`f`.`tgl_faktur`', 'dt' => 'tgl_faktur', 'field' => 'tgl_faktur', 'as' => 'tgl_faktur'),
    array('db' => '`f`.`no_faktur`', 'dt' => 'no_faktur', 'field' => 'no_faktur', 'as' => 'no_faktur'),
    array('db' => '`f`.`ppn_persen`', 'dt' => 'ppn_persen', 'field' => 'ppn_persen', 'as' => 'ppn_persen'),
    array('db' => '`f`.`ekatalog`', 'dt' => 'ekatalog', 'field' => 'ekatalog', 'as' => 'ekatalog'),
    array('db' => '`f`.`perusahaan`', 'dt' => 'perusahaan', 'field' => 'perusahaan', 'as' => 'perusahaan'),
    array('db' => '`f`.`tgl_bayar`', 'dt' => 'tgl_bayar', 'field' => 'tgl_bayar', 'as' => 'tgl_bayar'),
    array('db' => '`f`.`pembayaran_tunai`', 'dt' => 'pembayaran_tunai', 'field' => 'pembayaran_tunai', 'as' => 'pembayaran_tunai'),
    array('db' => '`f`.`cara_bayar`', 'dt' => 'cara_bayar', 'field' => 'cara_bayar', 'as' => 'cara_bayar'),
    array('db' => '`im`.`namaobat`', 'dt' => 'namaobat', 'field' => 'namaobat', 'as' => 'namaobat'),
    array('db' => '`g`.`jenis`', 'dt' => 'jenis', 'field' => 'jenis', 'as' => 'jenis'),
    array('db' => '`g`.`satuan`', 'dt' => 'satuan', 'field' => 'satuan', 'as' => 'satuan'),
    array('db' => '`g`.`fornas`', 'dt' => 'fornas', 'field' => 'fornas', 'as' => 'fornas'),
    array('db' => '`im`.`volume`', 'dt' => 'volume', 'field' => 'volume', 'as' => 'volume'),
    array('db' => '`im`.`harga`', 'dt' => 'harga', 'field' => 'harga', 'as' => 'harga'),
    array('db' => '`im`.`diskon`', 'dt' => 'diskon', 'field' => 'diskon', 'as' => 'diskon'),
    array('db' => '`im`.`total`', 'dt' => 'total', 'field' => 'total', 'as' => 'total'),
    array('db' => '`im`.`harga_satuan`', 'dt' => 'harga_satuan', 'field' => 'harga_satuan', 'as' => 'harga_satuan'),
    array('db' => '`im`.`nobatch`', 'dt' => 'nobatch', 'field' => 'nobatch', 'as' => 'nobatch'),
    array('db' => '`im`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' => 'expired'),
    array('db' => '`im`.`sumber`', 'dt' => 'sumber', 'field' => 'sumber', 'as' => 'sumber'),
    array('db' => '`f`.`bayar_apbd`', 'dt' => 'bayar_apbd', 'field' => 'bayar_apbd', 'as' => 'bayar_apbd'),
    array('db' => '`f`.`keterangan`', 'dt' => 'keterangan', 'field' => 'keterangan', 'as' => 'keterangan'),
    array('db' => '`f`.`sumber_pelunasan`', 'dt' => 'sumber_pelunasan', 'field' => 'sumber_pelunasan', 'as' => 'sumber_pelunasan'),
    array('db' => '`im`.`time`', 'dt' => 'time', 'field' => 'time', 'as' => 'time'),
);
// SQL server connection information
require_once('../../inc/set_env.php');
$sql_details = array(
    'user' => $userPdo,
    'pass' => $passPdo,
    'db'   => $dbPdo,
    'host' => $hostPdo
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
require('ssp.customized.class.php');

$joinQuery = "FROM `faktur` AS `f` INNER JOIN `itemfaktur` AS `im` ON(`f`.`id_faktur`=`im`.`id_faktur`)";
$joinQuery .= " INNER JOIN gobat g ON(im.id_obat=g.id_obat)";
// $extraWhere="";
// $extraWhere = " CAST(CONCAT(SUBSTRING(`f`.`tgl_faktur`,7,4),SUBSTRING(`f`.`tgl_faktur`,4,2),SUBSTRING(`f`.`tgl_faktur`,1,2)) AS UNSIGNED) >='".$awal."'";
$extraWhere = " CAST(CONCAT(SUBSTRING(`f`.`tgl_faktur`,7,4),SUBSTRING(`f`.`tgl_faktur`,4,2),SUBSTRING(`f`.`tgl_faktur`,1,2)) AS UNSIGNED) >='" . $awal . "' AND CAST(CONCAT(SUBSTRING(`f`.`tgl_faktur`,7,4),SUBSTRING(`f`.`tgl_faktur`,4,2),SUBSTRING(`f`.`tgl_faktur`,1,2)) AS UNSIGNED) <='" . $akhir . "'";
// $extraWhere = " `f`.`time`>='".$tanggal_awal."' AND `f`.`time`<='".$tanggal_akhir."'";
$groupBy = "";
$having = "";

echo json_encode(
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having)
);
