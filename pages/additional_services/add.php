<?php
require('../../inc/s.php');
include('../../functions/base.php');
$res = null;
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}


		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default active classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a> 
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
echo '<p style="display:inline-block;">pievienošana</p> <div style="display:inline-block; font-weight: bold;"> </div>';
$idR=findLastRow("additional_services_header");
?>

<script>
$('#clientCode').on('change', function() {
		var typez = $('#clientCode option:selected').attr('data-name');
            $('#ownerName').val(typez); 	
});




$(document).ready(function () {
    $('#send_profile').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            
						global: false,
            type: "POST",
						url : '/pages/additional_services/post.php?r=add',
            data: $(this).serializeArray(),
			
					beforeSend: function(){
						$('#savebtn').html("gaidiet...");
						$("#savebtn").prop("disabled",true);
					},		
					success: function (data) {
							console.log(data);
						  $('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id='+data);
					},
					error: function (request, status, error) {
							serviceError();
					}
        });
    });
});	  
</script>
<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/additional_services/additional_services.php');
    });
})
</script>
<form id="send_profile">
  <div class="form-row">

  
  
<script>
$('#clientCode').on('change', function() {
		var typez = $('#clientCode option:selected').attr('data-name');
            $('#clientName').val(typez);	
});
</script>
<script>
$('#ownerCode').on('change', function() {
var typex = $('#ownerCode option:selected').attr('data-oname');
            $('#ownerName').val(typex);	
});
</script>
<script>
$('#receiverCode').on('change', function() {
var typer = $('#receiverCode option:selected').attr('data-rname');
            $('#receiverName').val(typer);	
});
</script>
<script>
$('#clientCode').on('change', function() {
		var typez = $('#clientCode option:selected').attr('data-cnid');
		var agreements = $('#agreements option:selected').attr('data-cnid');

		$( "#secondOption" ).load( "/pages/additional_services/change.php?select="+typez+"" );
		$( "#resourceOption" ).load( "/pages/additional_services/change.php?view=sort_resource&select="+typez+"" );
		$( "#client_product_sel" ).load( "/pages/additional_services/change.php?view=client_product_sel&select="+typez+"&agreements="+agreements+"" );
		
});

function selAgreement(val) {

var val = val.value;
var val = encodeURI(val);

$( "#resourceOption" ).load( "/pages/additional_services/change.php?view=sort_resource&resource="+val+"" );

}


