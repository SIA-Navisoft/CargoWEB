<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="assembly";


require('../../inc/s.php');

$selected = $_GET['select'];
			
			echo '
			 
			  <select class="form-control selectpicker btn-group-xs"  id="ownerCode"  data-live-search="true" title="īpašnieka kods">';
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-oname="'.$rowc['Name'].'" value="'.$rowc['Code'].'"';
				  if($selected==$rowc['Code']){echo ' selected';}
				  echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
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