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

$r = $id = $action = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cardId'])){$cardId = htmlentities($_GET['cardId'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if($action=='deleteLine' && $id){
	
	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_line WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$form_data = array(	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");
	
	
	$result = mysqli_query($conn, "SELECT serialNo, serialNoPlan, scanId FROM cargo_line WHERE docNr='".$docNr."' AND id = '".intval($id)."'");
	
	if(mysqli_num_rows($result)>0){
		
		$row = mysqli_fetch_array($result);
		
		$result2 = mysqli_query($conn, "SELECT img FROM scanner_lines WHERE docNr='".$docNr."' AND id='".$row['scanId']."'");
		$row2 = mysqli_fetch_array($result2);
		if(mysqli_num_rows($result2)>0){
			
			
			mysqli_query($conn,"DELETE FROM scanner_lines_images WHERE docNr='".$docNr."' AND filekey='".$row2['img']."'") or die(mysqli_error($conn));
			
		}	
		mysqli_query($conn,"DELETE FROM scanner_lines WHERE docNr='".$docNr."' AND id='".$row['scanId']."'") or die(mysqli_error($conn));		
	}	
	
	deleteSomeRow("cargo_line", "WHERE id = '".intval($id)."'");

}

if($action=='approveLine' && $id){
	
	$form_data = array(
	'status' => 10
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
	$getDocNr = mysqli_query($conn, "SELECT docNr, location, status FROM cargo_header WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$status = $gdr['status'];

	$form_data = array(
	'status' => $status
	);
	updateSomeRow("cargo_line", $form_data, "WHERE docNr='".$docNr."' AND status='0'");	
	
}

if($action=='receiveLine' && $id){
	
	$form_data = array(
	'status' => 20
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");

	$getDocNr = mysqli_query($conn, "SELECT docNr, location, status, clientCode, clientName, ownerCode, ownerName, receiverCode, receiverName ,deliveryDate, cargoCode, resource FROM cargo_header WHERE id='".intval($id)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$status = $gdr['status'];
	
	$clientCode = $gdr['clientCode'];
	$clientName = $gdr['clientName'];
	$ownerCode = $gdr['ownerCode'];
	$ownerName = $gdr['ownerName'];
	$receiverCode = $gdr['receiverCode'];
	$receiverName = $gdr['receiverName'];
	$activityDate = $gdr['deliveryDate'];
	$cargoCode = $gdr['cargoCode'];
	$resource = $gdr['resource']; 

	
	
	
	$getLines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$docNr."' AND  status<'20'");
	while($row = mysqli_fetch_array($getLines)){

	$form_data = array(
	'docNr' => $row['docNr'],
	'cargoLine' => $row['id'],

	'cargoCode' => $cargoCode,	
	'productNr' => $row['productNr'],
	'deliveryDate' => $activityDate,
	'activityDate' => $activityDate,
	'type' => 'positive',
	'amount' =>  $row['amount'],
	'assistant_amount' => $row['assistant_amount'],
	'location' =>  $row['location'],
	'clientCode' => $clientCode,
	'clientName' => $clientName,
	'ownerCode' => $ownerCode,
	'ownerName' => $ownerName,
	'receiverCode' => $receiverCode,
	'receiverName' => $receiverName,		
	'enteredDate' => date('Y-m-d H:i:s'),
	'enteredBy' => $myid,
	'action' => 10,
	'productUmo' => $row['productUmo'],
	'assistantUmo' => $row['assistantUmo'],
	'status' => 20,
	'resource' => $resource,
	'tare' => $row['tare'],
	'gross' => $row['gross'],
	'net' => $row['net'],
	'cubicMeters' => $row['cubicMeters'],
	'serialNo' => $row['serialNo'],
	'batchNo' => $row['batchNo'],
	'lot_no' => $row['lot_no'],
	'container_type_no' => $row['container_type_no'],
	'thisTransport' => $row['thisTransport'],
	'declaration_type_no' => $row['declaration_type_no'],
	'cargo_status' => $row['cargo_status'],
	'seal_no' => $row['seal_no'],
	'weighing_act_no' => $row['weighing_act_no']
	);
	insertNewRows("item_ledger_entry", $form_data);	
		
	}

	$form_data = array(
		'status' => $status,
		'action' => 10
		);
		updateSomeRow("cargo_line", $form_data, "WHERE docNr='".$docNr."' AND status<'20'");

			$query = "INSERT INTO cargo_header_receive  (SELECT * FROM cargo_header WHERE id='".intval($id)."')";
			mysqli_query($conn, $query);
			
			$query2 = "INSERT INTO cargo_line_receive  (SELECT * FROM cargo_line WHERE docNr='".$docNr."')";
			mysqli_query($conn, $query2);			

			
		
	
}

if($action=='cancelLine' && $id && $cardId){
	$form_data = array(
	'status' => 0
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	

	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);	
	$docNr = $gdr['docNr'];
	
	$form_data_l = array(
	'status' => 0
	);
	updateSomeRow("cargo_line", $form_data_l, "WHERE docNr='".$docNr."' AND status='10'");	
}


// apstiprināt vienu cargo_line
if($action=='approveOneLine' && $id && $cardId){
	
	$form_data = array(
	'status' => 10
	);
	updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
	
	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];	
	
	$form_data = array(	
	'rowBeforeSent' => 1,
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");	
	
	
}

if($action=='receiveOneLine' && $id && $cardId){
	


	$getDocNr = mysqli_query($conn, "SELECT docNr, location, status, clientCode, clientName, ownerCode, ownerName, receiverCode, receiverName ,deliveryDate, cargoCode, resource  FROM cargo_header WHERE id='".intval($cardId)."'") or die(mysqli_error($conn));
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];
	
	$status = $gdr['status'];
	
	$clientCode = $gdr['clientCode'];
	$clientName = $gdr['clientName'];
	$ownerCode = $gdr['ownerCode'];
	$ownerName = $gdr['ownerName'];
	$receiverCode = $gdr['receiverCode'];
	$receiverName = $gdr['receiverName'];
	$activityDate = $gdr['deliveryDate'];
	$cargoCode = $gdr['cargoCode'];
	$resource = $gdr['resource'];

	$form_data = array(
		'status' => 20,
		'action' => 10
	);
	updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."'");		
	
	
	$getLines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".intval($id)."'");
	while($row = mysqli_fetch_array($getLines)){

	$form_data = array(
	'docNr' => $row['docNr'],

	'cargoCode' => $cargoCode,
	'cargoLine' => intval($id),	
	'productNr' => $row['productNr'],
	'deliveryDate' => $activityDate,
	'activityDate' => $activityDate,
	'type' => 'positive',
	'amount' =>  $row['amount'],
	'assistant_amount' => $row['assistant_amount'],
	'location' =>  $row['location'],
	'clientCode' => $clientCode,
	'clientName' => $clientName,
	'ownerCode' => $ownerCode,
	'ownerName' => $ownerName,
	'receiverCode' => $receiverCode,
	'receiverName' => $receiverName,		
	'enteredDate' => date('Y-m-d H:i:s'),
	'enteredBy' => $myid,
	'action' => 10,
	'productUmo' => $row['productUmo'],
	'assistantUmo' => $row['assistantUmo'],
	'status' => 20,
	'resource' => $resource,
	'tare' => $row['tare'],
	'gross' => $row['gross'],
	'net' => $row['net'],
	'cubicMeters' => $row['cubicMeters'],
	'serialNo' => $row['serialNo'],
	'batchNo' => $row['batchNo'],
	'lot_no' => $row['lot_no'],
	'container_type_no' => $row['container_type_no'],
	'thisTransport' => $row['thisTransport'],
	'declaration_type_no' => $row['declaration_type_no'],
	'cargo_status' => $row['cargo_status'],
	'seal_no' => $row['seal_no'],
	'weighing_act_no' => $row['weighing_act_no']	
	);
	insertNewRows("item_ledger_entry", $form_data);	
		
	}
	
	$form_data = array(	
	'rowSent' => 1,
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");	

echo $id.' && '.$cardId.' && '.$docNr;
	
}


if($action=='cancelOneLine' && $id && $cardId){
	$form_data = array(
	'status' => 0
	);
	updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."' LIMIT 1");

	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];	
	
	$form_data = array(	
	'rowBeforeSent' => 0,
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");		
}

	
	mysqli_close($conn);


?>

