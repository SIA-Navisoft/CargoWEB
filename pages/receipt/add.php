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

$(document).ready(function () {
    $('#send_profile').on('submit', function(e) {
        e.preventDefault();
		
		
        $.ajax({
            
						global: false,
            type: "POST",
						url : '/pages/receipt/post.php?r=add',
            data: $(this).serializeArray(),
			
					beforeSend: function(){
						$('#savebtn').html("gaidiet...");
						$("#savebtn").prop("disabled",true);
					},		
					success: function (data) {
							console.log(data);
						  $('#contenthere').load('/pages/receipt/receipt.php?view=edit&id='+data);
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
        $('#contenthere').load('/pages/receipt/receipt.php');
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
$('#senderCode').on('change', function() {
var typex = $('#senderCode option:selected').attr('data-sname');
            $('#senderName').val(typex);	
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
		$( "#resourceOption" ).load( "/pages/receipt/change.php?view=sort_resource&select="+typez+"" );
		
});

function selAgreement(val) {

var val = val.value;
var val = encodeURI(val);

$( "#resourceOption" ).load( "/pages/receipt/change.php?view=sort_resource&resource="+val+"" );

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
						echo '<option value="'.$rowa['contractNr'].'">'.$rowa['contractNr'].' - '.$rowa['customerNr'].'</option>';
					}
					?>
					</select>	
				</div>
			</div>
			
			<div class="form-group col-md-3">
			<label class="lb-sm" for="senderCode">nosūtītāja kods - nosaukums</label>
			
				<select class="form-control selectpicker btn-group-xs input-xs"  id="senderCode" name="senderCode"  data-live-search="true" title="nosūtītāja kods - nosaukums">
					<option></option>
				<?php
				$selectOwner = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, name FROM senders WHERE status='1'") or die(mysqli_error($conn));
				while($rowo = mysqli_fetch_array($selectOwner)){
					echo '<option data-sname="'.$rowo['name'].'" value="'.$rowo['Code'].'">'.$rowo['Code'].' - '.$rowo['name'].'</option>';
				}
				?>
				</select>	
			</div>			
			
			<div id="resourceOption">
				<div class="form-group col-md-3">	
				<label for="resource">pakalpojums</label>			
					<select class="form-control selectpicker btn-group-xs" id="resource"  data-live-search="true" title="pakalpojums" name="resource">
					</select>
				</div>			
			</div>	  
  
  
  
  
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

    <div class="form-group col-md-3">
      <label class="lb-sm" for="deliveryDate">piegādes dat.</label>
      <input type="text" class="form-control datepicker input-xs" id="deliveryDate" name="deliveryDate" value="<?=date('d.m.Y');?>">
    </div>


<?php
		echo '
			<div class="form-group col-md-3">
			<label class="lb-sm" for="deliveryCode">kravas tips</label>
				<select class="form-control selectpicker btn-group-xs input-xs"  id="deliveryCode" name="deliveryCode"  data-live-search="true" title="kravas tips">
					<option></option>';
			  $selectType = mysqli_query($conn, "SELECT code FROM cargo_code WHERE status='1'") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectType)){
				  echo '<option value="'.$rowt['code'].'">'.$rowt['code'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
?>
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
			</div>

			<div class="form-group col-md-3">
			  <label for="thisTransport">transporta nr.</label>
			  <input type="text" class="form-control" name="thisTransport" placeholder="transporta nr.">
			</div>
			<div class="clearfix"></div>
			
			<div class="form-group col-md-3">
				<label class="lb-sm"  for="shipId">kuģis izkraušanai</label>
				<input type="text" class="form-control input-xs" name="shipId" id="shipId" placeholder="kuģis izkraušanai">
			</div>			
			';
	
?>			
	
	<div class="form-group col-md-3">
			<label class="lb-sm"  for="acceptanceDate">pieņemšanas akta dat.</label>
			<input type="text" class="form-control datepicker input-xs" name="acceptanceDate" id="acceptanceDate" placeholder="pieņemšanas akta dat.">
	</div>

	<div class="form-group col-md-3">
      	<label class="lb-sm" for="acceptanceNr">pieņemšanas akta nr.</label>
      	<input type="text" class="form-control input-xs" id="acceptanceNr" name="acceptanceNr" placeholder="pieņemšanas akta nr.">
	</div>

    <div class="form-group col-md-3">
      <label for="declarationTypeNo">deklarācijas nr.</label>
      <input type="text" class="form-control" name="declarationTypeNo" id="declarationTypeNo" placeholder="deklarācijas nr.">
    </div>	

	
	
	 <div class="form-group col-md-3">
	  <label class="lb-sm"  for="cargoStatus">kravas status</label>
		<select class="form-control selectpicker btn-group-xs input-xs"  id="cargoStatus" name="cargoStatus"  data-live-search="true" title="kravas status">
		<option value="C">C - eksports</option>
		<option value="N">N - tranzīts</option>
		<option value="EU">EU</option>
		</select>	
	 </div>	
	
	
	<div cless="clearfix"></div>
	<div class="form-group col-md-3">										
		<div class="checkbox">
			<label><input type="checkbox" id="copyRest" name="copyRest" checked>dublēt līnijas vērtības(izņ. seriālo nr.)</label>
		</div>
	</div> 


			

       <input type="hidden" class="form-control" id="clientName" name="clientName">
	   <input type="hidden" class="form-control" id="ownerName" name="ownerName">
	   <input type="hidden" class="form-control" id="senderName" name="senderName">
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