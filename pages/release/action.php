<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


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

if (isset($_GET['issueAmount'])){$issueAmount = htmlentities($_GET['issueAmount'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issueDate'])){$issueDate = htmlentities($_GET['issueDate'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['actualDate'])){$actualDate = htmlentities($_GET['actualDate'], ENT_QUOTES, "UTF-8");}


	if(checkScannedIssuanceStatus($conn, $cardI)==100 || checkScannedIssuanceStatus($conn, $id)==100){
		
		echo 'STOP';
		die();
		
		
	}

if($issueDate){

	$issueDate = strtr($issueDate, '.', '-');
	$issueDate = date('Y-m-d', strtotime($issueDate));

}
if($actualDate){

	$actualDate = strtr($actualDate, '.', '-');
	$actualDate = date('Y-m-d', strtotime($actualDate));

}

include('../../functions/base.php');
require('../../inc/s.php');

if($action=='cancelLine' && $id){
	$form_data = array(
	'status' => 20
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
}

if($action=='approveLine' && $id){
	
	$form_data = array(
	'status' => 30
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
}

if($action=='receiveLine' && $id){
	
	$form_data = array(
	'status' => 40
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
}


// apstiprināt vienu cargo_line
if($action=='approveOneLine' && $id && $cardId){
	
	$query = mysqli_query($conn, "SELECT amount FROM cargo_line WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($query);	
	if($row['amount']>=$issueAmount){
	$form_data = array(
	'issueAmount' => safeHTML($issueAmount),
	'issueBy' => $myid,
	'issueDate' => safeHTML($issueDate),
	'actualDate' => safeHTML($actualDate),
	'status' => 30
	);
	updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."' LIMIT 1");	
	
	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];	
	
	$form_data = array(	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");	
	}
	
}

if($action=='receiveOneLine' && $id && $cardId){
	


	$getDocNr = mysqli_query($conn, "SELECT docNr, location, status, clientCode, clientName, ownerCode, ownerName, receiverCode, receiverName ,deliveryDate, cargoCode FROM cargo_header WHERE id='".intval($cardId)."'");
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


	$getLinesOrgValue = mysqli_query($conn, "SELECT amount FROM cargo_line WHERE id='".intval($id)."'");
	$glov = mysqli_fetch_array($getLinesOrgValue);
	
	if($glov['amount']==$issueAmount){
		
		$form_data = array(
		'issueAmount' => safeHTML($issueAmount),
		'issueBy' => $myid,
		'issueDate' => safeHTML($issueDate),	
		'status' => 40
		);
		updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."'");		
		
		
		$getLines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".intval($id)."'");
		while($row = mysqli_fetch_array($getLines)){

		$form_data = array(
		'docNr' => $row['docNr'],

		'cargoCode' => $cargoCode,	
		'productNr' => $row['productNr'],
		'activityDate' => $activityDate,
		'type' => 'negative',
		'amount' =>  $row['amount'],
		'location' =>  $row['location'],
		'clientCode' => $clientCode,
		'clientName' => $clientName,
		'ownerCode' => $ownerCode,
		'ownerName' => $ownerName,
		'receiverCode' => $receiverCode,
		'receiverName' => $receiverName,		
		'enteredDate' => date('Y-m-d H:i:s'),
		'orgLine' => intval($id),
		'enteredBy' => $myid
		);
		insertNewRows("item_ledger_entry", $form_data);	
			
		}		
		
	}else{
		
		$form_data = array(
		'issueAmount' => '',
		'issueBy' => $myid,
		'issueDate' => safeHTML($issueDate),
		'amount' => $glov['amount']-$issueAmount,
		'status' => 20
		);
		updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."'");		
		

		$namount = $glov['amount']-$issueAmount;

	  
		$query = "
		INSERT INTO cargo_line
          ( 
		  docNr,
          batchNo,
		  productNr,
		  productUmo,
		  transportNo,
		  activityDate,
		  amount,
		  issueAmount,
		  volume,
		  enteredDate,
		  location,
		  enteredBy,
		  thisDate,
		  issueBy,
		  issueDate,
		  thisTransport,
		  status,
		  editedBy,
		  editedDate,
		  orgLine
          )
        SELECT
		
		  docNr,
          batchNo,
		  productNr,
		  productUmo,
		  transportNo,
		  activityDate,
		  '".$issueAmount."',
		  '".$issueAmount."',
		  volume,
		  enteredDate,
		  location,
		  enteredBy,
		  thisDate,
		  issueBy,
		  issueDate,
		  thisTransport,
		  '40',
		  '".$myid."',
		  '".date('Y-m-d H:i:s')."',
		  '".intval($id)."' 		
           
        FROM cargo_line WHERE id='".intval($id)."'		
		";
		mysqli_query($conn, $query);	  
		
		$getLines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".intval($id)."'");
		while($row = mysqli_fetch_array($getLines)){

		$form_data = array(
		'docNr' => $row['docNr'],

		'cargoCode' => $cargoCode,	
		'productNr' => $row['productNr'],
		'activityDate' => $activityDate,
		'type' => 'negative',
		'amount' =>  $issueAmount,
		'location' =>  $row['location'],
		'clientCode' => $clientCode,
		'clientName' => $clientName,
		'ownerCode' => $ownerCode,
		'ownerName' => $ownerName,
		'receiverCode' => $receiverCode,
		'receiverName' => $receiverName,		
		'enteredDate' => date('Y-m-d H:i:s'),
		'orgLine' => intval($id),
		'enteredBy' => $myid
		);
		insertNewRows("item_ledger_entry", $form_data);	
			
		}		
		
	}
	

	
	$form_data = array(	
	'rowSent' => 1,
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");			
	
}

if($action=='cancelOneLine' && $id && $cardId){
	$form_data = array(
	'issueAmount' => '',
	'issueBy' => '',
	'issueDate' => '',	
	'status' => 20
	);
	updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($id)."' LIMIT 1");

	$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($cardId)."'");
	$gdr = mysqli_fetch_array($getDocNr);
	
	$docNr = $gdr['docNr'];	
	
	$form_data = array(	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");		
}

if($action=='deleteItButton' && $id){

	$isProcessed = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$id."'");
	$isP = mysqli_num_rows($isProcessed);

	if($myid==29 && $id && $isP==0){

		?>
		<script>
		$(document).ready(function () {
			$('#deleteIt').on('click', function(e) {
				e.preventDefault();
	
				if (confirm('UZMANĪBU! Vai tiešām vēlaties dzēst ierakstu?')) {
	
					$.ajax({
						url : '/pages/release/post.php?r=deleteIt&id=<?=$id;?>',
						type: "POST",
						data: $(this).serializeArray(),
						beforeSend: function(){
							$('#savebtn').html("gaidiet...");
							$("#savebtn").prop("disabled",true);
						},			
						success: function (data) {
							console.log(data);
	
							$('#contenthere').load('/pages/release/release.php?res=done');
						},
						error: function (jXHR, textStatus, errorThrown) {
							alert(errorThrown);
						}
					});
	
				}
	
			});
		});
		</script>
		<?php

		echo '<button type="submit" class="btn btn-default btn-xs" id="deleteIt" style="margin-left: 4px;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';
	}
	
}
	
	mysqli_close($conn);


?>

