<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="agreements";


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
if (isset($_GET['line'])){$line = htmlentities($_GET['line'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');

if($r=='add' && $id!=''){

	$dateFrom = strtr($_POST['dateFrom'], '.', '-');
	$dateFrom = date('Y-m-d', strtotime($dateFrom));

	$dateTo = strtr($_POST['dateTo'], '.', '-');
	$dateTo = date('Y-m-d', strtotime($dateTo));	

	$form_data = array(
		'contractNr' => mysqli_real_escape_string($conn, $id),
		'service' => mysqli_real_escape_string($conn, $_POST['resource']),
		'service_name' => mysqli_real_escape_string($conn, $_POST['serviceName']),
		'item' => mysqli_real_escape_string($conn, $_POST['product']),
		'uom' => mysqli_real_escape_string($conn, $_POST['uom']),
		'tariffs' => mysqli_real_escape_string($conn, $_POST['tariffs']),
		'keeping' => mysqli_real_escape_string($conn, $_POST['keeping']),
		'extra_resource' => mysqli_real_escape_string($conn, $_POST['extraResource']),
		'dateFrom' => mysqli_real_escape_string($conn, $dateFrom),
		'dateTo' => mysqli_real_escape_string($conn, $dateTo),
		'createdBy' => mysqli_real_escape_string($conn, $myid),
		
		'productMatch' => mysqli_real_escape_string($conn, $_POST['addMatch']),
		'productLength' => mysqli_real_escape_string($conn, $_POST['addLength'])		
		);
		insertNewRows("agreements_lines", $form_data);
}

if($r=='edit' && $id!='' && $line!=''){

	$dateFrom = strtr($_POST['dateFrom'], '.', '-');
	$dateFrom = date('Y-m-d', strtotime($dateFrom));

	$dateTo = strtr($_POST['dateTo'], '.', '-');
	$dateTo = date('Y-m-d', strtotime($dateTo));

	$form_data = array(
		'service' => mysqli_real_escape_string($conn, $_POST['resource']),
		'service_name' => mysqli_real_escape_string($conn, $_POST['serviceName']),
		'item' => mysqli_real_escape_string($conn, $_POST['product']),
		'uom' => mysqli_real_escape_string($conn, $_POST['uom']),
		'tariffs' => mysqli_real_escape_string($conn, $_POST['tariffs']),
		'keeping' => mysqli_real_escape_string($conn, $_POST['keeping']),
		'extra_resource' => mysqli_real_escape_string($conn, $_POST['extraResource']),
		'dateFrom' => mysqli_real_escape_string($conn, $dateFrom),
		'dateTo' => mysqli_real_escape_string($conn, $dateTo),
		'editedBy' => mysqli_real_escape_string($conn, $myid),
		'editedDate' => date('Y-m-d'),
		
		'productMatch' => mysqli_real_escape_string($conn, $_POST['addMatch']),
		'productLength' => mysqli_real_escape_string($conn, $_POST['addLength'])		
		);
	updateSomeRow("agreements_lines", $form_data, "WHERE id='".intval($line)."' AND contractNr='".mysqli_real_escape_string($conn, $id)."' LIMIT 1");	

}


if($r=='addAgreement'){


	$id=findLastRow("agreements")+1;
		  
	$contractNr = 'LIG'.sprintf("%'05d\n", $id);
	$contractNr = trim($contractNr);
 
	$getInfoAgr = mysqli_query($conn, "SELECT Name FROM n_customers WHERE Code='".mysqli_real_escape_string($conn, $_POST['customer'])."'");
	$giaRow = mysqli_fetch_array($getInfoAgr) or die(mysqli_error($conn));	

	$dateFrom = strtr($_POST['dateFrom'], '.', '-');
	$dateFrom = date('Y-m-d', strtotime($dateFrom));

	$dateTo = strtr($_POST['dateTo'], '.', '-');
	$dateTo = date('Y-m-d', strtotime($dateTo));

	if($_POST['forRelease']=='on'){$prepareAll = 1;}else{$prepareAll = 0;}
	if($_POST['useScan']=='on'){$useScan = 1;}else{$useScan = 0;}

	$form_data = array(
	'contractNr' => $contractNr,
	'customerNr' => mysqli_real_escape_string($conn, $_POST['customer']),
	'customerName' => $giaRow['Name'],
	'outerNr' => mysqli_real_escape_string($conn, $_POST['outerNr']),
	'freeDays' => mysqli_real_escape_string($conn, $_POST['freeDays']),
	'dateFrom' => mysqli_real_escape_string($conn, $dateFrom),
	'dateTo' => mysqli_real_escape_string($conn, $dateTo),
	'createdBy' => $myid,	
	'createdDate' => date('Y-m-d H:i:s'),
	'prepareAll' => $prepareAll,
	'useScan' => $useScan,
	'actForm' => intval($_POST['actForm'])
	);
	insertNewRows("agreements", $form_data);
	
	echo $contractNr;


}

if($r=='editAgreement'){

	$contractNr = mysqli_real_escape_string($conn, $_POST['contractNr']);
  
	$getInfoAgr = mysqli_query($conn, "SELECT Name FROM n_customers WHERE Code='".mysqli_real_escape_string($conn, $_POST['customer'])."'");
	$giaRow = mysqli_fetch_array($getInfoAgr) or die(mysqli_error($conn));	

	$dateFrom = strtr($_POST['dateFrom'], '.', '-');
	$dateFrom = date('Y-m-d', strtotime($dateFrom));

	$dateTo = strtr($_POST['dateTo'], '.', '-');
	$dateTo = date('Y-m-d', strtotime($dateTo));

	if($_POST['forRelease']=='on'){$prepareAll = 1;}else{$prepareAll = 0;}
	if($_POST['useScan']=='on'){$useScan = 1;}else{$useScan = 0;}

	$form_data = array(
	'customerNr' => mysqli_real_escape_string($conn, $_POST['customer']),
	'customerName' => $giaRow['Name'],
	'outerNr' => mysqli_real_escape_string($conn, $_POST['outerNr']),
	'freeDays' => mysqli_real_escape_string($conn, $_POST['freeDays']),
	'dateFrom' => mysqli_real_escape_string($conn, $dateFrom),
	'dateTo' => mysqli_real_escape_string($conn, $dateTo),
	'editedBy' => $myid,	
	'editedDate' => date('Y-m-d H:i:s'),
	'prepareAll' => $prepareAll,
	'useScan' => $useScan,
	'actForm' => intval($_POST['actForm'])
	);
	
	updateSomeRow("agreements", $form_data, "WHERE contractNr='".$contractNr."' LIMIT 1");	

}

	
mysqli_close($conn);
?>

