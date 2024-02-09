<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


require('../../inc/s.php');
include('../../functions/base.php');

if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
$id = intval($_GET['id']);


if (isset($_GET['s'])){$s = htmlentities($_GET['s'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceId'])){$issuanceId = htmlentities($_GET['issuanceId'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceStatus'])){$issuanceStatus = htmlentities($_GET['issuanceStatus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['acceptanceActNo'])){$acceptanceActNo = htmlentities($_GET['acceptanceActNo'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}

	if(checkScannedIssuanceStatus($conn, safeHTML($issuanceId))==100){
		
		echo 'STOP';
		die();
		
		
	}

echo 'zzzzzz';
if($view=='release_one_line'){

if($issuanceId){
	$getd = mysqli_query($conn, "SELECT thisTransport, declaration_type_no, `resource`, places, decks, forScan, destination FROM issuance_doc WHERE issuance_id='".$issuanceId."'");
	$getdRow = mysqli_fetch_array($getd);

	$thisTransport = $getdRow['thisTransport'];
	$declaration_type_no = $getdRow['declaration_type_no'];
	$destination = $getdRow['destination'];
	
}
	
			if($sr){
			$s = mysqli_real_escape_string($conn, $sr);	
			$search = " AND (cargo_line.productNr LIKE '%$s%' || cargo_line.thisTransport LIKE '%$s%' || cargo_line.thisDate LIKE '%$s%' || cargo_line.batchNo LIKE '%$s%')";	
			}else{
			$search = "";	
			}

		if($issuanceStatus==10 || !$id){ $dis = 'disabled'; }else{ $dis = null; }	
		if($dis=='disabled'){ $showHi = "cargo_line.status='40' AND cargo_line.issuance_id='".$issuanceId."'"; }else{ $showHi = "(cargo_line.status='20' OR cargo_line.status='30') AND (cargo_line.issuance_id='' OR cargo_line.issuance_id='".$issuanceId."')"; }
		
		

		$numz = null;
		if($acceptanceActNo){ $numz = " AND cargo_header.acceptance_act_no='".$acceptanceActNo."'"; }

		$destz = null;
		
		if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }


		$lines = mysqli_query($conn, "
				SELECT cargo_line.*, cargo_header.deliveryDate AS hDeliveryDate
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE  
				".$showHi." ".$search." AND cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$numz." ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' AND cargo_line.id='".$id."'
				
				ORDER BY cargo_header.deliveryDate
			") or die (mysqli_error($conn));		

		$r=0; $k=0;
		
		while($rowL = mysqli_fetch_array($lines)){
			if($rowL['for_issue']==1){ $dis = 'disabled'; }
			
			
			
			
			
			
			
			
			
		if($rowL['status']<20){
					
		}else{	

			
			echo '<td>';
			
			echo '<input type="hidden" name="docNr[]" value="'.$rowL['docNr'].'">';


			if(($rowL['status']==20 && $dis!='disabled')){		
				$r++;
				echo '<input type="hidden" name="lineId[]" value="'.$rowL['id'].'">';
			}
			if(($rowL['status']==30 && $dis!='disabled')){		
				$k++;
				echo '<input type="hidden" name="lineIdFinal[]" value="'.$rowL['id'].'">';
			}			
			echo '<div id="query_sort_e'.$rowL['id'].'"></div>';				
			
				if(($rowL['status']==20)){

					if($rowL['for_issue']==1){
						echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="cancelOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atvērt nodošanu</div>'; 
					}else{
						echo '<div class="btn btn-default btn-xs" style="margin-right: 2px;" onclick="approveOneLine('.$rowL['id'].')"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</div>';
					}	
				
				}

				if(($rowL['status']==30)){
										
				}
				
				if(($rowL['status']==40)){
					
					
					
				}	
				echo '</td>';
				
				
			echo '<td>';
				if($rowL['for_issue']==1){ echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>'; }
			echo '</td>';
					

		if($getdRow['forScan']==1){			
			echo '<td>';	
				if($rowL['status']==20 && $rowL['for_issue']==0 && $dis!='disabled'){
					
					$allPlaces = explode(',',$getdRow['places']);
					echo '<select class="form-control selectpicker btn-group-xs input-xs" name="issuePlacement[]" id="issuePlacement'.$rowL['id'].'" data-live-search="true" data-width="80px" title=" " '.$dis.'>';
					for ($x = 1; $x <= 7; $x++) {
						
						$checked_view=null;
						if (in_array('R'.$x, $allPlaces)){
							
							echo '<option value="R'.$x.'" '; if($rowL['placement']=='R'.$x){echo 'selected';} echo '>R'.$x.'</option>';

						}
						
					}
					
					$allDecks = explode(',',$getdRow['decks']);
					for ($x = 1; $x <= 7; $x++) {

						$checked_view=null;
						if (in_array('K'.$x, $allDecks)){
							
							echo '<option value="K'.$x.'" '; if($rowL['placement']=='K'.$x){echo 'selected';} echo '>K'.$x.'</option>';

						}
						
					}												
					
					echo '</select>';

				}else{
					echo $rowL['issuePlacement'];
				}	
			echo '</td>';

		}			
				
				echo '<td>';
				if($rowL['brack']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
				echo '</td>';
				
				echo '<td nowrap>'.date('d.m.Y', strtotime($rowL['hDeliveryDate'])).'</td>';
				
				echo '
					<td nowrap>
					'.$rowL['productNr'].' - ';
					$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
					$gNrow = mysqli_fetch_array($getNames);
					echo $gNrow['name1'].' '.$gNrow['name2'];
				echo '</td>';
				
						//parāda skenētāju
						$infoTable=$title=null;
						if($rowL['issuanceScannedBy']>0){

							$infoTable = '
								<div id="a1'.$rowL['id'].'" class="hidden">
								  <div class="popover-body">
									<table style="width:100%">
									  <tr>
										<td>Skenētājs:</td>
										<td nowrap>'.returnMeWho($rowL['issuanceScannedBy']).'</td>	
									  </tr>
									  <tr>
										<td>Laiks:</td>
										<td nowrap>'.date('d.m.Y H:i:s', strtotime($rowL['issuanceScannedDate'])).'</td>
									  </tr>
									</table>
								  </div>
								</div>									
							';
							
							
							$title = ' style="background-color: #eee; height: 20px; font-size: 12px; display: table-cell; vertical-align: middle;" data-toggle="popover" data-trigger="hover" data-popover-content="#a1'.$rowL['id'].'" data-placement="right" class="form-control"';
						}						
						
					echo '<td><div '.$title.'>'.$rowL['serialNo'].' '.$infoTable.'</div></td>';
	
echo '
<td>'.floatval($rowL['amount']).'</td>
<td>'.$rowL['productUmo'].'</td>';

if($rowL['status']==20 && $rowL['for_issue']==0){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control numbersOnly c_delta_t" data-idtdelta="'.$rowL['id'].'"  style="min-width: 70px;" name="issueAmount[]" id="issueAmount'.$rowL['id'].'"  placeholder="daudzums" value="'.floatval($rowL['amount']).'" 
	  
	  type="number" min="1" max="'.floatval($rowL['amount']).'" 
	  step="0.01"
	 onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<1){this.value=\'1\';}"
	  
	  
	  
	  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$hide.'  '.$dis.'>
	</div>						
	</td>';
		
}
if($rowL['status']==30 || $rowL['for_issue']==1){
	echo '
	<td>
	<div class="">
	  <input type="text" class="form-control" style="min-width: 70px;" name="issueAmountFinal[]" placeholder="daudzums" value="'.floatval($rowL['issueAmount']).'" readonly>
	</div>						
	</td>';							
}

				echo '
				<td>'.floatval($rowL['assistant_amount']).'</td>
				<td>'.$rowL['assistantUmo'].'</td>';

				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control numbersOnly" style="min-width: 70px;" name="issueAssistantAmount[]" id="issueAssistantAmount'.$rowL['id'].'"  placeholder="palīg mērv. daudzums" value="'.floatval($rowL['assistant_amount']).'" 
					  
					  type="number" min="1" max="'.floatval($rowL['assistant_amount']).'" 
					  step="0.01"
					 onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<1){this.value=\'1\';}"
					  
					  
					  
					  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$hide.' '.$dis.'>
					</div>						
					</td>';
						
				}



				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control" style="min-width: 70px;" name="issueAssistantAmountFinal[]" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['issue_assistant_amount']).'" readonly>
					</div>						
					</td>';							
				}


				echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'don" style="min-width: 70px;" placeholder="saņ. dok. neto (kg)" id="eDocNet'.$rowL['id'].'"  value="'.floatval($rowL['document_net']).'" disabled></td>';

				echo '
				<td>
				<input 

				type="text" min="1" max="'.floatval($rowL['place_count']).'" 
				step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['place_count']).'){this.value=\''.floatval($rowL['place_count']).'\';}else if(this.value<1){this.value=\'1\';}"

				class="form-control numbersOnly" style="min-width: 70px;" placeholder="vietu skaits" id="e_place_count'.$rowL['id'].'" name="e_place_count[]" '; 
				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['place_count']).'"';
				}

				if($rowL['status']==40 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['place_count']).'"';
				}

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.floatval($rowL['issue_place_count']).'" readonly';
				} echo ' '.$dis.'>';

				echo '
				<td>
				<input 

				type="text" min="1" max="'.floatval($rowL['tare']).'" 
				step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['tare']).'){this.value=\''.floatval($rowL['tare']).'\';}else if(this.value<1){this.value=\'1\';}"

				class="form-control numbersOnly" style="min-width: 70px;" placeholder="tara" id="eTare'.$rowL['id'].'" name="eTare[]" '; 

				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['tare']).'"';
				}

				if($rowL['status']==40 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['tare']).'"';
				}

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.floatval($rowL['issueTare']).'" readonly';
				} 

				echo ' '.$dis.'>';

				echo '
				<td>
				<input 

				type="text" min="1" max="'.floatval($rowL['gross']).'" 
				step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['gross']).'){this.value=\''.floatval($rowL['gross']).'\';}else if(this.value<1){this.value=\'1\';}"

				class="form-control numbersOnly" style="min-width: 70px;" placeholder="bruto" id="eGross'.$rowL['id'].'" name="eGross[]" '; 

				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['gross']).'"';
				}

				if($rowL['status']==40 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['gross']).'"';
				}

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.floatval($rowL['issueGross']).'" readonly';
				} echo ' '.$dis.'>';


				echo '
				<td>
				<input 

				type="text" min="1" max="'.floatval($rowL['net']).'" 
				step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['net']).'){this.value=\''.floatval($rowL['net']).'\';}else if(this.value<1){this.value=\'0\';}"

				class="form-control numbersOnly" style="min-width: 70px;" placeholder="neto" id="eNet'.$rowL['id'].'" name="eNet[]" '; 

				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['net']).'"';
				}

				if($rowL['status']==40 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['net']).'"';
				}

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.floatval($rowL['issueNet']).'" readonly';
				} echo ' '.$dis.'>';


				if($getdRow['resource']=='BERKR' || $getdRow['resource']=='BERKRSAN'){
					echo '<td nowrap><input type="text" class="form-control c_delta_t dt" data-idtdelta="'.$rowL['id'].'" style="min-width: 70px;" placeholder="faktiskais T" id="eDelta'.$rowL['id'].'"   '; 

					if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
						echo ' name="e_real_delta[]" value="'.floatval($rowL['amount']).'"';
					}
					
					if($rowL['status']==30 || $rowL['for_issue']==1){
						echo ' value="'.floatval($rowL['fact_for_delta']).'" readonly';
					} echo '>'; 
					
					
					
					
					
					echo '<td nowrap><input type="text" class="form-control edt" style="min-width: 70px;" placeholder="Δ T" id="eDeltaT'.$rowL['id'].'" '; 
					
					if($rowL['status']==20 && $rowL['for_issue']==0 || $rowL['status']==40){
						echo ' name="e_fact_for_delta[]" value="0" readonly';
					}
					
					if($rowL['status']==30 || $rowL['for_issue']==1){
						echo ' value="'.floatval($rowL['real_delta']).'" readonly';
					} echo '>'; 
				}



				echo '<td><input type="text" class="form-control '.$rowL['productUmo'].'dn" style="min-width: 70px;" placeholder="saņ. Δ neto" id="eDeltaNet'.$rowL['id'].'"  value="'.floatval($rowL['delta_net']).'" disabled>';

				echo '
				<td>
				<input 

				type="text" min="1" max="'.floatval($rowL['cubicMeters']).'" 
				step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['cubicMeters']).'){this.value=\''.floatval($rowL['cubicMeters']).'\';}else if(this.value<1){this.value=\'1\';}"

				class="form-control numbersOnly" style="min-width: 70px;" placeholder="m3" id="eCubicMeters'.$rowL['id'].'" name="eCubicMeters[]" '; 

				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['cubicMeters']).'"';
				}

				if($rowL['status']==40 && $rowL['for_issue']==0){
					echo ' value="'.floatval($rowL['cubicMeters']).'"';
				}

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.floatval($rowL['issueCubicMeters']).'" readonly';
				} echo ' '.$dis.'>';
				echo '</td>';

				echo '
				
				<td>'.$rowL['batchNo'].'</td>
				<td nowrap>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'</td>';


				echo '<td>'.$rowL['lot_no'].'</td>';

				echo '<td>'.$rowL['container_type_no'].'</td>';


				echo '<td>'.$rowL['thisTransport'].'</td>
				<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="transporta nr." id="issueThisTransport'.$rowL['id'].'" name="issueThisTransport[]" ';

				if($rowL['status']==20 && $rowL['for_issue']==0){ 
					echo ' value="'.$thisTransport.'"';
				}
				
				if($rowL['status']==40){
					echo ' value="'.$rowL['issue_thisTransport'].'"';
				}					
				
				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.$rowL['issue_thisTransport'].'" readonly';
				}						
				
				echo ' '.$dis.'></td>

				<td><input type="text" class="form-control" style="min-width: 100px;" placeholder="deklarācijas nr." id="issueDeclarationTypeNo'.$rowL['id'].'" name="issueDeclarationTypeNo[]" ';
				
				if($rowL['status']==20 && $rowL['for_issue']==0){
					echo ' value="'.$declaration_type_no.'"';
				}

				if($rowL['status']==40){
					echo ' value="'.$rowL['issue_declaration_type_no'].'"';
				}				

				if($rowL['status']==30 || $rowL['for_issue']==1){
					echo ' value="'.$rowL['issue_declaration_type_no'].'" readonly';
				}						
				
				echo ' '.$dis.'></td>';
				
				echo '<td>'.$rowL['cargo_status'].'</td>';

			  
				
				echo '<td>'.$rowL['seal_no'].'</td>';
				echo '<td>'.$rowL['weighing_act_no'].'</td>';
					
		}	

	
	
	
	
	
	
	
		}
	
	
	
	
	
}