</script>	
  <div class="form-row">
			
			<div class="form-group col-md-3 ">
			<label class="lb-sm" for="clientCode">klienta kods - nosaukums</label>
			  <select class="form-control selectpicker btn-group-xs input-xs"  id="clientCode" name="clientCode"  data-live-search="true" title="klienta kods - nosaukums">
				<option></option>
			  <?php
			  $selectClient = mysqli_query($conn, "
					SELECT DISTINCT(c.Code) AS Code, c.Name 
					FROM n_customers AS c
					LEFT JOIN agreements AS a
					ON c.Code=a.customerNr 
					WHERE (a.dateTo='0000-00-00 00:00:00' OR a.dateTo IS NULL OR a.dateTo>CURDATE()) AND a.status<20 AND a.deleted='0'
				") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-cnid="'.$rowc['Code'].'" data-name="'.$rowc['Name'].'" value="'.$rowc['Code'].'">'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
			  }
				?>
			  </select>	
			</div>

			<div id="secondOption">
				
				<div class="form-group col-md-3">
				<label class="lb-sm" for="agreements">līgums</label>
					<select class="form-control selectpicker btn-group-xs input-xs"  id="agreements" name="agreements" onchange="selAgreement(this)" data-live-search="true" title="līgums">
						<option></option>
					<?php
					$selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr FROM agreements WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0'") or die(mysqli_error($conn));
					while($rowa = mysqli_fetch_array($selectAgreements)){
						echo '<option  value="'.$rowa['contractNr'].'">'.$rowa['contractNr'].' - '.$rowa['customerNr'].'</option>';
					}
					?>
					</select>	
				</div>
			</div>		
  
    <div class="form-group col-md-3">
      <label class="lb-sm" for="deliveryDate">akta datums</label>
      <input type="text" class="form-control datepicker input-xs" id="deliveryDate" name="deliveryDate" value="<?=date('d.m.Y');?>">
    </div>  
  
	<?php
	
	
	$queryi = mysqli_query($conn, "SELECT id FROM additional_services_header ORDER BY id DESC");
	if(mysqli_num_rows($queryi)>0){
		$rowi = mysqli_fetch_array($queryi);
		$fakeNo = $rowi['id']+1;
	}
	?>
  
	<div class="form-group col-md-3">
      	<label class="lb-sm" for="acceptanceNr">akta nr.</label>
      	<input type="text" class="form-control input-xs" id="acceptanceNr" name="acceptanceNr" placeholder="akta nr." value="<?=$fakeNo;?>">
	</div>  
  
  
  
  
  <div class="clearfix"></div>
  
  
  
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="applicationDate">pieteikuma dat.</label>
			<input type="text" class="form-control datepicker input-xs" name="applicationDate" id="applicationDate" placeholder="pieteikuma dat." value="'.date('d.m.Y').'">
		</div>

		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="applicationNo">pieteikuma nr.</label>
		  <input type="text" class="form-control input-xs" name="applicationNo" id="applicationNo" placeholder="pieteikuma nr.">
		</div>
	
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="landingDate">pavadzīmess dat.</label>
			<input type="text" class="form-control datepicker input-xs" name="landingDate" id="landingDate" placeholder="pavadzīmes dat." value="'.date('d.m.Y').'">
		</div>

    <div class="form-group col-md-3">
      <label class="lb-sm" for="ladingNr">pavadzīmes nr.</label>
      <input type="text" class="form-control input-xs" id="ladingNr" name="ladingNr" placeholder="pavadzīmes nr">
    </div>
<div class="clearfix"></div>

  </div>			
			

<?php
			echo '
			<div class="form-group col-md-3">
			<label class="lb-sm" for="deliveryType">transporta veids</label>
				<select class="form-control selectpicker btn-group-xs input-xs" name="transport" data-live-search="true" title="transporta veids">
				<option></option>';
			  $selectTypeT = mysqli_query($conn, "
			  SELECT transport FROM transport
			  WHERE status=1 ORDER BY transport") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectTypeT)){
				  echo '<option value="'.$rowt['transport'].'"';
				  if($rowt['transport']==$row['transport']){ echo ' selected';}
				 echo ' >'.$rowt['transport'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';

?>			

    <div class="form-group col-md-3">
      <label for="home_delegate">STENA LINE PORTS VENTSPILS pārstāvis</label>
      <input type="text" class="form-control" name="home_delegate" id="home_delegate" placeholder="STENA LINE PORTS VENTSPILS pārstāvis">
    </div>

    <div class="form-group col-md-3">
      <label for="client_delegate">klienta pārstāvis</label>
      <input type="text" class="form-control" name="client_delegate" id="client_delegate" placeholder="klienta pārstāvis">
    </div>	
	
    <div class="form-group col-md-3">
      <label for="cargo_name">kravas nosaukums</label>
      <input type="text" class="form-control" name="cargo_name" id="cargo_name" placeholder="kravas nosaukums">
    </div>
	<div class="clearfix"></div>

    <div class="form-group col-md-4">
      <label for="description">sastādīšanas apraksts</label>
	  <textarea class="form-control" rows="2" cols="50" id="description" name="description" placeholder="sastādīšanas apraksts"></textarea>
    </div>	

       <input type="hidden" class="form-control" id="clientName" name="clientName">
	   <input type="hidden" class="form-control" id="ownerName" name="ownerName">
	   <input type="hidden" class="form-control" id="receiverName" name="receiverName">
	   <input type="hidden" class="form-control" id="cargoCode" name="cargoCode"> 

  </div>
  
  <div class="clearfix"></div><br>
  <button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>
</form>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?>