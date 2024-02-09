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

$query = mysqli_query($conn, "
        SELECT * FROM item_ledger_entry 
        WHERE 

        docNr='".$docNr."' AND
        
        (
                DATE_FORMAT(activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."'
        )
");


echo '<table border="1">
        <thead>
            <tr>
                <td>docNr</td>
                <td>deliveryDate</td>
                <td>activityDate</td>
                <td>amount</td>
                <td>action</td>
                <td>status</td>
                <td>dienas</td>
                <td>palicis</td>
                <td>jāmaksā</td>
            </tr>
        </thead>
        <tbody>';
$previous = '';
$lastiTo = NULL;
$lastId = NULL;
$lastDocNr = NULL;
while($row = mysqli_fetch_array($query)){



    $getHolding = mysqli_query($conn, "
    SELECT l.id AS lid, l.tariffs AS tariffs,
    (SELECT freeDays FROM agreements WHERE contractNr=l.contractNr) AS freeDays
    FROM agreements_lines AS l
    WHERE l.contractNr='LIG00064' AND l.item='".$row['productNr']."' AND l.keeping='on'");

    
    if(mysqli_num_rows($getHolding)>0){
    
        $gHrows = mysqli_fetch_array($getHolding);

        $deliveryDate = date('Y-m-d', strtotime($row['deliveryDate']));
        $activityDate = date('Y-m-d', strtotime($row['activityDate']));
        $nextAction = nextAction($conn, $from, $to, $row['id'], $row['docNr'], $activityDate);
        
        if($nextAction==''){$nextAction=$to;} 

        if(($lastDocNr!='' || $lastOrgId!=$row['orgLine'] ) && $lastDocNr != $row['docNr']){ $lastiTo = NULL; }
        if($lastId!=$row['orgLine']){ $lastiTo = NULL;}
     
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
        
        }

        $lastiTo = $tmpToDate;
        $lastDocNr = $row['docNr'];

    if($lineStatus=='GO'){

        if($row['action']==30 && $row['amount']<0){
            $qty = floatval($row['amount']) * (-1);
        }else{
            $qty = floatval($row['amount']);
        } 

        $productLeftOnDay = productLeftOnDay($conn, $row['docNr'], $row['productNr'], $tmpFromDate);

        $daysBetweenDates = daysBetweenDates($tmpFromDate, $tmpToDate);

        $convertValue = getTotValueUOM($conn, $row['productNr'], $productLeftOnDay, $row['productUmo'], $row['productUmo']);
        $thatValue = $convertValue * $daysBetweenDates * $gHrows['tariffs'];

        echo '<tr>
                <td>'.$lineStatus.' '.$row['docNr'].' ID: '.$row['id'].' FREE: '.$gHrows['freeDays'].'</td>
                <td>TMPF: '.$tmpFromDate.' TMPT: '.$tmpToDate.' NEXT: '.$nextAction.'  ( '.$tmpFromDate.' '.$tmpToDate.' ) '.$deliveryDate.'</td>
                <td>'.$activityDate.'</td>
                <td>'.$qty.'</td>
                <td>'.$row['action'].'</td>
                <td>'.$row['status'].'</td>

                <td>';
        
                    echo $daysBetweenDates;
                                
                echo '
                </td>

                <td>';

                    echo $productLeftOnDay;           

                echo '</td>
                <td>'.$thatValue.'</td>
            </tr>';
            $previous = $activityDate;
            $lastId = $row['orgLine'];
        }   

    }       

}

die(0);
$query = mysqli_query($conn, "

            SELECT e.id AS eid, e.productNr AS productNr, e.productUmo AS productUmo,
            e.amount AS amount, e.action AS action, l.resource AS resource,
            e.clientCode AS clientCode, e.clientName AS clientName, h.agreements AS agreements,
            (SELECT name FROM n_resource WHERE l.resource=id) AS resourceName, 
            a.uom AS uom, a.tariffs AS tariffs, a.id AS aid, a.service_name AS sname, e.enteredDate AS enteredDate,e.activityDate AS activityDate, e.movementDate AS movementDate,

            e.docNr AS docNr,
            e.cargoLine AS cargoLine,



            (select COUNT(id) 
            FROM web_holdings 
            WHERE agreement_id=h.agreements 
            AND customer_id=e.clientCode 

            AND (
                    DATE_FORMAT(e.movementDate,'%Y-%m-%d') 
                    BETWEEN DATE_FORMAT(period_from,'%Y-%m-%d') 
                    AND 
                    DATE_FORMAT(period_to,'%Y-%m-%d')
                )
            ) AS can


            FROM item_ledger_entry AS e

            LEFT JOIN cargo_line AS l 
            ON e.cargoLine=l.id OR e.orgLine=l.id

            LEFT JOIN cargo_header AS h
            ON l.docNr=h.docNr

            LEFT JOIN agreements_lines AS a
            ON h.agreements=a.contractNr AND e.productNr=a.item AND l.resource=a.service

            WHERE DATE_FORMAT(e.movementDate,'%Y-%m-%d') BETWEEN DATE_FORMAT('".$from."','%Y-%m-%d') AND DATE_FORMAT('".$to."','%Y-%m-%d')
            AND e.clientCode='40003250931' AND h.agreements='LIG00064'

            ORDER BY resourceName DESC

");
echo '
<table border="1"><tbody>
<tr>   
<td>eid</td><td>productNr</td><td>productUmo</td><td>amount</td><td>action</td><td>resource</td><td>clientCode</td><td>clientName</td><td>agreements</td><td>resourceName</td><td>uom</td>
<td>tariffs</td><td></td><td>sname</td><td>movementDate</td><td>activityDate</td><td>docNr</td><td>cargoLine</td><td>can</td>
</tr>
';
while($row = mysqli_fetch_array($query)){

echo '<tr>';    
echo '<td>'.$row['eid'].'</td><td>'.$row['productNr'].'</td><td>'.$row['productUmo'].'</td><td>'.$row['amount'].'</td><td>'.$row['action'].'</td><td>'.$row['resource'].'</td><td>'.$row['clientCode'].'</td><td>'.$row['clientName'].'</td><td>'.$row['agreements'].'</td><td>'.$row['resourceName'].'</td><td>';
echo $row['uom'].'</td><td>'.$row['tariffs'].'</td><td>'.$row['aid'].'</td><td>'.$row['sname'].'</td><td>'.$row['movementDate'].'</td><td>'.$row['activityDate'].'</td><td>'.$row['docNr'].'</td><td>'.$row['cargoLine'].'</td><td>'.$row['can'].'</td>';
echo '</tr>';

}
echo '
</tbody>
</table>
';




die(0);

echo '
<table border="1">
    <thead>
        <tr>
            <th colspan="7">Ievestā krava</th>
            <th colspan="5">Izsniegtā krava</th>
            <th colspan="3">Glabāšana</th>
        <tr>
        <tr>
            <th>Pieņemšanas<br>datums</th>
            <th>Ievests ar<br>a/m Nr.</th>
            <th>Pavadzīme<br>Nr.</th>
            <th>Ievesto<br>paku<br>skaits<br>faktiski</th>
            <th>Daudzums<br>m3</th>
            <th>B/L</th>
            <th>Status</th>
            <th>Izsniegšanas<br>datums</th>
            <th>Pavaddok<br>uments ar<br>ko krava<br>izsniegta ,<br>datums</th>
            <th>M/K</th>
            <th>Izsniegto<br>paku<br>skaits</th>
            <th>Daudzums<br>m3</th>
            <th>Kopējais<br>glabāšanas<br>diennakts<br>skaits</th>                        
            <th>Maksas<br>dienu skaits</th>
            <th>Glabāšanas<br>maksa</th>
        <tr>        
    </thead>
    <tbody>';

    $table = mysqli_query($conn, "
                    SELECT e.*, l.thisTransport AS thisTransport, l.cargo_status AS cargo_status, h.ladingNr AS ladingNr
                    FROM item_ledger_entry AS e
                    LEFT JOIN cargo_line AS l
                    ON e.docNr=l.docNr AND (e.cargoLine=l.id OR e.orgLine=l.id)
                    LEFT JOIN cargo_header AS h
                    ON e.docNr=h.docNr
                    WHERE (DATE_FORMAT(e.activityDate,'%Y-%m-%d') BETWEEN '".$from."' AND '".$to."') AND (e.status='20' OR e.status='40')
            ") or die(mysqli_error($conn));
    while($row = mysqli_fetch_array($table)){
        echo '
            <tr>';

            if($row['status']==20){
                echo '
                <td bgcolor="yellow">'.$row['activityDate'].'</td>
                <td bgcolor="yellow">'.$row['thisTransport'].'</td>
                <td bgcolor="yellow">'.$row['ladingNr'].'</td>
                <td bgcolor="yellow">?</td>
                <td bgcolor="yellow">'.$row['amount'].'</td>
                <td bgcolor="yellow">?</td>
                <td bgcolor="yellow">'.$row['cargo_status'].'</td>';
            }

            if($row['status']==40){
            echo '    
                <td bgcolor="green">'.$row['activityDate'].'</td>
                <td bgcolor="green">?</td>
                <td bgcolor="green">?</td>
                <td bgcolor="green">'.$row['amount'].'</td>
                <td bgcolor="green"></td>';
            }

            echo '    
                <td bgcolor="silver"></td>
                <td bgcolor="silver"></td>
                <td bgcolor="silver"></td>';

            echo '    
            </tr>';
    }
    echo '    
    </tbody>
</table>    
';

?>
