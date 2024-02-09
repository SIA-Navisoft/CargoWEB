<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="additional_services";


require('../../inc/s.php');

$selected = mysqli_real_escape_string($conn, $_GET['select']);
$resource = mysqli_real_escape_string($conn, $_GET['resource']);
$agreements = mysqli_real_escape_string($conn, $_GET['agreements']);


if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}

if(!$view){	

			echo '
			
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="agreements">līgums</label>
				<select class="form-control selectpicker btn-group-xs input-xs"  id="agreements" name="agreements" onchange="selAgreement(this)" data-live-search="true" title="līgums">
					<option></option>';
				
				$selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE customerNr='".$selected."' AND (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0' ORDER BY ID DESC") or die(mysqli_error($conn));
				while($rowa = mysqli_fetch_array($selectAgreements)){
					echo '<option  value="'.$rowa['contractNr'].'" data-agid="'.$rowa['contractNr'].'"  selected="selected">'.$rowa['contractNr'].' - '.$rowa['customerNr'];
						if($rowa['customerName']){echo '('.$rowa['customerName'].')';}
					echo '</option>';
				}
				echo '
				</select>	
			</div>			

			';

?>
<script>
$('#ownerCode').ready(function() {
var typex = $('#ownerCode option:selected').attr('data-oname');
$('#ownerName').val(typex);	
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<?php
}

if($view=='sort_resource'){
	echo '';

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
					<select class="form-control selectpicker btn-group-xs" id="resource"  data-live-search="true" title="pakalpojums" name="resource">';	
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
				</div>';

}

if($view=='client_product_sel'){

echo '
<div class="form-group col-md-3">	
	<label for="client_product">kravas nosaukums (prece)</label>			
	<select class="form-control selectpicker btn-group-xs" id="client_product"  data-live-search="true" title="kravas nosaukums (prece)" name="client_product">';		
		$result = mysqli_query($conn, "
			SELECT ni.code, ni.name1, ni.name2 
			FROM n_items ni
			
			LEFT JOIN agreements_lines al
			ON ni.code=al.item
			
			WHERE ni.status='1' AND al.contractNr='".$agreements."' AND al.service='".$selected."'
		") or die(mysqli_error($conn));
		while($row = mysqli_fetch_array($result)){
			echo '<option value="'.$row['code'].'">'.$row['code'].' ('.$row['name1'].' '.$row['name2'].')</option>';
		}	
	echo '
	</select>
</div>';	
}
?>
<script>
$(document).ready(function(){
	$('#resource').on('change', function() {
			var typez = $("select#resource option:selected").attr('value');
			var agreements = $("select#agreements option:selected").attr('value');			

			$( "#client_product_sel" ).load( "/pages/additional_services/change.php?view=client_product_sel&select="+typez+"&agreements="+agreements+"" );
			
	});
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>