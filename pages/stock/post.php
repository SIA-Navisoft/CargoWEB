<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="stock";


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

if($r=='addProduct'){

	if(!empty($_POST['addCode']) && !empty($_POST['addUom']) && !empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);
		$check = mysqli_query($conn, "SELECT code FROM n_items WHERE code='".$code."'");

		
		$project_name = mysqli_query($conn, "SELECT Name FROM n_projects WHERE Code='".safeHTML($_POST['addProject'])."'");		
		$pn = mysqli_fetch_array($project_name);

		if(mysqli_num_rows($check)>0){
			echo 'productCodeExists';			
		}else{
			
			if($_POST['noSerial']=='on'){
				$noSerial = 1;
			}else{
				$noSerial = 0;
			}			
			
			$form_data = array(
			'code' => $code,
			'unitOfMeasurement' => safeHTML($_POST['addUom']),
			'assistantUmo' => safeHTML($_POST['helpUom']),
			'defWeight' => safeHTML($_POST['addWeight']),
			'defVolume' => safeHTML($_POST['addVolume']),
			'barCode' => safeHTML($_POST['addBarcode']),
			'name1' => safeHTML($_POST['addName']),
			'project_code' => safeHTML($_POST['addProject']),
			'project_name' => $pn['Name'],
			'status' => 1,
			'no_serial' => $noSerial,
			'createdBy' => intval($myid),
			'createdDate' => date('Y-m-d H:i:s')
			);
			insertNewRows("n_items", $form_data);	
			
			$form_on = array(
			'productNr' => $code,
			'uom' => safeHTML($_POST['addUom']),
			'amount' => 1,
			'base' => 1,
			'status' => 1,

			'createdBy' => intval($myid),
			'createdDate' => date('Y-m-d H:i:s')
			);
			insertNewRows("additional_uom", $form_on);			
			
			echo 'added';			
		}

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}

