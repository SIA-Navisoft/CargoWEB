<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="assembly";


require('../../inc/s.php');

$view=$selecte=$eAmount=$amount=$eUom=$uom=$id=$do1=$do2=$do3=$do4=null;
$view = $_GET['view'];
$selected = $_GET['select'];
$selected = mysqli_real_escape_string($conn, $selected);


$eAmount = $_GET['eAmount'];
$eAmount = mysqli_real_escape_string($conn, $eAmount);

$amount = $_GET['amount'];
$amount = mysqli_real_escape_string($conn, $amount);


if($_GET['eUom']){
	$eUom = mysqli_real_escape_string($conn, $_GET['eUom']);
}else{
	$eUom = selectBaseUom($conn, $selected);
} 

$input = $_GET['input'];
$input = mysqli_real_escape_string($conn, $input);

$id = intval($_GET['id']);


?>

<?php

if($view=='eline'){

		$getNames = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$selected."' AND base='1' AND status='1'");
		$gNrow = mysqli_fetch_array($getNames);	

		echo '<input type="hidden" name="eUnitOfMeasurement[]" id="eUnitOfMeasurement'.$id.'" value="'.$gNrow['uom'].'" >';
		if($gNrow['uom']){echo $gNrow['uom'];}				

		$wGross=$wNet=$wVolume=$wPlace=null;
		if(($id) && checkIfMainUOM($conn, $selected, $eUom)==1){

		$baseUom=selectBaseUom($conn, $selected);



			if($input=='eAmount' || $input=='all'){

				$wGross = retAmA($conn, 10, $selected);

				$wNet = retAmA($conn, 20, $selected);
	
				$wVolume = retAmA($conn, 30, $selected);

				$wPlace = retAmA($conn, 50, $selected);
	
				if($wGross){$wGross=floatval($wGross);}
				if($wNet){$wNet=floatval($wNet);}
				if($wVolume){$wVolume=floatval($wVolume);}
				if($wPlace){$wPlace=floatval($wPlace);}

				?>
				<script>

				$(document).ready(function(){

					calculateFee3();

					
					function calculateFee3(){

						var qty=$("#eAmount<?=$id;?>");
						<?php 
						
						if($baseUom==$eUom){
						
							if($wGross){ ?>

								var total1=isNaN(parseInt(qty.val()* <?=$wGross;?>)) ? 0 :(qty.val()* <?=$wGross;?>);
								$("#eGross<?=$id;?>").val(total1);
							<?php
							}
							if($wNet){
							?>
								var total2=isNaN(parseInt(qty.val()* <?=$wNet;?>)) ? 0 :(qty.val()* <?=$wNet;?>);
								$("#eNet<?=$id;?>").val(total2);
							<?php
							}
							if($wVolume){
							?>
								var total3=isNaN(parseInt(qty.val()* <?=$wVolume;?>)) ? 0 :(qty.val()* <?=$wVolume;?>);
								$("#eCubicMeters<?=$id;?>").val(total3);
							<?php
							}
							if($wGross>0 && $wNet>0){
							?>

							
								var total4=isNaN(parseInt(total1-total2)) ? 0 :(total1-total2);
								$("#eTare<?=$id;?>").val(total4);
							

							<?php  
							}else{
								?>

									

								<?php
							}

							if($wPlace){
							?>
								var total5=isNaN(parseInt(qty.val()* <?=$wPlace;?>)) ? 0 :(qty.val()* <?=$wPlace;?>);
								$("#e_place_count<?=$id;?>").val(total5);
							<?php
							}
						
						}
						?>
						
					}

				});
				</script>
				<?
			}

			if($input=='AeAmount' || $input=='all'){

				$wGross = retAmB($conn, 10, $selected);

				$wNet = retAmB($conn, 20, $selected);
	
				$wVolume = retAmB($conn, 30, $selected);

				$wPlace = retAmB($conn, 50, $selected);
				
	
				if($wGross){$wGross=floatval($wGross);}
				if($wNet){$wNet=floatval($wNet);}
				if($wVolume){$wVolume=floatval($wVolume);}
				if($wPlace){$wPlace=floatval($wPlace);}

				?>
				<script>

				$(document).ready(function(){

					calculateFee3();

					
					function calculateFee3(){

						var qty=$("#eAssistantAmount<?=$id;?>");
						<?php 
						
						if($baseUom==$eUom){
						
							if($wGross){ ?>

								var total1=isNaN(parseInt(qty.val()* <?=$wGross;?>)) ? 0 :(qty.val()* <?=$wGross;?>);
								$("#eGross<?=$id;?>").val(total1);
							<?php
							}
							if($wNet){
							?>
								var total2=isNaN(parseInt(qty.val()* <?=$wNet;?>)) ? 0 :(qty.val()* <?=$wNet;?>);
								$("#eNet<?=$id;?>").val(total2);
							<?php
							}
							if($wVolume){
							?>
								var total3=isNaN(parseInt(qty.val()* <?=$wVolume;?>)) ? 0 :(qty.val()* <?=$wVolume;?>);
								$("#eCubicMeters<?=$id;?>").val(total3);
							<?php
							}
							if($wGross>0 && $wNet>0){
							?>

							
								var total4=isNaN(parseInt(total1-total2)) ? 0 :(total1-total2);
								$("#eTare<?=$id;?>").val(total4);
							

							<?php  
							}else{
								?>

									

								<?php
							}

							if($wPlace){
								?>
									var total5=isNaN(parseInt(qty.val()* <?=$wPlace;?>)) ? 0 :(qty.val()* <?=$wPlace;?>);
									$("#e_place_count<?=$id;?>").val(total5);
								<?php
								}							
						
						}
						?>
						
					}

				});
				</script>
				<?
			}

		}

}

