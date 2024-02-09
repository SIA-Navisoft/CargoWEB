<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="stock";


require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

$r = $id = $action = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');


if($action=='returnProduct' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("n_items", $form_data, "WHERE code='".safeHTML($id)."' LIMIT 1");
	echo 'restored';
}


if($action=='deleteProduct' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("n_items", $form_data, "WHERE code='".safeHTML($id)."' LIMIT 1");	
	echo 'deleted';
}

if($action=='returnEuom' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("additional_uom", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'restored';
}


if($action=='deleteEuom' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("additional_uom", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
	echo 'deleted';
}

if($action=='deleteLocationsCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("n_location", $form_data, "WHERE id='".$code."' LIMIT 1");
	echo 'deleted'; 	
}

if($action=='returnLocationsCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("n_location", $form_data, "WHERE id='".$code."' LIMIT 1");	
	echo 'restored';
}

	
	mysqli_close($conn);


?>

