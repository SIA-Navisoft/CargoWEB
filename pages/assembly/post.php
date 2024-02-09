<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="assembly";


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

	  $id=findLastRow("cargo_header");
      
      $invoice_number = 'PN'.sprintf("%'05d\n", $id+1);
	  $invoice_number = trim($invoice_number);
	if(!empty($_POST['cargoCode']) && !empty($_POST['ladingNr']) && !empty($_POST['clientCode'])){
		$cargoCode = safeHTML($_POST['cargoCode']);
	}else{
		$cargoCode = '';
	}

	$deliveryDate = strtr($_POST['deliveryDate'], '.', '-');
	$deliveryDate = date('Y-m-d', strtotime($deliveryDate));

	$form_data = array(
	'docNr' => safeHTML($invoice_number),
	'ladingNr' => safeHTML($_POST['ladingNr']),
	'agreements' => safeHTML($_POST['agreements']),
	'transportNo' => safeHTML($_POST['transportNo']),
	'deliveryDate' => safeHTML($deliveryDate),
	'deliveryType' => safeHTML($_POST['deliveryType']),
	'deliveryCode' => safeHTML($_POST['deliveryCode']),
	'location' => safeHTML($_POST['location']),
	'clientCode' => safeHTML($_POST['clientCode']),
	'clientName' => safeHTML($_POST['clientName']),
	'ownerCode' => safeHTML($_POST['ownerCode']),
	'ownerName' => safeHTML($_POST['ownerName']),
	'receiverCode' => safeHTML($_POST['receiverCode']),
	'receiverName' => safeHTML($_POST['receiverName']),		
	'status' => 20,
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),	
	'cargoCode' => $cargoCode
	);
	insertNewRows("cargo_header", $form_data);	

	$query = "UPDATE cargo_settings SET cargo_code=cargo_code+1";
	mysqli_query($conn, $query);	
}

