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
$table = 'gobat';

// Table's primary key
$primaryKey = 'id_obat';

$columns = array(
  array('db' => '`g`.`id_obat`','dt' => 'id_obat', 'field'=>'id_obat', 'as'=>'id_obat'),
  array('db' => '`g`.`kategori`','dt' => 'kategori', 'field'=>'kategori', 'as'=>'kategori'),
  array('db' => '`g`.`nama`','dt' => 'nama', 'field'=>'nama', 'as'=>'nama'),
  array('db' => '`g`.`nama_dagang`','dt' => 'nama_dagang', 'field'=>'nama_dagang', 'as'=>'nama_dagang'),
  array('db' => '`g`.`nama_fornas`','dt' => 'nama_fornas', 'field'=>'nama_fornas', 'as'=>'nama_fornas'),
  array('db' => '`g`.`kandungan`','dt' => 'kandungan', 'field'=>'kandungan', 'as'=>'kandungan'),
  array('db' => '`g`.`bentuk_sediaan`','dt' => 'bentuk_sediaan', 'field'=>'bentuk_sediaan', 'as'=>'bentuk_sediaan'),
  array('db' => '`g`.`satuan_jual`','dt' => 'satuan_jual', 'field'=>'satuan_jual', 'as'=>'satuan_jual'),
  array('db' => '`g`.`satuan`','dt' => 'satuan', 'field'=>'satuan', 'as'=>'satuan'),
  array('db' => '`g`.`kemasan`','dt' => 'kemasan', 'field'=>'kemasan', 'as'=>'kemasan'),
  array('db' => '`g`.`jenis`','dt' => 'jenis', 'field'=>'jenis', 'as'=>'jenis'),
  
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
 require( 'ssp.customized.class.php' );

 $joinQuery = " FROM `gobat` AS `g`";
 $extraWhere = " `g`.`flag_single_id`='new'";
 $groupBy = "";
 $having = "";

 echo json_encode(
 	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
 );
