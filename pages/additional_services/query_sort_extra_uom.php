<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="additional_services";


require('../../inc/s.php');

$view=$selected=$amount=$uom=null;
$view = $_GET['view'];
$selected = $_GET['select'];
$selected = mysqli_real_escape_string($conn, $selected);



if(!$view){
	
	$getNames = mysqli_query($conn, "
			SELECT assistantUmo 
			FROM n_items
			
			
			WHERE code='".$selected."' AND status='1'
		");

		
	$gNrow = mysqli_fetch_array($getNames);	

	echo '<input type="hidden" name="assistantUnitOfMeasurement" id="assistantUnitOfMeasurement" value="'.$gNrow['assistantUmo'].'" >';
	if($gNrow['assistantUmo']){echo $gNrow['assistantUmo'];}	

}

if($view=='eline'){

	$getNames = mysqli_query($conn, "
			SELECT assistantUmo 
			FROM n_items
			
			
			WHERE code='".$selected."' AND status='1'
		");

		
	$gNrow = mysqli_fetch_array($getNames);	

	echo '<input type="hidden" name="eAssistantUnitOfMeasurement[]" id="eAssistantUnitOfMeasurement" value="'.$gNrow['assistantUmo'].'" >';
	if($gNrow['assistantUmo']){echo $gNrow['assistantUmo'];}	

}



?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
