<?
ini_set('max_execution_time', 300);

error_reporting(0);
require('../../lock.php');

$page_file="prepare";



require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}
$page_file="prepare";
if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

$view = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['from'])){$from = htmlentities($_GET['from'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['to'])){$to = htmlentities($_GET['to'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['referenceType'])){$referenceType = htmlentities($_GET['referenceType'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

require('../../inc/s.php');
?>


<script>
$(document).ready(function () {
    $('#send_accepted').on('submit', function(e) {
        e.preventDefault();
	
        $.ajax({
            url : '/pages/prepare/post.php?r=add',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				$('#contenthere').load('/pages/prepare/prepare.php');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>

<?php                                
echo '

<div class="panel panel-default">
    <div class="panel-body"> 
';
    
    echo '<p style="display:inline-block;">datu sagatavošana</p>';

    echo '<div class="clearfix"></div>';

if($clientCode && $agreements){

    if($from){
        $dateFrom = date('Y-m-d 00:00:00', strtotime($from));
        $periodFrom = date('d.m.Y', strtotime($from));
    }else{
        $dateFrom = date('Y-m-01 00:00:00');
        $periodFrom = date('d.m.Y');
        $from = date('Y-m-d');
    }

    if($to){
        $dateTo = date('Y-m-d 23:59:59', strtotime($to));
        $periodTo = date('d.m.Y', strtotime($to));
    }else{
        $dateTo = date('Y-m-t 23:59:59');
        $periodTo = date('d.m.Y');
        $to = date('Y-m-d');
    }   

    if($clientCode){$c = " AND e.clientCode='".$clientCode."'";}else{$c = null;} 
    if($agreements){$a = " AND h.agreements='".$agreements."'";}else{$a = null;}    

        echo '
			<div class="table-responsive">
	<form id="send_accepted"> ';


			if($from){
				echo '<input type="hidden" name="pFrom" value="'.date('Y-m-d', strtotime($from)).'">';
			}else{
				echo '<input type="hidden" name="pFrom" value="'.date('Y-m-d').'">';
			}
			if($to){
				echo '<input type="hidden" name="pTo" value="'.date('Y-m-d', strtotime($to)).'">';
			}else{
				echo '<input type="hidden" name="pTo" value="'.date('Y-m-d').'">';
			}

			
			
			$sumTables = $sumTablee = $resTable = $glaTable = null;
			
			$sumTables .=  '
			<table class="table table-hover table-responsive report" border="1">
				<thead>
					<tr>
						<th>produkts - nosaukums</th>
						<th>pakalpojuma nosaukums</th>
						<th>pakalpojums</th>
	 
						<th>daudzums</th>  
						<th>mērvienība</th>                                     
						<th>kopā EUR</th>';


					echo '    
					</tr>
				<thead>
				<tbody>';



				$sumTablee .=  '
				</tbody>
			</table>';
			
			
			
			
			
			
			
			
			$resTable .= '
			<table class="table table-hover table-responsive report" border="1">
				<thead>
					<tr>
						<th>līgums</th>
						<th>klienta kods - nosaukums</th>
						<th>produkts - nosaukums</th>
						<th>pakalpojuma nosaukums</th>
						<th>pakalpojums</th>
						
						<th>tarifs EUR</th> 
						<th>daudzums</th>  
						<th>mērvienība</th>                                     
						<th>kopā EUR</th>   
					</tr>
				<thead>
				<tbody>
			';
		
	if($referenceType==1 || $referenceType==3){	
		
		$query = mysqli_query($conn, "
						SELECT e.id AS eid, l.id AS lid, e.productNr AS productNr, e.productUmo AS productUmo,
						e.amount AS amount, e.action AS action, e.resource AS resource, e.status AS status,
						e.clientCode AS clientCode, e.clientName AS clientName, h.agreements AS agreements,
						(SELECT name FROM n_resource WHERE e.resource=id) AS resourceName, 
						a.uom AS uom, a.tariffs AS tariffs, a.id AS aid, a.service_name AS sname, 
						e.enteredDate AS enteredDate,e.activityDate AS activityDate, e.movementDate AS movementDate, l.activityDate AS receive_date,

						e.docNr AS docNr,
						e.cargoLine AS cargoLine, e.orgLine,
						(SELECT prepareAll FROM agreements WHERE contractNr=a.contractNr) AS prepareAll, nc.Name, e.issuance_id

					   
						FROM  cargo_line_receive AS l 
						
				LEFT JOIN (
					
					(
					   SELECT 
						id, docNr, deliverydate, activitydate, assistant_amount, amount, productnr, productumo, status, cargoLine, orgLine, resource, action, clientCode, clientName, enteredDate, movementDate, issuance_id, 
						cargoLine  AS mold  
					   FROM item_ledger_entry
					)
					UNION
					(
						SELECT
						id, docNr, deliverydate, activitydate, assistant_amount, amount, productnr, productumo, status, cargoLine, orgLine, resource, action, clientCode, clientName, enteredDate, movementDate, issuance_id, 
						orgLine AS mold  
					   FROM item_ledger_entry		
					)
					
				 ) AS e
				ON e.mold = l.id
									
						
		

						LEFT JOIN cargo_header_receive AS h
						ON l.docNr=h.docNr

						LEFT JOIN agreements_lines AS a
						ON h.agreements=a.contractNr AND e.productNr=a.item AND e.resource=a.service AND a.deleted=0

						LEFT JOIN n_customers AS nc
						ON e.clientCode=nc.Code
						

						WHERE 
						
							(e.id NOT IN  (select wh.ledger_entry 
									FROM web_holdings AS wh
									WHERE agreement_id=h.agreements 
									AND wh.customer_id=e.clientCode 
									
									AND (
											DATE_FORMAT(e.activityDate,'%Y-%m-%d') 
											BETWEEN DATE_FORMAT(wh.period_from,'%Y-%m-%d') 
											AND 
											DATE_FORMAT(wh.period_to,'%Y-%m-%d')
											
										)
								)
							)
						
						AND DATE_FORMAT(e.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."'
						
						 
						".$c." ".$a."  AND e.status='20'
						AND e.resource != ''
						GROUP BY l.id
						ORDER BY e.id DESC, resourceName DESC
					   

		") or die(mysqli_error($conn));
		
				
		$iquery = mysqli_query($conn, "
						SELECT e.id AS eid, l.id AS lid, e.productNr AS productNr, e.productUmo AS productUmo,
						e.amount AS amount, e.action AS action, e.resource AS resource, e.status AS status,
						e.clientCode AS clientCode, e.clientName AS clientName, h.agreements AS agreements,
						(SELECT name FROM n_resource WHERE e.resource=id) AS resourceName, 
						a.uom AS uom, a.tariffs AS tariffs, a.id AS aid, a.service_name AS sname, 
						e.enteredDate AS enteredDate,e.activityDate AS activityDate, e.movementDate AS movementDate, l.activityDate AS receive_date,

						e.docNr AS docNr,
						e.cargoLine AS cargoLine, e.orgLine,
						(SELECT prepareAll FROM agreements WHERE contractNr=a.contractNr) AS prepareAll, nc.Name, e.issuance_id

					   
						FROM  cargo_line_receive AS l 
						
				LEFT JOIN (
					
					(
					   SELECT 
						id, docNr, deliverydate, activitydate, assistant_amount, amount, productnr, productumo, status, cargoLine, orgLine, resource, action, clientCode, clientName, enteredDate, movementDate, issuance_id, 
						cargoLine  AS mold  
					   FROM item_ledger_entry
					)
					UNION
					(
						SELECT
						id, docNr, deliverydate, activitydate, assistant_amount, amount, productnr, productumo, status, cargoLine, orgLine, resource, action, clientCode, clientName, enteredDate, movementDate, issuance_id, 
						orgLine AS mold  
					   FROM item_ledger_entry		
					)
					
				 ) AS e
				ON e.mold = l.id					
		

						LEFT JOIN cargo_header_receive AS h
						ON l.docNr=h.docNr

						LEFT JOIN agreements_lines AS a
						ON h.agreements=a.contractNr AND e.productNr=a.item AND e.resource=a.service AND a.deleted=0

						LEFT JOIN n_customers AS nc
						ON e.clientCode=nc.Code
						

						WHERE 
						
							(e.id NOT IN  (select wh.ledger_entry 
									FROM web_holdings AS wh
									WHERE agreement_id=h.agreements 
									AND wh.customer_id=e.clientCode 
									
									AND (
											DATE_FORMAT(e.activityDate,'%Y-%m-%d') 
											BETWEEN DATE_FORMAT(wh.period_from,'%Y-%m-%d') 
											AND 
											DATE_FORMAT(wh.period_to,'%Y-%m-%d')
											
										)
								)
							)
						
						AND DATE_FORMAT(e.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."'
						
				 
						".$c." ".$a."  AND e.status='40'
						AND e.resource != ''
						GROUP BY l.id 
						ORDER BY e.id DESC, resourceName DESC
					   
		") or die(mysqli_error($conn));			

		$i=0; $showIt=null;
		$thatValue = null;
		$thatValue2 = null;
		
		$firstquarter=array();
		
		$money = null;
		$vmoney = null;	
		$allTotal = null;
		
		$totals = $totalz = array();
		while($row = mysqli_fetch_array($query) OR $row = mysqli_fetch_array($iquery)){
			
			$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$row['productNr']."'");
			$gNrow = mysqli_fetch_array($getNames);

			if( ($row['agreements']) && ($row['clientCode']) && ($row['resource']) && ($row['uom']) && ($row['amount']>0) ){
			
				if($row['action']==30 && $row['amount']<0){
					$qty = floatval($row['amount']) * (-1);
				}else{
					$qty = floatval($row['amount']);
				}
				
				$tariffs = floatval($row['tariffs']);

				if($row['status']==20){
					$akhh = productReceivedOnPeriod($conn, $row['docNr'], $row['productNr'], $from, $to, $row['lid']);					
				}

				if($row['status']==40){
					$akhh = productRelesedOnPeriod($conn, $row['docNr'], $row['productNr'], $to, $row['lid']);
				}
				
				
				$convertValue = getTotValueUOM($conn, $row['productNr'], $akhh, $row['productUmo'], $row['uom']);
				
				$amount = $convertValue * $tariffs;
				
				if($row['status']==20){
					$dakhh1=$amount;
				}
				
				if($row['status']==40){
					$dakhh2=$amount;
				}			
				
				$resTable .= '
					<tr>
						<td nowrap>
						<input type="hidden" name="ledger_id[]" value="'.$row['eid'].'">
						<input type="hidden" name="prepareAll[]" value="'.$row['prepareAll'].'">
						<input type="hidden" name="description[]" value="'.$row['enteredDate'].'">
						<input type="hidden" name="agreements[]" value="'.$row['agreements'].'">
						<input type="hidden" name="productNr[]" value="'.$row['productNr'].'">
						<input type="hidden" name="convertValue[]" value="'.$convertValue.'">
						<input type="hidden" name="enteredDate[]" value="'.$row['enteredDate'].'">
						<input type="hidden" name="activityDate[]" value="'.$row['activityDate'].'">
						<input type="hidden" name="movementDate[]" value="'.$row['movementDate'].'">
						'.$row['agreements']; 
											 
						$resTable .= '
						</td>
						<td>
						<input type="hidden" name="clientCode[]" value="'.$row['clientCode'].'">
						'.$row['clientCode'].' - '.$row['Name'].'</td>
						<td>'.$row['productNr'].' - '.$gNrow['name1'].' '.$gNrow['name2'].'</td>
						<td>';
						if(($row['sname']=='') || ($row['sname']==$row['resourceName'])){
							$resTable .= $row['resourceName'];
							$resTable .= '<input type="hidden" name="resourceName[]" value="'.$row['resourceName'].'">';
						}else{
							$resTable .= $row['sname'];
							$resTable .= '<input type="hidden" name="resourceName[]" value="'.$row['sname'].'">';
						}                               
						$resTable .= '</td>
						<td>
						<input type="hidden" name="resource[]" value="'.$row['resource'].'">
						'.$row['resource'];
						
						$resTable .= '</td>';

						$resTable .= '
						
						<td><input type="hidden" name="tariffs[]" value="'.$tariffs.'">'.$tariffs.'</td> 
						<td><input type="hidden" name="qty[]" value="'.$akhh.'">'.$akhh.'</td> 
						<td><input type="hidden" name="uom[]" value="'.$row['uom'].'">'.$row['uom'].'</td>                                 
						<td nowrap><input type="hidden" name="amount[]" value="'.$amount.'">'.floatval($amount);
						
						$resTable .= '</td>';

							
							$totalz['productNr'] = $row['productNr'];
							$totalz['productName'] = $row['productNr'].' - '.$gNrow['name1'].' '.$gNrow['name2'];
							$totalz['resource'] = $row['resource'];
							$totalz['resourceName'] = $row['resourceName'];
							$totalz['qty'] = $akhh;
							$totalz['uom'] = $row['uom'];
							$totalz['amount'] = floatval($amount);
						
							array_push($totals, $totalz);
						

			$money += $amount;
			$vmoney += $qty;
			$allTotal += $amount;
						
						
					$resTable .= '    
					</tr>';
	 



			$i++; 
			$showIt='yes';       
			}

		
		}

	}

	if($referenceType==1 || $referenceType==2){
		
		$query = mysqli_query($conn, "


			SELECT l.id               AS lid, 
				   l.docNr            AS receive_docNr, 
				   l.thisTransport    AS receive_transport, 
				   l.productNr        AS receive_productNr, 
				   l.activityDate     AS receive_date, 
				   l.issueDate,
				   h.ladingNr         AS receive_ladingNr, 
				   l.place_count      AS receive_place_count, 
				   h.cargo_status     AS receive_cargo_status, 
				   h.clientCode       AS clientCode, 

				   ni.name1, 
				   ni.name2, 
				   h.agreements       AS agreements, 
				   nc.Name, 
				   a.uom              AS uom	  

			FROM cargo_line AS l 
		  
				LEFT JOIN cargo_header AS h 
				ON l.docNr = h.docNr 
					  
				LEFT JOIN agreements_lines AS a 
				ON h.agreements = a.contractnr 
				AND l.productnr = a.item 
				AND l.resource = a.service 
						 
 
						 
				LEFT JOIN n_customers AS nc 
				ON h.clientCode = nc.Code 
					  
				LEFT JOIN n_items AS ni 
				ON l.productNr = ni.code 			


					
				WHERE h.clientCode='".$clientCode."' AND h.agreements='".$agreements."'
				
				GROUP BY l.id
				ORDER BY l.activityDate ASC, l.docNr 
				
				
		") or die(mysqli_error($conn));




		$lastiTo = NULL;
		$lastDocNr = NULL;
		$lastOrgId = NULL;
		$lastId = NULL;
		$g=0;

				$gmoney = null;
				$gvmoney = null;

		$gtotals = $totalz = array();
		while($row = mysqli_fetch_array($query)){

			$getHolding = mysqli_query($conn, "
			SELECT l.id AS lid, l.tariffs AS tariffs,
			(SELECT freeDays FROM agreements WHERE contractNr=l.contractNr) AS freeDays,
			(SELECT prepareAll FROM agreements WHERE contractNr=l.contractNr) AS prepareAll,
			l.service AS service
			FROM agreements_lines AS l
			WHERE l.contractNr='".$agreements."' AND l.item='".$row['receive_productNr']."' AND l.keeping='on'");

			$gHrows = mysqli_fetch_array($getHolding);
			
			//sagatavot glabāšanu par izvēlēto posmu
			if(mysqli_num_rows($getHolding)>0 && $gHrows['prepareAll']==0){

				$getEzValues = mysqli_query($conn, "

					SELECT 

					
					ez.docNr            AS docNr, 
					ez.deliverydate     AS deliveryDate, 
					
					ez.assistant_amount AS assistant_amount, 
					ez.amount           AS amount, 
					ez.productnr        AS productNr, 
					ez.productumo       AS productUmo, 
					 
					ez.orgline,

					ez.id,
					ez.status as istatus, 
					ez.place_count as iplace_count, 
					ez.activityDate as iactivityDate, 
					ez.place_count as iassistant_amount,
					au.uom             AS auuom
					
					FROM (
				
						(
						
							SELECT id, status,
							orgLine,
							place_count,
							activityDate,
							docNr,
							cargoLine AS mold,

							deliverydate,
							assistant_amount,
							amount,
							productNr,
							productUmo

							FROM   item_ledger_entry
							WHERE cargoLine='".$row['lid']."'
							ORDER BY id DESC
						)
						UNION
						(
						
							SELECT id, status,
							orgLine,
							place_count,
							activityDate,
							docNr,
							orgLine AS mold,

							deliverydate,
							assistant_amount,
							amount,
							productNr,
							productUmo

							FROM   item_ledger_entry
							WHERE orgLine='".$row['lid']."'
							ORDER BY id DESC
						)
					
					) ez

					LEFT JOIN additional_uom AS au 
					ON ez.productnr = au.productnr 
					AND au.status = 1 
					AND au.convert_from = 1

					WHERE ez.mold>0  
					order by ez.id DESC
					LIMIT 1	

				");
				$ez = mysqli_fetch_array($getEzValues);

				$row['docNr'] = $ez['docNr'];
				$row['deliveryDate'] = $ez['deliveryDate'];
				$row['assistant_amount'] = $ez['assistant_amount'];
				$row['amount'] = $ez['amount'];
				$row['productNr'] = $ez['productNr'];
				$row['productUmo'] = $ez['productUmo'];
				$row['id'] = $ez['eid'];
				$row['auuom'] = $ez['auuom'];				
				
				if(($lastDocNr!='' || $lastOrgId!=$row['orgLine'] ) && $lastDocNr != $row['receive_docNr']){ $lastiTo = NULL; }
				if($lastId!=$row['orgLine']){ $lastiTo = NULL;}

				if($from){$from=date('Y-m-d', strtotime($from));}
				if($to){$to=date('Y-m-d', strtotime($to));}
			
				

				$deliveryDate = date('Y-m-d', strtotime($row['receive_date']));
				
					$adate=null;
					$adate.='A1';
					if($ez['istatus']==40){
						
						
						if($ez['iactivityDate']>$to){
							$activityDate = date('Y-m-d', strtotime($to));
							$adate.='B1';
						}else{
							$activityDate = date('Y-m-d', strtotime($ez['iactivityDate']));
							$adate.='C1';
						}				
						
						
					}else{
						$activityDate = date('Y-m-d', strtotime($to));
						$adate.='D1';
					}		
				
				$nextAction = nextAction($conn, $from, $to, $row['eid'], $row['docNr'], $activityDate);
				
				if($nextAction==''){$nextAction=$to;} 
				
				$lineStatus = 'GO';
				
				$nextPayDay = date('Y-m-d', strtotime("+".$gHrows['freeDays']." days", strtotime($deliveryDate)));
			
				$tmpFromDate = '';
				if($lastiTo!=''){
					//$tmpFromDate = date('Y-m-d', strtotime('+1 days', strtotime($lastiTo))); // IG 2018-08-13 aizkomentēju jo šķiet pārraksta datuno NO un līdz ar to bija nepareizs aprēķins (nepārbaudīju ko tas maina tālāk)
				}

				if($nextPayDay>$tmpFromDate){
					$tmpFromDate = $nextPayDay;
				}

				if($from>$tmpFromDate){
					$tmpFromDate=$from;
				}

				$inside=null;
				$inside.='A';

				$tmpToDate = $activityDate;
				if($activityDate!='' && $tmpFromDate>$activityDate){
					$tmpToDate =  $nextAction;
					$inside.='B';
				}

				if($tmpToDate=='' || $tmpToDate>$to){
					$tmpToDate = $to;
					$inside.='C';
				}

				if($nextPayDay>$tmpToDate){
					$lineStatus = 'EXIT';
				}

				$lastiTo = $tmpToDate;
				$lastDocNr = $row['receive_docNr'];
				
				
				
				
				if($lineStatus=='EXIT'){
					
				}
				
				
				
			if($lineStatus=='GO'){
			
				if($row['action']==30 && $row['amount']<0){
					$qty = floatval($row['amount']) * (-1);
				}else{
					$qty = floatval($row['amount']);
				} 

				if($row['auuom']){
					$auuom = $row['auuom'];
				}else{
					$auuom = $row['productUmo'];
				}
				
				$productLeftOnDay = $daysBetweenDates = $convertValue = $thatValue = null;
				$productLeftOnDay = productLeftOnDay($conn, $row['receive_docNr'], $row['receive_productNr'], $tmpFromDate, $row['lid']);

				$daysBetweenDates = daysBetweenDates($tmpFromDate, $tmpToDate);

				$convertValue = getTotValueUOM($conn, $row['receive_productNr'], $productLeftOnDay, $auuom, $row['uom']);
				
				$thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];

				if($productLeftOnDay){
					
				}
				
				if($myid==29){
					
				}

				$eee=$eee+$thatValue;	

			if($thatValue>0){
				
					$resTable .= '<input type="hidden" name="gLedger_id[]" value="'.$ez['id'].'">';
					$resTable .= '<input type="hidden" name="gPrepareAll[]" value="'.$gHrows['prepareAll'].'">';

					$resTable .= '<tr>';
				

					$iseeonly=null;
					
					if($myid==29000000){
						$iseeonly = ' <b>|||'.$inside.' - '.$adate.'||| >>'.$ez['iactivityDate'].'<< >>'.$row['receive_date'].'<< '.$tmpFromDate.' - '.$tmpToDate.' </b>'.$row['lid'].'</b> '.$row['receive_docNr'].' ID: '.$row['eid'].' '.$thatValue.' = '.$convertValue.' * '.$daysBetweenDates.' * '.$gHrows['tariffs'].' <b>'.$eee.'</b>';
					}

					$resTable .= '<td nowrap><input type="hidden" name="gAgreements[]" value="'.$row['agreements'].'">'.$row['agreements'].$iseeonly;

					$resTable .= '</td>';
					$resTable .= '<td nowrap>
							<input type="hidden" name="gClientCode[]" value="'.$row['clientCode'].'">
							'.$row['clientCode'].' - '.$row['Name'].' 
						  </td>';
					$resTable .= '<td>
							<input type="hidden" name="gProductNr[]" value="'.$row['receive_productNr'].'">
							'.$row['receive_productNr'].' - '.$row['name1'].' '.$row['name2'].'
						  </td>';
					$resTable .= '<td bgcolor="silver">GLABĀŠANA</td>';
					$resTable .= '<td><input type="hidden" value="'.$gHrows['service'].'" name="gResource[]">'.$gHrows['service'].'</td>';   
					$resTable .= '<td>
							<input type="hidden" name="gTariffs[]" value="'.floatval($gHrows['tariffs']).'">
							'.floatval($gHrows['tariffs']).'
						  </td>';
					$resTable .= '<td>
							<input type="hidden" name="gQty[]" value="'.$productLeftOnDay.'">
							'.$productLeftOnDay.'
						  </td>';                        
					$resTable .= '<td>
							<input type="hidden" name="gUom[]" value="'.$row['uom'].'">
							'.$row['uom'].'
						  </td>';                         
					$resTable .= '<td>
							<input type="hidden" name="gThatValue[]" value="'.$thatValue.'">
							'.$thatValue.'
						  </td>';
				
					$resTable .= '</tr>';

					
						$g++;
						$lastId = $row['orgLine'];
						

				$totalz['productNr'] = $row['receive_productNr'];
				$totalz['productName'] = $row['receive_productNr'].' - '.$row['name1'].' '.$row['name2'];
				$totalz['resource'] = $gHrows['service'];
				$totalz['resourceName'] = 'GLABĀŠANA';
				$totalz['qty'] = $productLeftOnDay;
				$totalz['uom'] = $row['uom'];
				$totalz['amount'] = floatval($thatValue);
			
				array_push($gtotals, $totalz);						
						
						
				$gmoney += $thatValue;
				$gvmoney += $productLeftOnDay;
				$allTotal += $thatValue;
						
					}

				}   

			}       

			//sagatavot glabāšanu par visu posmu
			if(mysqli_num_rows($getHolding)>0 && $gHrows['prepareAll']==1){

		if(($lastDocNr!='' || $lastOrgId!=$row['orgLine'] ) && $lastDocNr != $row['docNr']){ $lastiTo = NULL; }
		if($lastId!=$row['orgLine']){ $lastiTo = NULL;}

		if($from){$from=date('Y-m-d', strtotime($from));}
		if($to){$to=date('Y-m-d', strtotime($to));}



		$deliveryDate = date('Y-m-d', strtotime($row['deliveryDate']));
		$activityDate = date('Y-m-d', strtotime($row['activityDate']));
		$issueDate = date('Y-m-d', strtotime($row['issueDate']));

		$nextPayDay = date('Y-m-d', strtotime("+".$gHrows['freeDays']." days", strtotime($deliveryDate)));
		$cantPayDay=null;
		$cantPayDay = date('Y-m-d', strtotime("-".$gHrows['freeDays']." days", strtotime($activityDate)));

		$lineStatus = 'GO';

			$tmpFromDate = $nextPayDay;

			$tmpToDate = $to;
			if($activityDate<=$to){
				
			}
			
			
			
			if (($activityDate > $from) && ($activityDate < $to)){
				
			}else{
			  
			}

			// pārbauda lai piegādes datums ietilptu periodā kurā var piestādīt samaksu par glabāšanu
			if($deliveryDate>$cantPayDay){
				
			}
			


		$lastiTo = $tmpToDate;
		$lastDocNr = $row['docNr'];

		if($lineStatus=='GO'){

		if($row['action']==30 && $row['amount']<0){
			$qty = floatval($row['amount']) * (-1);
		}else{
			$qty = floatval($row['amount']);
		} 

		if($row['auuom']){
			$auuom = $row['auuom'];
		}else{
			$auuom = $row['productUmo'];
		}


		$productLeftOnDay = productLeftOnDay($conn, $row['docNr'], $row['productNr'], $tmpFromDate, $row['lid']);

		$daysBetweenDates = daysBetweenDates($tmpFromDate, $issueDate);

		

		$convertValue = getTotValueUOM($conn, $row['productNr'], $productLeftOnDay, $auuom, $row['uom']);
		
		

		$thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];
		
		
		if($thatValue>0 ){		
	
		}
		
				if($productLeftOnDay){
					
				}
				



		if($thatValue>0 ){
			$resTable .= '<input type="hidden" name="gLedger_id[]" value="'.$row['eid'].'">';
			$resTable .= '<input type="hidden" name="gPrepareAll[]" value="'.$gHrows['prepareAll'].'">';

			$resTable .= '<tr>';
		
			$resTable .= '<td nowrap>
					<input type="hidden" name="gAgreements[]" value="'.$row['agreements'].'">'; 
					
					$resTable .= $row['agreements'];

			$resTable .= '</td>';
			$resTable .= '<td>
					<input type="hidden" name="gClientCode[]" value="'.$row['clientCode'].'">
					'.$row['clientCode'].' - '.$row['Name'].'
				  </td>';
			$resTable .= '<td>
					<input type="hidden" name="gProductNr[]" value="'.$row['productNr'].'">
					'.$row['productNr'].' - '.$row['name1'].' '.$row['name2'].'
				  </td>';
			$resTable .= '<td>GLABĀŠANA</td>';
			$resTable .= '<td><input type="hidden" value="'.$gHrows['service'].'" name="gResource[]">'.$gHrows['service'].'</td>';   
			$resTable .= '<td>
					<input type="hidden" name="gTariffs[]" value="'.floatval($gHrows['tariffs']).'">
					'.floatval($gHrows['tariffs']).'
				  </td>';
			$resTable .= '<td>
					<input type="hidden" name="gQty[]" value="'.$productLeftOnDay.'">
					'.$productLeftOnDay.'
				  </td>';                        
			$resTable .= '<td>
					<input type="hidden" name="gUom[]" value="'.$row['uom'].'">
					'.$row['uom'].'
				  </td>';                         
			$resTable .= '<td>
					<input type="hidden" name="gThatValue[]" value="'.$thatValue.'">
					'.$thatValue.'
				  </td>';

			$resTable .= '</tr>';

				$g++;
				$lastId = $row['orgLine'];
				
				
				$totalz['productNr'] = $row['productNr'];
				$totalz['productName'] = $row['productNr'].' - '.$row['name1'].' '.$row['name2'];
				$totalz['resource'] = $gHrows['service'];
				$totalz['resourceName'] = 'GLABĀŠANA';
				$totalz['qty'] = $productLeftOnDay;
				$totalz['uom'] = $row['uom'];
				$totalz['amount'] = floatval($thatValue);
			
				array_push($gtotals, $totalz);
				
				
				$gmoney += $thatValue;
				$gvmoney += $productLeftOnDay;
				$allTotal += $thatValue;		
				
				
			}

		}

			}

		}

	}


	if($referenceType==1 || $referenceType==3){

		//papild pak summas
		$e_service = mysqli_query($conn, "
				SELECT asl.id, asl.amount, asl.resource , ash.agreements, ash.clientCode, al.uom, al.service_name, al.item, al.tariffs, nc.Name
				
				
				
				FROM additional_services_line asl 
				
				LEFT JOIN additional_services_header ash
				ON ash.docNr=asl.docNr

				LEFT JOIN agreements_lines AS al
				ON ash.agreements=al.contractNr AND asl.resource=al.service	
				
				LEFT JOIN n_customers AS nc
				ON ash.clientCode=nc.Code       
				
				WHERE ash.clientCode='".$clientCode."' AND ash.agreements='".$agreements."' AND ash.status='20' AND asl.submited=0
				AND DATE_FORMAT(ash.deliveryDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."' ") or die(mysqli_error($conn));
		$total_as = $e =  null;
		$etotals = $totalz = array();
		while($e_row = mysqli_fetch_array($e_service)){
			
			$total_as = $e_row['tariffs']*$e_row['amount'];
			$resTable .= '<tr>';
			$resTable .= '<input type="hidden" name="eService_line[]" value="'.$e_row['id'].'">';
			
			$resTable .= '<td><input type="hidden" name="eAgreements[]" value="'.$e_row['agreements'].'">'.$e_row['agreements'].'</td>';
			$resTable .= '<td><input type="hidden" name="eClientCode[]" value="'.$e_row['clientCode'].'">'.$e_row['clientCode'].' - '.$e_row['Name'].' </td>';
			$resTable .= '<td>-</td>';
			$resTable .= '<td>'.$e_row['service_name'];
			
			$resTable .= '<input type="hidden" name="eResourceName[]" value="'.$e_row['service_name'].'">'; 	
			
			$resTable .= '</td>';
			$resTable .= '<td><input type="hidden" value="'.$e_row['resource'].'" name="eResource[]">'.$e_row['resource'].'</td>';
			$resTable .= '<td><input type="hidden" name="eTariffs[]" value="'.floatval($e_row['tariffs']).'">'.floatval($e_row['tariffs']).'</td>';
			$resTable .= '<td><input type="hidden" name="eQty[]" value="'.floatval($e_row['amount']).'">'.floatval($e_row['amount']).'</td>';
			$resTable .= '<td><input type="hidden" name="eUom[]" value="'.$e_row['uom'].'">'.$e_row['uom'].'</td>';
			$resTable .= '<td><input type="hidden" name="eAmount[]" value="'.$total_as.'">'.$total_as.'</td>';
			
			
			
			$totalz['productNr'] = '-';
			$totalz['productName'] = '-';
			$totalz['resource'] = $e_row['resource'];
			$totalz['resourceName'] = $e_row['service_name'];
			$totalz['qty'] = floatval($e_row['amount']);
			$totalz['uom'] = $e_row['uom'];
			$totalz['amount'] = floatval($total_as);
		
			array_push($etotals, $totalz);		
			
			
			$allTotal += $total_as;
			
			$resTable .= '<tr>';
			$e++;
			
		}
	}

	if((mysqli_num_rows($query)>0 && $showIt=='yes') || ($g>0) || ($e>0)){ 
				
				$resTable .= '<input type="hidden" name="result" value="'.$i.'">';
				$resTable .= '<input type="hidden" name="gResult" value="'.$g.'">';
				$resTable .= '<input type="hidden" name="eResult" value="'.$e.'">';

				$resTable .= '<tr><td colspan="8" align="right">kopā, EUR</td><td><b>'.$allTotal.'</b></td></tr>';
				$resTable .= '
				</tbody>
			</table>';

			if($gvmoney){
					
			}

			$acount = count($firstquarter);



	function groupByPartAndType($input) {
	  $output = Array();

	  foreach($input as $value) {
		$output_element = &$output[$value['productNr'] . "_" . $value['resource'] . "_" . $value['uom'] . "_" . $value['resourceName']];
		$output_element['productNr'] = $value['productNr'];
		$output_element['productName'] = $value['productName'];
	   
		$output_element['resourceName'] = $value['resourceName'];
		$output_element['resource'] = $value['resource'];
		
		
		
		!isset($output_element['qty']) && $output_element['qty'] = 0;
		$output_element['qty'] += $value['qty'];

		$output_element['uom'] = $value['uom'];
		
		!isset($output_element['amount']) && $output_element['amount'] = 0;
		$output_element['amount'] += $value['amount'];
	  }

	  return array_values($output);
	}	
		
	$pak=$epak=$glab=null;
	if(isset($totals)){	
		$pak = groupByPartAndType($totals);
	}
	if(isset($gtotals)){
		$glab = groupByPartAndType($gtotals);
	}
	if(isset($etotals)){
		$epak = groupByPartAndType($etotals);
	}

	$pakCount = count($pak);
	$epakCount = count($epak);	
	$glabCount = count($glab);	
		
		$sTable = null;
		if($pakCount>0 || $epakCount>0 || $glabCount>0){
			echo $sumTables;	

			$tQty = $tAmount = null;
			if($pakCount>0){
				for ($i=0; $i<=$pakCount; $i++) {
					
					if(isset($pak[$i])){
						$sTable .= '<tr>';
						
							foreach($pak[$i] AS $key => $value){
								
										$productName=null;
										
										if($key=='productName'){
											$sTable .= '<td>'.$value.'</td>';
										}						
										if($key=='resourceName'){
											$sTable .= '<td>'.$value.'</td>';
										}
										if($key=='resource'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='qty'){
											$sTable .= '<td>'.$value.'</td>';
											$tQty += $value;
										}
										if($key=='uom'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='amount'){
											$sTable .= '<td>'.$value.'</td>';
											$tAmount += $value;
										}							
																	
							}
						
						$sTable .= '</tr>';
					}
				}
			}
			
			if($epakCount>0){
				for ($i=0; $i<=$epakCount; $i++) {
					
					if(isset($epak[$i])){
						$sTable .= '<tr>';
						
							foreach($epak[$i] AS $key => $value){
								
										if($key=='productName'){
											$sTable .= '<td>'.$value.'</td>';
										}							
										if($key=='resourceName'){
											$sTable .= '<td>'.$value.'</td>';
										}
										if($key=='resource'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='qty'){
											$sTable .= '<td>'.$value.'</td>';
											$tQty += $value;
										}
										if($key=='uom'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='amount'){
											$sTable .= '<td>'.$value.'</td>';
											$tAmount += $value;
										}							
																	
							}
						
						$sTable .= '</tr>';
					}
				}
			}
			
			if($glabCount>0){
				for ($i=0; $i<=$glabCount; $i++) {
					
					if(isset($glab[$i])){
						$sTable .= '<tr>';
						
							foreach($glab[$i] AS $key => $value){
								
										if($key=='productName'){
											$sTable .= '<td>'.$value.'</td>';
										}							
										if($key=='resourceName'){
											$sTable .= '<td>'.$value.'</td>';
										}
										if($key=='resource'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='qty'){
											$sTable .= '<td>'.$value.'</td>';
											$tQty += $value;
										}
										if($key=='uom'){
											$sTable .= '<td>'.$value.'</td>';
										}									
										if($key=='amount'){
											$sTable .= '<td>'.$value.'</td>';
											$tAmount += $value;
										}						
																	
							}
						
						$sTable .= '</tr>';
					}
				}	
			}

			if($tQty || $tAmount){
				$sTable .= '<tr><td colspan="3"></td><td><b>'.$tQty.'</b></td><td></td><td><b>'.$tAmount.'</b></td></tr>';
			}		
			echo $sTable;
			echo $sumTablee;		
		}
			
			echo $resTable;
			
			
			echo '
			<input type="button" class="btn btn-default btn-xs add-row" value="pievienot komentāru">
			<button type="button" class="btn btn-default btn-xs delete-row">noņemt komentāru</button><br><br>

			
			<button type="submit" class="btn btn-default btn-xs" id="savebtn">
				saglabāt
			</button>'; 
	}


}else{
    echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav izvēlēts klients un/vai līgums!';
}

echo '
</form>      

        </div>
    </div>     
</div>        
    ';

?>
<script>
    $(document).ready(function(){
        $(".add-row").click(function(){
            var markup = "<tr><td><input type='checkbox' name='record'></td><td></td><td>KOMENTĀRS</td><td colspan='6'><div class='form-group'><input type='text' maxlength='80' class='form-control' rows='1' name='comment[]' style='resize: none;'></div></td></tr>";
            $("table tbody").append(markup);
        });
        
        // Find and remove selected table rows
        $(".delete-row").click(function(){
            $("table tbody").find('input[name="record"]').each(function(){
            	if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                }
            });
        });
    }); 
</script> 

<div id="pleaseWait" style="display: none;">
    <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
</div>    