<?php
require('../../inc/s.php');
include('../../functions/base.php');
$res = null;
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}


		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-sm" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default active classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a> 
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
echo '<p style="display:inline-block;">pievienošana</p> <div id="printchatbox" style="display:inline-block; font-weight: bold;"> </div>';
$idR=findLastRow("cargo_header");
?>
<script>

var inputBox = document.querySelector('#ladingNr');
var dropDown = document.querySelector('#clientCode');
dropDown.addEventListener("change", myFunctionXY, false);
inputBox.addEventListener("keyup", myFunctionXY, false);

function myFunctionXY() {
  var ibVal = inputBox.value;
  var ddSel = $('#clientCode').find('option:selected').val();
  if(ibVal && ddSel){
	  var newI = ibVal +""+ ddSel;
	  document.getElementById('printchatbox').innerHTML = newI;
	  $('#cargoCode').val(newI); 
  }else{
	  document.getElementById('printchatbox').innerHTML = '';
	  $('#cargoCode').val(); 	  
  }
}
</script>
<script>
$('#clientCode').on('change', function() {
   
	var typez = $('#clientCode option:selected').attr('data-name');
    $('#ownerName').val(typez);
 
});
</script>
 
<script>	  
$('#send_profile').submit(function(event) {
    event.preventDefault();

    $.ajax({
        global: false,
        type: 'POST',
        url: '/pages/receipt/post.php?r=add',
        data: {
            docNr: $("#docNr").val(),
			cargoCode: $("#cargoCode").val(),
			ladingNr: $("#ladingNr").val(),
			deliveryDate: $("#deliveryDate").val(),
			deliveryType: $("#deliveryType").val(),
			deliveryCode: $("#deliveryCode").val(),
            clientCode: $("#clientCode").val(),
            ownerCode: $("#ownerCode").val(),
			ownerName: $("#ownerName").val(),
			receiverCode: $("#receiverCode").val(),
			receiverName: $("#receiverName").val(),
			clientName: $("#clientName").val(),
			location: $("#location").val()
			
        },
		beforeSend: function(){
			$('#savebtn').html("gaidiet...");
			$("#savebtn").prop("disabled",true);
		},		
        success: function (result) {
            console.log(result);
			$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$idR+1;?>');
        },
        error: function (request, status, error) {
            serviceError();
        }
    });
});	  
</script>
<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/receipt/receipt.php');
    });
})
</script>
<form id="send_profile">
  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="deliveryDate">piegādes dat.</label>
      <input type="text" class="form-control datepicker" id="deliveryDate" placeholder="piegādes dat.">
    </div>
    <div class="form-group col-md-3">
      <label for="ladingNr">pavadzīmes nr.</label>
      <input type="text" class="form-control" id="ladingNr" placeholder="pavadzīmes nr">
    </div>	
    <div class="form-group col-md-3">
      <label for="deliveryType">kravas tips</label>
      <input type="text" class="form-control" id="deliveryType" placeholder="kravas tips">
    </div>	
    <div class="form-group col-md-3">
      <label for="deliveryCode">kravas kods</label>
      <input type="text" class="form-control" id="deliveryCode" placeholder="kravas kods">
    </div>

  </div>
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

		$( "#secondOption" ).load( "/pages/receipt/change.php?select="+typez+"" );
		
});
</script>	
  <div class="form-row">
			<div class="clearfix"></div>
			<div class="form-group col-md-4">
			<label for="clientCode">klienta kods - nosaukums</label>
			  <select class="form-control selectpicker"  id="clientCode"  data-live-search="true" title="klienta kods - nosaukums">
			  <?php
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName, id FROM n_customers") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-cnid="'.$rowc['id'].'" data-name="'.$rowc['clientName'].'" value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' - '.$rowc['clientName'].'</option>';
			  }
				?>
			  </select>	
			</div>			

			<div class="form-group col-md-4">
			<label for="ownerCode">īpašnieka kods - nosaukums</label>
			<div id="secondOption">
			  <select class="form-control selectpicker"  id="ownerCode"  data-live-search="true" title="īpašnieka kods - nosaukums">
			  <?php
			  $selectOwner = mysqli_query($conn, "SELECT DISTINCT(ownerCode) AS ownerCode, ownerName FROM n_customers") or die(mysqli_error($conn));
			  while($rowo = mysqli_fetch_array($selectOwner)){
				  echo '<option data-oname="'.$rowo['ownerName'].'" value="'.$rowo['ownerCode'].'">'.$rowo['ownerCode'].' - '.$rowo['ownerName'].'</option>';
			  }
				?>
			  </select>	
			</div>	
			</div>
			
			<div class="form-group col-md-4">
			<label for="receiverCode">saņēmēja kods - nosaukums</label>
			  <select class="form-control selectpicker"  id="receiverCode"  data-live-search="true" title="saņēmēja kods - nosaukums">
			  <?php
			  $selectReceiver = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName, id FROM n_customers") or die(mysqli_error($conn));
			  while($rowr = mysqli_fetch_array($selectReceiver)){
				  echo '<option data-rname="'.$rowr['clientName'].'" value="'.$rowr['clientCode'].'">'.$rowr['clientCode'].' - '.$rowr['clientName'].'</option>';
			  }
				?>
			  </select>	
			</div>			
			
			<div class="form-group col-md-2">
			<label for="location">noliktava</label>
			  <select class="form-control selectpicker"  id="location"  data-live-search="true" title="noliktava">
			  <?php
			  $selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
			  while($rowl = mysqli_fetch_array($selectLocation)){
				  echo '<option  value="'.$rowl['id'].'">'.$rowl['id'].' - '.$rowl['name'].'</option>';
			  }
				?>
			  </select>	
			</div>			

       <input type="hidden" class="form-control" id="clientName">
	   <input type="hidden" class="form-control" id="ownerName">
	   <input type="hidden" class="form-control" id="receiverName">
	   <input type="hidden" class="form-control" id="cargoCode">

  </div>
  
  <div class="clearfix"></div>
  <button type="submit" class="btn btn-default" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>
</form>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?>