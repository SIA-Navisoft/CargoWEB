<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="assembly";



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
if (isset($_GET['selCard'])){$selCard = htmlentities($_GET['selCard'], ENT_QUOTES, "UTF-8");}

include('../../functions/base.php');
require('../../inc/s.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU
if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>
<style>
input[readonly]
{
    background-color: silver;
}
</style>
<script>
$(document).ready(function(){
    $('.classadd').on('click',function(){
        $('#contenthere').load('/pages/assembly/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classlist').on('click',function(){
        $('#contenthere').load('/pages/assembly/assembly.php<?=$glpage;?>');
    });
})
</script>

<script>
$(function() {
	$(".paging").delegate("a", "click", function() {	
		var url = $(this).attr('href');

		$('#contenthere').load('/pages/assembly/'+url);
		
		event.preventDefault();
	});
});
</script>

<?php
if(!$view){
?>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$('#contenthere').load('/pages/assembly/assembly.php?client='+val+'');
}
</script>
	<script>
	$('#removeFilter').on('click', function() {
		$('#contenthere').load('/pages/assembly/assembly.php');
	});
	</script>

<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';

				
			echo '<a class="btn btn-default classadd btn-xs"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';

			echo '	
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
	(select COUNT(id) from cargo_line WHERE docNr=cargo_header.docNr AND action='27') AS acount FROM cargo_header WHERE (status='20' || rowSent='1') ".$filterClient." ORDER BY acount DESC, deliveryDate";  //NEPIECIEŠAMAIS VAICĀJUMS
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
			<a class="btn btn-default active classlist btn-xs" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
			
		echo '<a class="btn btn-default classadd btn-xs"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';

		echo ' 
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

	$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	$rowSent = $row['rowSent']; 
	if($status>0 || $rowSent==1){$disabled='disabled';}else{$disabled=null;}	
	?>

	<script>
$(document).ready(function () {
    $('#send_profile').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/assembly/post.php?r=edit&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				
				$("#changebtn").on('click',function(e) {
				$('#changebtn').html("gaidiet...");
				$("#changebtn").prop("disabled",true);
				});
			},			
            success: function (data) {
				console.log(data);
				var productNr = $("#productNr").val();
				var amount = $("#amount").val();

				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=done');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
		  
	</script>

<script>
$(document).ready(function () {
    $('#change_storage').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/assembly/post.php?r=changeStorage&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				
				$("#changebtn").on('click',function(e) {
				$('#changebtn').html("gaidiet...");
				$("#changebtn").prop("disabled",true);
				});
			},			
            success: function (data) {
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=changed');
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
			url: '/pages/assembly/action.php?action=deleteLine&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=del');
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
			url: '/pages/assembly/action.php?action=approveLine&id='+val+'',
			beforeSend: function(){
				$('#approveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=approved');
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
			url: '/pages/assembly/action.php?action=receiveLine&id='+val+'',
			beforeSend: function(){
				$('#receiveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=received');
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

	$( "#secondOption" ).load( "pages/assembly/change.php?select="+typez+"" );	
});
</script>	
<script>
$('#cargoList').on('change', function() {
	var typez = $('#cargoList option:selected').attr('data-cncc');

	$( "#secondOptionCargo" ).load( "pages/assembly/move.php?select="+typez+"" );
	$( "#toCargo" ).load( "pages/assembly/to.php?select="+typez+"&mainId=<?=$id;?>" );	
});
</script>	
	<?php

	


	echo '



<div class="col-lg-6">
	<div class="panel panel-default">
		<div class="panel-body"> 

	<form id="send_profile">
	  <div class="form-row">
		  <input type="hidden" id="docNr" name="docNr" value="'.$row['docNr'].'">
		  
		    <div class="table-responsive">
				<table class="table table-hover table-responsive">
					<thead><tr><th colspan="2"><p style="display:inline-block;">No</p> '.$cCode.'</th></tr></thead>
					<tbody>
						<tr>
							<td>piegādes datums:</td>
							<td>'.date('d.m.Y', strtotime($row['deliveryDate'])).'</td>
						</tr>
						<tr>
							<td>pavadzīmes nr:</td>
							<td>'.$row['ladingNr'].'</td>
						</tr>
						<tr>
							<td>kravas tips:</td>
							<td>'.$row['deliveryType'].'</td>
						</tr>
						<tr>
							<td>kravas kods:</td>
							<td>'.$row['deliveryCode'].'</td>
						</tr>
						<tr>
							<td>klienta kods - nosaukums:</td>
							<td>'.$row['clientCode'].'</td>
						</tr>
						<tr>
							<td>īpašnieka kods - nosaukums:</td>
							<td>'.$row['ownerCode'].'</td>
						</tr>
						<tr>
							<td>saņēmēja kods - nosaukums:</td>
							<td>'.$row['receiverCode'].'</td>
						</tr>
						<tr>
							<td>līgums:</td>
							<td>'.$row['agreements'].'</td>
						</tr>
						<tr>
							<td>transporta nr.:</td>
							<td>'.$row['transportNo'].'</td>
						</tr>
						<tr>
							<td>noliktava</td>
							<td>
								
									<select class="form-control selectpicker btn-group-xs input-xs"  id="location" name="location"  data-live-search="true" title="noliktava"';
									
									echo '>
									';
									$selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
									while($rowl = mysqli_fetch_array($selectLocation)){
										echo '<option  value="'.$rowl['id'].'"';
										if($row['location']==$rowl['id']){echo ' selected';}
										echo '>'.$rowl['id'].' - '.$rowl['name'].'</option>';
									}
										echo '
									</select>		
																
							</td>
						</tr>																														
					</tbody>
				</table>
			</div>';
		 


		if($GXYrow['clientName']){$cName=$GXYrow['clientName'];}else{$cName=null;}
		if($GXYrow['ownerName']){$oName=$GXYrow['ownerName'];}else{$oName=null;}		
		echo '

	  </div>
       <input type="hidden" class="form-control" id="clientName" name="clientName" value="'.$cName.'">
	   <input type="hidden" class="form-control" id="ownerName" name="ownerName" value="'.$oName.'">
	   <input type="hidden" class="form-control" id="cargoCode" name="cargoCode" value="'.$cCode.'">
	   <input type="hidden" class="form-control" id="receiverName" name="receiverName" value="'.$rCode.'">


	  <div class="clearfix"></div>';
				
				
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

		echo '<div class="clearfix"></div>';
		
	echo '<button type="submit" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';
	  
	echo '
	<a href="/pages/receipt/status.php?id='.$id.'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
		<i class="glyphicon glyphicon-option-vertical"></i> statuss
	</a>	  
	  
	<div class="clearfix"></div>';


	echo '</form>
	
	
		</div>	
	</div>			
</div>	







<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-body"> 
				<div id="toCargo">

					<div class="page-header" style="margin-top: -5px;">
						<p style="display:inline-block;">Uz</p> -
					</div>
					krava nav izvēlēta.
				</div>	
				
				
			</div>	
		</div>			
</div>
<div class="clearfix"></div>';

// Apstiprināšanas tabula

		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE (docNr='".$row['docNr']."' OR issue_to='".$row['docNr']."') AND action='27'");

		if(mysqli_num_rows($lines)>0){
?>

<script>
	function cancelLine(event) {
		event.preventDefault();
		
		var val = $(event.target).data("col");


		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/assembly/action.php?action=cancelLine&id=<?=$id;?>&lineId='+val,
			beforeSend: function(){
				$('#cancelLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);

				var typez = $('#cargoList option:selected').attr('data-cncc');
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=changed&selCard='+typez+'&res=canceled');
				$( "#toCargo" ).load( "/pages/assembly/to.php?select="+typez+"&mainId=<?=$id;?>" );
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
			url: '/pages/assembly/action.php?action=moveLine&id=<?=$id;?>&lineId='+val,
			beforeSend: function(){
				$('#moveLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#moveLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);

				var typez = $('#cargoList option:selected').attr('data-cncc');
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=changed&selCard='+typez+'&res=canceled');
				$( "#toCargo" ).load( "/pages/assembly/to.php?select="+typez+"&mainId=<?=$id;?>" );

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
			<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-body">
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
							<th>no kravas</th>
							<th>uz kravu</th>
							<th>daudzums</th>
							<th>daudzums<br>pārvietošanai</th>
							<th>mērvienība</th>
							<th>palīg mērv.<br>daudzums</th>
							<th>palīg mērv.<br>daudzums<br>pārvietošanai</th>
							<th>palīg mērv-<br>ienībaība</th>';
							
			
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
							<td>'.returnCargoCode($conn, $rowL['docNr'], 2).'
							<td>'.returnCargoCode($conn, $rowL['issue_to'], 2).'						
							<td>'.floatval($rowL['amount']).'
							<td>'.floatval($rowL['issueAmount']).'
							<td>'.$rowL['productUmo'].'
							<td>'.floatval($rowL['assistant_amount']).'
							<td>'.floatval($rowL['issue_assistant_amount']).'
							<td>'.$rowL['assistantUmo'];

							echo '<td>';

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
			</div></div></div>
		</div>';

		}

// Apstiprināšanas tabula beidzas

echo '

<form id="send_change_card" onkeypress="return event.keyCode != 13;">	

<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-body">
			<center>
			
			<label>pakalpojums</label>
			<div style="display: inline-block;">
				<select data-width="200px" class="form-control selectpicker btn-group-xs" id="resourceNr" name="resourceNr" data-live-search="true" title="pakalpojums">';	
			
				$selectResource = mysqli_query($conn, "

							SELECT r.id, r.name
							FROM n_resource AS r
							LEFT JOIN agreements_lines AS al
							ON r.id=al.service
							WHERE al.contractNr='".$row['agreements']."' AND al.deleted!='1'  AND al.keeping!='on' AND al.deleted='0' AND al.extra_resource!='on'
							GROUP BY r.id					
							");
				while($rowi = mysqli_fetch_array($selectResource)){
					echo '<option value="'.$rowi['id'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
				}
				
				echo '
				</select>
			</div>
			
			<label>datums</label>
			<div style="display: inline-block;">
				<input type="text" class="form-control datepicker" id="activityDate" name="activityDate" value="'.date('d.m.Y').'" style="width: 200px !important;">
			</div>
			</center>
		</div>
	</div>
</div>	


	
  <br>
  <div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-body"> 
				<div class="page-header" style="margin-top: -5px;">
				  <select class="form-control selectpicker btn-group-xs" id="bzzzz"  data-live-search="true" title="kods" disabled>
				  ';
				  $selectCargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'") or die(mysqli_error($conn));
				  while($rowl = mysqli_fetch_array($selectCargo)){
					  echo '<option  value="'.$rowl['cargoCode'].'"';
					  if($row['cargoCode']==$rowl['cargoCode']){echo ' selected';}
					  echo '>'.$rowl['cargoCode'].'</option>';
				  }
					echo '
				  </select>					
				</div>
			
		<div class="table-responsive"> 
		<table id="one" class="table table-hover table-responsive">
		<thead> 
			<tr>
				
				<th>produkta<br>nr.</th>
				
				<th>partijas nr.</th>
				<th>noliktava</th>
				<th>daudzums</th>
				<th>mēr-<br>vienība</th>
				<th>papild. mērv.<br>daudzums</th>
				<th>papild. mēr-<br>vienība</th>
				<th>statuss</th>
				<th></th>

			</tr>
		</thead>
		<tbody>';

		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$row['docNr']."' AND action!='23' AND action!='27'");
		$r=0;
		while($rowL = mysqli_fetch_array($lines)){
			
			
			
		if($rowL['status']!=20){

		echo '<tr bgcolor="#eee" class="classlistedit" id="mOne'.$rowL['id'].'">
				

				<td>'.$rowL['productNr'];
				
				 
				echo '<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$row['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default btn-xs">
					i
				</a>						
				
				<td>'.$rowL['batchNo'];

				echo '
				
				<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
				<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
				<div class="Inputz" style="display: none;"><input type="text" style="width: 100px; border-radius: 5px;" value="'.floatval($rowL['amount']).'" disabled></div>
				
				<td>'.$rowL['productUmo'];

				echo '<td>'.floatval($rowL['assistant_amount']);
				echo '<td>'.$rowL['assistantUmo'];

				echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);	
				echo '<td>';
							
			echo '</tr>';			
		
		}else{		
		$r++;
		
		echo '<tr class="classlistedit" id="mOne'.$rowL['id'].'">
				
		
		
				<td>'.$rowL['productNr'].'

				<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$row['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
				i
				</a>';				
			
				echo '
				<td>'.$rowL['batchNo'];

				echo '
				
				<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
				<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
				<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
				onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 80px; border-radius: 5px;" class="forChangeOne" value="'.floatval($rowL['amount']).'"></div>
				
				<td>'.$rowL['productUmo'];

				echo '<td> 
				
				<div class="hOn">'.floatval($rowL['assistant_amount']).'</div>	
				<div class="Inputz" style="display: none;">
					<input type="number" min="0" max="'.floatval($rowL['assistant_amount']).'" step="0.01"
						   onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<0){this.value=\'0\';}" 
						   style="width: 60px; border-radius: 5px;" class="forAssistantAmountOne" value="'.floatval($rowL['assistant_amount']).'">
				</div>';
				echo '<td>'.$rowL['assistantUmo'];

				echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
				echo '
				<td><div class="checkbox 1 moveIcon" style="display: none;">
				<input type="hidden" class="changeInputsOne"  value="'.$rowL['id'].'" style="width: 50px;">
				<input type="hidden" class="productNrOne"  value="'.$rowL['productNr'].'" style="width: 50px;">
				<input type="hidden" class="amountOne"  value="'.floatval($rowL['amount']).'" style="width: 50px;">
				<input type="hidden" class="assistantAmountOne"  value="'.floatval($rowL['assistant_amount']).'" style="width: 50px;">
				<input type="hidden" class="enteredDateOne"  value="'.date('d.m.Y', strtotime($rowL['enteredDate'])).'" style="width: 50px;">
				<input type="hidden" class="locationOne"  value="'.$rowL['location'].'" style="width: 50px;">
				<input type="hidden" class="statusOne"  value="'.$rowL['status'].'" style="width: 50px;">	
				<input type="hidden" class="productUmoOne"  value="'.$rowL['productUmo'].'" style="width: 50px;">
				<input type="hidden" class="batchNoOne"  value="'.$rowL['batchNo'].'" style="width: 50px;">

				<input type="hidden" class="activityDateOne"  value="'.date('d.m.Y', strtotime($rowL['activityDate'])).'" style="width: 50px;">
				<input type="hidden" class="thisTransportOne"  value="'.$rowL['thisTransport'].'" style="width: 50px;">
				<input type="hidden" class="volumeOne"  value="'.$rowL['volume'].'" style="width: 50px;">
				<input type="hidden" class="thisDateOne"  value="'.date('d.m.Y', strtotime($rowL['thisDate'])).'" style="width: 50px;">
				
				<i class="glyphicon glyphicon-transfer"></i></div>';
				
				echo '
			</tr>';			
					
		}			
						
		}
		echo '<input type="hidden" name="changeCodeOne" value="'.$row['docNr'].'">';
		echo '<input type="hidden" name="changeResultOne" value="'.$r.'"> ';


		
	echo '</tbody>
	</table>
</div>	
				
				</div>
			</div>
  </div>
  <div class="col-lg-6">
			<div class="panel panel-default">
				<div class="panel-body"> 
				<div class="page-header" style="margin-top: -5px;">
				
				  <select class="form-control selectpicker btn-group-xs"  id="cargoList" data-live-search="true" title="kods"';
				 
				  echo '>
				  ';
				  if($selCard){$selCardNo = " AND docNr='".$selCard."'";}else{$selCardNo = null;}
				  $selectCargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE docNr!='".$row['docNr']."' ") or die(mysqli_error($conn));
				  
				  echo '<option data-cncc="none"></option>';
				  while($rowl = mysqli_fetch_array($selectCargo)){
					  echo '<option data-cncc="'.$rowl['docNr'].'"  value="'.$rowl['cargoCode'].'"';
					  if($selCard==$rowl['docNr']){echo ' selected';}
					  echo '>'.$rowl['cargoCode'].'</option>';
				  }
					echo '
				  </select>					
				</div>
					<div id="secondOptionCargo">';
					
					if($selCard){

			echo '
		<div class="table-responsive"> 
		<table id="two" class="table table-hover table-responsive">
		<thead> 
			<tr>
				
				<th>produkta<br>nr.</th>
				
				<th>partijas nr.</th>
				<th>noliktava</th>
				<th>daudzums</th>
				<th>mēr-<br>vienība</th>
				<th>papild. mērv.<br>daudzums</th>
				<th>papild. mēr-<br>vienība</th>				
				<th>statuss</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';

		$lines = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".mysqli_real_escape_string($conn, $selCard)."' AND action!='23' AND action!='27'");
		$r=0;
		while($rowL = mysqli_fetch_array($lines)){
			
			
			if($rowL['status']!=20){
						
				echo '<tr bgcolor="#eee" class="classlistedit" id="mOne'.$rowL['id'].'">

					
					<td>'.$rowL['productNr'];
					
					
					echo '<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$row['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
						i
					</a>						
					
					<td>'.$rowL['batchNo'];

					echo '
					
					<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
					<td><div class="hOn">'.floatval($rowL['amount']).'</div>	
					<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
					onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 100px; border-radius: 5px;" value="'.floatval($rowL['amount']).'" disabled></div>
					
					<td>'.$rowL['productUmo'];

					echo '<td>'.floatval($rowL['assistant_amount']);
					echo '<td>'.$rowL['assistantUmo'];

					echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);	
					echo '<td>';
								
				echo '</tr>';		
			
			}else{		
				
			$r++;
			
				echo '<tr class="classlistedit" id="mTwo'.$rowL['id'].'">

	

						<td>'.$rowL['productNr'].'

						<a href="productHistory.php?id='.rawurlencode($rowL['productNr']).'&docNr='.$row['docNr'].'&batchNo='.$rowL['batchNo'].'" id="modUrl" class="btn btn-default btn-xs" data-toggle="modal" data-id="'.$rowL['productNr'].'" data-target="#productHistory" data-remote="false" class="btn btn-default">
						i
						</a>';						
						
						echo '
						<td>'.$rowL['batchNo'];						
						
						echo '
						
						<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'						
						<td><div class="hOn">'.floatval($rowL['amount']).'</div>		
						<div class="Inputz" style="display: none;"><input type="number" min="0" max="'.floatval($rowL['amount']).'" step="0.01"
						onKeyUp="if(this.value>'.floatval($rowL['amount']).'){this.value=\''.floatval($rowL['amount']).'\';}else if(this.value<0){this.value=\'0\';}" style="width: 100px; border-radius: 5px;" class="forChangeTwo" value="'.floatval($rowL['amount']).'"></div>
						
						<td>'.$rowL['productUmo'];


						echo '<td> 
				
						<div class="hOn">'.floatval($rowL['assistant_amount']).'</div>	
						<div class="Inputz" style="display: none;">
							<input type="number" min="0" max="'.floatval($rowL['assistant_amount']).'" step="0.01"
								   onKeyUp="if(this.value>'.floatval($rowL['assistant_amount']).'){this.value=\''.floatval($rowL['assistant_amount']).'\';}else if(this.value<0){this.value=\'0\';}" 
								   style="width: 60px; border-radius: 5px;" class="forAssistantAmountTwo" value="'.floatval($rowL['assistant_amount']).'">
						</div>';
						echo '<td>'.$rowL['assistantUmo'];

						echo '<td>'.returnCargoLineStatus($conn, $rowL['id']);
						echo '
						<td><div class="checkbox 2">
						
						<input type="hidden" class="changeInputsTwo"  value="'.$rowL['id'].'" style="width: 50px;">
						<input type="hidden" class="productNrTwo"  value="'.$rowL['productNr'].'" style="width: 50px;">
						<input type="hidden" class="amountTwo"  value="'.floatval($rowL['amount']).'" style="width: 50px;">
						<input type="hidden" class="assistantAmountTwo"  value="'.floatval($rowL['assistant_amount']).'" style="width: 50px;">
						<input type="hidden" class="enteredDateTwo"  value="'.date('d.m.Y', strtotime($rowL['enteredDate'])).'" style="width: 50px;">
						<input type="hidden" class="locationTwo"  value="'.$rowL['location'].'" style="width: 50px;">
						<input type="hidden" class="statusTwo"  value="'.$rowL['status'].'" style="width: 50px;">
						<input type="hidden" class="productUmoTwo"  value="'.$rowL['productUmo'].'" style="width: 50px;">
						<input type="hidden" class="batchNoTwo"  value="'.$rowL['batchNo'].'" style="width: 50px;">	

						<input type="hidden" class="activityDateTwo"  value="'.date('d.m.Y', strtotime($rowL['activityDate'])).'" style="width: 50px;">
						<input type="hidden" class="thisTransportTwo"  value="'.$rowL['thisTransport'].'" style="width: 50px;">
						<input type="hidden" class="volumeTwo"  value="'.$rowL['volume'].'" style="width: 50px;">
						<input type="hidden" class="thisDateTwo"  value="'.date('d.m.Y', strtotime($rowL['thisDate'])).'" style="width: 50px;">
				
						<i class="glyphicon glyphicon-transfer"></i></div>								
					</tr>';
			}
		
		}
		echo '<input type="hidden" name="changeCodeTwo" value="'.$selCard.'">';
		echo '<input type="hidden" name="changeResultTwo" value="'.$r.'"> ';


		
	echo '</tbody>
	</table>
</div>	
				
				</div>
			</div>';

if($selCard){
?>
<script>
$(document).ready(function() {
$(".moveIcon").show();
$(".Inputz").css('display','block');
$(".hOn").css('display','none');
});
</script>
<?php }else{ ?>
<script>
$(document).ready(function() {
$(".moveIcon").hide();
});
</script>
<?php } 					
						
					}
					
					echo '
					</div>
				</div>
			</div>
  </div>
  <div class="clearfix"></div>';

	// VECĀ MAINĪT POGA
	echo '<button type="submit" id="changeBtn" class="btn btn-default btn-xs moveIcon" style="display: none; float: right;"><i class="glyphicon glyphicon-random" style="color: green;"></i> nodot</button>'; 

echo '	
</form> 
	';

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

<div class="modal fade" id="productHistory" tabindex="-1" role="dialog" aria-labelledby="productHistoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="productHistoryLabel">preces vēsture</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">aizvērt</button>
      </div>
    </div>
  </div>
</div>			

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

<script>
$('body').on('click','#one tbody tr td .checkbox.1', function(e){
	e.stopImmediatePropagation();
    var row = $(this).closest('tr').clone();

	var OrgAmountOne = $(row).find('.amountOne').val();
	var NewAmountOne = $(row).find('.forChangeOne').val();
	var OrgAmountOneE = $(row).find('.assistantAmountOne').val();
	var NewAmountOneE = $(row).find('.forAssistantAmountOne').val();


	

	if(OrgAmountOne == NewAmountOne){
		 $('#two tbody').append(row);
         $(this).closest('tr').remove();
		 $(row).css("backgroundColor", "silver");
		 $(row).find('.forChangeOne').attr("readonly", true);
		 $(row).find('.forAssistantAmountOne').attr("readonly", true);		 
	}else{

		var bzzz = $(row).find('input.changeInputsOne').val();
		$(row).find('.forChangeOne').attr("readonly", true);
		$(row).find('.forAssistantAmountOne').attr("readonly", true);

		var arrText = $('#two tbody').find('input.changeInputsOne').map(function(){
			return this.value;
		}).get();

		if($.inArray(bzzz, arrText) !== -1)
		{	

			var OrgAmountOne = $(row).find('.amountOne').val();
			
				
				var toOne = $('#mOne'+bzzz+'').find('.amountOne').val();

				var a = $('#two #mOne'+bzzz+' .forChangeOne').val();

				var a = parseFloat(a);
				var b = $(row).find('.forChangeOne').val();
				var b = parseFloat(b);
				var totz = a+b;


			// palīg m.
			var OrgAmountOneE = $(row).find('.assistantAmountOne').val();

				var toOneE = $('#mOne'+bzzz+'').find('.assistantAmountOne').val();

				
				var aE = $('#two #mOne'+bzzz+' .forAssistantAmountOne').val();
				var aE = parseFloat(aE);

				var bE = $(row).find('.forAssistantAmountOne').val();
				var bE = parseFloat(bE);

				var totzE = aE+bE;

				if(OrgAmountOneE == totzE){
					
					var abE = aE+bE;

					$('#two #mOne'+bzzz+' .forAssistantAmountOne').val(abE);

					$(this).closest('tr').remove();			
				}else{
					
					var abE = aE+bE;
					var bcE = toOneE-abE;
					
					$('#two #mOne'+bzzz+' .forAssistantAmountOne').val(abE);
					$(this).closest('tr').find('.forAssistantAmountOne').val(bcE);
					
				} 

				if(OrgAmountOne == totz){
					var ab = a+b;

					$('#two #mOne'+bzzz+' .forChangeOne').val(ab);

					$(this).closest('tr').remove();			
				}else{

					var ab = a+b;
					var bc = toOne-ab;

					$('#two #mOne'+bzzz+' .forChangeOne').val(ab);
					$(this).closest('tr').find('.forChangeOne').val(bc);
					
				}  
		}else{

				var toMove = OrgAmountOne-NewAmountOne;
				var toMoveE = OrgAmountOneE-NewAmountOneE;
				
				$('#two tbody').append(row);

				$(this).closest('tr').find('.forChangeOne').val(toMove);
				$(this).closest('tr').find('.forAssistantAmountOne').val(toMoveE);

				$(row).css("backgroundColor", "lightgreen");
				$(this).closest('tr').css("backgroundColor", "#ff3333"); 

				$(this).closest('tr').find('.forChangeOne').attr("readonly", true);
				$(this).closest('tr').find('.forAssistantAmountOne').attr("readonly", true);	
		} 
		  
	}

	$(row).find('input.changeInputsOne').prop('name', 'changeIdOne[]');
	$(row).find('input.productNrOne').prop('name', 'productNrOne[]');
	$(row).find('input.amountOne').prop('name', 'amountOne[]');
	
	$(row).find('input.enteredDateOne').prop('name', 'enteredDateOne[]');
	$(row).find('input.locationOne').prop('name', 'locationOne[]');
	$(row).find('input.statusOne').prop('name', 'statusOne[]'); 
	$(row).find('input.forChangeOne').prop('name', 'amountOrgOne[]');
	$(row).find('input.productUmoOne').prop('name', 'productUmoOne[]');
	$(row).find('input.batchNoOne').prop('name', 'batchNoOne[]');

	$(row).find('input.forAssistantAmountOne').prop('name', 'forAssistantAmountOne[]');
	$(row).find('input.assistantAmountOne').prop('name', 'assistantAmountOne[]');
	
	$(row).find('input.activityDateOne').prop('name', 'activityDateOne[]');
	$(row).find('input.thisTransportOne').prop('name', 'thisTransportOne[]');
	$(row).find('input.volumeOne').prop('name', 'volumeOne[]');
	$(row).find('input.thisDateOne').prop('name', 'thisDateOne[]');

	$(row).find('select.forResourceOne').prop('name', 'resourceNrOne[]');
			 

	var numOne = $('#one .checkbox.2').length;
	var numTwo = $('#two .checkbox.1').length;		
	
	if(numTwo>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');
	}
	if(numOne>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');
	}		
     
});
</script>
<script>
$('body').on('click','#one tbody tr td .checkbox.2', function(e){
	e.stopImmediatePropagation();

    var row = $(this).closest('tr').clone();

	var bzzz = $(row).find('input.changeInputsTwo').val();

	var arrText = $('#two tbody').find('input.changeInputsTwo').map(function(){
		return this.value;
	}).get();

	$(row).find('.forAssistantAmountTwo').attr("readonly", false);

	var OrgAssistantAmountTwo = $(row).find('.assistantAmountTwo').val();
	var NewAssistantAmountTwo = $(row).find('.forAssistantAmountTwo').val();


	$(row).find('.forAssistantAmountTwo').val(OrgAssistantAmountTwo);

	$(row).find('.forChangeTwo').attr("readonly", false);

	if($.inArray(bzzz, arrText) !== -1){
 
		var OrgAmountOne = $(row).find('.amountTwo').val();
		
		var toOne = $('#mTwo'+bzzz+'').find('.amountTwo').val();

		var a = $('#two #mTwo'+bzzz+' .forChangeTwo').val();
		
		var a = parseFloat(a);
		var b = $('#one #mTwo'+bzzz+' .forChangeTwo').val();
		var b = parseFloat(b);
		var totz = a+b;

		if(OrgAmountOne == totz){
			 $('#two #mTwo'+bzzz+' .forChangeTwo').closest('tr').remove();
			 $('#two tbody').append(row);
			 $(row).closest('tr').find('.forChangeTwo').val(totz);
			 $('#one #mTwo'+bzzz+' .forChangeTwo').closest('tr').remove();
			 $(row).css("backgroundColor", "transparent");			
		}else{
			
			var ab = a+b;
			var bc = toOne-ab;
			$('#two #mTwo'+bzzz+' .forChangeTwo').val(ab);
			$(this).closest('tr').find('.forChangeTwo').val(bc);
			
		} 

  }else{ 
         $('#two tbody').append(row);
         $(this).closest('tr').remove();
		 $(row).css("backgroundColor", "transparent");
		 $(row).find('.forChangeTwo').attr("readonly", false);		 
  }
		 
		 
	$(row).find('input.changeInputsTwo').removeAttr('name');
	$(row).find('input.productNrTwo').removeAttr('name');
	$(row).find('input.amountTwo').removeAttr('name'); 
	$(row).find('input.enteredDateTwo').removeAttr('name');
	$(row).find('input.locationTwo').removeAttr('name');
	$(row).find('input.statusTwo').removeAttr('name');  
	$(row).find('input.forChangeTwo').removeAttr('name');
	$(row).find('input.productUmoTwo').removeAttr('name');
	$(row).find('input.batchNoTwo').removeAttr('name');

	$(row).find('input.forAssistantAmountTwo').removeAttr('name');
	$(row).find('input.assistantAmountTwo').removeAttr('name');

	$(row).find('input.activityDateTwo').removeAttr('name');
	$(row).find('input.thisTransportTwo').removeAttr('name');
	$(row).find('input.volumeTwo').removeAttr('name');
	$(row).find('input.thisDateTwo').removeAttr('name');

	$(row).find('select.forResourceTwo').removeAttr('name');
	
	var numOne = $('#one .checkbox.2').length;
	var numTwo = $('#two .checkbox.1').length;		
		
	if(numOne===0){
		$("#cargoList").attr('disabled',false);
		$('.selectpicker').selectpicker('refresh');
	}
	if(numTwo>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');
	}		
		 
});
</script>
<script>
$('body').on('click','#two tbody tr td .checkbox.1', function(e){
	e.stopImmediatePropagation();
		
    var row = $(this).closest('tr').clone(); 
		 
	var bzzz = $(row).find('input.changeInputsOne').val();

	var arrText = $('#one tbody').find('input.changeInputsOne').map(function(){
		return this.value;
	}).get();

	$(row).find('.forAssistantAmountOne').attr("readonly", false);

	var OrgAssistantAmountOne = $(row).find('.assistantAmountOne').val();
	var NewAssistantAmountOne = $(row).find('.forAssistantAmountOne').val();

	$(row).find('.forAssistantAmountOne').val(OrgAssistantAmountOne);

	$(row).find('.forChangeOne').attr("readonly", false);

	if($.inArray(bzzz, arrText) !== -1){
  
		var OrgAmountOne = $(row).find('.amountOne').val();
		
		var toOne = $('#mOne'+bzzz+'').find('.amountOne').val();

		var a = $('#mOne'+bzzz+'').find('.forChangeOne').val();
		
		var a = parseFloat(a);
		var b = $(row).find('.forChangeOne').val();
		var b = parseFloat(b);
		var totz = a+b;

		if(OrgAmountOne == totz){
			 $('#mOne'+bzzz+'').closest('tr').remove();
			 $('#one tbody').append(row);
			 $(row).closest('tr').find('.forChangeOne').val(totz);
			 $(this).closest('tr').remove();
			 $(row).css("backgroundColor", "transparent");			
		}else{
			
			var ab = a+b;
			var bc = toOne-ab;

			$('#mOne'+bzzz+'').find('.forChangeOne').val(ab);
			$(this).closest('tr').find('.forChangeOne').val(bc);
			
		}  

    }else{
		 $('#one tbody').append(row);
         $(this).closest('tr').remove();
		 $(row).css("backgroundColor", "transparent");
		 $(row).find('.forChangeOne').attr("readonly", false);		 
    } 
		 

	$(row).find('input.changeInputsOne').removeAttr('name');
	$(row).find('input.productNrOne').removeAttr('name');
	$(row).find('input.amountOne').removeAttr('name');
	$(row).find('input.enteredDateOne').removeAttr('name');
	$(row).find('input.locationOne').removeAttr('name');
	$(row).find('input.statusOne').removeAttr('name');
	$(row).find('input.forChangeOne').removeAttr('name');
	$(row).find('input.productUmoOne').removeAttr('name');
	$(row).find('input.batchNoOne').removeAttr('name');
	
	$(row).find('input.forAssistantAmountOne').removeAttr('name');
	$(row).find('input.assistantAmountOne').removeAttr('name');

	$(row).find('input.activityDateOne').removeAttr('name');
	$(row).find('input.thisTransportOne').removeAttr('name');
	$(row).find('input.volumeOne').removeAttr('name');
	$(row).find('input.thisDateOne').removeAttr('name');		

	$(row).find('select.forResourceOne').removeAttr('name');

	var numOne = $('#one .checkbox.2').length;
	var numTwo = $('#two .checkbox.1').length;		
		
	if(numTwo===0){
		$("#cargoList").attr('disabled',false);
		$('.selectpicker').selectpicker('refresh');
	}		 
	if(numOne>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');			
	}

});
</script>

<script>
$('body').on('click','#two tbody tr td .checkbox.2', function(e){
	e.stopImmediatePropagation();
    var row = $(this).closest('tr').clone();

	var OrgAmountOne = $(row).find('.amountTwo').val();
	var NewAmountOne = $(row).find('.forChangeTwo').val();

	var OrgAmountOneE = $(row).find('.assistantAmountTwo').val();
	var NewAmountOneE = $(row).find('.forAssistantAmountTwo').val();	
		

	if(OrgAmountOne == NewAmountOne){
		 $('#one tbody').append(row);
         $(this).closest('tr').remove();
		 $(row).css("backgroundColor", "silver");
		 $(row).find('.forChangeTwo').attr("readonly", true);
		 $(row).find('.forAssistantAmountTwo').attr("readonly", true);
	}else{

		var bzzz = $(row).find('input.changeInputsTwo').val();
		$(row).find('.forChangeTwo').attr("readonly", true);
		$(row).find('.forAssistantAmountTwo').attr("readonly", true);

		var arrText = $('#one tbody').find('input.changeInputsTwo').map(function(){
			return this.value;
		}).get();

		if($.inArray(bzzz, arrText) !== -1)
		{

				var OrgAmountOne = $(row).find('.amountTwo').val();
				
				var toOne = $('#mTwo'+bzzz+'').find('.amountTwo').val();

				var a = $('#one #mTwo'+bzzz+' .forChangeTwo').val();
				
				var a = parseFloat(a);
				var b = $(row).find('.forChangeTwo').val();
				var b = parseFloat(b);
				var totz = a+b;

				if(OrgAmountOne == totz){
				
					var ab = a+b;
				
					$('#one #mTwo'+bzzz+' .forChangeTwo').val(ab);

					$(this).closest('tr').remove();			
				}else{
					
					var ab = a+b;
					var bc = toOne-ab;

					$('#one #mTwo'+bzzz+' .forChangeTwo').val(ab);
					$(this).closest('tr').find('.forChangeTwo').val(bc);
					
				} 

			// palīg m.
			var OrgAmountOneE = $(row).find('.assistantAmountTwo').val();

				var toOneE = $('#mTwo'+bzzz+'').find('.assistantAmountTwo').val();

				
				var aE = $('#one #mTwo'+bzzz+' .forAssistantAmountTwo').val();
				var aE = parseFloat(aE);

				var bE = $(row).find('.forAssistantAmountTwo').val();
				var bE = parseFloat(bE);

				var totzE = aE+bE;

				if(OrgAmountOneE == totzE){
					var abE = aE+bE;

					$('#one #mTwo'+bzzz+' .forAssistantAmountTwo').val(abE);

					$(this).closest('tr').remove();			
				}else{
					
					var abE = aE+bE;
					var bcE = toOneE-abE;
					$('#one #mTwo'+bzzz+' .forAssistantAmountTwo').val(abE);
					$(this).closest('tr').find('.forAssistantAmountTwo').val(bcE);
					
				} 				
				 
		}else{
		
				var toMove = OrgAmountOne-NewAmountOne;
				var toMoveE = OrgAmountOneE-NewAmountOneE;
	
				$('#one tbody').append(row);

				$(this).closest('tr').find('.forChangeTwo').val(toMove);
				$(this).closest('tr').find('.forAssistantAmountTwo').val(toMoveE);
				

				$(row).css("backgroundColor", "lightgreen");
				$(this).closest('tr').css("backgroundColor", "#ff3333"); 

				$(this).closest('tr').find('.forChangeTwo').attr("readonly", true);
				$(this).closest('tr').find('.forAssistantAmountTwo').attr("readonly", true);				
		} 
		  
	}		 
		 		 
	$(row).find('input.changeInputsTwo').prop('name', 'changeIdTwo[]');
	$(row).find('input.productNrTwo').prop('name', 'productNrTwo[]');
	$(row).find('input.amountTwo').prop('name', 'amountTwo[]');
	$(row).find('input.enteredDateTwo').prop('name', 'enteredDateTwo[]');
	$(row).find('input.locationTwo').prop('name', 'locationTwo[]');
	$(row).find('input.statusTwo').prop('name', 'statusTwo[]'); 
	$(row).find('input.forChangeTwo').prop('name', 'amountOrgTwo[]');
	$(row).find('input.productUmoTwo').prop('name', 'productUmoTwo[]');
	$(row).find('input.batchNoTwo').prop('name', 'batchNoTwo[]');

	$(row).find('input.forAssistantAmountTwo').prop('name', 'forAssistantAmountTwo[]');
	$(row).find('input.assistantAmountTwo').prop('name', 'assistantAmountTwo[]');

	$(row).find('input.activityDateTwo').prop('name', 'activityDateTwo[]');
	$(row).find('input.thisTransportTwo').prop('name', 'thisTransportTwo[]');
	$(row).find('input.volumeTwo').prop('name', 'volumeTwo[]');
	$(row).find('input.thisDateTwo').prop('name', 'thisDateTwo[]');

	$(row).find('select.forResourceTwo').prop('name', 'resourceNrTwo[]');


	var numOne = $('#one .checkbox.2').length;
	var numTwo = $('#two .checkbox.1').length;		
	
	if(numOne>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');
	}
	if(numTwo>0){
		$("#cargoList").attr('disabled',true);
		$('.selectpicker').selectpicker('refresh');
	}
		 

});
</script>

<script>
$(document).ready(function () {
    $('#send_change_card').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/assembly/post.php?r=changeCard&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				
				$('#changeBtn').html("<i class='glyphicon glyphicon-random' style='color: green;'></i> gaidiet...");
				$("#changeBtn").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				var typez = $('#cargoList option:selected').attr('data-cncc');
				$('#contenthere').load('/pages/assembly/assembly.php?view=edit&id=<?=$id;?>&res=changed&selCard='+typez+'');
				$( "#toCargo" ).load( "/pages/assembly/to.php?select="+typez+"&mainId=<?=$id;?>" );
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>
<?
if($id){
?>
	<script>
		var typez = $('#cargoList option:selected').attr('data-cncc');
		if(typez && typez!='none'){
			$( "#toCargo" ).load( "/pages/assembly/to.php?select="+typez+"&mainId=<?=$id;?>" );
		}
	</script>
<?
}
?>

<script>
// Fill modal with content from link href
$("#productHistory").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    $(this).find(".modal-body").load(link.attr("href"));
});
$('.decimal').keyup(function(){
    var val = $(this).val();
    if(isNaN(val)){
         val = val.replace(/[^0-9\.]/g,'');
         if(val.split('.').length>2) 
             val =val.replace(/\.+$/,"");
    }
    $(this).val(val); 
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?> 
