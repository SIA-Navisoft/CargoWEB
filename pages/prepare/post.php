<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="prepare";


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
$page_file="prepare";
$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

$r = $id = null;
if (isset($_GET['r'])){$r = htmlentities($_GET['r'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');

$returnMeWho = returnMeWho($myid);


if($r=='add'){

    $year = date('Y');
    $month = date('m');

    $per = $year.'_'.$month;

    $get_last_report_id = mysqli_query($conn, "
        select * from (
                select substring(report_id, -3) AS report_id from web_objects_invoices
                WHERE report_id LIKE '%".$per."%'
                having report_id >0 
                UNION ALL
                select substring(report_id, -3) AS report_id from web_objects_invoices_accepted
                WHERE report_id LIKE '%".$per."%'
                having report_id >0
                UNION ALL
                select substring(report_id, -3) AS report_id from web_objects_invoices_archived
                WHERE report_id LIKE '%".$per."%'
                having report_id >0
        ) a
        GROUP BY report_id ORDER BY report_id DESC    
    ");
    $glri = mysqli_fetch_array($get_last_report_id);  



    $report = $glri['report_id'] + 1;

    $report_id = $year.'_'.$month.'_'.sprintf("%'03d\n", $report);
    $report_id = trim($report_id);

    $result = safeHTML($_POST['result']);
    $gResult = safeHTML($_POST['gResult']);
	$eResult = safeHTML($_POST['eResult']);
    
	$pFrom = safeHTML($_POST['pFrom']);
    $pTo = safeHTML($_POST['pTo']);
	
	// pakalpojumu rindas
    for ($i=0; $i <= $result; $i++) {

        $agreements = safeHTML($_POST['agreements'][$i]);
        $clientCode = safeHTML($_POST['clientCode'][$i]);
        $resourceName = mysqli_real_escape_string($conn, $_POST['resourceName'][$i]);
        $resource = safeHTML($_POST['resource'][$i]);
		
        $uom = safeHTML($_POST['uom'][$i]);
		
		
		if($uom){ 
			$checkUom = mysqli_query($conn, "SELECT NAVcode FROM unit_of_measurement WHERE code='".$uom."' AND NAVcode!=''");
			if(mysqli_num_rows($checkUom)>0){
				$urow = mysqli_fetch_array($checkUom);
				$uom = $urow['NAVcode'];
			}
		}

        $quer = null;
        if($agreements && $clientCode && $resource){ $quer = "WHERE al.contractNr='".$agreements."' AND a.customerNr='".$clientCode."' AND al.service='".$resource."'";}
        		
        $get_RealDesc = mysqli_query($conn, "
            SELECT al.service_name 
            FROM agreements_lines AS al
            LEFT JOIN agreements AS a
            ON al.contractNr=a.contractNr
            ".$quer."
        ");
        $grd = mysqli_fetch_array($get_RealDesc);

		
        $tariffs = safeHTML($_POST['tariffs'][$i]);
        $amount = safeHTML($_POST['amount'][$i]);
        
        $qty = safeHTML($_POST['qty'][$i]);
        $ledger_id = intval($_POST['ledger_id'][$i]);
        $description = $grd['service_name'];
        $productNr = safeHTML($_POST['productNr'][$i]);
        $convertValue = safeHTML($_POST['convertValue'][$i]);
        $enteredDate = safeHTML($_POST['enteredDate'][$i]);
        $activityDate = safeHTML($_POST['activityDate'][$i]);
        $prepareAll = intval($_POST['prepareAll'][$i]);
		
       if(($agreements) && ($clientCode) && ($resource)){
		   
            $v4uuid = UUID::v4();
            $form_data = array(
                    'report_id' => $report_id,
                    'agreement_id' => $agreements,
                    'customer_id' => $clientCode,	
                    'service' => $resourceName,
                    'service_code' => $resource,
                    'description' => $description,
                    'unit' =>  $uom,
                    'sales_price' => $tariffs,  
                    'amount' => $amount,
                    'currency' => 'EUR',
                    'qty' => $qty,
                    'period_from' => $pFrom,
                    'period_to' => $pTo,
                    'createdBy' => $myid,
                    'createdByName' => $returnMeWho,
                    'createdDate' => date('Y-m-d H:i:s'),
                    'ledger_entry' => $ledger_id,
                    'prepareAll' => $prepareAll,
                    'uniqueID' => $v4uuid
                );
                insertNewRows("web_objects_invoices", $form_data);	
              
            }

    }

	
	// papild pakalpojumi
    for ($i=0; $i <= $eResult; $i++) {

        $agreements = safeHTML($_POST['eAgreements'][$i]);
        $clientCode = safeHTML($_POST['eClientCode'][$i]);
        $resourceName = mysqli_real_escape_string($conn, $_POST['eResourceName'][$i]);
        $resource = safeHTML($_POST['eResource'][$i]);
      
        $uom = safeHTML($_POST['eUom'][$i]);
		
		
		if($uom){ 
			$checkUom = mysqli_query($conn, "SELECT NAVcode FROM unit_of_measurement WHERE code='".$uom."' AND NAVcode!=''");
			if(mysqli_num_rows($checkUom)>0){
				$urow = mysqli_fetch_array($checkUom);
				$uom = $urow['NAVcode'];
			}
		}

		$quer = null;
        if($agreements && $clientCode && $resource){ $quer = "WHERE al.contractNr='".$agreements."' AND a.customerNr='".$clientCode."' AND al.service='".$resource."' AND al.extra_resource='on'";}
        $get_RealDesc = mysqli_query($conn, "
            SELECT al.service_name 
            FROM agreements_lines AS al
            LEFT JOIN agreements AS a
            ON al.contractNr=a.contractNr
            ".$quer."
        ");
        $grd = mysqli_fetch_array($get_RealDesc);		
		
        $tariffs = safeHTML($_POST['eTariffs'][$i]);
        $amount = safeHTML($_POST['eAmount'][$i]);
     
        $qty = safeHTML($_POST['eQty'][$i]);
        $ledger_id = intval($_POST['eLedger_id'][$i]);
        $description = $grd['service_name'];

        $prepareAll = intval($_POST['ePrepareAll'][$i]);
		$service_line = intval($_POST['eService_line'][$i]);
        
		
       if(($agreements) && ($clientCode) && ($resource)){
		   
            $v4uuid = UUID::v4();
            $form_data = array(
                    'report_id' => $report_id,
                    'agreement_id' => $agreements,
                    'customer_id' => $clientCode,	
                    'service' => $resourceName,
                    'service_code' => $resource,
                    'description' => $description,
                    'unit' =>  $uom,
                    'sales_price' => $tariffs,  
                    'amount' => $amount,
                    'currency' => 'EUR',
                    'qty' => $qty,
                    'period_from' => $pFrom,
                    'period_to' => $pTo,
                    'createdBy' => $myid,
                    'createdByName' => $returnMeWho,
                    'createdDate' => date('Y-m-d H:i:s'),
                    'ledger_entry' => $ledger_id,
                    'prepareAll' => $prepareAll,
                    'uniqueID' => $v4uuid
                );
                insertNewRows("web_objects_invoices", $form_data);	           
            }			
			
            $e_form_data = array(
                'submited' => 1,
				'report_id' => $report_id
            );
            updateSomeRow("additional_services_line", $e_form_data, "WHERE id='".$service_line."'");
    }	
	
	//glabāšanas rindas
    for ($i=0; $i <= $gResult; $i++) {
 
        $gAgreements = safeHTML($_POST['gAgreements'][$i]);
        $gClientCode = safeHTML($_POST['gClientCode'][$i]);
       
        $gResource = safeHTML($_POST['gResource'][$i]);
        
        $gUom = safeHTML($_POST['gUom'][$i]);
        $gTariffs = safeHTML($_POST['gTariffs'][$i]);
        $gThatValue = safeHTML($_POST['gThatValue'][$i]);
        
        $gQty = safeHTML($_POST['gQty'][$i]);
        $gLedger_id = intval($_POST['gLedger_id'][$i]);
        $gPrepareAll = intval($_POST['gPrepareAll'][$i]);   

       if(($gAgreements) && ($gClientCode)){

        $quer = null;
        if($gAgreements && $gClientCode && $gResource){ $quer = "WHERE al.contractNr='".$gAgreements."' AND a.customerNr='".$gClientCode."' AND al.service='".$gResource."'";}
        if($gAgreements && $gClientCode && !$gResource){ $quer = "WHERE al.contractNr='".$gAgreements."' AND a.customerNr='".$gClientCode."'";}

        $get_RealDesc = mysqli_query($conn, "
            SELECT al.service_name 
            FROM agreements_lines AS al
            LEFT JOIN agreements AS a
            ON al.contractNr=a.contractNr
            ".$quer."
        ");
        $grd = mysqli_fetch_array($get_RealDesc);

        $v4uuid = UUID::v4();
        $form_data_hold = array(
                'report_id' => $report_id,
                'agreement_id' => $gAgreements,
                'customer_id' => $gClientCode,	
                'service' => 'GLABĀŠANA',
                'service_code' => $gResource,
                'description' => $grd['service_name'],
                'unit' => $gUom,
                'sales_price' => $gTariffs,  
                'amount' => $gThatValue,
                'currency' => 'EUR',
                'qty' => $gQty,
                'period_from' => $pFrom,
                'period_to' => $pTo,
                'createdBy' => $myid,
                'createdByName' => $returnMeWho,
                'createdDate' => date('Y-m-d H:i:s'),
                'ledger_entry' => $gLedger_id,
                'uniqueID' => $v4uuid,
                'stock_service' => 1,
                'prepareAll' => $gPrepareAll
            );

            insertNewRows("web_objects_invoices", $form_data_hold);	        
			
        }
    }

    $comment_count = count($_POST['comment']);

for ($i=0; $i <= $comment_count; $i++) { 
    
    $comment = safeHTML($_POST['comment'][$i]);
    if($comment){

        if($_POST['agreements']){
            $agreements = safeHTML($_POST['agreements'][$i]);
        }elseif($_POST['gAgreements']){
            $agreements = safeHTML($_POST['gAgreements'][$i]);
        }

        if($_POST['clientCode']){
            $clientCode = safeHTML($_POST['clientCode'][$i]);
        }elseif($_POST['gAgreements']){
            $clientCode = safeHTML($_POST['gClientCode'][$i]);
        }

        $v4uuid = UUID::v4();
                            
        $form_data = array(
            'report_id' => $report_id,
            'agreement_id' => $agreements,
            'customer_id' => $clientCode,	
            'service' => 'KOMENTĀRS',
            'service_code' => 'ZZZZ',
            'description' => $comment,
            'period_from' => $pFrom,
            'period_to' => $pTo,       
            'createdBy' => $myid,
            'createdByName' => $returnMeWho,
            'createdDate' => date('Y-m-d H:i:s'),
            'uniqueID' => $v4uuid

        );
        insertNewRows("web_objects_invoices", $form_data);    
    }
}


    $query = "INSERT INTO web_holdings 
    (
          report_id
        , agreement_id
        , customer_id
        , service
        , service_code
        , description
        , unit
        , sales_price
        , amount
        , currency
        , qty
        , period_from
        , period_to
        , createdBy
        , createdDate
        , editedBy
        , editedDate                                   
        , ledger_entry
        , status
        , uniqueID
        , stock_service
        , prepareAll
    )
    SELECT 

          report_id
        , agreement_id
        , customer_id
        , service
        , service_code
        , description
        , unit
        , sales_price
        , amount
        , currency
        , qty
        , period_from
        , period_to
        , createdBy
        , createdDate  
        , editedBy
        , editedDate                                 
        , ledger_entry
        , 0
        , uniqueID
        , stock_service
        , prepareAll

    FROM web_objects_invoices WHERE report_id='".$report_id."'";        
    mysqli_query($conn, $query) or die(mysqli_error($conn));



} // ADD BEIDZAS

if($r=='edit'){

    $result = safeHTML($_POST['result']);

    for ($i=0; $i <= $result; $i++) {

        $comment = safeHTML($_POST['comment'][$i]);
        $line_id = intval($_POST['line_id'][$i]);
        $record = $_POST['record'][$i];

        if($line_id>0){
            
            $form_data = array(
                'description' => $comment,
                'editedBy' => intval($myid),
                'editedByName' => $returnMeWho,
                'editedDate' => date('Y-m-d H:i:s') 
            );
            updateSomeRow("web_objects_invoices", $form_data, "WHERE id='".$line_id."' AND service='KOMENTĀRS'");
       

            if($record=='on'){
                mysqli_query($conn,"DELETE FROM web_objects_invoices WHERE id = '".$line_id."' AND service='KOMENTĀRS'") or die(mysqli_error($conn));
            }    
       
        }
        

    }
    
}

if($r=='comment'){

    $report_id = safeHTML($_POST['report_id']);
    $pFrom = safeHTML($_POST['pFrom']);
    $pTo = safeHTML($_POST['pTo']);

    $agreements = safeHTML($_POST['agreements']);
    $clientCode = safeHTML($_POST['clientCode']);

    $v4uuid = UUID::v4();
                        
    $form_data = array(
        'report_id' => $report_id,
        'agreement_id' => $agreements,
        'customer_id' => $clientCode,	
        'service' => 'KOMENTĀRS',
        'service_code' => 'ZZZZ',
        'period_from' => $pFrom,
        'period_to' => $pTo,       
        'createdBy' => $myid,
        'createdByName' => $returnMeWho,
        'createdDate' => date('Y-m-d H:i:s'),
        'uniqueID' => $v4uuid

    );
    insertNewRows("web_objects_invoices", $form_data);
}

if($r=='delete'){

    $report_id = safeHTML($_POST['report_id']);

        if($report_id>0){
            mysqli_query($conn,"DELETE FROM web_objects_invoices WHERE report_id = '".$report_id."'");
            mysqli_query($conn,"DELETE FROM web_holdings WHERE report_id = '".$report_id."'");
        }
 

		 $e_form_data = array(
			'submited' => 0,
			'report_id' => 0
		);
		updateSomeRow("additional_services_line", $e_form_data, "WHERE report_id='".$report_id."'");
    
}

if($r=='approve'){

        $report_id = safeHTML($_POST['report_id']);

        if($report_id>0){    
            $query = "INSERT INTO web_objects_invoices_accepted 
            (
                  report_id
                , agreement_id
                , customer_id
                , service
                , service_code
                , description
                , unit
                , sales_price
                , amount
                , currency
                , qty
                , period_from
                , period_to
                , createdBy
                , createdByName
                , createdDate
                , editedBy
                , editedByName
                , editedDate 
                , acceptedBy
                , acceptedByName
                , acceptedDate                                   
                , ledger_entry
                , uniqueID
                , stock_service
                , prepareAll
            )
            SELECT 

                  report_id
                , agreement_id
                , customer_id
                , service
                , service_code
                , description
                , unit
                , sales_price
                , amount
                , currency
                , qty
                , period_from
                , period_to
                , createdBy
                , createdByName
                , createdDate  
                , editedBy
                , editedByName
                , editedDate                                 
                , '".intval($myid)."'
                , '".$returnMeWho."'
                , '".date('Y-m-d H:i:s')."'
                , ledger_entry
                , uniqueID
                , stock_service
                , prepareAll

            FROM web_objects_invoices WHERE report_id='".$report_id."'";        
            mysqli_query($conn, $query) or die(mysqli_error($conn));
            mysqli_query($conn,"DELETE FROM web_objects_invoices WHERE report_id = '".$report_id."'");


            $form_data = array(
                'status' => 10,
                'editedBy' => intval($myid),
                'editedByName' => $returnMeWho,
                'editedDate' => date('Y-m-d H:i:s'), 
                'acceptedBy' => intval($myid),
                'acceptedByName' => $returnMeWho,
                'acceptedDate' => date('Y-m-d H:i:s')                 
            );
            updateSomeRow("web_holdings", $form_data, "WHERE report_id = '".$report_id."'");


        }
    
}


if($r=='recall'){

        $report_id = safeHTML($_POST['report_id']);

        if($report_id>0){    
            $query = "INSERT INTO web_objects_invoices 
            (
                  report_id
                , agreement_id
                , customer_id
                , service
                , service_code
                , description
                , unit
                , sales_price
                , amount
                , currency
                , qty
                , period_from
                , period_to
                , createdBy
                , createdByName
                , createdDate
                , editedBy
                , editedByName
                , editedDate                                   
                , ledger_entry
                , uniqueID
                , stock_service
                , prepareAll
            )
            SELECT 

                  report_id
                , agreement_id
                , customer_id
                , service
                , service_code
                , description
                , unit
                , sales_price
                , amount
                , currency
                , qty
                , period_from
                , period_to
                , createdBy
                , createdByName
                , createdDate  
                , '".intval($myid)."'
                , '".$returnMeWho."'
                , '".date('Y-m-d H:i:s')."'                                 
                , ledger_entry
                , uniqueID
                , stock_service
                , prepareAll

            FROM web_objects_invoices_accepted WHERE report_id='".$report_id."'";        
            mysqli_query($conn, $query) or die(mysqli_error($conn));
            mysqli_query($conn,"DELETE FROM web_objects_invoices_accepted WHERE report_id = '".$report_id."'");

            $form_data = array(
                'status' => 0,
                'editedBy' => intval($myid),
                'editedByName' => $returnMeWho,
                'editedDate' => date('Y-m-d H:i:s'), 
                'acceptedBy' => 0,
                'acceptedByName' => '',
                'acceptedDate' => '0000-00-00 00:00:00'                 
            );
            updateSomeRow("web_holdings", $form_data, "WHERE report_id = '".$report_id."'");          
        }

}
