<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="report";


require('inc/s.php');
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

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}

mysqli_close($conn);


include('functions/base.php');
require('inc/s.php');

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['owner'])){$owner = htmlentities($_GET['owner'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['receiver'])){$receiver = htmlentities($_GET['receiver'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['product'])){$product = htmlentities($_GET['product'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['location'])){$location = htmlentities($_GET['location'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['cargoCode'])){$cargoCode = htmlentities($_GET['cargoCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dFrom'])){$dFrom = htmlentities($_GET['dFrom'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['dTo'])){$dTo = htmlentities($_GET['dTo'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['author'])){$author = htmlentities($_GET['author'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['selStatus'])){$selStatus = htmlentities($_GET['selStatus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['selService'])){$selService = htmlentities($_GET['selService'], ENT_QUOTES, "UTF-8");}

if($author){$author = mysqli_real_escape_string($conn, $author);}
if($selStatus){$selStatus = mysqli_real_escape_string($conn, $selStatus);}
if($selService){$selService = mysqli_real_escape_string($conn, $selService);}

$author = mysqli_real_escape_string($conn, $author);
$selStatus = mysqli_real_escape_string($conn, $selStatus);
$selService = mysqli_real_escape_string($conn, $selService);
 

$client = mysqli_real_escape_string($conn, $client);
$owner = mysqli_real_escape_string($conn, $owner);
$receiver = mysqli_real_escape_string($conn, $receiver);
$product = mysqli_real_escape_string($conn, $product);
$location = mysqli_real_escape_string($conn, $location);
$cargoCode = mysqli_real_escape_string($conn, $cargoCode);
$dFrom = mysqli_real_escape_string($conn, $dFrom);
$dTo = mysqli_real_escape_string($conn, $dTo);

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=print_report(".date('Y-m-d').").xls");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Description: File Transfer');  
header("Content-type: application/vnd.ms-excel");							


$weightFormat = getSettingUOM($conn, 'period_work_done_report_uom');	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,IE=9" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script src="js/jquery-3.2.1.js"></script> 
	<title>NNVT</title>
</head>
<body>
					<?php
					//skats uz ierakstiem
					if (!$view || $view=='print'){
						
						
						if($dFrom){ $fDate = $dFrom; }else{ $fDate = date('Y-m-01 00:00:00'); }

						if($dTo){ $tDate = $dTo; }else{ $tDate = date('Y-m-t 23:59:59'); }						
						
						if($dFrom){ $dateFrom = date('Y-m-d 00:00:00', strtotime($fDate)); }else{ $dateFrom = date('Y-m-01 00:00:00'); }	

						if($dTo){ $dateTo = date('Y-m-d 23:59:59', strtotime($tDate)); }else{ $dateTo = date('Y-m-t 23:59:59'); }



						if($client){ $tClient = " AND h.clientCode='".$client."'"; }else{ $tClient = null; }

						if($owner){ $tOwner = " AND h.ownerCode='".$owner."'"; }else{ $tOwner = null; }
				
						if($receiver){ $tReceiver = " AND h.receiverCode='".$receiver."'"; }else{ $tReceiver = null; }
				
						if($product){ $tProduct = " AND l.productNr='".$product."'"; }else{ $tProduct = null; }
							
						if($location){ $tLocation = " AND l.location='".$location."'"; }else{ $tLocation = null; }
				
						if($cargoCode){ $tCargoCode = " AND h.cargoCode='".$cargoCode."'"; }else{ $tCargoCode = null; }
				
						if($author){ $tAuthor = " AND l.enteredBy='".$author."'"; }else{ $tAuthor = null; }
				
						if($selStatus){ $tStatus = " AND status='".$selStatus."'"; }else{ $tStatus = null; }
				
						if($selService){ $tService = " AND action='".$selService."'"; }else{ $tService = null; }

						echo '<div class="clearfix"></div>';
						echo '<div id="results">';	

						$entrys = mysqli_query($conn, "

						SELECT l.*, h.cargoCode AS cargoCode, h.clientCode AS clientCode, h.clientName AS clientName, h.ownerCode AS ownerCode, h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName 
                            
						FROM cargo_line AS l 
						
						LEFT JOIN cargo_header AS h
						on l.docNr=h.docNr
				
						WHERE l.activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' ".$tClient." ".$tProduct." ".$tLocation." ".$tCargoCode." ".$tOwner." ".$tReceiver." ".$tAuthor."						
						
						") or die(mysqli_error($conn));

							$table = '<table border="1" style="font-size: 9pt;">';
							$table .= '<thead>';
							$table .= '<tr>';
							$table .= '<th bgcolor="#DAE5F0">datums</th>';
							$table .= '<th bgcolor="#DAE5F0">pakalpojums</th>';
							$table .= '<th bgcolor="#DAE5F0">status</th>';
							$table .= '<th bgcolor="#DAE5F0">dokumenta nr.</th>';
							
							$table .= '<th bgcolor="#DAE5F0">prece</th>';
							$table .= '<th bgcolor="#DAE5F0">apjoms</th>';				
							$table .= '<th bgcolor="#DAE5F0">noliktava</th>';
												
							$table .= '<th bgcolor="#DAE5F0">klienta kods - nosaukums</th>';
							$table .= '<th bgcolor="#DAE5F0">īpašnieka kods - nosaukums</th>';
							$table .= '<th bgcolor="#DAE5F0">saņēmēja kods - nosaukums</th>';
												
							$table .= '<th bgcolor="#DAE5F0">darbinieks</th>';
							$table .= '</tr>';
							$table .= '</thead>';
							$table .= '<tbody>';
										
										if(mysqli_num_rows($entrys)>0){
											$abAmount=null;
											while($eRow = mysqli_fetch_array($entrys)){

												$anotherQuery = mysqli_query($conn, "SELECT * FROM item_ledger_entry WHERE activityDate BETWEEN '".$dateFrom."' AND '".$dateTo."' AND docNr='".$eRow['docNr']."' AND (cargoLine='".$eRow['id']."' OR orgLine='".$eRow['id']."') ".$tService." ".$tStatus." ");
											
												while($aqRow = mysqli_fetch_array($anotherQuery)){
												
												if($aqRow['amount'] >= 0){
				
												$table .= '<tr>';
												$table .= '<td>'.$aqRow['activityDate'].'</td>';
				
												$table .= '<td>'.returnAction($aqRow['action']).'</td>';
				
												$table .= '<td nowrap>'.returnStatus($aqRow['status']).'</td>';

													$table .= '<td>'.$aqRow['cargoCode'].'</td>';
													$table .= '<td>'.$aqRow['productNr'].' - '.returnProductName($conn, $aqRow['productNr']).'</td>';
													$table .= '<td>'.$aqRow['amount'].' '.$aqRow['productUmo'].'</td>';
													$table .= '<td>'.$aqRow['location'].' - '.returnLocationName($conn, $aqRow['location']).'</td>';
													$table .= '<td>'.$aqRow['clientCode'].' - '.$aqRow['clientName'].'</td>';
													$table .= '<td>'.$aqRow['ownerCode'].' - '.$aqRow['ownerName'].'</td>';
													$table .= '<td>'.$aqRow['receiverCode'].' - '.$aqRow['receiverName'].'</td>';
													$table .= '<td>'.returnMeWho($aqRow['enteredBy']).'</td>';
				
													$table .= '</tr>';
												$abAmount += getTotValueUOM($conn, $aqRow['productNr'], $aqRow['amount'], $aqRow['productUmo'], $weightFormat);
												}

											}
									
										}
										}else{
											$table .= '<td colspan="9"> nav neviena ieraksta!</td>';
										}

											
														
										$table .= '</tbody>';
										$table .= '</table>';		
					}

					echo '<p class="total-title" style="display:inline-block;">perioda analītiskā atskaite: ('.$dateFrom.' - '.$dateTo.')
					<br>atskaiti izveidoja: '.returnMeWho($myid).' ('.date('Y-m-d H:i:s').') &nbsp;kopā:  '; if($abAmount>0){echo $abAmount;}else{echo '0';} echo '&nbsp;'.$weightFormat.'</p>';				

					echo $table;
					?>

</body>

</html>					