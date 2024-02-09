<!DOCTYPE html>
<html>
<body style="border: 0.1pt solid #ccc"> 
<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="release";

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];



require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

ob_start();

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

$page_header=$row['page_header'];

include('functions/base.php');

$view = $action = $id = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=incidents(".date('Y-m-d').").xls");

$query = mysqli_query($conn, "SELECT * FROM issuance_doc WHERE issuance_id='".$id."'");
$row = mysqli_fetch_array($query);

$destination = $row['destination'];

$query2 = mysqli_query($conn, "SELECT SUM(place_count) AS place_count, SUM(cubicMeters) AS cubicMeters FROM cargo_line WHERE productNr LIKE '".$destination." %' AND status>=20");
$row2 = mysqli_fetch_array($query2);

echo '<table>
		<tr>
			<td>Ievests :</td>
			<td align="right" style="vnd.ms-excel.numberformat:0">'.$row2['place_count'].'</td>
			<td>PAKAS</td>
			<td colspan="2"></td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00">'.floatval($row2['cubicMeters']).'</td>
			<td>m3</td>
		</tr>';

echo '</table>';
	 

echo '<br><br>';
	 
$query3 = mysqli_query($conn, "SELECT SUM(place_count) AS place_count, SUM(cubicMeters) AS cubicMeters FROM cargo_line WHERE productNr LIKE '".$destination." %' AND issuance_id='".$id."'");
$row3 = mysqli_fetch_array($query3);	  
	  
echo '<table>';

		echo '

		<tr>
			<td></td>
			<td><b>'.$row['transport_name'].'</b></td>
		</tr>

      </table>';

echo '<br><br>';	  
	  
	  echo '<table>';
		
		$query3 = mysqli_query($conn, "
			SELECT 
				SUM(if(placement LIKE 'R%', place_count, '')) AS place_count_r, 
				SUM(if(placement LIKE 'R%', cubicMeters, '')) AS cubicMeters_r, 
				SUM(if(placement LIKE 'K%', place_count, '')) AS place_count_k, 
				SUM(if(placement LIKE 'K%', cubicMeters, '')) AS cubicMeters_k 
				
			
			FROM cargo_line 
			WHERE productNr LIKE '".$destination." %' AND issuance_id='".$id."'
		");
		
		$row3 = mysqli_fetch_array($query3);		
		
		$place_count_r = $row3['place_count_r'];
		$cubicMeters_r = $row3['cubicMeters_r'];
		$place_count_k = $row3['place_count_k'];
		$cubicMeters_k = $row3['cubicMeters_k'];
		
		$place_count_t = $place_count_r+$place_count_k;
		$cubicMeters_t = $cubicMeters_r+$cubicMeters_k;
		
		echo '
		<tr>
			<td><b>RŪME:</b></td>
			<td></td>
			<td align="right" style="vnd.ms-excel.numberformat:0"><b>'.$place_count_r.'</b></td>
			<td><b>pakas =</b></td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00"><b>'.floatval($cubicMeters_r).'</b></td>
			<td><b>m3</b></td>
		</tr>
		<tr>
			<td><b>KLĀJS:</b></td>
			<td></td>
			<td align="right" style="vnd.ms-excel.numberformat:0"><b>'.$place_count_k.'</b></td>
			<td><b>pakas =</b></td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00"><b>'.floatval($cubicMeters_k).'</b></td>
			<td><b>m3</b></td>
		</tr>
		<tr>
			<td></td>
			<td><b>Kopā:</b></td>
			<td align="right" style="vnd.ms-excel.numberformat:0"><b>'.$place_count_t.'</b></td>
			<td><b>pakas =</b></td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00"><b>'.floatval($cubicMeters_t).'</b></td>
			<td><b>m3</b></td>
		</tr>
';
		
		
	  echo '</table>';	  

echo '<br><br>';
	
$table3 = null;
	
$query4 = mysqli_query($conn, "SELECT serialNo, place_count, cubicMeters, productNr FROM cargo_line WHERE productNr LIKE '".$destination." %' AND status=20 AND issuance_id=''");
$place_count_total=$cubicMetersTotal=0;
while($row4 = mysqli_fetch_array($query4)){
	$table3 .= '<tr>
			<td></td>
			<td>'.$row4['serialNo'].'</td>
			<td align="right" style="vnd.ms-excel.numberformat:0" colspan="2">'.$row4['place_count'].'</td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00" colspan="2">'.floatval($row4['cubicMeters']).'</td>
			<td>'.$row4['productNr'].'</td>
		
		  </tr>';
		  
		  $place_count_total += $row4['place_count'];
		  $cubicMetersTotal += floatval($row4['cubicMeters']);
}	


if($table3){
	
	echo '<table>';
	
	echo '<tr>
			<td><b>ATLIKUMS OSTĀ:</b></td>
			<td></td>
			<td align="right" style="vnd.ms-excel.numberformat:0">'.$place_count_total.'</td>
			<td>pakas</td>
			<td align="right" style="vnd.ms-excel.numberformat:0.00">'.$cubicMetersTotal.'</td>
			<td>m3</td>	
			<td></td>
		  </tr>';
	
	echo $table3;
	
	echo '<tr>
			<td></td>
			<td></td>
			<td align="right" style="vnd.ms-excel.numberformat:0" colspan="2">'.$place_count_total.'</td>			
			<td align="right" style="vnd.ms-excel.numberformat:0.00" colspan="2">'.$cubicMetersTotal.'</td>
			<td></td>	
		  </tr>';	
	
	echo '</table>';
	
}
?>
</body>
</html>