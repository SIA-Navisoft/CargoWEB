<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="additional_services";



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
    $('.classadd').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/additional_services/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/additional_services/additional_services.php<?=$glpage;?>');
    });
})
</script>

<script>
$(document).ready(function () {
    $('#deleteIt').on('click', function(e) {
        e.preventDefault();

		if (confirm('UZMANĪBU! Vai tiešām vēlaties dzēst ierakstus?')) {

			$.ajax({
				url : '/pages/additional_services/post.php?r=deleteIt&id=<?=$id;?>',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					$('#savebtn').html("gaidiet...");
					$("#savebtn").prop("disabled",true);
				},			
				success: function (data) {
					console.log(data);

					$('#contenthere').load('/pages/additional_services/additional_services.php?res=done');
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});

		}

    });
});

</script>

<script>
$(document).ready(function(){
    $('.classhistory').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/additional_services/additional_services.php?view=history');
    });
})
</script>



<?php
if(!$view){
?>
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#contenthere').load('/pages/additional_services/'+url);
			event.preventDefault();
		});
	});
	</script>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$('#contenthere').load('/pages/additional_services/additional_services.php?client='+val+'');
}
</script>
	<script>
	$('#removeFilter').on('click', function() {
		$('#contenthere').load('/pages/additional_services/additional_services.php');
	});
	</script>
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
				echo '<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
			echo '</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success" >saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">papildu pakalpojumi</p>';

		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilter"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-2 pull-right">
				<select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">
				<option></option>';
				
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM additional_services_header WHERE (status='0' OR status='10')") or die(mysqli_error($conn));
			
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			
			';		

?>

<script type="text/javascript">

