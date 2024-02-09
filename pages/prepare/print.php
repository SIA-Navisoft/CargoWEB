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

$view = $val = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['val'])){$val = htmlentities($_GET['val'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['cus'])){$cus = htmlentities($_GET['cus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['agr'])){$agr = htmlentities($_GET['agr'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['from'])){$from = htmlentities($_GET['from'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['to'])){$to = htmlentities($_GET['to'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['docNr'])){$docNr = htmlentities($_GET['docNr'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['productNr'])){$productNr = htmlentities($_GET['productNr'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['report'])){$report = htmlentities($_GET['report'], ENT_QUOTES, "UTF-8");}


if($from){$from = date('Y-m-d', strtotime($from));}

if($to){$to = date('Y-m-d', strtotime($to));}

include('../../functions/base.php');
require('../../inc/s.php');


if($myid!=29){
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=prepare_attachment_".$from."_".$to.".xls");
}

echo '
<style>
.colorOne {
    background-color: #ccc0da;
}
.colorTwo {
    background-color: #db9594;
}
</style>';
	
echo '
<table border="1">
    <thead>
	
		<tr>
		<td colspan="16">Atskaite <b>'.$report.'</b></td>
		<td colspan="2">periods <b>'.$from .'</b> - <b>'.$to.'</b></td>
		</tr>
        <tr>
		<td colspan="18"><b>'.returnClientName($conn, $cus).'</b>, līguma Nr.: <b>'.$agr.'</b></td>
        
        </tr>
        <tr>
            <th colspan="10" class="colorTwo">Ievestā krava</th>
            <th colspan="5" class="colorTwo">Izsniegtā krava</th>
            <th colspan="3" class="colorTwo">Glabāšana</th>
        <tr>
        <tr>
            <th class="colorOne">Pieņemšanas<br>datums</th>
			<th class="colorOne">Ievests ar<br>a/m Nr.</th>
			<th class="colorOne">Seriālais nr.</th>
            <th class="colorOne">Pavadzīme<br>Nr.</th>
            <th class="colorOne">Ievesto<br>paku<br>skaits<br>faktiski</th>
			<th class="colorOne">Daudzums</th>
			<th class="colorOne">Mērv.</th>
            <th class="colorOne">B/L</th>
            <th class="colorOne">Status</th>
            <th class="colorOne">Izsniegšanas<br>datums</th>
            <th class="colorOne">Pavaddok<br>uments ar<br>ko krava<br>izsniegta ,<br>datums</th>
            <th class="colorOne">M/K</th>
            <th class="colorOne">Izsniegto<br>paku<br>skaits</th>
			<th class="colorOne">Daudzums</th>
			<th class="colorOne">Mērv.</th>
            <th class="colorOne">Kopējais<br>glabāšanas<br>diennakts<br>skaits</th>                        
            <th class="colorOne">Maksas<br>dienu skaits</th>
            <th class="colorOne">Glabāšanas<br>maksa</th>
        <tr>        
    </thead>
    <tbody>';
	
$table = mysqli_query($conn, "

SELECT t.*
FROM (

    (
		SELECT 

		l.id AS lid, l.docNr AS receive_docNr, l.thisTransport AS receive_transport, l.serialNo, l.productNr AS receive_productNr, l.activityDate AS receive_date, h.ladingNr AS receive_ladingNr, l.place_count AS receive_place_count, 
		h.cargo_status AS receive_cargo_status, 
		woi.period_to, woi.prepareall AS prepareAll,
		  
		e.id AS eid, e.docNr AS docNr, e.deliverydate AS deliveryDate, e.activitydate AS activityDate, e.assistant_amount AS assistant_amount, e.amount AS amount, e.productnr AS productNr, e.productumo AS productUmo, e.status AS status, 
	   
		a.uom AS uom,  
		au.uom AS auuom , woi.unit ,
		
		(select z.status from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as istatus,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iplace_count,
		(select z.activityDate from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iactivityDate,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iassistant_amount
		
		
		FROM web_objects_invoices AS woi 
			LEFT JOIN item_ledger_entry    AS e  ON e.id = woi.ledger_entry 
			LEFT JOIN cargo_header_receive AS h  ON e.docNr = h.docNr 
			LEFT JOIN cargo_line_receive   AS l  ON e.cargoline = l.id OR e.orgline = l.id 
			
			LEFT JOIN agreements_lines     AS a  ON h.agreements = a.contractnr AND e.productnr = a.item  AND e.resource = a.service
			JOIN additional_uom       AS au ON e.productnr = au.productnr AND au.status = 1 

		WHERE woi.report_id='".$report."'

	)
	UNION ALL
    (
		SELECT 

		l.id AS lid, l.docNr AS receive_docNr, l.thisTransport AS receive_transport, l.serialNo, l.productNr AS receive_productNr, l.activityDate AS receive_date, h.ladingNr AS receive_ladingNr, l.place_count AS receive_place_count, 
		h.cargo_status AS receive_cargo_status, 
		woi.period_to, woi.prepareall AS prepareAll,
		   
		e.id AS eid, e.docNr AS docNr, e.deliverydate AS deliveryDate, e.activitydate AS activityDate, e.assistant_amount AS assistant_amount, e.amount AS amount, e.productnr AS productNr, e.productumo AS productUmo, e.status AS status, 
	   
		a.uom AS uom, 
		au.uom AS auuom, woi.unit ,
		
		(select z.status from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as istatus,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iplace_count,
		(select z.activityDate from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iactivityDate,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iassistant_amount
	  
		FROM web_objects_invoices_accepted AS woi 
			LEFT JOIN item_ledger_entry    AS e  ON e.id = woi.ledger_entry 
			LEFT JOIN cargo_header_receive AS h  ON e.docNr = h.docNr 
			LEFT JOIN cargo_line_receive   AS l  ON e.cargoline = l.id OR e.orgline = l.id 
			
			LEFT JOIN agreements_lines     AS a  ON h.agreements = a.contractnr AND e.productnr = a.item  AND e.resource = a.service
			JOIN additional_uom       AS au ON e.productnr = au.productnr AND au.status = 1 

		WHERE woi.report_id='".$report."'

	)
	UNION ALL
    (
		SELECT 

		l.id AS lid, l.docNr AS receive_docNr, l.thisTransport AS receive_transport, l.serialNo, l.productNr AS receive_productNr, l.activityDate AS receive_date, h.ladingNr AS receive_ladingNr, l.place_count AS receive_place_count, 
		h.cargo_status AS receive_cargo_status, 
		woi.period_to, woi.prepareall AS prepareAll,
		
		e.id AS eid, e.docNr AS docNr, e.deliverydate AS deliveryDate, e.activitydate AS activityDate, e.assistant_amount AS assistant_amount, e.amount AS amount, e.productnr AS productNr, e.productumo AS productUmo, e.status AS status, 
	   
		a.uom AS uom, 	 
		au.uom AS auuom , woi.unit ,
		
		(select z.status from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as istatus,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iplace_count,
		(select z.activityDate from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iactivityDate,
		(select z.place_count from item_ledger_entry as z where (z.orgLine=l.id OR z.cargoLine=l.id) AND e.docNr=l.docNr ORDER BY z.id DESC LIMIT 1) as iassistant_amount
	  
		FROM web_objects_invoices_archived AS woi 
			LEFT JOIN item_ledger_entry    AS e  ON e.id = woi.ledger_entry 
			LEFT JOIN cargo_header_receive AS h  ON e.docNr = h.docNr 
			LEFT JOIN cargo_line_receive   AS l  ON e.cargoline = l.id OR e.orgline = l.id 
			
			LEFT JOIN agreements_lines     AS a  ON h.agreements = a.contractnr AND e.productnr = a.item  AND e.resource = a.service
			JOIN additional_uom       AS au ON e.productnr = au.productnr AND au.status = 1 

		WHERE woi.report_id='".$report."'

	)
	
) AS t

		GROUP BY t.lid
		ORDER BY t.receive_date ASC, t.receive_docNr

") or die(mysqli_error($conn));



    $total = null;      
    while($row = mysqli_fetch_array($table)){



        $getHolding = mysqli_query($conn, "
        SELECT l.id AS lid, l.tariffs AS tariffs,
        (SELECT freeDays FROM agreements WHERE contractNr=l.contractNr) AS freeDays
        FROM agreements_lines AS l
        WHERE l.contractNr='".$agr."' AND l.item='".$row['receive_productNr']."' AND l.keeping='on'");
        
        $gHrows = mysqli_fetch_array($getHolding); 

		if($gHrows['freeDays']>0 && $row['prepareAll']==0 && $row['activityDate']){
			$freeDays = $gHrows['freeDays'];

	
			if(($lastDocNr!='' || $lastOrgId!=$row['orgLine'] ) && $lastDocNr != $row['docNr']){ $lastiTo = NULL; }
			if($lastId!=$row['orgLine']){ $lastiTo = NULL;}
		
			if($from){$from=date('Y-m-d', strtotime($from));}
			if($to){$to=date('Y-m-d', strtotime($to));}
		
			$deliveryDate = date('Y-m-d', strtotime($row['receive_date']));

			if($row['istatus']==40){
				
				
				if($row['iactivityDate']>$row['period_to']){
					$activityDate = date('Y-m-d', strtotime($row['period_to']));
				}else{
					$activityDate = date('Y-m-d', strtotime($row['iactivityDate']));
				}				
				
				
			}else{
				$activityDate = date('Y-m-d', strtotime($row['period_to']));
			}


			$nextAction = nextAction($conn, $from, $to, $row['eid'], $row['docNr'], $activityDate);
			
			if($nextAction==''){$nextAction=$to;} 
			
			
			$nextPayDay = date('Y-m-d', strtotime("+".$freeDays." days", strtotime($deliveryDate)));
			
			$tmpFromDate = '';
			if($lastiTo!=''){
				
			}
		
			if($nextPayDay>$tmpFromDate){
				$tmpFromDate = $nextPayDay;
			}
		
			if($from>$tmpFromDate){
				$tmpFromDate=$from;
			}
		
			$tmpToDate =  $activityDate;
			if($activityDate!='' && $tmpFromDate>$activityDate){
				$tmpToDate =  $nextAction;
			}
		
			if($tmpToDate=='' || $tmpToDate>$to){
				$tmpToDate = $to;
			}
		
			if($nextPayDay>$tmpToDate){
				$lineStatus = 'EXIT';
			}
		
			$lastiTo = $tmpToDate;
			$lastDocNr = $row['docNr'];
		
			if($row['auuom']){
				$auuom = $row['auuom'];
			}else{
				$auuom = $row['productUmo'];
			}

			$product_received = null;
			$product_received = productLeftOnDay($conn, $row['receive_docNr'], $row['receive_productNr'], $row['receive_date'], $row['lid']);

			$productLeftOnDay = null;
			$productLeftOnDay = productLeftOnDay($conn, $row['docNr'], $row['receive_productNr'], $tmpFromDate, $row['lid']);
		
			$daysBetweenDates = daysBetweenDates($tmpFromDate, $tmpToDate);

			if($row['status']==40){
				if($row['activityDate']>$row['period_to']){
					$daysBetweenDatesTotal = daysBetweenDates($deliveryDate, $row['period_to']);
				}else{
					$daysBetweenDatesTotal = daysBetweenDates($deliveryDate, $row['activityDate']);
				}
				
			}else{
				$daysBetweenDatesTotal = daysBetweenDates($deliveryDate, $row['period_to']);
			}
			
		
			$convertValue = getTotValueUOM($conn, $row['receive_productNr'], $productLeftOnDay, $auuom, $row['uom']);
			$thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];

						if($thatValue>0  || $daysBetweenDates>0){ 
						   
							echo '<tr>';
							
								//saņemts
								echo ' 
								<td>'.date('d.m.Y', strtotime($row['receive_date'])).'</td>
								<td>'.$row['receive_transport'].'</td>
								<td>'.$row['serialNo'].'</td>
								<td>'.$row['receive_ladingNr'].'</td>
								<td>'.$row['receive_place_count'].'</td>
								<td>'.$product_received.'</td>
								<td>'.$row['unit'].'</td>
								<td></td>
								<td>'.$row['receive_cargo_status'].'</td>';
								
								//izdots
								echo ' 
								<td>'; if($row['istatus']==40){echo date('Y-m-d', strtotime($row['iactivityDate']));} echo '</td>
								<td>'; if($row['istatus']==40){echo '';} echo '</td>
								<td>'; if($row['istatus']==40){echo '';} echo '</td>
								<td>'; if($row['istatus']==40){echo $row['iassistant_amount'];} echo '</td>
								<td>'; if($row['istatus']==40){echo $productLeftOnDay;} echo '</td>';
								echo '<td>'; if($row['istatus']==40 && $productLeftOnDay){echo $row['unit'];} echo '</td>';

								//glabāts
								echo '  
								<td>'.$daysBetweenDatesTotal.'</td>
								<td>'.$daysBetweenDates.'</td>
								<td  align="right" style="vnd.ms-excel.numberformat:0.00">'.number_format($thatValue, 2, '.', '').'</td>';

							echo '</tr>';
							
						}                
							
						$total += $thatValue;
		}

if($gHrows['freeDays']>0 && $row['prepareAll']==1 && $row['iactivityDate']){
    $freeDays = $gHrows['freeDays'];


if(($lastDocNr!='' || $lastOrgId!=$row['orgLine'] ) && $lastDocNr != $row['docNr']){ $lastiTo = NULL; }
if($lastId!=$row['orgLine']){ $lastiTo = NULL;}

if($from){$from=date('Y-m-d', strtotime($from));}
if($to){$to=date('Y-m-d', strtotime($to));}

$deliveryDate = date('Y-m-d', strtotime($row['receive_date']));
$activityDate = date('Y-m-d', strtotime($row['activityDate']));

$nextPayDay = date('Y-m-d', strtotime("+".$freeDays." days", strtotime($deliveryDate)));
$cantPayDay = date('Y-m-d', strtotime("-".$freeDays." days", strtotime($activityDate)));

$tmpFromDate = $nextPayDay;

if($activityDate<=$to){
    $tmpToDate = $activityDate;
}
$lineStatus='OK';
// pārbauda lai piegādes datums ietilptu periodā kurā var piestādīt samaksu par glabāšanu
if($deliveryDate>$cantPayDay){
    $lineStatus='EXIT';
}

    if($lineStatus=='OK'){

$lastiTo = $tmpToDate;
$lastDocNr = $row['docNr'];

if($row['auuom']){
    $auuom = $row['auuom'];
}else{
    $auuom = $row['productUmo'];
}

$product_received = null;
$product_received = productLeftOnDay($conn, $row['receive_docNr'], $row['receive_productNr'], $row['receive_date'], $row['lid']);

$productLeftOnDay = productLeftOnDay($conn, $row['docNr'], $row['productNr'], $tmpFromDate, null);

$daysBetweenDates = daysBetweenDates($tmpFromDate, $tmpToDate);

if($row['status']==40 || $row['istatus']==40){
    $daysBetweenDatesTotal = daysBetweenDates($deliveryDate, $row['activityDate']);
}else{
    $daysBetweenDatesTotal = daysBetweenDates($deliveryDate, $row['period_to']);
}

$convertValue = getTotValueUOM($conn, $row['productNr'], $productLeftOnDay, $auuom, $row['uom']);

$thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];
 
		if($thatValue>0){ 
 
			echo '<tr>';

            
                            //saņemts
                            echo ' 
                            <td>'.date('Y-m-d', strtotime($row['receive_date'])).'</td>
							<td>'.$row['receive_transport'].'</td> 
							<td>'.$row['serialNo'].'</td>
                            <td>'.$row['receive_ladingNr'].'</td>
                            <td>'.$row['receive_place_count'].'</td>
							<td>'.$product_received.'</td>
							<td>'.$row['unit'].'</td>
                            <td></td>
                            <td>'.$row['receive_cargo_status'].'</td>';
                            
                            //izdots
                            echo ' 
                            <td>'; if($row['status']==40 || $row['istatus']==40){echo date('Y-m-d', strtotime($row['activityDate']));} echo '</td>
                            <td>'; if($row['status']==40 || $row['istatus']==40){echo '';} echo '</td>
                            <td>'; if($row['status']==40 || $row['istatus']==40){echo '';} echo '</td>
                            <td>'; if($row['status']==40 || $row['istatus']==40){echo $row['assistant_amount'];} echo '</td>
							<td>'; if($row['status']==40 || $row['istatus']==40){echo $productLeftOnDay;} echo '</td>';
							echo '<td>'; if($row['istatus']==40 && $productLeftOnDay){echo $row['unit'];} echo '</td>';
    
                            //glabāts
                            echo '  
                            <td>'.$daysBetweenDatesTotal.'</td>
                            <td>'.$daysBetweenDates.'</td>
                            <td>'.$thatValue.'</td>';

                echo '</tr>';
        }
        $total += $thatValue;
    }    				
                
}

    }
    echo '   
    
        <tr>
            <td colspan="13"></td>
            <td >Kopā, EUR</td>
			<td ><b>'.number_format($total, 2, '.', '').'</b></td>
        </tr>

    </tbody>
</table>    
';
?>
