<?php
error_reporting(E_ALL ^ E_NOTICE);
die(0);
require('../../lock.php');
include('../../functions/base.php');

$page_file="relase";


require('../../inc/s.php');

$view=$selected=$eAmount=$eUom=null;
$view = $_GET['view'];
$selected = $_GET['select'];
$selected = mysqli_real_escape_string($conn, $selected);

$docNr = $_GET['docNr'];
$docNr = mysqli_real_escape_string($conn, $docNr);

$eAmount = $_GET['eAmount'];
$eAmount = mysqli_real_escape_string($conn, $eAmount);

if($_GET['eUom']){
	$eUom = mysqli_real_escape_string($conn, $_GET['eUom']);
}else{
	$eUom = selectBaseUom($conn, $selected);
} 


$id = intval($_GET['id']);


echo '<center>we are here id: '.$id.' selected: '.$selected.' eUom: '.$eUom.' docNr: '.$docNr.'</center>';

if($view=='eline'){

		$wGross=$wNet=$wVolume=null;
		if(($id) && checkIfMainUOM($conn, $selected, $eUom)==1){

			$wGrossT = retAm($conn, 10, $selected);

			$wNetT = retAm($conn, 20, $selected);

			$wVolumeT = retAm($conn, 30, $selected);			

			$wGross = retAmLeft($conn, $docNr, $selected, $eUom, 'gross');

			$wNet = retAmLeft($conn, $docNr, $selected, $eUom, 'net');

			$wVolume = retAmLeft($conn, $docNr, $selected, $eUom, 'cubicMeters');

			$wGross = $wGrossT - $wGross * -1;
			$wNet = $wNetT - $wNet * -1;
			$wVolume = $wVolumeT - $wVolume * -1;

			if($wGross){$wGross=floatval($wGross);}
			if($wNet){$wNet=floatval($wNet);}
			if($wVolume){$wVolume=floatval($wVolume);}

			echo '<center>gross: '.$wGross.' net: '.$wNet.' cubicMeters: '.$wVolume.'</center><br>';
		

		$baseUom=selectBaseUom($conn, $selected);

		?>
		<script>

		$(document).ready(function(){

			calculateFee3();
	
			function calculateFee3(){

				var qty=$("#issueAmount<?=$id;?>");
				$("#eGross<?=$id;?>").val('0');
				$("#eNet<?=$id;?>").val('0');
				$("#eTare<?=$id;?>").val('0');
				$("#eCubicMeters<?=$id;?>").val('0');
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

							$("#eTare<?=$id;?>").val('0');

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
