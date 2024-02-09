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

$r = $id = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');

if($r=='add'){

	  $id=findLastRow("additional_services_header")+1;
      
      $invoice_number = 'AS'.sprintf("%'05d\n", $id);
	  $invoice_number = trim($invoice_number);
	if(!empty($_POST['cargoCode']) && !empty($_POST['ladingNr']) && !empty($_POST['clientCode'])){
		$cargoCode = safeHTML($_POST['cargoCode']);
	}else{
		$cargoCode = '';
	}

	$deliveryDate = strtr($_POST['deliveryDate'], '.', '-');
	$deliveryDate = date('Y-m-d', strtotime($deliveryDate));
	
	$landingDate = strtr($_POST['landingDate'], '.', '-');
	$landingDate = date('Y-m-d', strtotime($landingDate));	
	
	if($_POST['copyRest']=='on'){
		$copyRest = 1;
	}else{
		$copyRest = 0;
	}

	$applicationDate = strtr($_POST['applicationDate'], '.', '-');
	$applicationDate = date('Y-m-d', strtotime($applicationDate));

	$form_data = array(
	'docNr' => safeHTML($invoice_number),
	'ladingNr' => safeHTML($_POST['ladingNr']),

	'application_no' => safeHTML($_POST['applicationNo']),
	'application_date' => $applicationDate,
	'landingDate' => safeHTML($landingDate),
	'deliveryDate' => safeHTML($deliveryDate),
	'agreements' => safeHTML($_POST['agreements']),
	'clientCode' => safeHTML($_POST['clientCode']),
	'clientName' => safeHTML($_POST['clientName']),	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),		
	'acceptance_act_no' => safeHTML($_POST['acceptanceNr']),
	'transport' => safeHTML($_POST['transport']),
	'transport_name' => safeHTML($_POST['transportName']),	
	'home_delegate' => safeHTML($_POST['home_delegate']),
	'client_delegate' => safeHTML($_POST['client_delegate']),
	'client_product' => safeHTML($_POST['client_product']),
	'cargo_name' => safeHTML($_POST['cargo_name']),
	'description' => safeHTML($_POST['description'])
	);
	insertNewRows("additional_services_header", $form_data);

	echo $id;
	
}

