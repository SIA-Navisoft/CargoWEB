<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="cargo_setup";


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


if($action=='returnType' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("cargo_type", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
	echo 'restored';
}


if($action=='deleteType' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("cargo_type", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'deleted'; 	
}

if($action=='deleteTransport' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("transport", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'deleted'; 	
}

if($action=='returnTransport' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("transport", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
	echo 'restored';
}

if($action=='returnCode' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("cargo_code", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'restored';	
}


if($action=='deleteCode' && $id){
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("cargo_code", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	echo 'deleted'; 
}

if($action=='returnUomCode' && $id){
	$code = safeHTML($id);

	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("unit_of_measurement", $form_data, "WHERE code='".$code."' LIMIT 1");	
	echo 'restored';	
}


if($action=='deleteUomCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("unit_of_measurement", $form_data, "WHERE code='".$code."' LIMIT 1");
	echo 'deleted';
}

if($action=='returnCountryCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("countries", $form_data, "WHERE Code='".$code."' LIMIT 1");	
	echo 'restored';
}


if($action=='deleteCountryCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("countries", $form_data, "WHERE Code='".$code."' LIMIT 1");
	echo 'deleted'; 	
}


if($action=='returnReceiverCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("receivers", $form_data, "WHERE Code='".$code."' LIMIT 1");	
	echo 'restored';
}


if($action=='deleteReceiverCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("receivers", $form_data, "WHERE Code='".$code."' LIMIT 1");
	echo 'deleted'; 	
}

if($action=='returnSenderCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("senders", $form_data, "WHERE Code='".$code."' LIMIT 1");	
	echo 'restored';
}

if($action=='returnDestinationCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 1
	);
	updateSomeRow("destinations", $form_data, "WHERE Code='".$code."' LIMIT 1");	
	echo 'restored';
}


if($action=='deleteSenderCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("senders", $form_data, "WHERE Code='".$code."' LIMIT 1");
	echo 'deleted'; 	
}

if($action=='deleteDestinationCode' && $id){
	$code = safeHTML($id);
	
	$form_data = array(
	'editedBy' => intval($myid),
	'editedDate' => date('Y-m-d H:i:s'),
	'status' => 0
	);
	updateSomeRow("destinations", $form_data, "WHERE Code='".$code."' LIMIT 1");
	echo 'deleted'; 	
}
	
	mysqli_close($conn);


?>