if($r=='edit' && $id){

	$form_data = array(
	'location' => safeHTML($_POST['location']),	
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
		
}






if($r=='editTwo' && $id){
echo $_POST['locationTwo'].' <';
	$form_dataTwo = array(
	'location' => safeHTML($_POST['locationTwo']),
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s')
	);
	updateSomeRow("cargo_header", $form_dataTwo, "WHERE id='".intval($id)."' LIMIT 1");
		
}




if($r=='changeStorage' && $id){

	$resource = mysqli_real_escape_string($conn, $_POST['resourceNr']);

	$records = safeHTML($_POST['lResult']);

	$actionDate = strtr($_POST['actionDate'], '.', '-');
	$actionDate = date('Y-m-d', strtotime($actionDate));

	for ($i=0; $i <= $records; $i++) {
		$lineId = safeHTML($_POST['lineId'][$i]);
		$changeLocation = safeHTML($_POST['changeLocation'][$i]);
		$orgAmount = safeHTML($_POST['orgAmount'][$i]);
		$newAmount = safeHTML($_POST['newAmount'][$i]);
		$productNr = safeHTML($_POST['productNr'][$i]);
		$docNr = safeHTML($_POST['docNr'][$i]);
		$orgLocation = safeHTML($_POST['orgLocation'][$i]);
		
		$orgAmount = str_replace(',', '.', $orgAmount);
		$newAmount = str_replace(',', '.', $newAmount);
		
		if($changeLocation && $newAmount>0){
			
			
			if($orgAmount==$newAmount){
				$form_data = array(
				'activityDate' => $actionDate,
				'location' => $changeLocation
				);
				updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");	

			
				$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE docNr='".$docNr."'");
				$row = mysqli_fetch_array($result);
				$cargoCode = $row['cargoCode'];
				$clientCode = $row['clientCode'];
				$clientName = $row['clientName'];
				$ownerCode = $row['ownerCode'];
				$ownerName = $row['ownerName'];
				$receiverCode = $row['receiverCode'];
				$receiverName = $row['receiverName'];

				$form_data_log_n = array(
				'docNr' => $docNr,
				'activityDate' => $actionDate, 
				'cargoCode' => $cargoCode,	
				'type' => 'negative',				
				'productNr' => $productNr,
				'amount' => $orgAmount*(-1),
				'location' => $orgLocation,	
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,				
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'orgLine' => $lineId,
				'transfer' => 1,
				'resource' => $resource
				);
				insertNewRows("item_ledger_entry", $form_data_log_n);
				
				$form_data_log_p = array(
				'docNr' => $docNr,
				'activityDate' => $actionDate, 
				'cargoCode' => $cargoCode,	
				'type' => 'positiv',				
				'productNr' => $productNr,
				'amount' => $orgAmount,
				'location' => $changeLocation,	
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,				
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'transfer' => 1,
				'resource' => $resource
				);
				insertNewRows("item_ledger_entry", $form_data_log_p);	

				
			}
			
			if($orgAmount!=$newAmount){
				$leftAmount = $orgAmount-$newAmount;
				
				$form_data = array(
				'activityDate' => $actionDate,
				'location' => $changeLocation,
				'amount' => $newAmount
				);
				updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");	

				$form_data_i = array(
				'docNr' => $docNr,
				'activityDate' => $actionDate,
				'productNr' => $productNr,
				'amount' => $leftAmount,
				'enteredDate' => date('Y-m-d H:i:s'),
				'location' => $orgLocation,
				'enteredBy' => $myid,
				'status' => 20
				);
				insertNewRows("cargo_line", $form_data_i);

				$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE docNr='".$docNr."'");
				$row = mysqli_fetch_array($result);
				$cargoCode = $row['cargoCode'];
				$clientCode = $row['clientCode'];
				$clientName = $row['clientName'];
				$ownerCode = $row['ownerCode'];
				$ownerName = $row['ownerName'];
				$receiverCode = $row['receiverCode'];
				$receiverName = $row['receiverName'];

				$form_data_log_n = array(
				'docNr' => $docNr,
				'activityDate' => $actionDate, 
				'cargoCode' => $cargoCode,	
				'type' => 'negative',				
				'productNr' => $productNr,
				'amount' => $newAmount*(-1),
				'location' => $orgLocation,	
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,				
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'orgLine' => $lineId,
				'transfer' => 1,
				'resource' => $resource
				);
				insertNewRows("item_ledger_entry", $form_data_log_n);
				
				$form_data_log_p = array(
				'docNr' => $docNr,
				'activityDate' => $actionDate, 
				'cargoCode' => $cargoCode,	
				'type' => 'positive',				
				'productNr' => $productNr,
				'amount' => $newAmount,
				'location' => $changeLocation,	
				'clientCode' => $clientCode,
				'clientName' => $clientName,
				'ownerCode' => $ownerCode,
				'ownerName' => $ownerName,
				'receiverCode' => $receiverCode,
				'receiverName' => $receiverName,				
				'enteredDate' => date('Y-m-d H:i:s'),
				'enteredBy' => $myid,
				'orgLine' => $lineId,
				'transfer' => 1,
				'resource' => $resource
				);
				insertNewRows("item_ledger_entry", $form_data_log_p);	

				
				
			}
			
		
		}	

	}
}

// pārlikt preci uz citu kartiņu
if($r=='changeCard' && $id){

	$resource = mysqli_real_escape_string($conn, $_POST['resourceNr']);

	if($resource){	
		
		$changeResultOne = safeHTML($_POST['changeResultOne']);
		$changeResultTwo = safeHTML($_POST['changeResultTwo']);	

		$changeCodeOne = safeHTML($_POST['changeCodeOne']);
		$changeCodeTwo = safeHTML($_POST['changeCodeTwo']);		

		$activityDate = safeHTML($_POST['activityDate']);
		$activityDate = date('Y-m-d', strtotime($activityDate));	

		$cargo_line = 'cargo_line'; // table
		
		// PIRMAIS BLOKS
		for ($i=0; $i <= $changeResultOne; $i++) {

			$changeIdOne = safeHTML($_POST['changeIdOne'][$i]);		
			$productUmoOne = safeHTML($_POST['productUmoOne'][$i]);
			$batchNoOne = safeHTML($_POST['batchNoOne'][$i]);	
			$productUmoTwo = safeHTML($_POST['productUmoTwo'][$i]);

			$resourceOne = safeHTML($_POST['resourceNrOne'][$i]);
			$resourceTwo = safeHTML($_POST['resourceNrTwo'][$i]);
			
			if($changeIdOne){
						$productNrOne = safeHTML($_POST['productNrOne'][$i]);
						$amountOne = safeHTML($_POST['amountOne'][$i]);

						$forAssistantAmountOne = safeHTML($_POST['forAssistantAmountOne'][$i]);
						$assistantAmountOne = safeHTML($_POST['assistantAmountOne'][$i]);

						$enteredDateOne = strtr($_POST['enteredDateOne'][$i], '.', '-');
						$enteredDateOne = date('Y-m-d', strtotime($enteredDateOne));
						
						$locationOne = safeHTML($_POST['locationOne'][$i]);
						$statusOne = safeHTML($_POST['statusOne'][$i]);

						$activityDateOne = strtr($_POST['activityDateOne'][$i], '.', '-');
						$activityDateOne = date('Y-m-d', strtotime($activityDateOne));

						$thisTransportOne = safeHTML($_POST['thisTransportOne'][$i]);
						$volumeOne = safeHTML($_POST['volumeOne'][$i]);

						$thisDateOne = strtr($_POST['thisDateOne'][$i], '.', '-');
						$thisDateOne = date('Y-m-d', strtotime($thisDateOne));					
															
						$amountOrgOne = safeHTML($_POST['amountOrgOne'][$i]);
						
						if($amountOne == $amountOrgOne){

							$form_data = array(
								'issueActivityDate' => $activityDate,
								'issueAmount' => $amountOne,
								'issue_assistant_amount' => $forAssistantAmountOne,
								'action' => 27,	
								'issue_to' => $changeCodeTwo,
								'issue_resource' => $resource
								);
								updateSomeRow("cargo_line", $form_data, "WHERE id='".$changeIdOne."'");
								
						}else{

							$form_data = array(
								'issueActivityDate' => $activityDate,
								'issueAmount' => $amountOrgOne,
								'issue_assistant_amount' => $forAssistantAmountOne,
								'action' => 27,	
								'issue_to' => $changeCodeTwo,
								'issue_resource' => $resource
								);
								updateSomeRow("cargo_line", $form_data, "WHERE id='".$changeIdOne."'");						
						}
			$form_data = array(	
			'lastBy' => $myid,
			'lastDate' => date('Y-m-d H:i:s')
			);					
			updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
			}
		
		} // for ends	
		
		// OTRAIS BLOKS
		for ($i=0; $i <= $changeResultTwo; $i++) {

			$changeIdTwo = safeHTML($_POST['changeIdTwo'][$i]);		
			$productUmoTwo = safeHTML($_POST['productUmoTwo'][$i]);
			$batchNoTwo = safeHTML($_POST['batchNoTwo'][$i]);	
			$productUmoOne = safeHTML($_POST['productUmoOne'][$i]);

			$resourceOne = safeHTML($_POST['resourceNrOne'][$i]);
			$resourceTwo = safeHTML($_POST['resourceNrTwo'][$i]);		

			if($changeIdTwo){
						$productNrTwo = safeHTML($_POST['productNrTwo'][$i]);
						$amountTwo = safeHTML($_POST['amountTwo'][$i]);

						$forAssistantAmountTwo = safeHTML($_POST['forAssistantAmountTwo'][$i]);
						$assistantAmountTwo = safeHTML($_POST['assistantAmountTwo'][$i]);

						$enteredDateTwo = strtr($_POST['enteredDateTwo'][$i], '.', '-');
						$enteredDateTwo = date('Y-m-d', strtotime($enteredDateTwo));

						$locationTwo = safeHTML($_POST['locationTwo'][$i]);
						$statusTwo = safeHTML($_POST['statusTwo'][$i]);

						$activityDateTwo = strtr($_POST['activityDateTwo'][$i], '.', '-');
						$activityDateTwo = date('Y-m-d', strtotime($activityDateTwo));

						$thisTransportTwo = safeHTML($_POST['thisTransportTwo'][$i]);
						$volumeTwo = safeHTML($_POST['volumeTwo'][$i]);

						$thisDateTwo = strtr($_POST['thisDateTwo'][$i], '.', '-');
						$thisDateTwo = date('Y-m-d', strtotime($thisDateTwo));					
															
						$amountOrgTwo = safeHTML($_POST['amountOrgTwo'][$i]);
						
						if($amountTwo == $amountOrgTwo){

							$form_data = array(
								'issueActivityDate' => $activityDate,
								'issueAmount' => $amountTwo,
								'issue_assistant_amount' => $forAssistantAmountTwo,
								'action' => 27,	
								'issue_to' => $changeCodeOne,
								'issue_resource' => $resource
								);
								updateSomeRow("cargo_line", $form_data, "WHERE id='".$changeIdTwo."'");


						}else{

							$form_data = array(
								'issueActivityDate' => $activityDate,
								'issueAmount' => $amountOrgTwo,
								'issue_assistant_amount' => $forAssistantAmountTwo,
								'action' => 27,	
								'issue_to' => $changeCodeOne,
								'issue_resource' => $resource
								);
								updateSomeRow("cargo_line", $form_data, "WHERE id='".$changeIdTwo."'");
								
							
						}
			$form_data = array(	
			'lastBy' => $myid,
			'lastDate' => date('Y-m-d H:i:s')
			);					
			updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
			}
			
		} // for ends			

	} // check if resource
	
}


























	
	mysqli_close($conn);


?>

