<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
$table = 'migrasi_obat';
// Table's primary key
$primaryKey = 'id_migrasi';

$columns = array(
    array('db' => '`m`.`id_migrasi`', 'dt' => 'id_migrasi', 'field' => 'id_migrasi', 'as' => 'id_migrasi'),
    array('db' => '`m`.`created_at`', 'dt' => 'created_at', 'field' => 'created_at', 'as' => 'created_at'),
    array('db' => '`m`.`id_obat_lama`', 'dt' => 'id_obat_lama', 'field' => 'id_obat_lama', 'as' => 'id_obat_lama'),
    array('db' => '`m`.`id_obat`', 'dt' => 'id_obat', 'field' => 'id_obat', 'as' => 'id_obat'),
    array('db' => '`m`.`jenis`', 'dt' => 'jenis', 'field' => 'jenis', 'as' => 'jenis'),
    array('db' => '`m`.`merk`', 'dt' => 'merk', 'field' => 'merk', 'as' => 'merk'),
    array('db' => '`m`.`pabrikan`', 'dt' => 'pabrikan', 'field' => 'pabrikan', 'as' => 'pabrikan'),
    array('db' => '`m`.`sumber_dana`', 'dt' => 'sumber_dana', 'field' => 'sumber_dana', 'as' => 'sumber_dana'),
    array('db' => '`m`.`harga_beli`', 'dt' => 'harga_beli', 'field' => 'harga_beli', 'as' => 'harga_beli'),
    array('db' => '`m`.`no_batch`', 'dt' => 'no_batch', 'field' => 'no_batch', 'as' => 'no_batch'),
    array('db' => '`m`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' => 'expired'),
    array('db' => '`m`.`vol`', 'dt' => 'vol', 'field' => 'vol', 'as' => 'vol'),
    array('db' => '`g`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' => 'nama'),
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

// $joinQuery = " FROM `migrasi_obat` AS `m`";
$joinQuery = " FROM `migrasi_obat` AS `m` INNER JOIN `gobat` AS `g` ON(`m`.`id_obat`=`g`.`id_obat`)";
// $extraWhere = " `g`.`flag_single_id`='new'";
$extraWhere = "";
$groupBy = "";
$having = "";
//testing injector
// $request = [
//     "columns" => [
//         "searchable" => false,
//         "data" => NULL,
//     ]
// ];
echo json_encode(
    // SSP::simple($request, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having)
    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having)
);
