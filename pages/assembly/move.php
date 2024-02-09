<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="assembly";


require('../../inc/s.php');

$selected = $_GET['select'];
if($selected=='none'){$selected = '';}	

			echo ' 
		<div class="table-responsive">
		<table id="two" class="table table-hover table-responsive">
		<thead> 
			<tr>
				
				<th>produkta<br>nr.</th>
			
				<th>partijas nr.</th>
				<th>noliktava</th>
				<th>daudzums</th>
				<th>mēr-<br>vienība</th>
				<th>papild. mērv.<br>daudzums</th>
				<th>papild. mēr-<br>vienība</th>				
				<th>statuss</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';

		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".mysqli_real_escape_string($conn, $selected)."' AND action!='23'");
		$r=0;
		while($rowL = mysqli_fetch_array($lines)){
					
			if($rowL['status']!=20){

				echo '<tr bgcolor="#eee" class="classlistedit" id="mOne'.$rowL['id'].'">
					
					<td>'.$rowL['productNr'];
					
					
					echo '<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$rowL['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
						i
					</a>						
					
					<td>'.$rowL['batchNo'];

					echo '
					
					<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
					<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
					<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
					onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 100px; border-radius: 5px;" value="'.floatval($rowL['amount']).'" disabled></div>
					
					<td>'.$rowL['productUmo'];

					echo '<td>'.floatval($rowL['assistant_amount']);
					echo '<td>'.$rowL['assistantUmo'];		

					echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);	
					echo '<td>';
								
				echo '</tr>';			
			
			}else{	
			$r++;
			
				echo '<tr class="classlistedit" id="mTwo'.$rowL['id'].'">
					
				
				
					<td>'.$rowL['productNr'].'
					
					<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$rowL['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
						i
					</a>';				
						
					echo '
					<td>'.$rowL['batchNo'];					

					echo '
					
					<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
					<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
					<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
					onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 80px; border-radius: 5px;" class="forChangeTwo" value="'.floatval($rowL['amount']).'"></div>	
					
					<td>'.$rowL['productUmo'];

					echo '<td> 
				
					<div class="hOn">'.floatval($rowL['assistant_amount']).'</div>	
					<div class="Inputz" style="display: none;">
						<input type="number" min="0" max="'.floatval($rowL['assistant_amount']).'" step="0.01"
							   onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<0){this.value=\'0\';}" 
							   style="width: 60px; border-radius: 5px;" class="forAssistantAmountTwo" value="'.floatval($rowL['assistant_amount']).'">
					</div>';
					echo '<td>'.$rowL['assistantUmo'];
					
					echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
					echo '<td><div class="checkbox 2">
					
					<input type="hidden" class="changeInputsTwo"  value="'.$rowL['id'].'" style="width: 50px;">
					<input type="hidden" class="productNrTwo"  value="'.$rowL['productNr'].'" style="width: 50px;">
					<input type="hidden" class="amountTwo"  value="'.floatval($rowL['amount']).'" style="width: 50px;">
					<input type="hidden" class="assistantAmountTwo"  value="'.floatval($rowL['assistant_amount']).'" style="width: 50px;">
					<input type="hidden" class="enteredDateTwo"  value="'.date('d.m.Y', strtotime($rowL['enteredDate'])).'" style="width: 50px;">
					<input type="hidden" class="locationTwo"  value="'.$rowL['location'].'" style="width: 50px;">
					<input type="hidden" class="statusTwo"  value="'.$rowL['status'].'" style="width: 50px;">	
					<input type="hidden" class="productUmoTwo"  value="'.$rowL['productUmo'].'" style="width: 50px;">
					<input type="hidden" class="batchNoTwo"  value="'.$rowL['batchNo'].'" style="width: 50px;">	

					<input type="hidden" class="activityDateTwo"  value="'.date('d.m.Y', strtotime($rowL['activityDate'])).'" style="width: 50px;">
					<input type="hidden" class="thisTransportTwo"  value="'.$rowL['thisTransport'].'" style="width: 50px;">
					<input type="hidden" class="volumeTwo"  value="'.$rowL['volume'].'" style="width: 50px;">
					<input type="hidden" class="thisDateTwo"  value="'.date('d.m.Y', strtotime($rowL['thisDate'])).'" style="width: 50px;">
					
					<i class="glyphicon glyphicon-transfer"></i></div>								
				</tr>';
			}	
		}
		echo '<input type="hidden" name="changeCodeTwo" value="'.$selected.'">';
		echo '<input type="hidden" name="changeResultTwo" value="'.$r.'"> ';


		
	echo '</tbody>
	</table>
</div>	
				
				</div>
			</div>';

if($selected){
?>
<script>
$(document).ready(function() {
$(".moveIcon").show();
});
</script>
<?php }else{ ?>
<script>
$(document).ready(function() {
$(".moveIcon").hide();
});
</script>
<?php } ?>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
   $(".Inputz").css('display','block');
   $(".hOn").css('display','none');
});
</script>