<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="movement";


require('../../inc/s.php');

$selected = $_GET['select'];
			
			echo '
			 
			  <select class="form-control selectpicker"  id="ownerCode"  data-live-search="true" title="īpašnieka kods">';
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
   $('.selectpicker').selectpicker();
});
</script>