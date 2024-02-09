<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="additional_services";


require('../../inc/s.php');

$view=$selecte=$eAmount=$amount=$eUom=$uom=$id=$do1=$do2=$do3=$do4=null;
$view = $_GET['view'];
$selected = $_GET['select'];
$selected = mysqli_real_escape_string($conn, $selected);


$eAmount = $_GET['eAmount'];
$eAmount = mysqli_real_escape_string($conn, $eAmount);

$amount = $_GET['amount'];
$amount = mysqli_real_escape_string($conn, $amount);

$do1 = $_GET['do1'];
$do2 = $_GET['do2'];
$do3 = $_GET['do3'];
$do4 = $_GET['do4'];

if($_GET['eUom']){
	$eUom = mysqli_real_escape_string($conn, $_GET['eUom']);
}else{
	$eUom = selectBaseUom($conn, $selected);
}



if($_GET['uom']){
	$uom = mysqli_real_escape_string($conn, $_GET['uom']);
}else{
	$uom = selectBaseUom($conn, $selected);
}


$id = intval($_GET['id']);



?>
<style>
.iclass {

}
</style>
<?

if(!$view){
	
				$getNames = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$selected."' AND base='1'");
				$gNrow = mysqli_fetch_array($getNames);	

				echo '<input type="hidden" name="unitOfMeasurement" id="unitOfMeasurement" value="'.$gNrow['uom'].'" >';
				if($gNrow['uom']){echo $gNrow['uom'];}	


}


if($view=='eline'){

				$getNames = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$selected."' AND base='1'");
				$gNrow = mysqli_fetch_array($getNames);	

				echo '<input type="hidden" name="eUnitOfMeasurement[]" id="eUnitOfMeasurement'.$id.'" value="'.$gNrow['uom'].'" >';
				if($gNrow['uom']){echo $gNrow['uom'];}				
}

if(($id) && ($view!='' || $view=='eTare' && $view=='eGross' && $view=='eNet' && $view=='eCubicMeters') && checkIfMainUOM($conn, $selected, $eUom)==1){
	

	$wGross = retAm($conn, 10, $selected);

	$wNet = retAm($conn, 20, $selected);

	$wVolume = retAm($conn, 30, $selected);

	$wContainer = retAm($conn, 40, $selected);

	if($wGross){$wGross=floatval($wGross);}
	if($wNet){$wNet=floatval($wNet);}
	if($wVolume){$wVolume=floatval($wVolume);}
	if($wContainer){$wContainer=floatval($wContainer);}

$baseUom=selectBaseUom($conn, $selected);

?>
<script>

$(document).ready(function(){

	calculateFee3();

	$('#eAmount<?=$id;?>').on('keyup change', function(){

	calculateFee3();
	}); 

	
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
			if($wContainer){
			?>
				var total4=isNaN(parseInt(qty.val()* <?=$wContainer;?>)) ? 0 :(qty.val()* <?=$wContainer;?>);
				$("#eTare<?=$id;?>").val(total4);
			<?php } 
		
		}
		?>
		
	}

});
</script>
<?

}

if($view=='eTare' && $id){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly eTare iclass" placeholder="tara" id="eTare'.$id.'" name="eTare[]" value="'.floatval($rowL['tare']).'" '.$disabled.'>
		</div>
	';
}

if($view=='eGross' && $id){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly iclass" placeholder="bruto" id="eGross'.$id.'" name="eGross[]" value="'.floatval($rowL['gross']).'" '.$disabled.'>
		</div>
	';
}

if($view=='eNet' && $id){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly iclass" placeholder="neto" id="eNet'.$id.'" name="eNet[]" value="'.floatval($rowL['net']).'" '.$disabled.'>
		</div>
	';
}

if($view=='eCubicMeters' && $id){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly iclass" placeholder="m3" id="eCubicMeters'.$id.'" name="eCubicMeters[]" value="'.floatval($rowL['cubicMeters']).'" '.$disabled.'>
		</div>
	';
}


