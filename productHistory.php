<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="product_catalog";


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

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('functions/base.php');

$user_group_array = array("w_user" => "NNVT", "w_partner" => "");


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}
?>







<?php
require('inc/s.php');
echo '<div class="form-group col-md-16" style="height: 10px; background-color: red;"></div>';
$productNr = rawurldecode($_GET['id']);
$docNr = rawurldecode($_GET['docNr']);
$batchNo = rawurldecode($_GET['batchNo']);

echo 'prece: <b>'.$productNr.'</b> kartiņa: <b>'.$docNr.'</b> partijas nr.: <b>'.$batchNo.'</b>';
$selectData = mysqli_query($conn, "

SELECT * FROM item_ledger_entry AS i
JOIN cargo_line AS c
ON i.productNr=c.productNr
WHERE i.productNr='".mysqli_real_escape_string($conn, $productNr)."' 
AND c.batchNo='".mysqli_real_escape_string($conn, $batchNo)."'

") or die(mysqli_error($conn));

$total = null;
$totalz = null;	
$table=null;	
while($row = mysqli_fetch_array($selectData)){	

if($docNr==$row['docNr']){
			$total+=$row['amount'];
}else{
	$totalz+=$row['amount'];
}		
	
			$table .= '<tr>';
			$table .= '	<td>'.$row['docNr'];
			$table .= '	<td>'.date('Y-m-d', strtotime($row['activityDate']));
			$table .= '	<td>'.$row['location'];				
			$table .= '	<td>'.$row['amount'];
			$table .= '</tr>';
}


echo '
<div class="table-responsive">
	<table class="table table-hover table-responsive">
		<thead>
			<tr>
				<th>documenta numurs</th>
				<th>datums</th>
				<th>noliktava</th>
				<th>daudzums</th>
			</tr>
		</thead>
		<tbody>';
		echo $docNr.' atlikums: '.$total.' <br> atlikums: '.$totalz;

echo $table;

		echo '	
		</tbody>			
</div>
';

?>