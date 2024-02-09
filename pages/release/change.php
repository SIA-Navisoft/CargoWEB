<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


require('../../inc/s.php');

$selected = mysqli_real_escape_string($conn, $_GET['select']);
$resource = mysqli_real_escape_string($conn, $_GET['resource']);


if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}

if(!$view){
			echo '
			 
			  <select class="form-control selectpicker btn-group-xs"  id="ownerCode"  data-live-search="true" title="īpašnieka kods">';
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(ownerCode) AS ownerCode, ownerName, id FROM n_customers") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-oname="'.$rowc['ownerName'].'" value="'.$rowc['ownerCode'].'"';
				  if($selected==$rowc['id']){echo ' selected';}
				  echo '>'.$rowc['ownerCode'].' - '.$rowc['ownerName'].'</option>';
			  }
			  echo '</select>	';


?>
<script>
$('#ownerCode').ready(function() {
	
	var typex = $('#ownerCode option:selected').attr('data-oname');
	
	$('#ownerName').val(typex);

});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker btn-group-xs').selectpicker btn-group-xs();
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

<?php

}

if($view=='sort_agreements'){

echo '

            <div class="form-group col-md-3">
            <label for="agreements">līgums</label>
                <select class="form-control selectpicker btn-group-xs"  name="agreements" id="agreements"  data-live-search="true" title="līgums" onchange="selAgreement(this)" '.$disabled.'>
                    ';
					
                    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE customerNr='".mysqli_real_escape_string($conn, $selected)."' AND (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0' ORDER BY id DESC") or die(mysqli_error($conn));
                    while($rowa = mysqli_fetch_array($selectAgreements)){
                        echo '<option data-cna="'.$rowa['contractNr'].'" value="'.$rowa['contractNr'].'"';
                        if($row['agreements']==$rowa['contractNr']){echo ' selected';}
                        echo ' selected="selected">'.$rowa['contractNr'].' - '.$rowa['customerNr'];
                        if($rowa['customerName']){echo '('.$rowa['customerName'].')';}
                    echo '</option>';
                    }
                    echo '
                </select>		
            </div>
			';

?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<?php 
}



if($view=='sort_resource'){

				if($selected){
					$selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE customerNr='".$selected."' AND (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0' ORDER BY id ASC") or die(mysqli_error($conn));
					$rowa = mysqli_fetch_array($selectAgreements);	
					
					$contractNr = $rowa['contractNr'];
				}
				
				if($resource){
					$contractNr = $resource;
				}				

				echo '
				<div class="form-group col-md-3">	
				<label for="resource">pakalpojums</label>			
					<select class="form-control selectpicker btn-group-xs" id="resource"  data-live-search="true" title="pakalpojums" name="resource" '.$dis.' '.$hide.'>';	
					$selectResource = mysqli_query($conn, "
						SELECT r.id AS id, r.name AS name 
						FROM n_resource AS r
						LEFT JOIN agreements_lines AS al
						ON r.id=al.service
						WHERE al.contractNr='".$contractNr."' AND al.deleted!='1'  AND al.keeping!='on' AND al.deleted='0' AND al.extra_resource!='on'
						GROUP BY r.id
						") or die(mysqli_error($conn));
					while($rowi = mysqli_fetch_array($selectResource)){
						echo '<option value="'.$rowi['id'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
					}
					
					echo '
					</select>
				</div>
			';

?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<?php 
}



if($view=='sort_number'){

		echo '
		<div class="form-group col-md-3">
			<label for="thatNumber">pieņemšanas akta nr.</label>
			<select class="form-control selectpicker btn-group-xs"  name="thatNumber" id="thatNumber"  data-live-search="true" title="pieņemšanas akta nr." onchange="selNumber(this)" '.$disabled.'>
			<option value=""></option>	
			';

				if($selected){
					$selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE customerNr='".$selected."'
					AND (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0'
					ORDER BY id ASC") or die(mysqli_error($conn));
					$rowa = mysqli_fetch_array($selectAgreements);	
					
					$contractNr = $rowa['contractNr'];
				}

				if($contractNr){ $agrr = " AND agreements='".$contractNr."'"; }else{ $agrr = null; }
				$query = mysqli_query($conn, "					
				
				SELECT cargo_header.acceptance_act_no 

				FROM cargo_header

				JOIN cargo_line
				ON cargo_header.docNr=cargo_line.docNr

				WHERE cargo_header.clientCode='".$selected."' ".$agrr." 
				 
				AND cargo_header.status>=20 AND cargo_header.status!=40 
				AND cargo_line.for_issue!=1

				GROUP BY cargo_header.acceptance_act_no	");
				while($row = mysqli_fetch_array($query)){
					echo '<option value="'.$row['acceptance_act_no'].'">'.$row['acceptance_act_no'].'</option>';
				}
			
			echo '
			</select>	
		</div>		
		';
?>

<script>
$(document).ready(function() {
$('.selectpicker').selectpicker();
});
</script>

<?php 
}



if($view=='sort_receiver'){

echo '

			<div class="form-group col-md-2 ">
			<label class="lb-sm" for="receiverCountry">saņēmēja valsts</label>
			  <select class="form-control selectpicker btn-group-xs input-xs" name="receiverCountry" id="receiverCountry"  data-live-search="true" title="saņēmēja vasts">';
			  $selectReceiver = mysqli_query($conn, "SELECT Code, country FROM countries") or die(mysqli_error($conn));
			  while($rowr = mysqli_fetch_array($selectReceiver)){
				  echo '<option value="'.$rowr['Code'].'"';
					if($rowr['Code']==$selected){ echo ' selected';}
				  echo '>'.$rowr['Code'].' - '.$rowr['country'].'</option>';
			  }
				echo '
			  </select>	
			</div>
			';
			
			echo '
			 <div class="form-group col-md-2">
			  <label class="lb-sm"  for="cargoStatus">kravas status</label>
				<select class="form-control selectpicker btn-group-xs input-xs" id="cargoStatus" name="cargoStatus"  data-live-search="true" title="kravas status">';
						
						$wCountry=null;
						if($selected){
							$wCountry = " WHERE Code='".$selected."'";
							
							$selectReceiverCountry = mysqli_query($conn, "SELECT cargo_status FROM countries ".$wCountry."") or die(mysqli_error($conn));
							$rowrc = mysqli_fetch_array($selectReceiverCountry);
							
							echo '
							<option value="C" '; if($rowrc['cargo_status']=='C'){echo 'selected';} echo '>C - eksports</option>
							<option value="EU" '; if($rowrc['cargo_status']=='EU'){echo 'selected';} echo '>EU</option>';
						}else{
							echo '
							<option value="C">C - eksports</option>
							<option value="EU">EU</option>';
						}							
		
				echo '
				</select>	
			 </div>';			

?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<?php } ?>

