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

if($r=='add'){

	  $id=findLastRow("cargo_header")+1;
      
      $invoice_number = 'PN'.sprintf("%'05d\n", $id);
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

	$acceptanceDate = strtr($_POST['acceptanceDate'], '.', '-');
	$acceptanceDate = date('Y-m-d', strtotime($acceptanceDate));

	
	
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
	'ship' => safeHTML($_POST['shipId']),

	'transportNo' => safeHTML($_POST['transportNo']),
	'deliveryDate' => safeHTML($deliveryDate),
	'deliveryType' => safeHTML($_POST['deliveryType']),
	'deliveryCode' => safeHTML($_POST['deliveryCode']),
	'location' => safeHTML($_POST['location']),
	'agreements' => safeHTML($_POST['agreements']),
	'clientCode' => safeHTML($_POST['clientCode']),
	'clientName' => safeHTML($_POST['clientName']),	
	'ownerCode' => safeHTML($_POST['clientCode']),
	'ownerName' => safeHTML($_POST['clientName']),
	'senderCode' => safeHTML($_POST['senderCode']),
	'senderName' => safeHTML($_POST['senderName']),
	'receiverCode' => safeHTML($_POST['receiverCode']),
	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),		
	'cargoCode' => $cargoCode,

	'weighing_act_no' => safeHTML($_POST['weighingNr']),
	'container_type_no' => safeHTML($_POST['containerType']),
	
	'cargo_status' => safeHTML($_POST['cargoStatus']),
	'seal_no' => safeHTML($_POST['sealNr']),
	'acceptance_act_no' => safeHTML($_POST['acceptanceNr']),
	'acceptance_act_date' => safeHTML($acceptanceDate),
	'lot_no' => safeHTML($_POST['lotNr']),
	'transport' => safeHTML($_POST['transport']),
	'transport_name' => safeHTML($_POST['transportName']),
	'resource' => mysqli_real_escape_string($conn, $_POST['resource']),
	'copyRest' => $copyRest,
	'thisTransport' => safeHTML($_POST['thisTransport']),
	'declaration_type_no' => safeHTML($_POST['declarationTypeNo'])
	);
	insertNewRows("cargo_header", $form_data);

	echo $id;
	
}

