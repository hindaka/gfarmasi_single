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
$table = 'temp_stok_awal';

// Table's primary key
$primaryKey = 'id_temp';

//get parameter
$id_warehouse =  isset($_GET['w']) ? $_GET['w'] : '';
$id_obat =  isset($_GET['o']) ? $_GET['o'] : '';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => '`g`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`s`.`volume`', 'dt' => 'volume', 'field' => 'volume', 'as' => 'volume'),
  array( 'db' => '`s`.`harga_beli`', 'dt' => 'harga_beli', 'field' => 'harga_beli', 'as' => 'harga_beli'),
  array( 'db' => '`s`.`no_batch`', 'dt' => 'no_batch', 'field' => 'no_batch', 'as' => 'no_batch'),
  array( 'db' => '`s`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' => 'expired'),
  array( 'db' => '`s`.`merk`', 'dt' => 'merk', 'field' => 'merk', 'as' => 'merk'),
  array( 'db' => '`s`.`sumber_dana`', 'dt' => 'sumber_dana', 'field' => 'sumber_dana', 'as' => 'sumber_dana'),
  array( 'db' => '`s`.`tuslah`', 'dt' => 'tuslah', 'field' => 'tuslah', 'as' => 'tuslah'),
  array( 'db' => '`s`.`id_temp`', 'dt' => 'id_temp', 'field' => 'id_temp', 'as' => 'id_temp')
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

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );
// SELECT * FROM resep r INNER JOIN `warehouse_out` wo ON(r.id_resep=wo.id_resep) INNER JOIN warehouse_stok ws ON(wo.id_warehouse_stok=ws.id_warehouse_stok) INNER JOIN gobat g ON(ws.id_obat=g.id_obat) WHERE r.id_resep='113741'
$joinQuery = "FROM `temp_stok_awal` AS `s` INNER JOIN `gobat` AS `g` ON(`g`.`id_obat`=`s`.`id_obat`)";
$extraWhere = "`s`.`id_warehouse`='".$id_warehouse."' AND `s`.`id_obat`='".$id_obat."' AND `s`.`sync`='n'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