?>
<script>
$(document).ready(function(){
    $(".c_delta_t").keyup(function(){

		  var idtdelta = $(this).data('idtdelta');

		  var id = $(this).attr('id');
          var val1 = $("#issueAmount"+idtdelta).val();

		  var baz = 'issueAmount'+idtdelta;

		  if(id==baz){
			$("#eDelta"+idtdelta).val(val1);
		  }
		  

          var val2 = $("#eDelta"+idtdelta).val();
		  var total = val2-val1; 
		  total = total.toFixed(2);
          $("#eDeltaT"+idtdelta).val(total);
   });
});
</script>
<?php
if($issuanceId){
	
	$isProcessed = mysqli_query($conn, "SELECT id FROM cargo_line WHERE issuance_id='".$issuanceId."'");
	$isP = mysqli_num_rows($isProcessed);

	if($issuanceId && $isP==0){

	?>
	<script>
	$(document).ready(function () {

		$('.deleteIt').html('<button type="submit" class="btn btn-default btn-xs" id="deleteIt" style="margin-left: 4px;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>');

	});
	</script>
	<?php

	}else{
		?>
			<script>
				$(document).ready(function () {

					$('.deleteIt').html('');

				});
			</script>
		<?php
	}
}
?>
<script>
$(function() {
  $("[data-toggle=popover]").popover({
    html: true,
    content: function() {
      var content = $(this).attr("data-popover-content");
      return $(content).children(".popover-body").html();
    },
    title: function() {
      var title = $(this).attr("data-popover-content");
      return $(title).children(".popover-heading").html();
    }
  });
});

$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