if($r=='edit' && $id){


	if(!empty($_POST['cargoCode']) && !empty($_POST['ladingNr']) && !empty($_POST['clientCode'])){
		$cargoCode = safeHTML($_POST['cargoCode']);
	}else{
		$cargoCode = '';
	}

	$deliveryDate = strtr($_POST['deliveryDate'], '.', '-');
	$deliveryDate = date('Y-m-d', strtotime($deliveryDate));

	$landingDate = strtr($_POST['landingDate'], '.', '-');
	$landingDate = date('Y-m-d', strtotime($landingDate));
	
	if(checkScannedHeaderStatus($conn, safeHTML($_POST['docNr']))==100){
		
		echo 'STOP';
		die();
		
		
	}

	if($_POST['copyRest']=='on'){
		$copyRest = 1;
	}else{
		$copyRest = 0;
	}

	$applicationDate = strtr($_POST['applicationDate'], '.', '-');
	$applicationDate = date('Y-m-d', strtotime($applicationDate));	

	$acceptanceDate = strtr($_POST['acceptanceDate'], '.', '-');
	$acceptanceDate = date('Y-m-d', strtotime($acceptanceDate));	

	$form_data = array(
	'ladingNr' => safeHTML($_POST['ladingNr']),

	
	'application_no' => safeHTML($_POST['applicationNo']),
	'application_date' => $applicationDate,
	'landingDate' => safeHTML($landingDate),
	'ship' => safeHTML($_POST['shipId']),

	'transportNo' => safeHTML($_POST['transportNo']),
	'deliveryDate' => safeHTML($deliveryDate),

	'deliveryType' => safeHTML($_POST['deliveryType']),
	'deliveryCode' =>safeHTML($_POST['deliveryCode']),
	'location' => safeHTML($_POST['location']),	
	'agreements' => safeHTML($_POST['agreements']),	
	'clientCode' => safeHTML($_POST['clientCode']),
	'clientName' => safeHTML($_POST['clientName']),
	'ownerCode' => safeHTML($_POST['clientCode']),
	'ownerName' => safeHTML($_POST['clientName']),
	'senderCode' => safeHTML($_POST['senderCode']),
	'senderName' => safeHTML($_POST['senderName']),	
	'receiverCode' => safeHTML($_POST['receiverCode']),

	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),	
	'cargoCode' => $cargoCode,

	'weighing_act_no' => safeHTML($_POST['weighingNr']),
	'container_type_no' => safeHTML($_POST['containerType']),

	'cargo_status' => safeHTML($_POST['cargoStatus']),
	'seal_no' => safeHTML($_POST['sealNr']),
	'acceptance_act_no' => safeHTML($_POST['acceptanceNr']),
	'acceptance_act_date' => safeHTML($acceptanceDate),
	'lot_no' => safeHTML($_POST['lotNr']),
	'transport' => safeHTML($_POST['transport']),
	'transport_name' => safeHTML($_POST['transportName']),
	'resource' => mysqli_real_escape_string($conn, $_POST['resource']),
	'copyRest' => $copyRest,
	'thisTransport' => safeHTML($_POST['thisTransportEdit']),
	'declaration_type_no' => safeHTML($_POST['declarationTypeNo'])
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
	$checkIssueance = mysqli_query($conn, "SELECT deliveryDate, resource, cargo_status FROM cargo_header WHERE docNr='".safeHTML($_POST['docNr'])."'");
	
	if(mysqli_num_rows($checkIssueance)==1){
		$iRow = mysqli_fetch_array($checkIssueance);
		$deliveryDate = date('Y-m-d', strtotime($iRow['deliveryDate']));
		
		$resource = mysqli_real_escape_string($conn, $iRow['resource']);	
		$cargo_status = $iRow['cargo_status'];	
	}
	
	if((isset($_POST['add_to_post'])) && (isset($_POST['productNr']) && $_POST['productNr']!='') && (isset($_POST['amount']) && $_POST['amount']>0) && mysqli_num_rows($checkIssueance)==1){

		$amount = safeHTML($_POST['amount']);
		$amount = str_replace(',', '.', $amount);		

		$assistantAmount = safeHTML($_POST['assistantAmount']);
		$assistantAmount = str_replace(',', '.', $assistantAmount);		

		$volume = safeHTML($_POST['volume']);
		$volume = str_replace(',', '.', $volume);

		$place_count = safeHTML($_POST['place_count']);
		$place_count = str_replace(',', '.', $place_count);		

		$thisDate = strtr($_POST['thisDate'], '.', '-');
		$thisDate = date('Y-m-d', strtotime($thisDate));	

		if($_POST['brack']=='on'){
			$brack = 1;
		}else{
			$brack = 0;
		}

		$delta_net = $_POST['net'] - $_POST['document_net'];

		$serial=null;
		$query =  mysqli_query($conn, "
        SELECT id 
        FROM cargo_line
		WHERE docNr='".safeHTML($_POST['docNr'])."' AND serialNo='".safeHTML($_POST['serialNo'])."'");
		
		if(mysqli_num_rows($query)==0){
			$serial = safeHTML($_POST['serialNo']);
			$serialPlan = safeHTML($_POST['serialNoPlan']);
		}

		if(isset($_POST['eresourceNr'])){
			$res_r = safeHTML($_POST['eresourceNr']);
		}

		if($_POST['extraResource']!='on'){
			$form_data_l = array(
			'docNr' => safeHTML($_POST['docNr']),
			'batchNo' => safeHTML($_POST['batchNo']),
			'ladingNr' => safeHTML($_POST['appendLadingNr']),
			'serialNo' => $serial,
			'serialNoPlan' => $serialPlan,
			
			'place_count' => safeHTML($_POST['place_count']),
			'transportNo' => safeHTML($_POST['transportNo']),
			'activityDate' => safeHTML($deliveryDate),
		
			'productNr' => safeHTML($_POST['productNr']),
			'productUmo' => safeHTML($_POST['unitOfMeasurement']),
			'assistantUmo' => safeHTML($_POST['assistantUnitOfMeasurement']),
			'amount' => $amount,
			'assistant_amount' => $assistantAmount,
			'volume' => $volume,
			'location' => safeHTML($_POST['lineLocation']),
			'thisDate' => safeHTML($thisDate),
			'thisTransport' => safeHTML($_POST['thisTransport']),		
			'enteredDate' => date('Y-m-d H:i:s'),
			'enteredBy' => $myid,
			'tare' => safeHTML($_POST['tare']),
			'gross' => safeHTML($_POST['gross']),
			'net' => safeHTML($_POST['net']),
			'cubicMeters' => safeHTML($_POST['cubicMeters']),
			
			'weighing_act_no' => safeHTML($_POST['weighingNr']),
			'container_type_no' => safeHTML($_POST['containerType']),
			'declaration_type_no' => safeHTML($_POST['declarationsType']),
			'cargo_status' => $cargo_status,
			'seal_no' => safeHTML($_POST['sealNr']),
			'lot_no' => safeHTML($_POST['lotNr']),
			'brack' => $brack,
			'document_net' => safeHTML($_POST['document_net']),
			'delta_net' => intval($delta_net),
			'resource' => $resource,
			'extra_resource' => safeHTML($_POST['extraResource'])		
			);
		insertNewRows("cargo_line", $form_data_l);

		}

		if($_POST['extraResource']){

			$form_data_2 = array(
				'docNr' => safeHTML($_POST['docNr']),
				
				'activityDate' => safeHTML($deliveryDate),
				'productUmo' => safeHTML($_POST['unitOfMeasurement']),
				'amount' => $amount,
				'thisDate' => safeHTML($thisDate),
					
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'resource' => $res_r,
				'extra_resource' => safeHTML($_POST['extraResource'])		
				);
				insertNewRows("cargo_line", $form_data_2);

		}
	
		
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


		$serial=$serialPlan=null;
		$query =  mysqli_query($conn, "
        SELECT id 
        FROM cargo_line
		WHERE docNr='".safeHTML($_POST['docNr'])."' AND serialNo='".safeHTML($_POST['eSerialNo'][$i])."' AND id!='".intval($_POST['eLineId'][$i])."'");
		
		if(mysqli_num_rows($query)==0){
			$serial = safeHTML($_POST['eSerialNo'][$i]);
			$serialPlan = safeHTML($_POST['eSerialNoPlan'][$i]);
		}

		
		if($_POST['eEresourceNr'][$i]!=''){
			$res_e = safeHTML($_POST['eEresourceNr'][$i]);


			$form_data_e = array(
				'amount' => safeHTML($_POST['eAmount'][$i]),	
				'productUmo' => safeHTML($_POST['eUnitOfMeasurement'][$i]),
				'editedBy' => $myid,
				'editedDate' => date('Y-m-d H:i:s'),
				'resource' => $res_e
			);
				updateSomeRow("cargo_line", $form_data_e, "WHERE id='".intval($_POST['eLineId'][$i])."' AND status='0' AND extra_resource='on'");	
				echo '<'.$_POST['eLineId'][$i].' M HIT HERE '.$i.'> ';		
		}else{
			echo '<'.$_POST['eLineId'][$i].' HIT HERE '.$i.'> ';
			
			if(isset($_POST['eProductNr'][$i])){
			
				$form_data_e = array(
						
					'productNr' => safeHTML($_POST['eProductNr'][$i]),
					'place_count' => safeHTML($_POST['e_place_count'][$i]),
					'batchNo' => safeHTML($_POST['eBatchNo'][$i]),
					'ladingNr' => safeHTML($_POST['eThisLadingNr'][$i]),
					'serialNo' => $serial,
					'serialNoPlan' => $serialPlan,
					'location' => safeHTML($_POST['eLineLocation'][$i]),
					'thisDate' => safeHTML($eThisDate),
					'thisTransport' => safeHTML($_POST['eThisTransport'][$i]),
					'amount' => safeHTML($_POST['eAmount'][$i]),
					'assistant_amount' => safeHTML($_POST['eAssistantAmount'][$i]),	
					'productUmo' => safeHTML($_POST['eUnitOfMeasurement'][$i]),	
					'assistantUmo' => safeHTML($_POST['eAssistantUnitOfMeasurement'][$i]),
					'volume' => safeHTML($_POST['eVolume'][$i]),
					'activityDate' => $deliveryDate,
					'editedBy' => $myid,
					'editedDate' => date('Y-m-d H:i:s'),
					'tare' => safeHTML($_POST['eTare'][$i]),
					'gross' => safeHTML($_POST['eGross'][$i]),
					'net' => safeHTML($_POST['eNet'][$i]),
					'cubicMeters' => safeHTML($_POST['eCubicMeters'][$i]),

					'weighing_act_no' => safeHTML($_POST['eWeighingNr'][$i]),
					'container_type_no' => safeHTML($_POST['eContainerType'][$i]),
					'declaration_type_no' => safeHTML($_POST['eDeclarationsType'][$i]),
					'cargo_status' => $cargo_status,
					'seal_no' => safeHTML($_POST['eSealNr'][$i]),
					'lot_no' => safeHTML($_POST['eLotNr'][$i]),
					'brack' => $eBrack,
					'document_net' => safeHTML($_POST['e_document_net'][$i]),
					'delta_net' => intval($delta_net),
					'resource' => $resource
				);
				
				updateSomeRow("cargo_line", $form_data_e, "WHERE id='".intval($_POST['eLineId'][$i])."' AND status='0' AND (extra_resource!='on' OR extra_resource IS NULL)");
			
			}
		}		
	}

		$form_data_e_all = array(
		'editedBy' => $myid,
		'editedDate' => date('Y-m-d H:i:s'),
		'resource' => $resource
		);
		updateSomeRow("cargo_line", $form_data_e_all, "WHERE docNr='".safeHTML($_POST['docNr'])."' AND status<'30' AND extra_resource!='on'");	

}	
	
echo 'OK';	

}

if($r=='addComment' && $id){
	
		$form_data_e = array(
		'comment' => safeHTML($_POST['comment'])
		);
		updateSomeRow("cargo_line", $form_data_e, "WHERE id='".intval($id)."'");	
		
		$getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_line WHERE id='".intval($id)."'");
		$row = mysqli_fetch_array($getDocNr);
		
		$docNr = $row['docNr'];
		
		
		$form_data = array(	
		'lastBy' => $myid,
		'lastDate' => date('Y-m-d H:i:s')
		);
		updateSomeRow("cargo_header", $form_data, "WHERE docNr='".$docNr."' LIMIT 1");		
}

if($r=='deleteIt' && $id){

	$query = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($query);
	
	$docNr = $row['docNr'];

	$clogs = mysqli_query($conn, "SELECT id FROM xml_imports WHERE docNr='".$docNr."' AND status=0 LIMIT 1");
	if(mysqli_num_rows($clogs)==1){
		
		$form_data = array(	
		'status' => 1,
		'deletedBy' => $myid,
		'deletedDate' => date('Y-m-d H:i:s')
		);
		updateSomeRow("xml_imports", $form_data, "WHERE docNr='".$docNr."' AND status=0 LIMIT 1");	
		
	}	

	$result = mysqli_query($conn, "SELECT docNr FROM cargo_line WHERE docNr='".$docNr."'");
	
	if(mysqli_num_rows($result)>0){
		mysqli_query($conn,"DELETE FROM cargo_line WHERE docNr='".$docNr."'") or die(mysqli_error($conn));
		mysqli_query($conn,"DELETE FROM scanner_lines WHERE docNr='".$docNr."'") or die(mysqli_error($conn));
		mysqli_query($conn,"DELETE FROM scanner_lines_images WHERE docNr='".$docNr."'") or die(mysqli_error($conn));		
	}

	mysqli_query($conn,"DELETE FROM cargo_header WHERE id = '".intval($id)."'") or die(mysqli_error($conn));
	
	
	

		
}


 	
	mysqli_close($conn);


?>

