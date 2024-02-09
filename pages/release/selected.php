<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";



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
if (isset($_GET['eid'])){$eid = htmlentities($_GET['eid'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['do'])){$do = htmlentities($_GET['do'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['cargo'])){$cargo = htmlentities($_GET['cargo'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['agrNr'])){$agrNr = htmlentities($_GET['agrNr'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['num'])){$num = htmlentities($_GET['num'], ENT_QUOTES, "UTF-8");}



require('../../inc/s.php');

if($eid){
	$getd = mysqli_query($conn, "SELECT issuance_id, agreements, clientCode, thisTransport, declaration_type_no, acceptance_act_no FROM issuance_doc WHERE id='".intval($eid)."'");
	$getdRow = mysqli_fetch_array($getd);
		
	$agreements = $getdRow['agreements'];
	$clientCode = $getdRow['clientCode']; 
	$issuance = $getdRow['issuance_id'];
	$thisTransport = $getdRow['thisTransport'];
	$declaration_type_no = $getdRow['declaration_type_no'];	
	$acceptance_act_no = $getdRow['acceptance_act_no'];
	
	if(!$num && $acceptance_act_no!=''){
		$num = $acceptance_act_no;
	}
	
		
}

if(isset($eid)){
	$isetEid = 'y';
}else{
	$isetEid = 'n';
}




$disabled='disabled';


$view='edit'; 

if($view=='edit'){

?>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("/pages/release/search.php?view=cargoList&agr=<?=$agreements;?>&cli=<?=$clientCode;?>&eid=<?=$eid;?>&docNr=<?=$row['docNr'];?>&num=<?=$num;?>&issuance=<?=$issuance;?>", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>	  
<?php

			echo '
			<div class="clearfix"></div>
			';			  
	  

			function checkAllStatus($conn, $agr, $cli, $status, $issuance_id){
		
				$Query = mysqli_query($conn, "
					SELECT cargo_line.id FROM cargo_line 
		
					LEFT JOIN cargo_header 
					ON cargo_line.docNr=cargo_header.docNr 
		
					WHERE cargo_line.status='".$status."' 
					AND cargo_line.issuance_id='".$issuance_id."'
					AND cargo_header.clientCode='".$cli."'
					AND cargo_header.agreements='".$agr."'			
				");
				
				if(mysqli_num_rows($Query)>0){
					return 1;
				}else{
					return 0;
				}
				
			}
		
			function checkAllStatusLines($conn, $agr, $cli, $issuance_id){
				
				$Query = mysqli_query($conn, "
					SELECT cargo_line.id FROM cargo_line 

					LEFT JOIN cargo_header 
					ON cargo_line.docNr=cargo_header.docNr 

					WHERE cargo_header.clientCode='".$cli."' 
					AND cargo_header.agreements='".$agr."' 
					AND cargo_line.issuance_id='".$issuance_id."' AND cargo_line.for_issue='1' AND cargo_line.status='30'
				");
				
				if(mysqli_num_rows($Query)>0){
					return 1;
				}else{
					return 0;
				}
				
			}
		

	echo '
<br>	
<div class="panel panel-default">
	<div class="panel-body">
		<div class="table-responsive">';
		
if($dis!='disabled' && $isetEid=='y'){
	
?>
	<script>
	$(document).ready(function () {
		$('#release_lines').on('submit', function(e) {			
			e.preventDefault();
		
			var searchWait = $('#searchWait').val();
			var searchWait = encodeURI(searchWait);
			
				var action = document.activeElement.getAttribute('id');		
			
			$.ajax({
				url : '/pages/release/post.php?r=release_lines&action='+action,
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('.actionLine').html("gaidiet...");
					$(".actionLine").prop("disabled",true);

				},			
				success: function (data) {
					console.log(data);

					var testStr = data;

					if(testStr.indexOf("issuanceClosed")){
						$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$eid;?>&sr='+searchWait+'&res=approved');
					}else{
						$('#selected').load('/pages/release/selected.php?view=edit&id=<?=$id;?>&eid=<?=$eid;?>&sr='+searchWait+'&res=approved');
					}
									
					
					
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>

<form id="release_lines">	
<input type="hidden" name="header_thisTransport" value="<?=$thisTransport;?>">
<input type="hidden" name="header_declaration_type_no" value="<?=$declaration_type_no;?>">	
<?php	

}


		
// KOPSUMMAS DAUDZUMI

echo '<div class="clearfix"></div>';		

	echo '<div id="totSums">';
	if($sr){
		$s = mysqli_real_escape_string($conn, $sr);	
		$search = " AND (cargo_line.productNr LIKE '%$s%' || cargo_line.thisTransport LIKE '%$s%' || cargo_line.thisDate LIKE '%$s%' || cargo_line.batchNo LIKE '%$s%')";	
	}else{
		$search = "";	
	} 

	if($dis=='disabled'){ 
		$showHi = "cargo_line.status='40' AND cargo_line.issuance_id='".$issuance."'"; 
	}else{ 
		$showHi = "(cargo_line.status='20' OR cargo_line.status='30') AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$issuance."')"; 
	}	
	
	$numz = null;
	if($num){ $numz = " AND cargo_header.acceptance_act_no='".$num."'"; }

	if($issuance){
		$getd = mysqli_query($conn, "SELECT destination FROM issuance_doc WHERE issuance_id='".$issuance."'");
		$getdRow = mysqli_fetch_array($getd);

		$destination = $getdRow['destination'];	
	}

	$destz = null;
	
	if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }


	$inCargo = mysqli_query($conn, "
					SELECT COUNT(cargo_line.id) AS count, SUM(cargo_line.amount) as amount, SUM(cargo_line.issueAmount) as issueAmount, 
					SUM(cargo_line.volume) as volume, 
					SUM(cargo_line.tare) as t, SUM(cargo_line.issueTare) as it, 
					SUM(cargo_line.gross) as b, SUM(cargo_line.issueGross) as ib, 
					SUM(cargo_line.net) as n, SUM(cargo_line.issueNet) as isn,
					SUM(cargo_line.cubicMeters) as a, SUM(cargo_line.issueCubicMeters) as ia, 
					SUM(cargo_line.delta_net) as dn,
					SUM(cargo_line.document_net) as don,					
					cargo_line.productUmo, cargo_line.productNr, SUM(cargo_line.place_count) AS pc, SUM(cargo_line.issue_place_count) AS ipc,
					
					SUM(cargo_line.assistant_amount) as assistant_amount, SUM(cargo_line.issue_assistant_amount) as issue_assistant_amount, cargo_line.assistantUmo, cargo_line.status, cargo_line.resource, cargo_line.extra_resource, cargo_line.for_issue
					
					FROM cargo_line 

					LEFT JOIN cargo_header 
					ON cargo_line.docNr=cargo_header.docNr 					

					WHERE ".$showHi." ".$search." AND cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27'
					
					AND cargo_line.for_issue=1

					GROUP BY cargo_line.productUmo
		
			") or die (mysqli_error($conn));

	if(mysqli_num_rows($inCargo)>0){
	
	echo '<p style="display:inline-block;">daudzumi</p>';
	
		echo '
		<div class="table-responsive">
			<table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
				<thead>
					
					<th>rindas izdošanai</th>
					
					<th>mērvienība</th>
					<th>daudzums</th>
					<th>daudzums izdošanai</th>
					<th>palīg mērvienība</th>
					<th>atlikušais palīg mērv. daudzums</th>
					<th>palīg mērv. daudzums izdošanai</th>
					<th>saņ. dok. neto (kg)</th>
					<th>vietu skaits</th>
					<th nowrap>tara (kg)</th>
					<th nowrap>bruto (kg)</th>
					<th nowrap>neto (kg)</th>
					<th nowrap>saņ. Δ neto (kg)</th>
					<th nowrap>apjoms (m3)</th>
				</thead>
				<tbody>';	
				
					while($icRow = mysqli_fetch_array($inCargo)){				
						
						echo '				
						<tr>
							
							<td>'.$icRow['count'].'</td>';
							
							echo '	
							<td>'.$icRow['productUmo'].'</td>
							<td>'.floatval($icRow['amount']).'</td>
							<td>';
							if(floatval($icRow['issueAmount']>0)){
								echo floatval($icRow['issueAmount']);
							}else{
								echo floatval($icRow['amount']);
							}
							
							echo '</td>


							<td>'.$icRow['assistantUmo'].'</td>
							<td>'.floatval($icRow['assistant_amount']).'</td>
							<td>';
							if(floatval($icRow['issue_assistant_amount']>0)){
								echo floatval($icRow['issue_assistant_amount']);
							}else{
								echo floatval($icRow['assistant_amount']);
							}
							
							echo '</td>
							<td>'.floatval($icRow['don']).'</td>';
							
							echo '<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['pc']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['pc']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ipc']);
							}

							echo '</td>';							
							
							echo '<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['t']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['t']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['it']);
							}

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['b']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['b']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ib']);
							}						

							echo '</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['n']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['n']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['isn']);
							}

							echo '</td>
							<td>'.floatval($icRow['dn']).'</td>
							<td>';
							
							if($icRow['status']==20 && $icRow['for_issue']==0){
								echo floatval($icRow['a']);
							}
							
							if($icRow['status']==40 && $icRow['for_issue']==0){
								echo floatval($icRow['a']);
							}
							
							if($icRow['status']==30 || $icRow['for_issue']==1){
								echo floatval($icRow['ia']);
							}						
							
							echo '</td>
							
						</tr>';
					
					}
				echo '
				</tbody>
			</table>
		</div>				
		';
		
	}
	echo '</div>';

//^^ KOPSUMMAS DAUDZUMI	


		
echo '<div id="results">';	

?>
<script>
$(function() {
	$(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		
		url = url.replace('release', 'selected');
			
		var clientCode = $('#clientCode').val();
		var agreements = $('#agreements').val();
		
		var view = '<?=$view;?>'; 
		if(view){
			var view = '&view='+view+'&clientCode='+clientCode+'&agreements='+agreements;
		}else{
			var view = '&clientCode='+clientCode+'&agreements='+agreements;
		}

		if(view){
			
			var sr = '<?=$sr;?>';
			if(sr){
				var search = '<?=$sr;?>';
			}else{
				var search = $('#searchWait').val();
			}
			
			
			$('#selected').load('/pages/release/'+url+''+view+'&id=<?=$id;?>&showToGive=<?=$showToGive;?>&sr='+search);
		}else{
			$('#selected').load('/pages/release/'+url+''+view);
		}
		event.preventDefault();
	});
});
</script>
<?

		echo '
		<div class="table-responsive"><table class="table table-hover table-responsive" border="1" style="border: 1px solid #ddd !important;">
		<thead> 
			<tr>

			<th>darbība  '; echo'</th>
			
			<th>iekļauts dokumentā</th>
			<th>brāķis<br><br></th>
			<th>piegādes<br>dat.</th>
			<th>produkta nr</th>
			
			<th>seriālais nr.</th>
			<th>atlikušais daudzums</th>
			<th>mērvienība</th>
			
			<th>daudzums<br>izdošanai</th>

			<th>atlikušais palīg mērv. daudzums</th>
			<th>palīg mērvienība</th>
			
			<th>palīg mērv. daudzums<br>izdošanai</th>			
			<th>saņ. dok. neto (kg)</th>
			<th>vietu skaits</th>
			<th>tara (kg)</th>
			<th>bruto (kg)</th>
			<th>neto (kg)</th>
			<th>saņ. Δ neto (kg)</th>
			<th>m3 (kg)</th>

			
			<th>partijas nr.</th>
			<th>noliktava</th>

			<th>markas nr.</th>
			<th>konteinera tips</th>


			<th>saņ. transporta nr.</th>
			<th>izs. transporta nr.</th>

			<th>deklarācijas nr.</th>
			<th>kravas status</th>
			<th>plombes numurs</th>



			<th>svēršanas akta nr.</th>

				
			</tr>
		</thead>
		<tbody>';


			if($sr){
			$s = mysqli_real_escape_string($conn, $sr);	
			$search = " AND (productNr LIKE '%$s%' || thisTransport LIKE '%$s%' || thisDate LIKE '%$s%' || batchNo LIKE '%$s%')";	
			}else{
			$search = "";	
			}


			$agr = $cli = $numz = $showAvaible = null;

			if($clientCode){ $cli = " AND cargo_header.clientCode='".$clientCode."'"; }
			
			if($agreements){ $agr = " AND cargo_header.agreements='".$agreements."'"; }

			if($num){ $numz = " AND cargo_header.acceptance_act_no='".$num."'"; }

			if(!$issuance){$showAvaible = "AND cargo_line.for_issue!=1";}
			
			if($dis=='disabled'){ $showHi = " AND cargo_line.status='40' AND cargo_line.issuance_id='".$issuance."'"; }else{ $showHi = " AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$issuance."')"; }
			
			
			
			
		$rec_limit = $i_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
		$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 			
			
		$lines = "
				SELECT cargo_line.*, cargo_header.deliveryDate AS hDeliveryDate
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE   
				cargo_line.action!='23' AND cargo_line.action!='27' ".$showHi." ".$search." ".$cli." ".$agr." ".$numz." ".$showAvaible."
				
					AND (
							(
							
								cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
								FROM cargo_line 
								LEFT JOIN cargo_header 
                                ON cargo_line.docNr = cargo_header.docNr 
								WHERE cargo_line.action!='23' AND cargo_line.action!='27' ".$showHi." ".$search." ".$cli." ".$agr." ".$numz." ".$showAvaible."
								AND cargo_line.status = '30') 
								
							) OR (
							
								cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docNr = cargo_header.docNr 
								WHERE cargo_line.action!='23' AND cargo_line.action!='27' ".$showHi." ".$search." ".$cli." ".$agr." ".$numz." ".$showAvaible."
                                AND cargo_line.status = '30') 
							) 
									   
                             )
										 
				ORDER BY cargo_header.deliveryDate
			";



			list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $lines);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

			echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	


		$r=0; $k=0;
		while($rowL = mysqli_fetch_array($resultGL7)){
			
			
		if($rowL['status']<20){
					
		}else{	
			echo '<input type="hidden" name="docNr[]" value="'.$rowL['docNr'].'">';

			if($rowL['status']==20 && $dis!='disabled' && $isetEid=='y'){		
				$r++;
				echo '<input type="hidden" name="lineId[]" value="'.$rowL['id'].'">';
			}
			if($rowL['status']==30 && $dis!='disabled' && $isetEid=='y'){		
				$k++;
				echo '<input type="hidden" name="lineIdFinal[]" value="'.$rowL['id'].'">';
			}			
		
			echo '	<tr class="classlistedit">';


			echo '<td nowrap>';
			
				if(($rowL['status']==20)){

					if($issuance){
						if($rowL['for_issue']==1){
							echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="cancelOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atvērt nodošanu</div>';
						}else{
							echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="approveOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</div>';
						}
					}else{
						echo '-';
					}						
				}

				if(($rowL['status']==30)){
					echo returnCargoLineStatus($conn, $rowL['id']);								
				}
			
			
				if(($rowL['status']==40)){
					
					echo returnCargoLineStatus($conn, $rowL['id']);
					
				}							
			
			echo '<td>';
			if($rowL['for_issue']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
			
			echo '<td>';
			if($rowL['brack']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
			
			echo '<td nowrap>'.date('d.m.Y', strtotime($rowL['hDeliveryDate']));

					echo '
						<td nowrap>
						'.$rowL['productNr'].' - ';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];

						echo '<td>'.$rowL['serialNo'];						

echo '
<td>'.floatval($rowL['amount']).'
<td>'.$rowL['productUmo'];

if(($rowL['status']==20 && $rowL['for_issue']==0)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly lineAmount'.$rowL['id'].'" style="min-width: 70px;" id="issueAmount'.$rowL['id'].'" name="issueAmount[]" placeholder="daudzums" value="'.floatval($rowL['amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" ';
	  
	  if($isetEid=='y'){echo ' required ';}else{echo ' disabled ';}
	  
	  echo '>
	</div>						
	';
		
}

if(($rowL['status']==30 || $rowL['for_issue']==1)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control lineAmount'.$rowL['id'].'" style="min-width: 70px;" name="issueAmountFinal[]" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" readonly>
	</div>						
	';							
}



// palīg mērvienība
?>
<script>
  
$('#issueAmount<?=$rowL['id'];?>').on("keyup", calculate);

function calculate() {
var sum = 0;
var valz =  $('#issueAmount<?=$rowL['id'];?>').val();
var amount =  '<?=floatval($rowL['amount']);?>';
var extra = '<?=floatval($rowL['assistant_amount']);?>';
sum = (extra/amount)*valz;

$("#issueAssistantAmount<?=$rowL['id'];?>").val(sum);
}
</script>
<?php						
echo '
<td>'.floatval($rowL['assistant_amount']).'
<td>'.$rowL['assistantUmo'];

if(($rowL['status']==20 && $rowL['for_issue']==0)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly lineAmount'.$rowL['id'].'" style="min-width: 70px;" id="issueAssistantAmount'.$rowL['id'].'" name="issueAssistantAmount[]" placeholder="daudzums" value="'.floatval($rowL['assistant_amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['assistant_amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" ';
	  
	  if($isetEid=='y'){echo ' required ';}else{echo ' disabled ';}
	  
	  echo '>
	</div>						
	';
		
}

if(($rowL['status']==30 || $rowL['for_issue']==1)){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control lineAmount'.$rowL['id'].'" style="min-width: 70px;" name="issueAssistantAmountFinal[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" readonly>
	</div>						
	';							
}						

echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'don" style="min-width: 70px;" placeholder="saņ. dok. neto (kg)" id="eDocNet'.$rowL['id'].'"  value="'.floatval($rowL['document_net']).'" disabled>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['place_count']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['place_count']).'){this.value=\''.floatval($rowL['place_count']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="vietu skaits" id="e_place_count'.$rowL['id'].'" name="e_place_count[]" value="';

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo floatval($rowL['place_count']);
}

if($rowL['status']==40){
	
	echo ' value="';
	if($rowL['issue_place_count']=='' || $rowL['issue_place_count']==0){ echo floatval($rowL['place_count']); }else{ echo floatval($rowL['issue_place_count']); }
	echo '"';	
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo floatval($rowL['issue_place_count']);
}

echo '"'; if(($rowL['status']==30 || $rowL['for_issue']==1)){echo 'readonly';} echo '>		
';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['tare']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['tare']).'){this.value=\''.floatval($rowL['tare']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="tara" id="eTare'.$rowL['id'].'" name="eTare[]" value="';
if($rowL['status']==20 && $rowL['for_issue']==0){
	echo floatval($rowL['tare']);
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo floatval($rowL['tare']);
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo floatval($rowL['issueTare']);
} 
echo '"'; if(($rowL['status']==30 || $rowL['for_issue']==1)){echo 'readonly';} echo '>		
';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['gross']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['gross']).'){this.value=\''.floatval($rowL['gross']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="bruto" id="eGross'.$rowL['id'].'" name="eGross[]" value="'; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo floatval($rowL['gross']);
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo floatval($rowL['gross']);
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo floatval($rowL['issueGross']);
} echo '"'; if(($rowL['status']==30 || $rowL['for_issue']==1)){echo 'readonly';} echo '>	
';


echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['net']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['net']).'){this.value=\''.floatval($rowL['net']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="neto" id="eNet'.$rowL['id'].'" name="eNet[]" value="'; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo floatval($rowL['net']);
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo floatval($rowL['net']);
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo floatval($rowL['issueNet']);
} echo '"'; if(($rowL['status']==30 || $rowL['for_issue']==1)){echo 'readonly';} echo '>			
';

echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'dn" style="min-width: 70px;" placeholder="saņ. Δ neto" id="eDeltaNet'.$rowL['id'].'"  value="'.floatval($rowL['delta_net']).'" disabled>';

echo '
<td>
<input 

type="text" min="1" max="'.floatval($rowL['cubicMeters']).'" 
step="0.01"
onKeyUp="if(this.value>'.floatval($rowL['cubicMeters']).'){this.value=\''.floatval($rowL['cubicMeters']).'\';}else if(this.value<1){this.value=\'1\';}"

class="form-control numbersOnly" style="min-width: 70px;" placeholder="m3" id="eCubicMeters'.$rowL['id'].'" name="eCubicMeters[]" value="'; 

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo floatval($rowL['cubicMeters']);
}

if($rowL['status']==40 && $rowL['for_issue']==0){
	echo floatval($rowL['cubicMeters']);
}

if($rowL['status']==30 || $rowL['for_issue']==1){
	echo floatval($rowL['issueCubicMeters']);
} echo '"'; if(($rowL['status']==30)){echo 'readonly';} echo '>					
';

						echo '
						
						<td>'.$rowL['batchNo'].'
						<td nowrap>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']);

						echo '<td>'.$rowL['lot_no'].'</td>';
						echo '<td>'.$rowL['container_type_no'].'</td>';

						echo '
						<td>'.$rowL['thisTransport'].'
						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="transporta nr." id="issueThisTransport'.$rowL['id'].'" name="issueThisTransport[]" ';  

						if($rowL['status']==20 && $rowL['for_issue']==0){ 
							echo ' value="'.$thisTransport.'"';
						}
						
						if($rowL['status']==40){
							echo ' value="'.$rowL['issue_thisTransport'].'"';
						}							
						
						if($rowL['status']==30 || $rowL['for_issue']==1){
							echo ' value="';
							if($rowL['issue_thisTransport']==''){echo $thisTransport;}else{echo $rowL['issue_thisTransport'];}
							echo '" readonly';
						}						
						
						echo ' '.$dis.'>

						<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="deklarācijas nr." id="issueDeclarationTypeNo'.$rowL['id'].'" name="issueDeclarationTypeNo[]" '; 

						if($rowL['status']==20 && $rowL['for_issue']==0){
							echo ' value="'.$declaration_type_no.'"';
						}
		
						if($rowL['status']==40){
							echo ' value="'.$rowL['issue_declaration_type_no'].'"';
						}						
						
						if($rowL['status']==30 || $rowL['for_issue']==1){
							echo ' value="';
							if($rowL['issue_declaration_type_no']==''){echo $declaration_type_no;}else{echo $rowL['issue_declaration_type_no'];}
							echo '" readonly';
						}

						echo ' '.$dis.'>
						<td>';

						echo '<td>'.$rowL['cargo_status'].'</td>';						
						
						echo '<td>'.$rowL['seal_no'].'</td>';
						echo '<td>'.$rowL['weighing_act_no'].'</td>';
						
						  
					echo '						
					</tr>';
					
		}		
			
		}
if($dis!='disabled' && $isetEid=='y'){		
	$knowIt = mysqli_query($conn, "SELECT * FROM issuance_doc WHERE id='".intval($eid)."'");
	$kIrow = mysqli_fetch_array($knowIt);		
	

	
	echo '<input type="hidden" id="issueDateFinal" name="issueDateFinal" value="'.date('d.m.Y', strtotime($kIrow['issueDate'])).'">';
	echo '<input type="hidden" id="actualDateFinal" name="actualDateFinal" value="'.date('d.m.Y', strtotime($kIrow['actualDate'])).'">';
	echo '<input type="hidden" id="issuance_id" name="issuance_id" value="'.$kIrow['issuance_id'].'">';
	
	echo '<input type="hidden" name="issueResult" value="'.$r.'">';
	echo '<input type="hidden" name="issueResultFinal" value="'.$k.'">';	
	echo '</tbody>
	</table>';
echo '</div>';
				
}

			
			
			if(checkAllStatusLines($conn, $kIrow['agreements'], $kIrow['clientCode'], $kIrow['issuance_id'])==0){
				
				if(checkAllStatus($conn, $kIrow['agreements'], $kIrow['clientCode'], 30, '')==0){
					echo '<button type="submit" class="btn btn-default btn-xs actionLine" id="approveLine" style="margin-right: 2px;"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot dokumentu</button>';
				}
			
			}
			

			if(checkAllStatus($conn, $kIrow['agreements'], $kIrow['clientCode'], 30, $kIrow['issuance_id'])==1){
				echo '<a target="_blank" href="print?view=izs&id='.$kIrow['issuance_id'].'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
				echo '<button type="submit" class="btn btn-default btn-xs actionLine" style="margin-right: 2px;" id="cancelLine"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atvērt nodošanu</button>';
			
				if($p_edit=='on'){
					echo '<button type="submit" class="btn btn-default btn-xs actionLine" id="receiveLine"><i class="glyphicon glyphicon-share" style="color: green;"></i> izdot</button>';
				}
			}
			
		
?>		
</form>
<?php	
	}	
	
	echo '
	</div>
	</div>
</div>';



	
}	// view==edit ends

?>

<script>
$("#checkall").change(function () {
    $("input:checkbox.cb-element").prop('checked', $(this).prop("checked"));

	var target = $('input:checkbox').parent().find('input[type=hidden]').val();
	
    if(target == 0)
    {
        target = 1;
		
    }
    else
    {
        target = 0;
    }
	
    $('input:checkbox').parent().find('input[type=hidden]').val(target);	
	
});
</script>

<script>
$('input[type=checkbox]').on("change",function(){
	
    var target = $(this).parent().find('input[type=hidden]').val();
	
    if(target == 0)
    {
        target = 1;
    }
    else
    {
        target = 0;
    }
	
    $(this).parent().find('input[type=hidden]').val(target);
});

</script>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<script>
$(document).ready(function() {
	$('form').on('keyup change', 'input, select, textarea', function(){
		$("#savebtn").css("background","#F1F103");
	});
});

$(document).on('dp.change', 'input', function(e) {
	var shown = $(this).attr('dp.shown');
	if (typeof shown != 'undefined' && shown && e.date != e.oldDate)
	$("#savebtn").css("background","#F1F103");
});

$(document).on('dp.show', 'input', function(e) {
	$(this).attr('dp.shown', 1);
});

</script>
<?php include_once("../../datepicker.php"); ?>