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

$amount = $_GET['amount'];
$amount = mysqli_real_escape_string($conn, $amount);

if($_GET['uom']){
	$uom = mysqli_real_escape_string($conn, $_GET['uom']);
}else{
	$uom = selectBaseUom($conn, $selected);
}
$input = $_GET['input'];
$input = mysqli_real_escape_string($conn, $input);

$id = intval($_GET['id']);

if(!$view){
	
	$getNames = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$selected."' AND base='1' AND status='1'");
	$gNrow = mysqli_fetch_array($getNames);	

	echo '<input type="hidden" name="unitOfMeasurement" id="unitOfMeasurement" value="'.$gNrow['uom'].'" >';
	if($gNrow['uom']){echo $gNrow['uom'];}	

	$wGrossA=$wNetA=$wVolumeA=null;
	if((checkIfMainUOM($conn, $selected, $uom)==1)){

		$baseUom=selectBaseUom($conn, $selected);
		$wGrossA=$wNetA=$wVolumeA=$wContainerA=$wPlaceA=null;
		
		
		if($input=='amount' || $input=='all'){
		
			if($baseUom==$uom){
				
				$wGrossA = retAmA($conn, 10, $selected);

				$wNetA = retAmA($conn, 20, $selected);

				$wVolumeA = retAmA($conn, 30, $selected);

				$wPlaceA = retAmA($conn, 50, $selected);

				if($wGrossA){$wGrossA=floatval($wGrossA);}
				if($wNetA){$wNetA=floatval($wNetA);}
				if($wVolumeA){$wVolumeA=floatval($wVolumeA);}
				if($wPlaceA){$wPlaceA=floatval($wPlaceA);}
				?>
				<script>


					calculateFee2();
	

					function calculateFee2(){

						var qty=$("#amount");

						<?php if($wGrossA){ ?>

								total1=null;
								total1=isNaN(parseInt(qty.val()* <?=$wGrossA;?>)) ? 0 :(qty.val()* <?=$wGrossA;?>);
								$("#gross").val(total1);

						<?php } if($wNetA){ ?>

								total2=null;
								total2=isNaN(parseInt(qty.val()* <?=$wNetA;?>)) ? 0 :(qty.val()* <?=$wNetA;?>);
								$("#net").val(total2);

						<?php } if($wGrossA>0 && $wNetA>0){ ?>

							

								total4=null;
								total4=isNaN(parseInt(total1-total2)) ? 0 :(total1-total2);

								$("#tare").val(total4);
						<?php }else{ ?>
	
							
						<?php } ?>
						<?php if($wVolumeA){ ?>

								total3=null;
								total3=isNaN(parseInt(qty.val()* <?=$wVolumeA;?>)) ? 0 :(qty.val()* <?=$wVolumeA;?>);
								$("#cubicMeters").val(total3);

						<?php }	if($wPlaceA){ ?>

								total5=null;
								total5=isNaN(parseInt(qty.val()* <?=$wPlaceA;?>)) ? 0 :(qty.val()* <?=$wPlaceA;?>);
								$("#place_count").val(total5);
						<?php } ?>
						
					}
					

				</script>
				<?
			}

		}
		
		if($input=='aAmount' || $input=='all'){
		
			if($baseUom==$uom){
				
				$wGrossA = retAmB($conn, 10, $selected);

				$wNetA = retAmB($conn, 20, $selected);

				$wVolumeA = retAmB($conn, 30, $selected);

				$wPlaceA = retAmB($conn, 50, $selected);

				if($wGrossA){$wGrossA=floatval($wGrossA);}
				if($wNetA){$wNetA=floatval($wNetA);}
				if($wVolumeA){$wVolumeA=floatval($wVolumeA);}
				if($wPlaceA){$wPlaceA=floatval($wPlaceA);}
				?>
				<script>


					calculateFee2();
	

					function calculateFee2(){

						var qty=$("#assistantAmount");

						<?php if($wGrossA){ ?>

								total1=null;
								total1=isNaN(parseInt(qty.val()* <?=$wGrossA;?>)) ? 0 :(qty.val()* <?=$wGrossA;?>);
								$("#gross").val(total1);

						<?php } if($wNetA){ ?>

								total2=null;
								total2=isNaN(parseInt(qty.val()* <?=$wNetA;?>)) ? 0 :(qty.val()* <?=$wNetA;?>);
								$("#net").val(total2);

						<?php } if($wGrossA>0 && $wNetA>0){ ?>

							

								total4=null;
								total4=isNaN(parseInt(total1-total2)) ? 0 :(total1-total2);

								$("#tare").val(total4);
						<?php }else{ ?>
							

								//$("#tare").val('0'); //aizkomentēju jo nevarēju saprast vai vajag uzlikt 0
								
							
						<?php } ?>
						<?php if($wVolumeA){ ?>

								total3=null;
								total3=isNaN(parseInt(qty.val()* <?=$wVolumeA;?>)) ? 0 :(qty.val()* <?=$wVolumeA;?>);
								$("#cubicMeters").val(total3);

						<?php }	if($wPlaceA){ ?>

								total5=null;
								total5=isNaN(parseInt(qty.val()* <?=$wPlaceA;?>)) ? 0 :(qty.val()* <?=$wPlaceA;?>);
								$("#place_count").val(total5);
						<?php } ?>
						
					}
					

				</script>
				<?
			}

		}
	
	}

}

?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