if($r=='edit' && $id){


	$deliveryDate = strtr($_POST['deliveryDate'], '.', '-');
	$deliveryDate = date('Y-m-d', strtotime($deliveryDate));

	$landingDate = strtr($_POST['landingDate'], '.', '-');
	$landingDate = date('Y-m-d', strtotime($landingDate));

	$applicationDate = strtr($_POST['applicationDate'], '.', '-');
	$applicationDate = date('Y-m-d', strtotime($applicationDate));	

	$form_data = array(
	'ladingNr' => safeHTML($_POST['ladingNr']),

	
	'application_no' => safeHTML($_POST['applicationNo']),
	'application_date' => $applicationDate,
	'landingDate' => safeHTML($landingDate),
	'deliveryDate' => safeHTML($deliveryDate),	
	'agreements' => safeHTML($_POST['agreements']),	
	'clientCode' => safeHTML($_POST['clientCode']),
	'clientName' => safeHTML($_POST['clientName']),
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),	
	'acceptance_act_no' => safeHTML($_POST['acceptanceNr']),
	'transport' => safeHTML($_POST['transport']),
	'transport_name' => safeHTML($_POST['transportName']),	
	'home_delegate' => safeHTML($_POST['home_delegate']),
	'client_delegate' => safeHTML($_POST['client_delegate']),
	'client_product' => safeHTML($_POST['client_product']),
	'cargo_name' => safeHTML($_POST['cargo_name']),
	'description' => safeHTML($_POST['description'])	
	);
	updateSomeRow("additional_services_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
	$checkIssueance = mysqli_query($conn, "SELECT deliveryDate FROM additional_services_header WHERE docNr='".safeHTML($_POST['docNr'])."'");
	
	if(mysqli_num_rows($checkIssueance)==1){
		$iRow = mysqli_fetch_array($checkIssueance);
		$deliveryDate = date('Y-m-d', strtotime($iRow['deliveryDate']));	
	}

	if((isset($_POST['add_to_post'])) && mysqli_num_rows($checkIssueance)==1){


		$amount = safeHTML($_POST['amount']);
		$amount = str_replace(',', '.', $amount);					

		$thisDate = strtr($_POST['thisDate'], '.', '-');
		$thisDate = date('Y-m-d', strtotime($thisDate));

		$delta_net = $_POST['net'] - $_POST['document_net'];

		$serial=null;
		$query =  mysqli_query($conn, "
        SELECT id 
        FROM additional_services_line
		WHERE docNr='".safeHTML($_POST['docNr'])."' AND serialNo='".safeHTML($_POST['serialNo'])."'");
		
		if(mysqli_num_rows($query)==0){
			$serial = safeHTML($_POST['serialNo']);
		}

		if(isset($_POST['eresourceNr'])){
			$res_r = safeHTML($_POST['eresourceNr']);
		}

			$form_data_l = array(
			'docNr' => safeHTML($_POST['docNr']),
			
			'serialNo' => $serial,
			
			'transportNo' => safeHTML($_POST['transportNo']),
			'activityDate' => safeHTML($deliveryDate),
		
			'productUmo' => safeHTML($_POST['unitOfMeasurement']),
			
			'amount' => $amount,
			
			'thisDate' => safeHTML($thisDate),
			'thisTransport' => safeHTML($_POST['thisTransport']),		
			'enteredDate' => date('Y-m-d H:i:s'),
			'enteredBy' => $myid,

			'resource' => safeHTML($_POST['resource']),
			
			'comment' => safeHTML($_POST['comment'])		
			);
		insertNewRows("additional_services_line", $form_data_l);
	
		
	}

if($_POST['lResult']>0){


	$records = safeHTML($_POST['lResult']);

	for ($i=0; $i <= $records; $i++) {	

		$eThisDate = strtr($_POST['eThisDate'][$i], '.', '-');
		$eThisDate = date('Y-m-d', strtotime($eThisDate));		

		if($_POST['eBrack'][$i]==1){
			$eBrack = 1;
		}
		if($_POST['eBrack'][$i]==0){
			$eBrack = 0;
		}
	
		$delta_net = $_POST['eNet'][$i] - $_POST['e_document_net'][$i];


		$serial=null;
		$query =  mysqli_query($conn, "
        SELECT id 
        FROM additional_services_line
		WHERE docNr='".safeHTML($_POST['docNr'])."' AND serialNo='".safeHTML($_POST['eSerialNo'][$i])."' AND id!='".intval($_POST['eLineId'][$i])."'");
		
		if(mysqli_num_rows($query)==0){
			$serial = safeHTML($_POST['eSerialNo'][$i]);
		}


		
		$form_data_e = array(
		
		'productNr' => safeHTML($_POST['eProductNr'][$i]),
		
		'batchNo' => safeHTML($_POST['eBatchNo'][$i]),
		'serialNo' => $serial,
		
		'thisDate' => safeHTML($eThisDate),
		'thisTransport' => safeHTML($_POST['eThisTransport'][$i]),
		'amount' => safeHTML($_POST['eAmount'][$i]),
			
		'productUmo' => safeHTML($_POST['eUnitOfMeasurement'][$i]),	

		'activityDate' => $deliveryDate,
		'editedBy' => $myid,
		'editedDate' => date('Y-m-d H:i:s'),

		'resource' => safeHTML($_POST['eEresourceNr'][$i]),
		'comment' => safeHTML($_POST['eComment'][$i])
	);
		updateSomeRow("additional_services_line", $form_data_e, "WHERE id='".intval($_POST['eLineId'][$i])."' AND status='0'");
		
	}

}	
	
echo 'OK';	

}



if($r=='deleteIt' && $id){

	$query = mysqli_query($conn, "SELECT docNr FROM additional_services_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($query);
	
	$docNr = $row['docNr'];

	$result = mysqli_query($conn, "SELECT docNr FROM additional_services_line WHERE docNr='".$docNr."'");
	
	if(mysqli_num_rows($result)>0){
		mysqli_query($conn,"DELETE FROM additional_services_line WHERE docNr='".$docNr."'") or die(mysqli_error($conn));
	}

	mysqli_query($conn,"DELETE FROM additional_services_header WHERE id = '".intval($id)."'") or die(mysqli_error($conn));
		
}


 	
	mysqli_close($conn);


?>

