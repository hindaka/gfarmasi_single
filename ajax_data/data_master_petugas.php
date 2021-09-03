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
$table = 'pegawai';

// Table's primary key
$primaryKey = 'id_pegawai';

//get parameter
// $id_resep = isset($_GET['r']) ? $_GET['r'] : '';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => '`p`.`id_pegawai`', 'dt' => 'id_pegawai', 'field' => 'id_pegawai', 'as' =>'id_pegawai'),
  array( 'db' => '`p`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama')
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
$joinQuery = "FROM `pegawai` AS `p`";
$extraWhere = "`p`.`id_depart`='3' AND `p`.`id_pegawai` NOT IN(SELECT id_pegawai FROM petugas WHERE instalasi='GFARMASI')";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