if($r=='add_additional'){ //uuom

if($_POST['forExtraUom']=='on'){$forExtraUom = 1;}else{$forExtraUom = 0;}
if($_POST['convertFrom']=='on'){$convertFrom = 1;}else{$convertFrom = 0;}

if(empty($_POST['uuom'])){
	if(!empty($_POST['addUomAdd']) && !empty($_POST['addCodeAdd'])){
		
		$code = safeHTML($_POST['productCodeAdd']);
		$uom = safeHTML($_POST['addUomAdd']);

		if($uom!='KG'){
			$check = mysqli_query($conn, "
				SELECT uom 
				FROM additional_uom 
				WHERE productNr='".$code."' 
				AND uom='".$uom."' 
				AND status='1'									
			");
		}else{
			$check = mysqli_query($conn, "
				SELECT uom 
				FROM additional_uom 
				WHERE productNr='".$code."' 
				AND uom='".$uom."' 
				AND status='1'

				AND CASE
					WHEN (SELECT COUNT(uom) FROM additional_uom WHERE productNr='".$code."' AND status='1' AND uom='KG') < 2 THEN uom!='KG'
					ELSE uom='KG'					      
				END										
			");
		}
		
		

		if(mysqli_num_rows($check)>0){
			echo 'productCodeExists';			
		}else{
			$form_data = array(
			'productNr' => $code,
			'uom' => $uom,
			'amount' => safeHTML($_POST['addCodeAdd']),
			'weight' => safeHTML($_POST['addWeight']),

			'status' => 1,
			'for_extra_uom' => $forExtraUom,
			'createdBy' => intval($myid),
			'createdDate' => date('Y-m-d H:i:s'),
			'convert_from' => $convertFrom
			);
			insertNewRows("additional_uom", $form_data);	
			echo 'added';			
		}

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
}else{
	if(!empty($_POST['addUomAdd']) && !empty($_POST['addCodeAdd'])){
		
		$code = safeHTML($_POST['productCodeAdd']);
		$uuom = intval($_POST['uuom']);
		$uom = safeHTML($_POST['addUomAdd']);
			
		$check = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$code."' AND uom='".$uom."' AND id!='".$uuom."' AND status=1");
		
		if(mysqli_num_rows($check)>0){
			echo 'productCodeExists';			
		}else{			
			$form_data = array(
			'uom' => $uom,
			'amount' => safeHTML($_POST['addCodeAdd']),
			'weight' => safeHTML($_POST['addWeight']),
			'for_extra_uom' => $forExtraUom,
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s'),
			'convert_from' => $convertFrom
			);
			updateSomeRow("additional_uom", $form_data, "WHERE id='".$uuom."' LIMIT 1");	
			echo 'done';
		}
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}			
}	
}

if($r=='editProduct'){
	
	if(!empty($_POST['addCode']) && !empty($_POST['addUom']) && !empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);
		$check = mysqli_query($conn, "SELECT code FROM n_items WHERE code='".$code."'");
		
		if(mysqli_num_rows($check)==0){
			echo 'productCodeDoesNotExists';			
		}else{
				$UOM = safeHTML($_POST['addUom']);
				
				$chckUom = mysqli_query($conn, "SELECT * FROM additional_uom WHERE productNr='".$code."'");
				if(mysqli_num_rows($chckUom)>0){
					$cuRow = mysqli_fetch_array($chckUom);
					if($cuRow['base']==1 && $cuRow['uom']==$UOM){
						
					}else{
						$result = mysqli_query($conn,"DELETE FROM additional_uom WHERE productNr='".$code."'");
						
						$form_on = array(
						'productNr' => $code,
						'uom' => $UOM,
						'amount' => 1,
						'base' => 1,
						'status' => 1,
						'createdBy' => intval($myid),
						'createdDate' => date('Y-m-d H:i:s')
						);
						insertNewRows("additional_uom", $form_on);						
					}
				}else{

					$form_on = array(
					'productNr' => $code,
					'uom' => $UOM,
					'amount' => 1,
					'base' => 1,
					'status' => 1,
					'createdBy' => intval($myid),
					'createdDate' => date('Y-m-d H:i:s')
					);
					insertNewRows("additional_uom", $form_on);\				
					
				}	

				$project_name = mysqli_query($conn, "SELECT Name FROM n_projects WHERE Code='".safeHTML($_POST['addProject'])."'");		
				$pn = mysqli_fetch_array($project_name);				

				
				if($_POST['noSerial']=='on'){
					$noSerial = 1;
				}else{
					$noSerial = 0;
				}				
				
				$form_data = array(
				'unitOfMeasurement' => $UOM ,
				'assistantUmo' => safeHTML($_POST['helpUom']),
				'defWeight' => safeHTML($_POST['addWeight']),
				'defVolume' => safeHTML($_POST['addVolume']),			
				'barCode' => safeHTML($_POST['addBarcode']),
				'name1' => safeHTML($_POST['addName']),
				'project_code' => safeHTML($_POST['addProject']),
				'project_name' => $pn['Name'],
				'no_serial' => $noSerial,				
				'editedBy' => intval($myid),
				'editedDate' => date('Y-m-d H:i:s')
				);
				updateSomeRow("n_items", $form_data, "WHERE code='".$code."' LIMIT 1");	
				echo 'done';				
			}
			
		

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}
	
}


if($r=='editResource'){

		if($_POST['result']>0){

			$records = intval($_POST['result']);

			for ($i=0; $i <= $records; $i++) {

				$resource_id = safeHTML($_POST['resource_id'][$i]);
				$from = safeHTML($_POST['from'][$i]);
				$to = safeHTML($_POST['to'][$i]);
				


				if($resource_id!=''){
					
					$checkIfExist = mysqli_query($conn, "SELECT id FROM resource_data WHERE resource_id='".$resource_id."'");
					if(mysqli_num_rows($checkIfExist)>0){
						//update

						$form_data = array(
						'from' => $from,
						'to' => $to,				
						'editedBy' => intval($myid),
						'editedDate' => date('Y-m-d H:i:s')
						);
						updateSomeRow("resource_data", $form_data, "WHERE resource_id='".$resource_id."' LIMIT 1");	

					}else{
						//insert

						$form_data = array(
						'resource_id' => $resource_id,
						'from' => $from,
						'to' => $to,
						'editedBy' => intval($myid),
						'editedDate' => date('Y-m-d H:i:s')
						);
						insertNewRows("resource_data", $form_data);

					}
				}

			}

		}
		
}

if($r=='editlocations'){

	if(!empty($_POST['addName'])){
		
		$code = safeHTML($_POST['addCode']);		

			$form_data = array(
			'name' => safeHTML($_POST['addName']),
			'editedBy' => intval($myid),
			'editedDate' => date('Y-m-d H:i:s')
			);
			updateSomeRow("n_location", $form_data, "WHERE id='".$code."' LIMIT 1");	
			echo 'done';			

	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';
	}	
	
}

if($r=='addlocations'){
	
	if(!empty($_POST['addName']) && !empty($_POST['addCode'])){
		
		$check = mysqli_query($conn, "SELECT id FROM n_location WHERE id='".safeHTML($_POST['addCode'])."'");

		if(mysqli_num_rows($check)>0){
			echo 'productCodeExists';			
		}else{		
		
			$form_data = array(
			'id' => safeHTML($_POST['addCode']),
			'name' => safeHTML($_POST['addName']),
			'createdBy' => $myid,
			'createdDate' => date('Y-m-d H:i:s'),		
			'status' => 1
			);
			insertNewRows("n_location", $form_data);	
			echo 'added';
			
		}
	
	}else{
		echo 'error';//'kāds no laukiem nav aizpildīts.';		
	}	
	
}

	
	mysqli_close($conn);


?>