if(($view=='tare' || $view=='gross' || $view=='net' || $view=='cubicMeters') && (checkIfMainUOM($conn, $selected, $uom)==1)){

	$baseUom=selectBaseUom($conn, $selected);
	$wGrossA=$wNetA=$wVolumeA=$wContainerA=null;
	if($baseUom==$uom){
		
		$wGrossA = retAm($conn, 10, $selected);

		$wNetA = retAm($conn, 20, $selected);

		$wVolumeA = retAm($conn, 30, $selected);

		$wContainerA = retAm($conn, 40, $selected);

		if($wGrossA){$wGrossA=floatval($wGrossA);}
		if($wNetA){$wNetA=floatval($wNetA);}
		if($wVolumeA){$wVolumeA=floatval($wVolumeA);}
		if($wContainerA){$wContainerA=floatval($wContainerA);}

		?>
		<script>

		$(document).ready(function(){

			calculateFee2();

			$('#amount').on('keyup change', function(){

			calculateFee2();
			}); 

			function calculateFee2(){

				var qty=$("#amount");
				   
				<?php 
					if($wGrossA){ ?>
						total1=null;
						total1=isNaN(parseInt(qty.val()* <?=$wGrossA;?>)) ? 0 :(qty.val()* <?=$wGrossA;?>);
						$("#gross").val(total1);
					<?php
					}
					if($wNetA){
					?>
						total2=null;
						total2=isNaN(parseInt(qty.val()* <?=$wNetA;?>)) ? 0 :(qty.val()* <?=$wNetA;?>);
						$("#net").val(total2);
					<?php
					}
					if($wVolumeA){
					?>
						total3=null;
						total3=isNaN(parseInt(qty.val()* <?=$wVolumeA;?>)) ? 0 :(qty.val()* <?=$wVolumeA;?>);
						$("#cubicMeters").val(total3);
					<?php
					}
					if($wContainerA){
					?>
						total4=null;
						total4=isNaN(parseInt(qty.val()* <?=$wContainerA;?>)) ? 0 :(qty.val()* <?=$wContainerA;?>);
						$("#tare").val(total4);
					<?php } 
						
				?>
				
			}
			
		});

		</script>
		<?
	}
}



if($view=='tare'){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly" placeholder="tara" id="tare" name="tare" value="0" '.$disabled.'>
		</div>
	';
}

if($view=='gross'){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly" placeholder="bruto" id="gross" name="gross" value="0" '.$disabled.'>
		</div>
	';
}

if($view=='net'){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly" placeholder="neto" id="net" name="net" value="0" '.$disabled.'>
		</div>
	';
}

if($view=='cubicMeters'){

	echo '
		<div class="">
			<input type="text" class="form-control numbersOnly" placeholder="m3" id="cubicMeters" name="cubicMeters" value="0" '.$disabled.'>
		</div>
	';
}


















?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<?php
die(0);
if($selected=='none'){$selected = '';}			
			echo ' 
		<div class="table-responsive">
		<table id="two" class="table table-hover table-responsive">
		<thead> 
			<tr>
				<th>produkta nr</th>
				<th>nosaukums</th>
				<th>noliktava</th>
				<th>daudzums</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';

		$lines = mysqli_query($conn, "SELECT * FROM additional_services_line WHERE docNr='".mysqli_real_escape_string($conn, $selected)."' AND status='20'");
		$r=0;
		while($rowL = mysqli_fetch_array($lines)){
		$r++;
		
			echo '<tr class="classlistedit" id="mTwo'.$rowL['id'].'">
					<td>'.$rowL['productNr'];
					
					echo '
					<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$selected.'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
						i
					</a>';						
											
					
					$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
					$gNrow = mysqli_fetch_array($getNames);
					echo '
					<td>'.$gNrow['name1'].' '.$gNrow['name2'].'	('.$gNrow['unitOfMeasurement'].')	
					<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
					<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
					<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
					onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 100px; border-radius: 5px;" class="forChangeTwo" value="'.floatval($rowL['amount']).'"></div>	
					<td><div class="checkbox 2">
					
					<input type="hidden" class="changeInputsTwo"  value="'.$rowL['id'].'" style="width: 50px;">
					<input type="hidden" class="productNrTwo"  value="'.$rowL['productNr'].'" style="width: 50px;">
					<input type="hidden" class="amountTwo"  value="'.floatval($rowL['amount']).'" style="width: 50px;">
					<input type="hidden" class="enteredDateTwo"  value="'.$rowL['enteredDate'].'" style="width: 50px;">
					<input type="hidden" class="locationTwo"  value="'.$rowL['location'].'" style="width: 50px;">
					<input type="hidden" class="statusTwo"  value="'.$rowL['status'].'" style="width: 50px;">		
					<i class="glyphicon glyphicon-transfer"></i></div>								
				</tr>';
			
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