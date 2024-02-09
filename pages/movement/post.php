<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="movement";


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

	if(!empty($_POST['cargoCode']) && !empty($_POST['ladingNr']) && !empty($_POST['clientCode'])){
		$cargoCode = safeHTML($_POST['cargoCode']);
	}else{
		$cargoCode = '';
	}

	$deliveryDate = strtr($_POST['deliveryDate'], '.', '-');
	$deliveryDate = date('Y-m-d', strtotime($deliveryDate));	

	$form_data = array(
	'ladingNr' => safeHTML($_POST['ladingNr']),
	'transportNo' => safeHTML($_POST['transportNo']),
	'agreements' => safeHTML($_POST['agreements']),
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
	'lastBy' => $myid,
	'lastDate' => date('Y-m-d H:i:s'),		
	'cargoCode' => $cargoCode
	);
	updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");
	
	if((isset($_POST['productNr']) && $_POST['productNr']!='') && (isset($_POST['amount']) && $_POST['amount']>0)){
		
		$form_data_l = array(
		'docNr' => safeHTML($_POST['docNr']),
		'productNr' => safeHTML($_POST['productNr']),
		'amount' => safeHTML($_POST['amount'])
		);
		insertNewRows("cargo_line", $form_data_l);		
		
	}

}

if($r=='changeStorage' && $id){
	
	$resourceNr = mysqli_real_escape_string($conn, $_POST['resourceNr']); // jaunais pakalpojums

	$records = safeHTML($_POST['lResult']);

	$actionDate = strtr($_POST['actionDate'], '.', '-');
	$actionDate = date('Y-m-d', strtotime($actionDate));	

	for ($i=0; $i <= $records; $i++) {
		$lineId = safeHTML($_POST['lineId'][$i]);
		$changeLocation = safeHTML($_POST['changeLocation'][$i]);
		$orgAmount = safeHTML($_POST['orgAmount'][$i]);
		$newAmount = safeHTML($_POST['newAmount'][$i]);
		$productNr = safeHTML($_POST['productNr'][$i]);
		$productUmo = safeHTML($_POST['productUmo'][$i]);
		$batchNo = safeHTML($_POST['batchNo'][$i]);
		$docNr = safeHTML($_POST['docNr'][$i]);
		$orgLocation = safeHTML($_POST['orgLocation'][$i]);
		$cChange = safeHTML($_POST['cChange'][$i]);

		$orgAmountExtra = safeHTML($_POST['orgAmountExtra'][$i]);
		$newAmountExtra = safeHTML($_POST['newAmountExtra'][$i]);

		$orgAmountExtra = str_replace(',', '.', $orgAmountExtra);
		$newAmountExtra = str_replace(',', '.', $newAmountExtra);

		$mThisDate = strtr($_POST['mThisDate'][$i], '.', '-');
		$mThisDate = date('Y-m-d', strtotime($mThisDate));


		$mThisTransport = safeHTML($_POST['mThisTransport'][$i]);
		$mVolume = safeHTML($_POST['mVolume'][$i]);
		
		$orgAmount = str_replace(',', '.', $orgAmount);
		$newAmount = str_replace(',', '.', $newAmount);
		
		
		if(isset($_POST['changeLocationAll']) && $_POST['changeLocationAll']!=''){

		$changeLocationAll = safeHTML($_POST['changeLocationAll']);
		
		if($changeLocationAll && $newAmount>0){
			

			if($orgAmount==$newAmount){
				
				$form_data = array(
				'issueActivityDate' => $actionDate,
				'issue_location' => $changeLocationAll,
				'issueAmount' => $newAmount,
				'issue_assistant_amount' => $newAmountExtra, 
				'action' => 23,	
				'issue_resource' => $resourceNr
				);
				updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");		
				
			}
			
			if($orgAmount!=$newAmount){
				$leftAmount = $orgAmount-$newAmount;	
				
				$form_data = array(
					'issueActivityDate' => $actionDate,
					'issue_location' => $changeLocationAll,
					'issueAmount' => $newAmount,
					'issue_assistant_amount' => $newAmountExtra, 
					'action' => 23,	
					'issue_resource' => $resourceNr
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");				
				
			}

				$form_data = array(	
				'lastBy' => $myid,
				'lastDate' => date('Y-m-d H:i:s')
				);
				updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");			
		
		}
		
		}else{
			
		
		
		if($changeLocation && $newAmount>0){
			

			if($orgAmount==$newAmount){
				
				$form_data = array(
					'issueActivityDate' => $actionDate,
					'issue_location' => $changeLocation,
					'issueAmount' => $newAmount,
					'issue_assistant_amount' => $newAmountExtra, 
					'action' => 23,	
					'issue_resource' => $resourceNr
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");
								
			}
			
			if($orgAmount!=$newAmount){
				$leftAmount = $orgAmount-$newAmount;
				
				$form_data = array(
					'issueActivityDate' => $actionDate,
					'issue_location' => $changeLocation,
					'issueAmount' => $newAmount,
					'issue_assistant_amount' => $newAmountExtra, 
					'action' => 23,	
					'issue_resource' => $resourceNr
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".$lineId."'");
				
			}

				$form_data = array(	
				'lastBy' => $myid,
				'lastDate' => date('Y-m-d H:i:s')
				);
				updateSomeRow("cargo_header", $form_data, "WHERE id='".intval($id)."' LIMIT 1");			
		
		}

		}		

	}
}
	
	mysqli_close($conn);


?>

