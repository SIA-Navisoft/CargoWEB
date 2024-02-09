<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="additional_services";


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
if (isset($_GET['cardId'])){$cardId = htmlentities($_GET['cardId'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if($action=='deleteLine' && $id){
	
	$getDocNr = mysqli_query($conn, "SELECT docNr FROM additional_services_line WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$form_data = array(	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("additional_services_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");
	
	deleteSomeRow("additional_services_line", "WHERE id = '".intval($id)."'");

}

if($action=='approveLine' && $id){
	
	$form_data = array(
	'status' => 10
	);
	updateSomeRow("additional_services_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
	$getDocNr = mysqli_query($conn, "SELECT docNr, status FROM additional_services_header WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$status = $gdr['status'];

	$form_data = array(
	'status' => $status
	);
	updateSomeRow("additional_services_line", $form_data, "WHERE docNr='".$docNr."' AND status='0'");	
	
}

if($action=='receiveLine' && $id){
	
	$form_data = array(
	'status' => 20
	);
	updateSomeRow("additional_services_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");

	$getDocNr = mysqli_query($conn, "SELECT docNr, status, clientCode, clientName, deliveryDate FROM additional_services_header WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	$status = $gdr['status'];

	$form_data = array(
		'status' => $status,
		'action' => 10
		);
		updateSomeRow("additional_services_line", $form_data, "WHERE docNr='".$docNr."' AND status<'20'");

}

if($action=='cancelLine' && $id && $cardId){
	$form_data = array(
	'status' => 0
	);
	updateSomeRow("additional_services_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	

	$getDocNr = mysqli_query($conn, "SELECT docNr FROM additional_services_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);	
	$docNr = $gdr['docNr'];
	
	$form_data_l = array(
	'status' => 0
	);
	updateSomeRow("additional_services_line", $form_data_l, "WHERE docNr='".$docNr."' AND status='10'");	
}



	
	mysqli_close($conn);


?>

