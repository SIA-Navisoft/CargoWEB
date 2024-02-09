<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="movement";



require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

$view = $action = $section = $id = $query = $searchquery= null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['ser'])){$ser = htmlentities($_GET['ser'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU
if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/movement/movement.php<?=$glpage;?>');
    });
})
</script>

<script>
$(function() {
	$(".paging").delegate("a", "click", function() {	
		var url = $(this).attr('href');
		
		$('#contenthere').load('/pages/movement/'+url);
		event.preventDefault();
	});
});
</script>

<?php
if(!$view){
?>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/movement/movement.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$('#contenthere').load('/pages/movement/movement.php?client='+val+'');
}
</script>
	<script>
	$('#removeFilter').on('click', function() {
		$('#contenthere').load('/pages/movement/movement.php');
	});
	</script>
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success" >saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">darbības</p>';
		
		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilter"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-4 pull-right">
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			  
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header WHERE (status='20')") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			<div class="clearfix"></div>
			';		
		
	if($client){$filterClient = ' AND clientCode="'.$client.'"';}else{$filterClient = null;}

	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(select COUNT(id) from cargo_line WHERE docNr=cargo_header.docNr AND action='23') AS acount FROM cargo_header WHERE (status='20' || rowSent='1') ".$filterClient." ORDER BY acount DESC, deliveryDate";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>piegādes dat.</th>
						
						<th>pavadzīmes nr.</th>
						<th>pieņemšanas akta nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>īpašnieka kods - nosaukums</th>
						<th>statuss</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				if($row['acount']>0){$acount=' bgcolor="yellow"';}else{$acount=null;}
				echo '	<tr class="classlistedit" onclick="newDoc('.$row['id'].')" '.$acount.'>
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							
							echo '
							<td>'.$row['ladingNr'].'
							<td>'.$row['acceptance_act_no'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['ownerCode'].' - '.$row['ownerName'].'
							<td>'.returnCargoStatus($conn, $row['id']).'
						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
}
if($view=='edit'){
	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
		</div>';
		if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
		if ($res=="del"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">izdzēsts!</div></div>';}
		if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
		if ($res=="approved"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">apstiprināts!</div></div>';}
		if ($res=="received"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saņemts!</div></div>';}
		if ($res=="changed"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">pārvietots!</div></div>';}
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';
			  
	echo '</div>';	
	$GetXY = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$GXYrow = mysqli_fetch_array($GetXY);
	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	echo '<p style="display:inline-block;">pārvietot</p> <div id="printchatbox" style="display:inline-block; font-weight: bold;">'.$cCode.'</div>';

	$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	$rowSent = $row['rowSent'];
	if($status>0 || $rowSent){$disabled='disabled';}else{$disabled=null;}	
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
	$('#send_profile').submit(function(event) {
		event.preventDefault();

		$.ajax({
			global: false,
			type: 'POST',
			url: '/pages/movement/post.php?r=edit&id=<?=$id;?>',
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
				clientName: $("#clientName").val(),
				receiverCode: $("#receiverCode").val(),
				receiverName: $("#receiverName").val(),					
				productNr: $("#productNr").val(),
				amount: $("#amount").val(),
				agreements: $("#agreements").val(),
				transportNo: $("#transportNo").val(),
				location: $("#location").val()
			},
			beforeSend: function(){
				
				$("#savebtn").click(function(e) {
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
				});
			},			
			success: function (result) {
				console.log(result);
				var productNr = $("#productNr").val();
				var amount = $("#amount").val();

				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=done');
				
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	});	  
	</script>

<script>
$(document).ready(function () {
    $('#change_storage').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/movement/post.php?r=changeStorage&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				
				$('#changebtn').html("<i class='glyphicon glyphicon-move' style='color: black;'></i> gaidiet...");
				$("#changebtn").prop("disabled",true);

			},			
            success: function (data) {
				console.log(data);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=changed');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>		

	<script>	  
	function delLine(val) {
		event.preventDefault();

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/movement/action.php?action=deleteLine&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=del');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function approveLine(val) {
		event.preventDefault();

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/movement/action.php?action=approveLine&id='+val+'',
			beforeSend: function(){
				$('#approveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=approved');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function receiveLine(val) {
		event.preventDefault();

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/movement/action.php?action=receiveLine&id='+val+'',
			beforeSend: function(){
				$('#receiveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=received');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
	
	<script>
	$('#clientCode').on('change', function() {
		var typez = $('#clientCode option:selected').attr('data-name');
		$('#clientName').val(typez);
	});
	</script>
	
	<script>
	$('#receiverCode').on('change', function() {
		var typer = $('#receiverCode option:selected').attr('data-rname');
		$('#receiverName').val(typer);
	});
	</script>	
	
	<script>
	$('#ownerCode').on('change', function() {
		var typex = $('#ownerCode option:selected').attr('data-oname');
		$('#ownerName').val(typex);	
	});
	</script>	

<script>
$('#clientCode').on('change', function() {

	var typez = $('#clientCode option:selected').attr('data-cnid');

	$( "#secondOption" ).load( "pages/movement/change.php?select="+typez+"" );
		
});
</script>	
	<?php
	echo '
	<form id="send_profile">
	  <div class="form-row">';
	  
	  
	  
	  
	  echo '
		  <div class="form-group col-md-3">
		  <label class="lb-sm">klienta kods - nosaukums</label>
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="klienta kods - nosaukums"  disabled>
			  <option></option>';
			$selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers") or die(mysqli_error($conn));
			while($rowc = mysqli_fetch_array($selectClient)){
				echo '<option data-cnid="'.$rowc['Code'].'" data-name="'.$rowc['Name'].'" value="'.$rowc['Code'].'"';
				if($row['clientCode']==$rowc['Code']){echo ' selected';}
				echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
			}
			
			echo '
			</select>	
		  </div>';				
		  
		  echo '		  
			  <div class="form-group col-md-3">
			  <label class="lb-sm" >īpašnieka kods - nosaukums</label>
			  
				  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="īpašnieka kods" disabled>
				  <option></option>';
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-oname="'.$rowc['Name'].'" value="'.$rowc['Code'].'"';
				  if($row['ownerCode']==$rowc['Code']){echo ' selected';}
				  echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
			  }
			  
			  echo '
			  </select>
			  </div>';

			  echo '
			  <div class="form-group col-md-3">
			  <label class="lb-sm">līgums</label>
				  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="līgums" disabled>
				  
			  ';
			  $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements") or die(mysqli_error($conn));
			  while($rowa = mysqli_fetch_array($selectAgreements)){
				  echo '<option  value="'.$rowa['contractNr'].'"';
				  if($row['agreements']==$rowa['contractNr']){echo ' selected';}
				  echo '>'.$rowa['contractNr'].' - '.$rowa['customerNr'];
				  if($rowa['customerName']){echo '('.$rowa['customerName'].')';}
			  echo '</option>';
			  }
				  echo '
			  </select>		
			  </div>			
		 ';
		  
		  echo '
			  <div class="form-group col-md-3">	
			  <label>pakalpojums</label>			
				  
				  <select class="form-control selectpicker btn-group-xs" data-live-search="true" title="pakalpojums" disabled>';	
				  $selectResource = mysqli_query($conn, "
					  SELECT r.id, r.name 
					  
					  FROM agreements_lines AS a 
					  LEFT JOIN n_resource AS r
					  ON a.service=r.id
					  WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on'
					  GROUP BY a.service
				  ") or die (mysqli_error($conn));
				  while($rowi = mysqli_fetch_array($selectResource)){
					  echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
						  if($row['resource']==$rowi['id']){ echo ' selected';}
					  echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
				  }
				  
				  echo '
				  </select>					
			  </div>';





	  echo '
	  <div class="form-group col-md-3">
		  <label class="lb-sm">pieteikuma dat.</label>
		  <input type="text" class="form-control input-xs" placeholder="pieteikuma dat." value="'.date('d.m.Y', strtotime($row['application_date'])).'" disabled>
	  </div>

	  <div class="form-group col-md-3">
	  <label class="lb-sm">pieteikuma nr.</label>
	  <input type="text" class="form-control input-xs" placeholder="pieteikuma nr." value="'.$row['application_no'].'" disabled>
	  </div>

	  <div class="form-group col-md-3">
		  <label class="lb-sm">pavadzīmess dat.</label>
		  <input type="text" class="form-control input-xs" placeholder="pavadzīmes dat." value="'.date('d.m.Y', strtotime($row['landingDate'])).'" disabled>
	  </div>

	  <div class="form-group col-md-3">
		<label class="lb-sm">pavadzīmes nr</label>
		<input type="text" class="form-control input-xs" placeholder="pavadzīmes nr" value="'.$row['ladingNr'].'" disabled>
	  </div>

	  <div class="form-group col-md-3">
		<label class="lb-sm">piegādes dat.</label>
		<input type="text" class="form-control datepicker input-xs" placeholder="piegādes dat." value="'.date('d.m.Y', strtotime($row['deliveryDate'])).'" disabled>
	  </div>	  

	  ';

	  echo '
		  <div class="form-group col-md-3">
		  <label class="lb-sm">kravas tips</label>
			  <select class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="kravas tips"  disabled>
			  <option></option>';
			$selectType = mysqli_query($conn, "SELECT code FROM cargo_code WHERE status='1'") or die(mysqli_error($conn));
			while($rowt = mysqli_fetch_array($selectType)){
				echo '<option value="'.$rowt['code'].'"';
				if($row['deliveryCode']==$rowt['code']){echo ' selected';}
				echo '>'.$rowt['code'].'</option>';
			}
			
			echo '
			</select>	
		  </div>';			
		



		  echo '
		  
		  <div class="form-group col-md-2">
		  <label class="lb-sm">transporta veids</label>
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="transporta veids" disabled>
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

		  echo '
		  <div class="form-group col-md-3">
			  <label class="lb-sm">kuģis</label>
			  <input type="text" class="form-control input-xs" placeholder="kuģis" value="'.$row['ship'].'" disabled>
		  </div>
		  <div class="clearfix"></div>
		  ';


echo ' 
<div class="form-group col-md-3">
<label class="lb-sm">pieņemšanas akta nr.</label>
<input type="text" class="form-control input-xs" placeholder="pieņemšanas akta nr."  value="'.$row['acceptance_act_no'].'" disabled>
</div>';

echo ' 
<div class="form-group col-md-3">
									  
<div class="checkbox">
   <label><input type="checkbox" id="copyRest" name="copyRest"'; if($row['copyRest']==1){echo ' checked';} echo ' disabled>dublēt līnijas vērtības(izņ. seriālo nr.)</label>
</div>
</div>';
			
		echo '<div class="clearfix"></div>';		
		if($isDocNr){

			
			$inCargo = mysqli_query($conn, "
							SELECT SUM(amount) as amount, SUM(volume) as volume, 
							SUM(tare) as t, SUM(gross) as b, SUM(net) as n, SUM(cubicMeters) as a,
							productUmo, productNr FROM cargo_line WHERE docNr='".$isDocNr."' GROUP BY productUmo") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			
			echo '<p style="display:inline-block;">daudzumi</p>';
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive">
						<thead>
							<th>mērvienība</th>
							<th>daudzums</th>
							<th>tara (kg)</th>
							<th>bruto (kg)</th>
							<th>neto (kg)</th>
							<th>apjoms (m3)</th>
						</thead>
						<tbody>
						';			
			while($icRow = mysqli_fetch_array($inCargo)){				
				echo '

						
							<tr>
								<td>'.$icRow['productUmo'].'</td>
								<td>'.floatval($icRow['amount']).'</td>
								<td>'.floatval($icRow['t']).'</td>
								<td>'.floatval($icRow['b']).'</td>
								<td>'.floatval($icRow['n']).'</td>
								<td>'.floatval($icRow['a']).'</td>
								
							</tr>

				';
			
			}
				echo '
						</tbody>
					</table>
				</div>				
				';
				
			}
		}		
			
			
		if($GXYrow['clientName']){$cName=$GXYrow['clientName'];}else{$cName=null;}
		if($GXYrow['ownerName']){$oName=$GXYrow['ownerName'];}else{$oName=null;}		
		echo '

	  </div>
       <input type="hidden" class="form-control" id="clientName" value="'.$cName.'">
	   <input type="hidden" class="form-control" id="ownerName" value="'.$oName.'">
	   <input type="hidden" class="form-control" id="cargoCode" value="'.$cCode.'">
	   <input type="hidden" class="form-control" id="receiverName" value="'.$rCode.'">

	  <div class="clearfix"></div>';

	echo '
	<a href="/pages/receipt/status.php?id='.$id.'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
		<i class="glyphicon glyphicon-option-vertical"></i> statuss
	</a>  
	  
	  <div class="clearfix"></div>
	';

	echo '</form>';

// Apstiprināšanas tabula
 
		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$row['docNr']."' AND action='23'");

		if(mysqli_num_rows($lines)>0){
?>

<script>
	function cancelLine(event) {
		event.preventDefault();
		
		var val = $(event.target).data("col");


		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/movement/action.php?action=cancelLine&id=<?=$id;?>&lineId='+val,
			beforeSend: function(){
				$('#cancelLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=canceled');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}
	<?php if($p_edit=='on'){ ?>
	function moveLine(event) {
		event.preventDefault();

		var val = $(event.target).data("rl");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/movement/action.php?action=moveLine&id=<?=$id;?>&lineId='+val,
			beforeSend: function(){
				$('#moveLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#moveLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/movement/movement.php?view=edit&id=<?=$id;?>&res=approved');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}
	<?php } ?>		
</script>

<?php
			echo '
			<br>	
			<div class="panel panel-default">
			<p style="display:inline-block;">nepieciešama apstiprināšana</p>
				<div class="panel-body">
				<form id="">
					<div class="table-responsive">
					<table class="table-responsive"><table class="table table-hover table-responsive">
					<thead> 
						<tr>
							<th>produkta nr</th>
							<th>pakalpojums</th>
							<th>partijas nr.</th>
							<th>no noliktavas</th>
							<th>uz noliktavu</th>
							<th>daudzums</th>
							<th>daudzums pārvietošanai</th>
							<th>mērvienība</th>
							<th>palīg mērv. daudzums</th>
							<th>palīg mērv. daudz. pārv.</th>
							<th>palīg mērvienība</th>							
							';
							
			
							echo '<th>darbība</th>';
							
						echo '	
						</tr>
					</thead>
					<tbody>';

			$r=0;
			while($rowL = mysqli_fetch_array($lines)){
			

				echo '	<tr bgcolor="#eee" class="classlistedit">
							<td>'.$rowL['productNr'].'<br>';
							$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
							$gNrow = mysqli_fetch_array($getNames);
							echo $gNrow['name1'].' '.$gNrow['name2'];
							echo '
							<td>'.$rowL['issue_resource'].'
							<td>'.$rowL['batchNo'].'
							<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'
							<td>'.$rowL['issue_location'].' - '.returnLocationName($conn, $rowL['issue_location']).'						
							<td>'.floatval($rowL['amount']).'
							<td>'.floatval($rowL['issueAmount']).'
							<td>'.$rowL['productUmo'].'
							<td>'.floatval($rowL['assistant_amount']).'
							<td>'.floatval($rowL['issue_assistant_amount']).'
							<td>'.$rowL['assistantUmo'];							
							

							echo '<td nowrap>';

								echo '<a class="btn btn-default btn-xs" style="margin-right: 2px;" data-col="'.$rowL['id'].'" id="cancelLine'.$rowL['id'].'" onclick="cancelLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt</a>';	
							if($p_edit=='on'){
								echo '<a class="btn btn-default btn-xs" data-rl="'.$rowL['id'].'" id="moveLine'.$rowL['id'].'" onclick="moveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> apstiprināt</a>';
							}
							
						echo '						
						</tr>';			

						
				
			}

			echo '</tbody>
			</table>
			
			</form>
			</div>
			</div>
		</div>';

		}

// Apstiprināšanas tabula beidzas

	echo '
<br>	
<div class="panel panel-default">
	<div class="panel-body">
	<form id="change_storage">
		<div class="table-responsive">
		<table class="table-responsive"><table class="table table-hover table-responsive">
		<thead> 
			<tr>
				<th>produkta nr</th>
				<th>partijas nr.</th>
				<th>noliktava</th>
				<th>daudzums</th>
				<th>mērvienība</th>';

					echo'
					
					<th>daudzums pārvietošanai</th>


					<th>palīg mērv. daudzums</th>
					<th>palīg mērvienība</th>
					<th>palīg mērv. daudz. pārv.</th>	

					<th></th>
					<th>jaunā noliktava</th>';

				echo '<th>statuss</th>';
				
			echo '	
			</tr>
		</thead>
		<tbody>';
 
		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$row['docNr']."' AND action!='23' AND action!='27'");
		$r=0;
		while($rowL = mysqli_fetch_array($lines)){
		
		if($rowL['status']!=20){
			echo '	<tr bgcolor="#eee" class="classlistedit">
						<td>'.$rowL['productNr'].'<br>';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];
						echo '
						
						<td>'.$rowL['batchNo'].'
						<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
						<td>'.floatval($rowL['amount']).'
						<td>'.$rowL['productUmo'].'
						<td>'.floatval($rowL['assistant_amount']).'
						<td>'.$rowL['assistantUmo'];						
						echo '<td>';
						
						
						
						echo '
						<td>
							<input type="text" class="form-control input-xs numbersOnly" value="'.floatval($rowL['amount']).'" style="width: 200px !important;" disabled> 						
						
						<td colspan="2">
						
							
						  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="noliktava" disabled>
						  ';
						  echo '<option  value=""></option>';
						  $selectLocationMove = mysqli_query($conn, "SELECT id, name FROM n_location WHERE id!='".$rowL['location']."'") or die(mysqli_error($conn));
						  while($rowm = mysqli_fetch_array($selectLocationMove)){
							  echo '<option  value="'.$rowm['id'].'">'.$rowm['id'].' - '.$rowm['name'].' </option>';
						  }
							echo '
						  </select>	';
					
					echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
						  
					echo '						
					</tr>';			
		}else{
		$r++;
			
			echo '	<tr class="classlistedit">
						<td>'.$rowL['productNr'].'<br>';
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						echo $gNrow['name1'].' '.$gNrow['name2'];
						echo '
						<input type="hidden" name="lineId[]" value="'.$rowL['id'].'"> 
						<input type="hidden" name="orgAmount[]" value="'.floatval($rowL['amount']).'">
						<input type="hidden" name="orgAmountExtra[]" value="'.floatval($rowL['assistant_amount']).'"> 
						<input type="hidden" name="docNr[]" value="'.$rowL['docNr'].'"> 
						<input type="hidden" name="productNr[]" value="'.$rowL['productNr'].'"> 
						<input type="hidden" name="orgLocation[]" value="'.$rowL['location'].'">
						

						<input type="hidden" name="mThisDate[]" value="'.date('d.m.Y', strtotime($rowL['thisDate'])).'">
						<input type="hidden" name="mThisTransport[]" value="'.$rowL['thisTransport'].'">
						<input type="hidden" name="mVolume[]" value="'.$rowL['volume'].'">

						<td>'.$rowL['batchNo'].'
						<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
						<td>'.floatval($rowL['amount']).'
						<td>'.$rowL['productUmo'];
									
						echo '
						<td>
							<input type="hidden" name="productUmo[]" value="'.$rowL['productUmo'].'">
							<input type="hidden" name="batchNo[]" value="'.$rowL['batchNo'].'">
							<input 
							type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
							onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" 
							class="form-control input-xs numbersOnly" name="newAmount[]" value="'.floatval($rowL['amount']).'" style="width: 120px !important;"> 	

					

						<td>'.floatval($rowL['assistant_amount']).'
						<td>'.$rowL['assistantUmo'].'					
						<td>
							<input 
							type="number" min="0" max="'.floatval($rowL['assistant_amount']).'" step="0.01"
							onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<0){this.value=\'0\';}"
							class="form-control input-xs numbersOnly" name="newAmountExtra[]" value="'.floatval($rowL['assistant_amount']).'" style="width: 120px !important;">  							

						<td>

						<td>
						
						  <select class="form-control selectpicker btn-group-xs input-xs"  name="changeLocation[]"  data-live-search="true" title="noliktava">
						  ';
						  echo '<option  value=""></option>';
						  $selectLocationMove = mysqli_query($conn, "SELECT id, name FROM n_location WHERE id!='".$rowL['location']."'") or die(mysqli_error($conn));
						  while($rowm = mysqli_fetch_array($selectLocationMove)){
							  echo '<option  value="'.$rowm['id'].'">'.$rowm['id'].' - '.$rowm['name'].' </option>';
						  }
							echo '
						  </select>	';
					
					echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
						  
					echo '						
					</tr>';
		}
					
			
		}
		
			echo '<input type="hidden" name="lResult" value="'.$r.'"> ';
		
		echo '<tr>
		<td colspan="8" align="right">
		
		
		<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="pakalpojums" name="resourceNr">';	
		$selectResource = mysqli_query($conn, "
			SELECT r.id, r.name
			FROM n_resource AS r
			LEFT JOIN agreements_lines AS al
			ON r.id=al.service
			WHERE al.contractNr='".$row['agreements']."' AND al.deleted!='1'  AND al.keeping!='on' AND al.deleted='0' AND al.extra_resource!='on'
			GROUP BY r.id			
		");
		while($rowi = mysqli_fetch_array($selectResource)){
			echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
				if($rowL['resource']==$rowi['id']){echo ' selected';}
			echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
		}
		
		echo '
		</select>		
		
		
		<td><input type="text" class="form-control datepicker input-xs" name="actionDate" value="'.date('d.m.Y').'" style="width: 200px !important;">
		<td>
		<td>
		
		  <select class="form-control selectpicker btn-group-xs input-xs"  name="changeLocationAll"  data-live-search="true" title="noliktava">
		  ';
		  echo '<option  value=""></option>';
		  $selectLocationMove = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
		  while($rowm = mysqli_fetch_array($selectLocationMove)){
			  echo '<option  value="'.$rowm['id'].'">'.$rowm['id'].' - '.$rowm['name'].' </option>';
		  }
			echo '
		  </select>';			
		// ORĢINĀLI PĀRVIETOT POGA
		echo '<td><button type="submit" class="btn btn-default btn-xs" id="changebtn"><i class="glyphicon glyphicon-move" style="color: black;"></i> nodot</button>
		</tr>';
		
	echo '</tbody>
	</table>
	
	</form>
	</div>
	</div>
</div>';
	
}	

?>
			<?php if($id){ ?>
			<br><br>
			<div class="panel panel-default" style="margin-bottom: -5px;">
				<div class="panel-body">
					<div style="float: right; margin: -10px;">pēdējās aktivitātes veica: <?=lastActionBy($conn, $id);?></div>
				</div>
			</div>
			<?php } ?>

<div class="modal fade" id="showStatus" tabindex="-1" role="dialog" aria-labelledby="showStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  
      <div class="modal-body">
        
      </div>

    </div>
  </div>
</div>	

<script>
// Fill modal with content from link href
$("#showStatus").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
});
</script>

<script>
    $(document).ready(function() {
        $('.numbersOnly').keypress(function (event) {
            return isNumber(event, this)
			
        });
    });
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    } 
</script>
<script>
setTimeout(function() {
    $('#hideMessage').fadeOut('fast');
}, 3000);
</script>
<script>
$('#savebtn').on('click', function() {
	$("#productNr").prop('required',true);
	$("#amount").prop('required',true);
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?> 