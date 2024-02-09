<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="agreements";


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


if($action=='deleteLine' && $id){
	$form_data = array(
	'deletedBy' => intval($myid),
	'deletedDate' => date('Y-m-d H:i:s'),
	'deleted' => 1
	);
	updateSomeRow("agreements_lines", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'deleted';
}


if($action=='agrApprove' && $id){
	
	$contractNr = mysqli_real_escape_string($conn, $id);

	$form_data = array(
	'status' => 10,
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("agreements", $form_data, "WHERE contractNr='".$contractNr."' LIMIT 1");
	
}

if($action=='agrClose' && $id){
	
	$contractNr = mysqli_real_escape_string($conn, $id);

	$form_data = array(
	'status' => 20,
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("agreements", $form_data, "WHERE contractNr='".$contractNr."' LIMIT 1");
	
}

if($action=='agrUnlock' && $id){
	
	$contractNr = mysqli_real_escape_string($conn, $id);

	$form_data = array(
	'status' => 0,
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("agreements", $form_data, "WHERE contractNr='".$contractNr."' LIMIT 1");
	
}

if($action=='agrDelete' && $id){
	
	$contractNr = mysqli_real_escape_string($conn, $id);
	
	$form_data = array(
	'deletedBy' => intval($myid),
	'deletedDate' => date('Y-m-d H:i:s'),
	'deleted' => 1
	);
	updateSomeRow("agreements", $form_data, "WHERE contractNr='".$contractNr."' LIMIT 1");
	echo 'deleted';
	
}

	mysqli_close($conn);


?>

