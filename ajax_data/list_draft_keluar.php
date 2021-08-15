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
$table = 'obatkeluar_parent';

// Table's primary key
$primaryKey = 'id_obatkeluar_parent';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
// $columns = array(
//     array(
//       'db' => 'id_obat',
//       'dt' => 0,
//       'formatter' => function($d,$row){
//           return "<input class='minimal chk' id='pilihObat' type='checkbox' name='pilihObat[]' value='".$row['id_obat']."'>";
//         }
//     ),
//     array( 'db' => 'nama',  'dt' => 1 ),
//     array( 'db' => 'jenis',   'dt' => 2 ),
// );
$columns = array(
    array('db' => '`b`.`id_obatkeluar_parent`','dt' => 'id_obatkeluar_parent', 'field'=>'id_obatkeluar_parent', 'as'=>'id_obatkeluar_parent'),
    array('db' => '`b`.`id_warehouse`','dt' => 'id_warehouse', 'field'=>'id_warehouse', 'as'=>'id_warehouse'),
    array('db' => '`w`.`nama_ruang`','dt' => 'nama_ruang', 'field'=>'nama_ruang', 'as'=>'nama_ruang'),
    array('db' => '`b`.`tanggal_keluar`','dt' => 'tanggal_keluar', 'field'=>'tanggal_keluar', 'as'=>'tanggal_keluar'),
    array('db' => '`b`.`pemesan`','dt' => 'pemesan', 'field'=>'pemesan', 'as'=>'pemesan'),
    array('db' => '`b`.`sumber_dana`','dt' => 'sumber_dana', 'field'=>'sumber_dana', 'as'=>'sumber_dana'),

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

 $joinQuery = "FROM `obatkeluar_parent` AS `b` INNER JOIN `warehouse` AS `w` ON(`b`.`id_warehouse`=`w`.`id_warehouse`)";
 $extraWhere = "`b`.`status_keluar`='draft'";
 $groupBy = "";
 $having = "";

 echo json_encode(
 	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
 );