function getStates(value) {
	var client = '<?=$client;?>';
    var search = $.post("/pages/additional_services/search.php", {name:value, client:client},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>


<?php	
	
echo ' <div style="float: right; display: inline-block;" ><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div><div class="clearfix"></div>'; 	

			
	
	echo '<div id="results">';	
			
	if($client){$filterClient = ' AND clientCode="'.$client.'"';}else{$filterClient = null;}
	


	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(SELECT SUM(amount) FROM additional_services_line WHERE additional_services_line.docNr=additional_services_header.docNr) as vienibas,
	
	(SELECT thisTransport FROM additional_services_line WHERE additional_services_line.docNr=additional_services_header.docNr LIMIT 1) AS lineThisTransport
	 FROM additional_services_header WHERE (status='0' || status='10') ".$filterClient."";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>akta datums</th>
						
						<th>pavadzīmes nr.</th>
						<th>akta nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>STENA LINE PORTS VENTSPILS pārstāvis</th>
						<th>kravas nosaukums</th>
						<th>statuss</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				echo '	<tr class="classlistedit" onclick="newDoc('.$row['id'].')">
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							
							echo '
							<td>'.$row['ladingNr'].'
							<td>'.$row['acceptance_act_no'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['home_delegate'].'
							<td>'.$row['cargo_name'].'
							<td>'.returnExtraStatus($conn, $row['id']).'

						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
		echo '</div>';
}
if($view=='edit'){
	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
			
			echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
			echo '<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
		echo '</div>';
		if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
		if ($res=="del"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">izdzēsts!</div></div>';}
		if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
		if ($res=="approved"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">nodots!</div></div>';}
		if ($res=="received"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">apstiprināts!</div></div>';}
		if ($res=="canceled"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">atsaukts!</div></div>';}
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';
			  
	echo '</div>';	
	$GetXY = mysqli_query($conn, "SELECT * FROM additional_services_header WHERE id='".intval($id)."'") or die(mysqli_error($conn));
	$GXYrow = mysqli_fetch_array($GetXY);
	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	
	
	echo '<p style="display:inline-block;">labot</p> <div style="display:inline-block; font-weight: bold;">'.$isDocNr.'</div>';

	$result = mysqli_query($conn,"SELECT * FROM additional_services_header WHERE id='".intval($id)."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	if($status>0){$disabled='disabled';}else{$disabled=null;}
	?>

	<script>

$(document).ready(function () {
    $('#send_profile').on('submit', function(e) {
        e.preventDefault();
		var str = this.dataset.key;
		
		
        $.ajax({
            url : '/pages/additional_services/post.php?r=edit&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				var productNr = $("#productNr").val();
				var amount = $("#amount").val();
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=done');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});	
	</script>


	<script>	  
	function delLine(event) {
		event.preventDefault();

		var val = $(event.target).data("dol");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=deleteLine&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=del');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function approveLine(event) {
		
		event.preventDefault();
		
		var val = $(event.target).data("rowid");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=approveLine&id='+val+'',
			beforeSend: function(){
				$('#approveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=approved');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function approveOneLine(event) {
		event.preventDefault();

		var val = $(event.target).data("aol");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=approveOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#approveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=approved');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
	<script>	  
	function receiveLine(event) {
		event.preventDefault();

		var val = $(event.target).data("rl");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=receiveLine&id='+val+'',
			beforeSend: function(){
				$('#receiveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=received');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function receiveOneLine(event) {
		event.preventDefault();

		var val = $(event.target).data("rol");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=receiveOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#receiveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=received');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
	<script>	  
	function cancelLine(event) {
		event.preventDefault();

		var val = $(event.target).data("cl");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=cancelLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#cancelLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=canceled');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>	  
	function cancelOneLine(event) {
		event.preventDefault();
		
		var val = $(event.target).data("col");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/additional_services/action.php?action=cancelOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#cancelOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id=<?=$id;?>&res=canceled');
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

	$( "#secondOption" ).load( "pages/additional_services/change.php?select="+typez+"" );
	$( "#resourceOption" ).load( "/pages/additional_services/change.php?view=sort_resource&select="+typez+"" );
		
});

function selAgreement(val) {

var val = val.value;
var val = encodeURI(val);

$( "#resourceOption" ).load( "/pages/additional_services/change.php?view=sort_resource&resource="+val+"" );

}
</script>	

<style>
.asterisc {
  display: inline-block;
  color: red;
}
</style>

	<?php
	echo '
	<form id="send_profile">
	  <div class="form-row">';


	echo '
	<div class="form-group col-md-3">	
		<label class="asterisc">* obligāti lauki</label>
	</div>
	<div class="clearfix"></div>';	  
	  
		echo '
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="clientCode">klienta kods - nosaukums <span class="asterisc">*</span></label>
				<select class="form-control selectpicker btn-group-xs input-xs"  name="clientCode" id="clientCode"  data-live-search="true" title="klienta kods - nosaukums"  '.$disabled.'>
				<option></option>';
			  $selectClient = mysqli_query($conn, "
				SELECT DISTINCT(c.Code) AS Code, c.Name 
				FROM n_customers AS c
				LEFT JOIN agreements AS a
				ON c.Code=a.customerNr 
				WHERE (a.dateTo='0000-00-00 00:00:00' OR a.dateTo IS NULL OR a.dateTo>CURDATE()) AND a.status<20 AND a.deleted='0'
			  ") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option data-cnid="'.$rowc['Code'].'" data-name="'.$rowc['Name'].'" value="'.$rowc['Code'].'"';
				  if($row['clientCode']==$rowc['Code']){echo ' selected';}
				  echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';				

				echo '
				<div id="secondOption">
				<div class="form-group col-md-3">
				<label class="lb-sm"  for="agreements">līgums <span class="asterisc">*</span></label>
					<select class="form-control selectpicker btn-group-xs input-xs"  name="agreements" id="agreements"  data-live-search="true" title="līgums" '.$disabled.'>
					<option></option>
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
				</div></div>			
			</div>';

		
		echo '
		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="deliveryDate">akta datums <span class="asterisc">*</span></label>
		  <input type="text" class="form-control datepicker input-xs" name="deliveryDate" id="deliveryDate" placeholder="akta datums" value="'.date('d.m.Y', strtotime($row['deliveryDate'])).'" '.$disabled.'>
		</div>	  

		';
		
		if(!$row['acceptance_act_no']){$falseActNo = $row['id'];}else{$falseActNo=$row['acceptance_act_no'];}
		
		echo ' 
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="acceptanceNr">akta nr. <span class="asterisc">*</span></label>
			<input type="text" class="form-control input-xs" id="acceptanceNr" name="acceptanceNr" placeholder="akta nr."  value="'.$falseActNo.'" '.$disabled.'>
		</div><div class="clearfix"></div>';			
			
			
			
		echo '
	  	<div class="form-group col-md-3">
	  		<label class="lb-sm"  for="applicationDate">pieteikuma dat. <span class="asterisc">*</span></label>
	 	    <input type="text" class="form-control datepicker input-xs" name="applicationDate" id="applicationDate" placeholder="pieteikuma dat." value="'; if($row['application_date']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['application_date']));} echo '" '.$disabled.'>
  		</div>

		<div class="form-group col-md-3">
		<label class="lb-sm"  for="applicationNo">pieteikuma nr.</label>
		<input type="text" class="form-control input-xs" name="applicationNo" id="applicationNo" placeholder="pieteikuma nr." value="'.$row['application_no'].'" '.$disabled.'>
		</div>
		
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="landingDate">pavadzīmess dat.</label>
			<input type="text" class="form-control datepicker input-xs" name="landingDate" id="landingDate" placeholder="pavadzīmes dat." value="'; if($row['landingDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['landingDate']));} echo '" '.$disabled.'>
		</div>

		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="ladingNr">pavadzīmes nr</label>
		  <input type="hidden" name="docNr" id="docNr" value="'.$row['docNr'].'">
		  <input type="text" class="form-control input-xs" name="ladingNr" id="ladingNr" placeholder="pavadzīmes nr" value="'.$row['ladingNr'].'" '.$disabled.'>
		</div><div class="clearfix"></div>';

		
		
		
			echo '
			
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="deliveryType">transporta veids <span class="asterisc">*</span></label>
				<select class="form-control selectpicker btn-group-xs input-xs" name="transport" data-live-search="true" title="transporta veids"  '.$disabled.'>
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
      <label for="home_delegate">STENA LINE PORTS VENTSPILS pārstāvis <span class="asterisc">*</span></label>
      <input type="text" class="form-control" name="home_delegate" id="home_delegate" placeholder="STENA LINE PORTS VENTSPILS pārstāvis" value="'.$row['home_delegate'].'" '.$disabled.'>
    </div>

    <div class="form-group col-md-3">
      <label for="client_delegate">klienta pārstāvis <span class="asterisc">*</span></label>
      <input type="text" class="form-control" name="client_delegate" id="client_delegate" placeholder="klienta pārstāvis" value="'.$row['client_delegate'].'" '.$disabled.'>
    </div>';	


	
	echo '
    <div class="form-group col-md-3">
      <label for="cargo_name">kravas nosaukums <span class="asterisc">*</span></label>
      <input type="text" class="form-control" name="cargo_name" id="cargo_name" placeholder="kravas nosaukums" value="'.$row['cargo_name'].'" '.$disabled.'>
    </div>
	<div class="clearfix"></div>

    <div class="form-group col-md-4">
      <label for="description">sastādīšanas apraksts <span class="asterisc">*</span></label>
	  <textarea class="form-control" rows="2" cols="50" id="description" name="description" placeholder="sastādīšanas apraksts" '.$disabled.'>'.$row['description'].'</textarea>
    </div>	
	<div class="clearfix"></div><br>
 ';

 
 
 		$allowSubmit=null;	
	    if($row['clientCode']!='' && $row['agreements']!='' && $row['deliveryDate']!='0000-00-00 00:00:00' && $falseActNo!='' && $row['application_date']!='0000-00-00 00:00:00' && $row['transport']!='' && $row['home_delegate']!='' && $row['client_delegate']!='' && $row['cargo_name']!='' && $row['description']!=''){
			$allowSubmit = 'Y';
		}
 
		
		if($GXYrow['clientName']){$cName=$GXYrow['clientName'];}else{$cName=null;} 
		if($GXYrow['ownerName']){$oName=$GXYrow['ownerName'];}else{$oName=null;}
		if($GXYrow['receiverName']){$rCode=$GXYrow['receiverName'];}else{$rCode=null;}		
		echo '

	  </div>
       <input type="hidden" class="form-control" name="clientName" id="clientName" value="'.$cName.'">
	   <input type="hidden" class="form-control" name="ownerName" id="ownerName" value="'.$oName.'">
	    <input type="hidden" class="form-control" name="cargoCode" id="cargoCode" value="'.$cCode.'">
		<input type="hidden" class="form-control" name="receiverName" id="receiverName" value="'.$rCode.'">


	  ';
	  if(($status==0)){echo '<button type="submit" class="btn btn-default btn-xs" id="saveIt"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';}
	  
	  $getDocNr = mysqli_query($conn, "SELECT docNr FROM additional_services_header WHERE id='".intval($id)."'");
		$gdr = mysqli_fetch_array($getDocNr);
		
		$docNr = $gdr['docNr'];	
		
		$checkIfLine = mysqli_query($conn, "SELECT id FROM additional_services_line WHERE docNr='".$docNr."'");
		$cif = mysqli_num_rows($checkIfLine);
	  

	$isProcessed = mysqli_query($conn, "SELECT id FROM additional_services_line WHERE docNr='".$docNr."' AND status>0");
	$isP = mysqli_num_rows($isProcessed);

	if(($status==0)&&($isP==0)){echo '<button type="submit" class="btn btn-default btn-xs" id="deleteIt" style="margin-left: 4px;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';}


	  echo '<div class="clearfix"></div><br>';



			
	
	
	$getLastLine = mysqli_query($conn, "SELECT * FROM additional_services_line WHERE docNr='".$row['docNr']."' ORDER BY id DESC") or die(mysqli_error($conn));
	$gllRow = mysqli_fetch_array($getLastLine);
	
	if($gllRow['thisDate']!='0000-00-00 00:00:00' && $gllRow['thisDate']!=''){$devTime = date('d.m.Y', strtotime($gllRow['thisDate']));}else{$devTime = date('d.m.Y', strtotime($row['deliveryDate']));}
	
	if($gllRow['thisTransport']!=''){$devTransport = $gllRow['thisTransport'];}else{$devTransport = $row['transportNo'];}	
	echo '
				<div class="clearfix"></div>';
?>

<script>
function updateDue(t) {
	
	t = t.replace(/\D/g,'');

	if(t>0){
		var total = parseInt(document.getElementById("e_document_net"+t).value);
		var val2 = parseInt(document.getElementById("eNet"+t).value);
		// to make sure that they are numbers
		if (!total) { total = 0; }
		if (!val2) { val2 = 0; }
		var ansD = document.getElementById("eDeltaNet"+t);
		ansD.value =  val2 - total;
	}

	if(t==0){
		var total = parseInt(document.getElementById("document_net").value);
		var val2 = parseInt(document.getElementById("net").value);
		// to make sure that they are numbers
		if (!total) { total = 0; }
		if (!val2) { val2 = 0; }
		var ansD = document.getElementById("deltaNet");
		ansD.value =  val2 - total;
	}

}
</script>
	
<?php
	echo '	
	<div class="table-responsive"><table class="table-responsive"><table class="table table-hover table-responsive">
		<thead> 
			<tr>

				<th>palīg pakalpojums</th>
				<th>daudzums</th>
				<th>mērvienība</th>
				<th>transporta nr.</th>
				<th>seriālais nr.</th>
				<th>komentārs</th>';
				if(($status==0)){echo '<th>darbība</th>';}
			echo '
			</tr>
		</thead>
		<tbody>';
		
		$lines = mysqli_query($conn, "
			SELECT * FROM additional_services_line WHERE docNr='".$row['docNr']."'
			");	

		$l=0;
		while($rowL = mysqli_fetch_array($lines)){
			
			if($rowL['status']>0){$disabled='disabled';}else{$disabled=null;}						
							
if($rowL['status']==0){

			echo '	<tr class="classlistedit"';  echo '>';
			
				echo '<td><input type="hidden" name="eLineId[]" value="'.$rowL['id'].'">';

				
					echo '	
					<div>
					<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" id="eExtra_r'.$rowL['id'].'" title="palīg pakalpojums" name="eEresourceNr['.$l.']" '.$disabled.'>';	
					$selectResource = mysqli_query($conn, "
						SELECT r.id, r.name, a.uom
						
						FROM agreements_lines AS a 
						LEFT JOIN n_resource AS r
						ON a.service=r.id
						WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on' AND a.extra_resource='on'
						GROUP BY a.service
					");
					
					
					while($rowi = mysqli_fetch_array($selectResource)){
						echo '<option value="'.$rowi['id'].'" data-uom="'.$rowi['uom'].'"';
							if($rowL['resource']==$rowi['id']){ echo ' selected';}
						echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
					}
					
					echo '
					</select>
					</div>';
?>
<script>
	$(document).ready(function() {


		$(document).on('change', '#eExtra_r<?=$rowL['id'];?>', function(){

			var typez = $(this).find('option:selected').attr('data-uom');

			var input = '<input type="hidden" name="eUnitOfMeasurement[<?=$l;?>]" id="eUnitOfMeasurement[<?=$l;?>]" value="'+typez+'">';

			$('#query_sort_e<?=$rowL['id'];?>').html(typez+input);
		});	

	});	
</script>
<?php		

					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control input-xs numbersOnly eAmount" style="min-width: 50px;" placeholder="daudzums" id="eAmount'.$rowL['id'].'"  name="eAmount['.$l.']" value="'.floatval($rowL['amount']).'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
					</div>						
					';

					echo '<td>';
					echo '<div id="query_sort_e'.$rowL['id'].'">';
					echo '<input type="hidden" name="eUnitOfMeasurement['.$l.']" value="'.$rowL['productUmo'].'" >';
					if($rowL['productUmo']){echo $rowL['productUmo'];}
					echo '</div>';
					
					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control numbersOnly input-xs" style="min-width: 80px;" placeholder="transporta nr." name="eThisTransport['.$l.']" value="'.$rowL['thisTransport'].'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
					</div>';					

						$reque = null;
						if($rowL['no_serial']!=1){
							$reque =  ' oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required ';
						}


						  echo '
						  <td>	 
						  <div class="">
							<input type="text" class="form-control input-xs numbersOnly" style="min-width: 80px;" placeholder="seriālais nr." name="eSerialNo['.$l.']" id="eSerialNo'.$rowL['id'].'" onchange="changeEserial($(this).attr(\'id\'));" value="'.$rowL['serialNo'].'" '.$reque.' '.$disabled.'>
						  </div>';


						echo '<td>
								<textarea class="form-control" rows="2" cols="50" id="eComment['.$l.']" name="eComment['.$l.']" placeholder="komentārs" '.$disabled.'>'.$rowL['comment'].'</textarea>
						</td>';
					
						if($status==0){
							
							echo '<td nowrap>';
							
							if($rowL['status']==0){
								
							echo '<button class="btn btn-default btn-xs" data-dol="'.$rowL['id'].'" onclick="delLine(event)" id="del'.$rowL['id'].'" style="display: inline-block;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';								
								
							}
						
							if(($rowL['status']==0) && ($rowL['location']!='') && ($cif>0) && ($allowSubmit=='Y')){
											echo '
											<a class="btn btn-default btn-xs" data-aol="'.$rowL['id'].'" id="approveOneLine'.$rowL['id'].'" onclick="approveOneLine(event)" style="display: inline-block;"><i class="glyphicon glyphicon-ok" style="color: blue;"></i> nodot</a>';
							}

							if(($rowL['status']==10)){

								echo '<a class="btn btn-default btn-xs" style="margin-right: 2px;" data-col="'.$rowL['id'].'" id="cancelOneLine'.$rowL['id'].'" onclick="cancelOneLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt</a>';	
								
								if($p_edit=='on' && $allowSubmit=='Y'){
									echo '<a class="btn btn-default btn-xs" data-rol="'.$rowL['id'].'" id="receiveOneLine'.$rowL['id'].'" onclick="receiveOneLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> apstiprināt</a>';
								}
							}

							if(($rowL['status']>=20)){
								echo returnCargoLineStatus($conn, $rowL['id']);
							}
						}

						
						
						
					echo '</tr>';
					$l++;
	
}else{
	

			echo '	<tr bgcolor="#eee" class="classlistedit"'; echo '>';

						echo '
						<td>';

							echo '	
							<div>
							<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="palīg pakalpojums" disabled>';	
							$selectResource = mysqli_query($conn, "
								SELECT r.id, r.name, a.uom
								
								FROM agreements_lines AS a 
								LEFT JOIN n_resource AS r
								ON a.service=r.id
								WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on' AND a.extra_resource='on'
								GROUP BY a.service
							");
							while($rowi = mysqli_fetch_array($selectResource)){
								echo '<option value="'.$rowi['id'].'" data-uom="'.$rowi['uom'].'"';
									if($rowL['resource']==$rowi['id']){ echo ' selected';}
								echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
							}
							
							echo '
							</select>
							</div>';

						
echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 50px;" placeholder="daudzums" value="'.floatval($rowL['amount']).'" disabled>
</div>						
';						
						
						
echo '<td>';
if($rowL['productUmo']){echo $rowL['productUmo'];}
					


						echo '
						<td>
						<div class="">
						  <input type="text" class="form-control input-xs" style="min-width: 80px;" placeholder="transporta nr." value="'.$rowL['thisTransport'].'" disabled>
						</div>';						  
						  
						  echo '
						  <td>	
  
						  <div class="">
							<input type="text" class="form-control input-xs" style="min-width: 80px;" placeholder="seriālais nr." value="'.$rowL['serialNo'].'" disabled>
						  </div>';
						  
						echo '<td>
								<textarea class="form-control" rows="2" cols="50" id="eComment['.$l.']" name="eComment['.$l.']" placeholder="komentārs" '.$disabled.'>'.$rowL['comment'].'</textarea>
						</td>';						  

	
					echo '</tr>';	
}


		}
		echo '<input type="hidden" name="lResult" value="'.$l.'">';

		
		if(($status==0)){
			
		?>
		
		
<script>


$(document).on("change", function(){
var appendDate = $("#appendDate").val(); 

$("#thisDate").val(appendDate);
});
</script>
	
<script>

$(document).ready(function(){
	$('#extraResource').click(function(){

		if ($(this).is(':checked')){	
			$('#box_resource').load('pages/additional_services/recourse_sort.php?view=extrar&agr=<?=$row['agreements'];?>');
			$('.hides').hide();

		}else{
			$('#box_resource').load('pages/additional_services/recourse_sort.php?view=extrar&section=org&agr=<?=$row['agreements'];?>');

			$('.hides').show();
		
			$(function(){
			$('#productNr')
        		.trigger('change');
			});

	}

	});
});	

$(document).ready(function() {


	$(document).on('change', '#extra_r', function(){

		var typez = $(this).find('option:selected').attr('data-uom');

		var input = '<input type="hidden" name="unitOfMeasurement" id="unitOfMeasurement" value="'+typez+'">';

        $('#query_sort').html(typez+input);
    });	

});	

</script>


		<?php
			echo '
		<tr>';

			echo '
			<td>';

			echo '
				<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="palīg pakalpojums" name="resource" id="extra_r" '.$disabled.'>';	
				
				$selectResource = mysqli_query($conn, "							
                    SELECT r.id, r.name, a.uom 
                    
                    FROM agreements_lines AS a 
                    LEFT JOIN n_resource AS r
                    ON a.service=r.id
                    WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on' AND a.extra_resource='on'
                    GROUP BY a.service							
							
				") or die(mysqli_error($conn));
				while($rowi = mysqli_fetch_array($selectResource)){
					echo '<option value="'.$rowi['id'].'" data-uom="'.$rowi['uom'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
				}
				
				echo '
				</select>
			</td>';

			echo '
			<td '.$tdStyle.'>
				<div class="">
				  <input style="min-width: 50px;" type="text" class="form-control input-xs numbersOnly" placeholder="daudzums" name="amount" id="amount" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" >
				  <div id="er2"></div>
				</div>				
			</td>';			
	

			echo '
			<td '.$tdStyle.'>
				<div id="query_sort">';
						  
				echo  '
				</div>
			</td>';
	
			echo '
			<td class="hides">
			<div class="">
			  <input type="text"  class="form-control input input-xs" value="" id="thisTransport" name="thisTransport" placeholder="transporta nr.">
			</div>
			</td>';		
			
			echo '
			<td class="hides">
				<div class="">
				  <input style="min-width: 80px;" type="text" class="form-control input-xs numbersOnly" placeholder="seriālais nr." name="serialNo" id="serialNo"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" >
				</div>				
			</td>';
			
			echo '<td class="hides">
					<textarea class="form-control" rows="2" cols="50" id="comment" name="comment" placeholder="komentārs"></textarea>
				  </td>';

			?>

<script>

$(document).ready(function() {
  $('#serialNo').on('change', function() {
    var value = $(this).val();
    $.ajax({
      type: 'post',
      url: 'pages/additional_services/serialNoCheck.php?id=<?=$id;?>&serial='+value,
      success: function(r) {
		  console.log('pages/additional_services/serialNoCheck.php?id=<?=$id;?>&serial='+value);
		  console.log(r);

		  if(r=='error'){
			$('#serialNo').css("background-color", "rgb(217, 83, 79)");
			$('#serialNo').css("color", "white");
			$('#savebtn').hide();
			$('#saveIt').hide();
		  }

		  if(r=='done'){
			$('#serialNo').css("background-color", "white");
			$('#serialNo').css("color", "#555");
			$('#savebtn').show();
			$('#saveIt').show();			
		  }		  

      }
    });
  });  
});

function changeEserial(val){
		  	
			var value = $('#'+val).val();
			var t = val.replace(/\D/g,'');
			$.ajax({
			type: 'post',
			url: 'pages/additional_services/serialNoCheck.php?id=<?=$id;?>&line='+t+'&serial='+value,
			success: function(r) {
				console.log('pages/additional_services/serialNoCheck.php?id=<?=$id;?>&line='+t+'&serial='+value);
				console.log(r);

				if(r=='error'){
					$('#'+val).css("background-color", "rgb(217, 83, 79)");
					$('#'+val).css("color", "white");
					$('#savebtn').hide();
					$('#saveIt').hide();
				}

				if(r=='done'){
					$('#'+val).css("background-color", "white");
					$('#'+val).css("color", "#555");
					$('#savebtn').show();
					$('#saveIt').show();			
				}		  

			}
			});
	  }
</script>

			<?php

			echo '
			<td '.$tdStyle.' colspan="2"><button type="submit" onclick="chBtn()" class="btn btn-default btn-xs class" data-key="pievienot" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button></td>				
		</tr>';
		}
	echo '</tbody>
	</table>
	<input type="hidden" id="addToPost" value="pievienot">
	</div>
	</form>';
		
	?>
	<script>
		function chBtn() {
			$('#addToPost').attr('name', 'add_to_post');
		}
	</script>
	<?php


		if(($row['status']==0) && ($cif>0) && ($allowSubmit=='Y')){	
			echo '<button type="submit" class="btn btn-default btn-xs" id="approveLine" data-rowid="'.$row['id'].'" onclick="approveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</button>';
		}

		if(($row['status']==10)){
			
			echo '<a target="_blank" href="print?view=additional_services&id='.$id.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
			echo '<button type="submit" class="btn btn-default btn-xs" style="margin-right: 2px;" data-cl="'.$row['id'].'" id="cancelLine" onclick="cancelLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt nodošanu</button>';		
			
			if($p_edit=='on' && $allowSubmit=='Y'){
				echo '<button type="submit" class="btn btn-default btn-xs" data-rl="'.$row['id'].'" id="receiveLine" onclick="receiveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> apstiprināt</button>';
			}
		}
		if($row['status']==20){
			echo '<a target="_blank" href="print?view=additional_services&id='.$id.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
		}
	
	
}	



// saņemšanas vēsture
if($view=='history' && !$id){
?>





<script>
function newDoc(val) {
	$('#contenthere').load('/pages/additional_services/additional_services.php?view=history&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$('#contenthere').load('/pages/additional_services/additional_services.php?view=history&client='+val+'');
}
</script>
	<script>
	$('#removeFilter').on('click', function() {
		$('#contenthere').load('/pages/additional_services/additional_services.php?view=history');
	});
	</script>
	
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#contenthere').load('/pages/additional_services/'+url+'&view=history&client=<?=$client;?>');
			event.preventDefault();
		});
	});
	</script>	
<?php	
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default  classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
				echo '<a class="btn btn-default active classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
			echo '</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success" >saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">saņemšanas vēsture</p>';

		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilter"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			  
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM additional_services_header") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			
			';		

?>

<script type="text/javascript">

function getStates(value) {
	var client = '<?=$client;?>';
    var search = $.post("/pages/additional_services/search.php", {name:value, client:client},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>


<?php	
	
echo ' <div style="float: right; display: inline-block;" ><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div><div class="clearfix"></div>'; 	

			
	
	echo '<div id="results">';	
			
	if($client){$filterClient = ' WHERE clientCode="'.$client.'" AND status>=20';}else{$filterClient = ' WHERE status>=20';}
	


	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM additional_services_header ".$filterClient." ";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>akta datums</th>
						
						<th>pavadzīmes nr.</th>
						<th>akta nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>STENA LINE PORTS VENTSPILS pārstāvis</th>
						<th>kravas nosaukums</th>
						<th>statuss</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				echo '	<tr class="classlistedit" onclick="newDoc('.$row['id'].')">
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							
							echo '
							<td>'.$row['ladingNr'].'
							<td>'.$row['acceptance_act_no'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['home_delegate'].'
							<td>'.$row['cargo_name'].'
							<td>'.returnExtraStatus($conn, $row['id']).'

						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
		}
		echo '</div>';
} // saņemšanas vēsture beidzas


// saņemšanas vēstures rindas
if($view=='history' && $id){

	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
			
			echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
			echo '<a class="btn btn-default active classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
		echo '</div>';

			  
	echo '</div>';	
	$GetXY = mysqli_query($conn, "SELECT * FROM additional_services_header WHERE id='".intval($id)."'");
	$GXYrow = mysqli_fetch_array($GetXY);
	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	echo '<p style="display:inline-block;">vēsture</p> <div style="display:inline-block; font-weight: bold;">'.$isDocNr.'</div>';

	$result = mysqli_query($conn,"SELECT * FROM additional_services_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	if($status>0 || $p_edit!='on'){$disabled='disabled';}else{$disabled=null;}
	?>

	
	<?php
	echo '
	  <div class="form-row">';






		echo '
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="clientCode">klienta kods - nosaukums</label>
				<select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="klienta kods - nosaukums"  disabled>
				<option></option>';
			  $selectClient = mysqli_query($conn, "
				SELECT DISTINCT(c.Code) AS Code, c.Name 
				FROM n_customers AS c
				LEFT JOIN agreements AS a
				ON c.Code=a.customerNr 
				WHERE (a.dateTo='0000-00-00 00:00:00' OR a.dateTo IS NULL OR a.dateTo>CURDATE()) AND a.status<20 AND a.deleted='0'
			  ") or die(mysqli_error($conn));
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
				<label class="lb-sm"  for="agreements">līgums</label>
					<select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="līgums" disabled>
					<option></option>
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
			</div>';

			
		echo '
		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="deliveryDate">akta datums</label>
		  <input type="text" class="form-control datepicker input-xs" placeholder="akta datums" value="'.date('d.m.Y', strtotime($row['deliveryDate'])).'" disabled>
		</div>';
		
		echo ' 
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="acceptanceNr">akta nr.</label>
			<input type="text" class="form-control input-xs" id="acceptanceNr" value="'.$row['acceptance_act_no'].'" disabled>
		</div><div class="clearfix"></div>';			
			
			
		echo '
	  	<div class="form-group col-md-3">
	  		<label class="lb-sm"  for="applicationDate">pieteikuma dat.</label>
	 	    <input type="text" class="form-control datepicker input-xs" placeholder="pieteikuma dat." 	value="'.date('d.m.Y', strtotime($row['application_date'])).'" disabled>
  		</div>

		<div class="form-group col-md-3">
		<label class="lb-sm"  for="applicationNo">pieteikuma nr.</label>
		<input type="text" class="form-control input-xs" placeholder="pieteikuma nr." value="'.$row['application_no'].'" disabled>
		</div>
		
		<div class="form-group col-md-3">
			<label class="lb-sm"  for="landingDate">pavadzīmess dat.</label>
			<input type="text" class="form-control datepicker input-xs" placeholder="pavadzīmes dat." value="'.date('d.m.Y', strtotime($row['landingDate'])).'" disabled>
		</div>

		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="ladingNr">pavadzīmes nr</label>
		  <input type="hidden" name="docNr" id="docNr" value="'.$row['docNr'].'">
		  <input type="text" class="form-control input-xs" placeholder="pavadzīmes nr" value="'.$row['ladingNr'].'" disabled>
		</div><div class="clearfix"></div>';
		
		
			echo '
			
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="deliveryType">transporta veids</label>
				<select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="transporta veids"  disabled>
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
      <label for="home_delegate">STENA LINE PORTS VENTSPILS pārstāvis</label>
      <input type="text" class="form-control" placeholder="STENA LINE PORTS VENTSPILS pārstāvis" value="'.$row['home_delegate'].'" disabled>
    </div>

    <div class="form-group col-md-3">
      <label for="client_delegate">klienta pārstāvis</label>
      <input type="text" class="form-control" placeholder="klienta pārstāvis" value="'.$row['client_delegate'].'" disabled>
    </div>';	


	
	echo '
    <div class="form-group col-md-3">
      <label for="cargo_name">kravas nosaukums</label>
      <input type="text" class="form-control" placeholder="kravas nosaukums" value="'.$row['cargo_name'].'" disabled>
    </div>
	<div class="clearfix"></div>

    <div class="form-group col-md-4">
      <label for="description">sastādīšanas apraksts</label>
	  <textarea class="form-control" rows="2"  cols="50" placeholder="sastādīšanas apraksts" disabled>'.$row['description'].'</textarea>
    </div>	
	<div class="clearfix"></div><br>
 ';



		echo '<div class="clearfix"></div>

	  </div>


	  <div class="clearfix"></div>';
	  	  
	  
	  echo '<div class="clearfix"></div>';
	
	$getLastLine = mysqli_query($conn, "SELECT thisDate, thisTransport FROM additional_services_line WHERE docNr='".$row['docNr']."' ORDER BY id DESC") or die(mysqli_error($conn));
	$gllRow = mysqli_fetch_array($getLastLine);
	
	if($gllRow['thisDate']!='0000-00-00 00:00:00' && $gllRow['thisDate']!=''){$devTime = date('d.m.Y', strtotime($gllRow['thisDate']));}else{$devTime = date('d.m.Y', strtotime($row['deliveryDate']));}
	
	if($gllRow['thisTransport']!=''){$devTransport = $gllRow['thisTransport'];}else{$devTransport = $row['transportNo'];}	

?>
<script>
$(document).ready(function(){
    $(".toggler").click(function(e){
        e.preventDefault();
        $('.cat'+$(this).attr('data-prod-cat')).toggle();
    });
});
</script>
<?php
	
	echo '<div class="clearfix"></div>
	
	
	<div class="table-responsive"><table class="table-responsive"><table class="table table-hover table-responsive">
		<thead> 
			<tr>
				<th>palīg pakalpojums</th>
				<th>daudzums</th>
				<th>mērvienība</th>
				<th>transporta nr.</th>
				<th>seriālais nr.</th>
				<th>komentārs</th>
			</tr>
		</thead>
		<tbody>';
		
		
		$lines = mysqli_query($conn, "SELECT * FROM additional_services_line WHERE docNr='".$row['docNr']."'");
		while($rowL = mysqli_fetch_array($lines)){
			
			echo '	<tr>';
			
				echo '<td><input type="hidden" name="eLineId[]" value="'.$rowL['id'].'">';

				
					echo '	
					<div>
					<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="palīg pakalpojums" disabled>';	
					$selectResource = mysqli_query($conn, "
						SELECT r.id, r.name, a.uom
						
						FROM agreements_lines AS a 
						LEFT JOIN n_resource AS r
						ON a.service=r.id
						WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on' AND a.extra_resource='on'
						GROUP BY a.service
					");
					
					
					while($rowi = mysqli_fetch_array($selectResource)){
						echo '<option value="'.$rowi['id'].'" data-uom="'.$rowi['uom'].'"';
							if($rowL['resource']==$rowi['id']){ echo ' selected';}
						echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
					}
					
					echo '
					</select>
					</div>';
	

					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control input-xs" style="min-width: 50px;" placeholder="daudzums" value="'.floatval($rowL['amount']).'" disabled>
					</div>						
					';

					echo '<td>'; if($rowL['productUmo']){echo $rowL['productUmo'];}

					
					echo '
					<td>
					<div class="">
					  <input type="text" class="form-control input-xs" style="min-width: 80px;" placeholder="transporta nr."  value="'.$rowL['thisTransport'].'"  disabled>
					</div>';					
								

						  echo '<td><input type="text" class="form-control input-xs" style="min-width: 80px;" placeholder="seriālais nr." value="'.$rowL['serialNo'].'" disabled>';


						echo '<td>
								<textarea class="form-control" rows="2" cols="50" placeholder="komentārs" disabled>'.$rowL['comment'].'</textarea>
						</td>';	
						
					echo '</tr>';
									
		}

		
	echo '</tbody>
	</table></div>';	
	
		if($row['status']==20){
			echo '<a target="_blank" href="print?view=additional_services&id='.$id.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt</a>';
		}
	
} // saņemšanas vēstures rindas beidzas


?>
			<?php if($id){ ?>
			<br><br>
			<div class="panel panel-default" style="margin-bottom: -5px;">
				<div class="panel-body">
					<div style="float: right; margin: -10px;">pēdējās aktivitātes veica: <?=lastActionByAS($conn, $id);?></div>
				</div>
			</div>
			<?php } ?>
			


<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker('render');
});
</script>

<?php include_once("../../datepicker.php"); ?>