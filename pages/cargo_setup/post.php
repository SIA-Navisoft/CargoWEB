<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="receipt";


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

$r = $id = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');

if($r=='add_transport'){

	$form_data = array(
	'transport' => safeHTML($_POST['add_transport']),
	'createdBy' => $myid,
	'createdDate' => date('Y-m-d H:i:s'),		
	'status' => 1
	);
	insertNewRows("transport", $form_data);	
	echo 'added';
}

if($r=='cargoSetupType'){

	$form_data = array(
	'type' => safeHTML($_POST['addType']),
	'createdBy' => $myid,
	'createdDate' => date('Y-m-d H:i:s'),		
	'status' => 1
	);
	insertNewRows("cargo_type", $form_data);	
	echo 'added';
}

if($r=='cargoSetupCode'){

	$form_data = array(
	'code' => safeHTML($_POST['addCode']),
	'createdBy' => $myid,
	'createdDate' => date('Y-m-d H:i:s'),		
	'status' => 1
	);
	insertNewRows("cargo_code", $form_data);	
	echo 'added';
}

if($r=='addUomCode'){

	$check = mysqli_query($conn, "select code FROM unit_of_measurement WHERE code ='".safeHTML($_POST['addUom'])."'");

	if(mysqli_num_rows($check)==0){
		$form_data = array(
		'code' => safeHTML($_POST['addUom']),
		'name' => safeHTML($_POST['addName']),
		'NAVcode' => safeHTML($_POST['NAVuom']),
		'createdBy' => $myid,
		'createdDate' => date('Y-m-d H:i:s'),		
		'status' => 1
		);
		insertNewRows("unit_of_measurement", $form_data);	
		echo 'added';

	}else{
		echo 'uomcodeexists';
	}
}

if($r=='rru'){

	if(isset($_POST['rru'])){
		$form_data = array(
			'receipt_report_uom' => safeHTML($_POST['rru']),
			'editedBy' => $myid,
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("settings", $form_data, "");
	}
	
	if(isset($_POST['paam'])){
		$form_data1 = array(
			'period_work_done_report_uom' => safeHTML($_POST['paam']),
			'editedBy' => $myid,
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("settings", $form_data1, "");	
	}

	
}


if($r=='editUom'){
	
	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'name' => safeHTML($_POST['addName']),
			'NAVcode' => safeHTML($_POST['NAVuom']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("unit_of_measurement", $form_data, "WHERE code='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}


if($r=='addCountry'){
	
	if(!empty($_POST['addName']) && !empty($_POST['addCode'])){
		
		$form_data = array(
		'Code' => safeHTML($_POST['addCode']),
		'country' => safeHTML($_POST['addName']),
		'cargo_status' => safeHTML($_POST['cargoStatus']),
		'createdBy' => $myid,
		'createdDate' => date('Y-m-d H:i:s'),		
		'status' => 1
		);
		insertNewRows("countries", $form_data);	
		echo 'added';
	
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';		
	}
}

if($r=='editCountry'){

	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'country' => safeHTML($_POST['addName']),
			'cargo_status' => safeHTML($_POST['cargoStatus']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("countries", $form_data, "WHERE Code='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}

if($r=='addReceiver'){
	
	if(!empty($_POST['addName']) && !empty($_POST['addCode'])){
		
		$form_data = array(
		'Code' => safeHTML($_POST['addCode']),
		'name' => safeHTML($_POST['addName']),
		'country' => safeHTML($_POST['receiverCountry']),
		'createdBy' => $myid,
		'createdDate' => date('Y-m-d H:i:s'),		
		'status' => 1
		);
		insertNewRows("receivers", $form_data);	
		echo 'added';
	
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';		
	}
}

if($r=='addSender'){
	
	if(!empty($_POST['addName']) && !empty($_POST['addCode'])){
		
		$form_data = array(
		'Code' => safeHTML($_POST['addCode']),
		'name' => safeHTML($_POST['addName']),
		'createdBy' => $myid,
		'createdDate' => date('Y-m-d H:i:s'),		
		'status' => 1
		);
		insertNewRows("senders", $form_data);	
		echo 'added';
	
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';		
	}
}

if($r=='addDestination'){
	
	if(!empty($_POST['addName']) && !empty($_POST['addCode'])){
		
		$form_data = array(
		'Code' => safeHTML($_POST['addCode']),
		'name' => safeHTML($_POST['addName']),
		'createdBy' => $myid,
		'createdDate' => date('Y-m-d H:i:s'),		
		'status' => 1
		);
		insertNewRows("destinations", $form_data);	
		echo 'added';
	
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';		
	}
}

if($r=='editReceiver'){

	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'name' => safeHTML($_POST['addName']),
			'country' => safeHTML($_POST['receiverCountry']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("receivers", $form_data, "WHERE Code='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}

if($r=='editSender'){

	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'name' => safeHTML($_POST['addName']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("senders", $form_data, "WHERE Code='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}

if($r=='editDestination'){

	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'name' => safeHTML($_POST['addName']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("destinations", $form_data, "WHERE Code='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}
	
	mysqli_close($conn);


?>

