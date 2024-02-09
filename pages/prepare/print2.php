<?php
declare (strict_types=1);
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'true');

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

if($from){$from = date('Y-m-d', strtotime($from));}

if($to){$to = date('Y-m-d', strtotime($to));}

include('../../functions/base.php');
require('../../inc/s.php');

//header("Content-type: application/vnd-ms-excel");
//header("Content-Disposition: attachment; filename=prepare_attachment_".$from."_".$to.".xls");

echo '
<style>
.colorOne {
    background-color: #ccc0da;
}
.colorTwo {
    background-color: #db9594;
}
</style>

<table border="1">
    <thead>
        <tr>
        <td colspan="13"></td>
        <td colspan="2">PIELIKUMS '.$from .' - '.$to.'</td>
        </tr>
        <tr>
            <th colspan="7" class="colorTwo">Ievestā krava</th>
            <th colspan="5" class="colorTwo">Izsniegtā krava</th>
            <th colspan="3" class="colorTwo">Glabāšana</th>
        <tr>
        <tr>
            <th class="colorOne">Pieņemšanas<br>datums</th>
            <th class="colorOne">Ievests ar<br>a/m Nr.</th>
            <th class="colorOne">Pavadzīme<br>Nr.</th>
            <th class="colorOne">Ievesto<br>paku<br>skaits<br>faktiski</th>
            <th class="colorOne">Daudzums<br>m3</th>
            <th class="colorOne">B/L</th>
            <th class="colorOne">Status</th>
            <th class="colorOne">Izsniegšanas<br>datums</th>
            <th class="colorOne">Pavaddok<br>uments ar<br>ko krava<br>izsniegta ,<br>datums</th>
            <th class="colorOne">M/K</th>
            <th class="colorOne">Izsniegto<br>paku<br>skaits</th>
            <th class="colorOne">Daudzums<br>m3</th>
            <th class="colorOne">Kopējais<br>glabāšanas<br>diennakts<br>skaits</th>                        
            <th class="colorOne">Maksas<br>dienu skaits</th>
            <th class="colorOne">Glabāšanas<br>maksa</th>
        <tr>        
    </thead>
    <tbody>';

                    

    $table = mysqli_query($conn, "

            SELECT 
            e.id AS eid, e.docNr AS docNr, e.deliveryDate AS deliveryDate, e.activityDate AS activityDate, e.assistant_amount AS assistant_amount, 
            e.amount AS amount, e.productNr AS productNr, e.productUmo AS productUmo, e.status AS status,
            h.ladingNr AS ladingNr, h.transport_name AS transport_name, cargo_status AS cargo_status,

            i.id AS iid, i.deliveryDate AS ideliveryDate, i.activityDate AS iactivityDate, i.amount AS iamount, i.assistant_amount AS iassistant_amount,
            i.productNr AS iproductNr, i.docNr AS idocNr, i.productUmo AS iproductUmo

            FROM item_ledger_entry AS e

            LEFT JOIN cargo_header AS h
            ON e.docNr=h.docNr

            LEFT JOIN item_ledger_entry AS i
            ON e.docNr=i.docNr AND (DATE_FORMAT(i.activityDate,'%Y-%m-%d') BETWEEN '".$from."' AND '".$to."') AND i.status='40' 
            
            WHERE e.status='20' 
            AND e.activityDate !='0000-00-00 00:00:00' AND e.deliveryDate !='0000-00-00 00:00:00' 
            AND i.activityDate !='0000-00-00 00:00:00' AND i.deliveryDate !='0000-00-00 00:00:00'
           GROUP BY i.id

        ") or die(mysqli_error($conn));

    $total = null;      
    while($row = mysqli_fetch_array($table)){



        $getHolding = mysqli_query($conn, "
        SELECT l.id AS lid, l.tariffs AS tariffs,
        (SELECT freeDays FROM agreements WHERE contractNr=l.contractNr) AS freeDays
        FROM agreements_lines AS l
        WHERE l.contractNr='".$agr."' AND l.item='".$row['productNr']."' AND l.keeping='on'");
        
        $gHrows = mysqli_fetch_array($getHolding); 
    
        if($gHrows['freeDays']>0){
            $freeDays = $gHrows['freeDays'];                    

                        $deliveryDate = date('Y-m-d', strtotime($row['ideliveryDate']));
                        $activityDate = date('Y-m-d', strtotime($row['iactivityDate']));
                        $nextAction = nextAction($conn, $from, $to, $row['iid'], $row['idocNr'], $activityDate);
                        
                        if($nextAction==''){$nextAction=$to;} 
                        
                        $lineStatus = 'GO';
                        
                        $nextPayDay = date('Y-m-d', strtotime("+".$gHrows['freeDays']." days", strtotime($deliveryDate)));
                        
                        $tmpFromDate = '';
                        if($lastiTo!=''){
                            $tmpFromDate = date('Y-m-d', strtotime('+1 days', strtotime($lastiTo)));
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
                        $lastDocNr = $row['idocNr'];
                
                
                        $productLeftOnDay = productLeftOnDay($conn, $row['idocNr'], $row['iproductNr'], $tmpFromDate, $row['iid']);
                
                        echo $row['idocNr'].', '.$row['iproductNr'].', '.$tmpFromDate.', '.$row['iid'].'<br>';

                        $daysBetweenDatesTotal = daysBetweenDates($tmpFromDate, $tmpToDate)-1;
                        
                        $daysBetweenDates = daysBetweenDates($tmpFromDate, $tmpToDate)-1;
                
                        $convertValue = getTotValueUOM($conn, $row['iproductNr'], $productLeftOnDay, $row['iproductUmo'], $row['iproductUmo']);

                        $thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];

                        echo '<tr>
                        
                            <td>'.$row['docNr'].' '.$row['eid'].' '.$row['status'].' '.date('Y-m-d', strtotime($row['deliveryDate'])).'</td>
                            <td>'.$row['transport_name'].'</td>
                            <td>'.$row['ladingNr'].'</td>
                            <td>'.$row['assistant_amount'].'</td>
                            <td>'.$row['amount'].'</td>
                            <td>?</td>
                            <td>'.$row['cargo_status'].'</td>

                            <td>'.$row['iid'].' '.date('Y-m-d', strtotime($row['iactivityDate'])).'</td>
                            <td>?</td>
                            <td>?</td>
                            <td>'.$row['iassistant_amount'].'</td>
                            <td>'.$row['iamount'].'</td>
 

                               
                            <td>'.$daysBetweenDatesTotal.'</td>
                            <td>'.$daysBetweenDates.'</td>
                            <td>'.$thatValue.'</td>';

                        echo '</tr>';
                        
                        $total += $thatValue;
        }
    }
    echo '   
    
        <tr>
            <td colspan="13"></td>
            <td colspan="2">total: <div style="float: right;">'.$total.'</div></td>
        </tr>

    </tbody>
</table>    
';

?>
