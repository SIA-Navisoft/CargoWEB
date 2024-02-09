<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="receipt";


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

include('../../functions/base.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;} 

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}


if(!$view){

?>
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#contenthere').load('/pages/receipt/'+url);
			event.preventDefault();
		});
	});
	</script>
<?php	
	
	require('../../inc/s.php');
	
	$client = mysqli_real_escape_string($conn, $_POST['client']);;
	
	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " AND (
		docNr LIKE '%$s%' || 
		ladingNr LIKE '%$s%' || 
		clientCode LIKE '%$s%' ||
		clientName LIKE '%$s%' || 
		ownerCode LIKE '%$s%' || 
		ownerName LIKE '%$s%' ||
		receiverCode LIKE '%$s%' || 
		receiverName LIKE '%$s%' || 
		cargoCode LIKE '%$s%' ||
		deliveryDate LIKE '%$s%' || 
		deliveryType LIKE '%$s%' || 
		deliveryCode LIKE '%$s%' ||
		location LIKE '%$s%' || 
		createdSource LIKE '%$s%' ||
		thisTransport LIKE '%$s%' ||
		acceptance_act_no LIKE '%$s%'
		) ";
		$searchUrl = '&search='.$s;
		
	}else{
		$searchUrl = null;	
		$search = "";
	}
	
	if($client){$filterClient = ' AND clientCode="'.$client.'"';}else{$filterClient = null;}
	
	$keepFilter=null;
	if($client){$keepFilter.='client='.$client.'&';}


	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view='.$keepFilter.$searchUrl.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(SELECT SUM(amount) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as vienibas,
	(SELECT SUM(gross) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as bruto,
	(SELECT thisTransport FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr LIMIT 1) AS lineThisTransport
	 FROM cargo_header WHERE (status='0' || status='10') ".$filterClient." ".$search." ORDER BY CASE WHEN scanStatus=200 THEN scanStatus END DESC, deliveryDate ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						
						<th>pavadzīmes nr.</th>
						<th>dokumenta nr. (iekš.)</th>
						<th>piegādes dat.</th>
						
						
						<th>transporta nr.</th>
						<th>pieņemšanas akta nr.</th>
						<th>vienības</th>
						<th>bruto (kg)</th>
						
						<th>nosaukums</th>				
						<th>statuss</th>
						<th>importa datums</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}

				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}

				echo '	<tr class="classlistedit" '.$trAction.'>
							<td '.$show_green.'>'.$row['ladingNr'].'
							<td>'.$row['docNr'].'
				
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							

							echo '<td>'; 							
							if($row['thisTransport']){
								echo $row['thisTransport'];
							}else{
								echo $row['lineThisTransport']; // p'ec piepras'ijuma r'ad'it pirmo transportu
							}

							echo '<td>'.$row['acceptance_act_no'].'
							<td>'.floatval($row['vienibas']).'
							<td>'.floatval($row['bruto']).'
							
							<td>'.$row['clientName'];				
							echo '<td>'.returnCargoStatus($conn, $row['id']).'

							<td>';

							if($row['importDate']!='0000-00-00 00:00:00'){
								echo $row['importDate'];
							}

							echo '							
						</tr>';
			}
			
			echo '</tbody></table></div>';
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}	
			
}


if($view=='history'){

?>
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#contenthere').load('/pages/receipt/'+url+'&client=<?=$client;?>');
			event.preventDefault();
		});
	});
	</script>
<?php

	require('../../inc/s.php');

	$client = mysqli_real_escape_string($conn, $_POST['client']);

	
	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " (
		docNr LIKE '%$s%' || 
		ladingNr LIKE '%$s%' || 
		clientCode LIKE '%$s%' ||
		clientName LIKE '%$s%' || 
		ownerCode LIKE '%$s%' || 
		ownerName LIKE '%$s%' ||
		receiverCode LIKE '%$s%' || 
		receiverName LIKE '%$s%' || 
		cargoCode LIKE '%$s%' ||
		deliveryDate LIKE '%$s%' || 
		deliveryType LIKE '%$s%' || 
		deliveryCode LIKE '%$s%' ||
		location LIKE '%$s%' || 
		createdSource LIKE '%$s%' ||
		thisTransport LIKE '%$s%' ||
		acceptance_act_no LIKE '%$s%'
		) ";
		$searchUrl = '&search='.$s;
		
	}else{
		$searchUrl = null;	
		$search = "";	
	}

	$filterClient = null;		
	if($client && $search){$filterClient = ' WHERE clientCode="'.$client.'" AND '.$search;}
	if(!$client && $search){$filterClient = ' WHERE '.$search;}
	if($client && !$search){$filterClient = ' WHERE clientCode="'.$client.'"';}
	
	

	$keepFilter=null;
	if($client){$keepFilter.='&client='.$client;}
	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view=history'.$keepFilter.$searchUrl.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM cargo_header_receive ".$filterClient." ORDER BY CASE WHEN scanStatus=200 THEN scanStatus END DESC, deliveryDate ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 	
	
	if ($count_GL7!=0){

		echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
					<th>pavadzīmes nr.</th>
					<th>dokumenta nr. (iekš.)</th>
					<th>piegādes dat.</th>

					<th>pieņemšanas akta nr.</th>
					<th>klienta kods - nosaukums</th>				
					<th>īpašnieka kods - nosaukums</th>
					<th>statuss</th>
					<th>importa datums</th>
				</tr></thead><tbody>';
		while($row = mysqli_fetch_array($resultGL7)){

				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}

				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}

				echo '	<tr class="classlistedit" '.$trAction.'>
						<td '.$show_green.'>'.$row['ladingNr'].'
						<td>'.$row['docNr'].'
				
						<td>';
						if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
						echo '
						<td>'.$row['acceptance_act_no'].'
						<td>'.$row['clientCode'].' - '.$row['clientName'].'				
						<td>'.$row['ownerCode'].' - '.$row['ownerName'].'
						<td>'.returnCargoStatus($conn, $row['id']).'

						<td>';

						if($row['importDate']!='0000-00-00 00:00:00'){
							echo $row['importDate'];
						}

						echo '							

					</tr>';
		}
		
		echo '</tbody></table></div>';
	}else{
		echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
	}

}	
		
?>