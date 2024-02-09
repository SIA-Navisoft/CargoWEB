<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="receipt";



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

?>
<style>
.tooltip-inner{

  background-color: black !important;
  word-wrap: break-word !important;
  text-align: left !important;
  white-space: pre-line !important;
}
</style>

<?
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
        $('#contenthere').load('/pages/receipt/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/receipt/receipt.php<?=$glpage;?>');
    });
})
</script>

<script>

$(document).ready(function () {
    $('#deleteIt').on('click', function(e) {
        e.preventDefault();

		if (confirm('UZMANĪBU! Vai tiešām vēlaties dzēst ierakstus?')) {

			$.ajax({
				url : '/pages/receipt/post.php?r=deleteIt&id=<?=$id;?>',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					$('#savebtn').html("gaidiet...");
					$("#savebtn").prop("disabled",true);
				},			
				success: function (data) {
					console.log(data);

					$('#contenthere').load('/pages/receipt/receipt.php?res=done');
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
        $('#contenthere').load('/pages/receipt/receipt.php?view=history');
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
			
			$('#contenthere').load('/pages/receipt/'+url);
			event.preventDefault();
		});
	});
	</script>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/receipt/receipt.php?client='+val+'&search='+search);
}
</script>
	<script>
	$('#removeFilterCust').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/receipt/receipt.php?search='+search);
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
		
		echo '<p style="display:inline-block;">saņemšana</p>';

		if($client){echo '
			<div class="alert alert-info alert-dismissible" role="alert" style="display:inline-block; padding-top: 5px; padding-bottom: 5px;">
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCust"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-2 pull-right">
				<select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">
				<option></option>';
				
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header WHERE (status='0' OR status='10')") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  if($rowc['clientCode']){
					echo '<option value="'.$rowc['clientCode'].'"';
						if($rowc['clientCode']==$client){ echo ' selected';}
					echo ' >'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
				  }
			  }
			  
			  echo '
			  </select>	
			</div>
			
			';		

?>

<script type="text/javascript">

function getStates(value) {
	var client = '<?=$client;?>';
    var search = $.post("/pages/receipt/search.php", {name:value, client:client},function(data){
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
	
echo ' <div style="float: right; display: inline-block;" ><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt" value="'.$_GET['search'].'"></div></div><div class="clearfix"></div>'; 	

			
	
	echo '<div id="results">';	
			
	if($_GET['search']){
		$s = mysqli_real_escape_string($conn, $_GET['search']);	
		$search = " AND (
		docNr LIKE '%$s%' || 
		ladingNr LIKE '%$s%' || 
		clientCode LIKE '%$s%' ||
		clientName LIKE '%$s%' || 
		ownerCode LIKE '%$s%' || 
		ownerName LIKE '%$s%' ||
		receiverCode LIKE '%$s%' || 
		receiverName LIKE '%$s%' || 
		cargoCode LIKE '%$s%' ||
		deliveryDate LIKE '%$s%' || 
		deliveryType LIKE '%$s%' || 
		deliveryCode LIKE '%$s%' ||
		location LIKE '%$s%' || 
		createdSource LIKE '%$s%' ||
		thisTransport LIKE '%$s%' ||
		acceptance_act_no LIKE '%$s%'
		) ";
		$searchUrl = '&search='.$s;
			
	}else{
		$searchUrl = null;	
		$search = "";	
	}			
			
			
	if($client){$filterClient = ' AND clientCode="'.$client.'"';}else{$filterClient = null;}
	
	$keepFilter=null;
	if($client){$keepFilter.='&client='.$client;}


	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?view='.$keepFilter.$searchUrl.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(SELECT SUM(amount) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as vienibas,
	(SELECT SUM(gross) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as bruto,
	(SELECT thisTransport FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr LIMIT 1) AS lineThisTransport
	 FROM cargo_header WHERE (status='0' || status='10') ".$filterClient." ".$search." ORDER BY CASE WHEN scanStatus=200 THEN scanStatus END DESC, deliveryDate ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>pavadzīmes nr.</th>
						<th>dokumenta nr. (iekš.)</th>
						<th>piegādes dat.</th>

						<th>transporta nr.</th>
						<th>pieņemšanas akta nr.</th>
						<th>vienības</th>
						<th>bruto (kg)</th>
						
						<th>nosaukums</th>				
						
						<th>statuss</th>
						<th>importa datums</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}
				
				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}				

				echo '	<tr class="classlistedit" '.$trAction.'>
							
							<td '.$show_green.'>'.$row['ladingNr'].'
							
							<td>'.$row['docNr'].'
				
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							
							

							echo '<td>'; 							
							if($row['thisTransport']){
								echo $row['thisTransport'];
							}else{
								echo $row['lineThisTransport']; // p'ec piepras'ijuma r'ad'it pirmo transportu
							}

							echo '<td>'.$row['acceptance_act_no'].'
							<td>'.floatval($row['vienibas']).'
							<td>'.floatval($row['bruto']).'
							
							<td>'.$row['clientName'];				
							
							echo '<td>'.returnCargoStatus($conn, $row['id']).'

							<td>';

							if($row['importDate']!='0000-00-00 00:00:00'){
								echo $row['importDate'];
							}

							echo '							
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
		if ($res=="received"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saņemts!</div></div>';}
		if ($res=="canceled"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">atsaukts!</div></div>';}
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';
			  
	echo '</div>';	
	$GetXY = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$GXYrow = mysqli_fetch_array($GetXY);

	$importedDoc=null;
	if($GXYrow['importDate']!='0000-00-00 00:00:00'){$importedDoc = '<div style="display:inline-block; float: right;">importa datums <b>'.$GXYrow['importDate'].'</b></div>';}

	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	echo '<p style="display:inline-block;">labot</p> <div id="printchatbox" style="display:inline-block; font-weight: bold;">'.$cCode.'</div> dokumenta nr. (iekš.) <b>'.$GXYrow['docNr'].'</b> '.$importedDoc;

	$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	
	
	if($row['scanStatus']==100){

		header("Location: ".$page_file."");
		die(0);			
		
	}
	
	
	$status = $row['status'];
	if($status>0){$disabled='disabled';}else{$disabled=null;}
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

		$(document).ready(function () {
			$('#send_profile').on('submit', function(e) {
				e.preventDefault();

				var str = this.dataset.key;
				
				
				$.ajax({
					url : '/pages/receipt/post.php?r=edit&id=<?=$id;?>',
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
						$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=done');
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
			url: '/pages/receipt/action.php?action=deleteLine&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=del');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}


	function showPic(event) {
		event.preventDefault();
		
		var val = '';
		var val = $(event.currentTarget).data("pic");

		$.ajax({
			global: false,
			type: 'GET',
			url: 'showPic.php?filekey='+val,
			beforeSend: function(){
				$('data-'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("data-"+val+"").prop("disabled",true);
			},				
			success: function (result) {

				$('.showPic').show();
				
				$('.panel-body.forPic').html(result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
		
	}

	$(".showPic").draggable();
	
	$(".closeIt").on('click', function(){
		$('.showPic').hide();
	});
	
	
	
	</script>
	

<div class="panel panel-default showPic" style="display: none; position:fixed; z-index: 1000;"> 
	<div class="panel-heading"> 
		<h3 class="panel-title" style="display: inline-block;">Bilde no skenēšanas</h3> 
		<button type="button" class="close closeIt" style="float: right; display: inline-block;">&times;</button>
	</div> 
	<div class="panel-body forPic"> ... </div> 
</div>	
	
	<script>	  
	function approveLine(event) {
		
		event.preventDefault();
		
		var val = $(event.target).data("rowid");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/receipt/action.php?action=approveLine&id='+val+'',
			beforeSend: function(){
				$('#approveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=approved');
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
			url: '/pages/receipt/action.php?action=approveOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#approveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#approveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=approved');
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
			url: '/pages/receipt/action.php?action=receiveLine&id='+val+'',
			beforeSend: function(){
				$('#receiveLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=received');
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
			url: '/pages/receipt/action.php?action=receiveOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#receiveOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#receiveOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=received');
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
			url: '/pages/receipt/action.php?action=cancelLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#cancelLine').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelLine").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=canceled');
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
			url: '/pages/receipt/action.php?action=cancelOneLine&id='+val+'&cardId=<?=$id;?>',
			beforeSend: function(){
				$('#cancelOneLine'+val).html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cancelOneLine"+val).prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$id;?>&res=canceled');
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
	$('#senderCode').on('change', function() {
		var typex = $('#senderCode option:selected').attr('data-sname');
		
		$('#senderName').val(typex);
		
	});
	</script>	

<script>
$('#clientCode').on('change', function() {
    
		var typez = $('#clientCode option:selected').attr('data-cnid');

		$( "#secondOption" ).load( "pages/receipt/change.php?select="+typez+"" );
		$( "#resourceOption" ).load( "/pages/receipt/change.php?view=sort_resource&select="+typez+"" );
		
});

function selAgreement(val) {

var val = val.value;
var val = encodeURI(val);

$( "#resourceOption" ).load( "/pages/receipt/change.php?view=sort_resource&resource="+val+"" );

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
			<div id="secondOption">';
			
				echo '
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
				</div>			
			</div>';
			
			echo '
			<div class="form-group col-md-3">
			<label class="lb-sm" for="senderCode">nosūtītāja kods - nosaukums</label>
			
				<select class="form-control selectpicker btn-group-xs input-xs"  id="senderCode" name="senderCode"  data-live-search="true" title="nosūtītāja kods - nosaukums">
					<option></option>';
				
				$selectOwner = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, name FROM senders WHERE status='1'") or die(mysqli_error($conn));
				while($rowo = mysqli_fetch_array($selectOwner)){
					echo '<option data-sname="'.$rowo['name'].'" value="'.$rowo['Code'].'"';
					if($row['senderCode']==$rowo['Code']){echo ' selected';}
					echo '>'.$rowo['Code'].' - '.$rowo['name'].'</option>';
				}
				
				echo '
				</select>	
			</div>			
			';
			
			
			echo '
			<div id="resourceOption">
				<div class="form-group col-md-3">	
				<label for="resource">pakalpojums <span class="asterisc">*</span></label>			
					
					<select class="form-control selectpicker btn-group-xs" id="resource"  data-live-search="true" title="pakalpojums" name="resource" '.$disabled.'>';	
					$selectResource = mysqli_query($conn, "
						SELECT r.id, r.name 
						
						FROM agreements_lines AS a 
						LEFT JOIN n_resource AS r
						ON a.service=r.id
						WHERE a.contractNr='".$row['agreements']."' AND a.keeping!='on' AND a.extra_resource!='on' AND a.deleted='0'
						GROUP BY a.service
					");
					while($rowi = mysqli_fetch_array($selectResource)){
						echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
							if($row['resource']==$rowi['id']){ echo ' selected';}
						echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
					}
					
					echo '
					</select>					
				</div>			
			</div>				
			';	  
	  
		echo '
	  	<div class="form-group col-md-3">
	  		<label class="lb-sm"  for="applicationDate">pieteikuma dat. <span class="asterisc">*</span></label>
	 	    <input type="text" class="form-control datepicker input-xs" name="applicationDate" id="applicationDate" placeholder="pieteikuma dat." 	value="'.date('d.m.Y', strtotime($row['application_date'])).'" '.$disabled.'>
  		</div>

		<div class="form-group col-md-3">
		<label class="lb-sm"  for="applicationNo">pieteikuma nr. <span class="asterisc">*</span></label>
		<input type="text" class="form-control input-xs" name="applicationNo" id="applicationNo" placeholder="pieteikuma nr." value="'.$row['application_no'].'" '.$disabled.'>
		</div>

		<div class="form-group col-md-3">
			<label class="lb-sm"  for="landingDate">pavadzīmess dat. <span class="asterisc">*</span></label>
			<input type="text" class="form-control datepicker input-xs" name="landingDate" id="landingDate" placeholder="pavadzīmes dat." value="'.date('d.m.Y', strtotime($row['landingDate'])).'" '.$disabled.'>
		</div>

		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="ladingNr">pavadzīmes nr <span class="asterisc">*</span></label>
		  <input type="hidden" name="docNr" id="docNr" value="'.$row['docNr'].'">
		  <input type="text" class="form-control input-xs" name="ladingNr" id="ladingNr" placeholder="pavadzīmes nr" value="'.$row['ladingNr'].'" '.$disabled.'>
		</div>

		<div class="form-group col-md-3">
		  <label class="lb-sm"  for="deliveryDate">piegādes dat. <span class="asterisc">*</span></label>
		  <input type="text" class="form-control datepicker input-xs" name="deliveryDate" id="deliveryDate" placeholder="piegādes dat." value="'.date('d.m.Y', strtotime($row['deliveryDate'])).'" '.$disabled.'>
		</div>	  

		';

		echo '
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="deliveryCode">kravas tips</label>
				<select class="form-control selectpicker btn-group-xs input-xs"  name="deliveryCode" id="deliveryCode"  data-live-search="true" title="kravas tips"  '.$disabled.'>
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
			
			<div class="form-group col-md-3">
			<label class="lb-sm"  for="deliveryType">transporta veids <span class="asterisc">*</span></label>
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
			  <label for="thisTransportEdit">transporta nr. <span class="asterisc">*</span></label>
			  <input type="text" class="form-control" name="thisTransportEdit" id="thisTransportEdit" placeholder="transporta nr." value="'.$row['thisTransport'].'" '.$disabled.'>
			</div>			
			<div class="clearfix"></div>

			<div class="form-group col-md-3">
				<label class="lb-sm"  for="shipId">kuģis izkraušanai <span class="asterisc">*</span></label>
				<input type="text" class="form-control input-xs" name="shipId" id="shipId" placeholder="kuģis izkraušanai" value="'.$row['ship'].'" '.$disabled.'>
			</div>
			
			';

$aDate = null;
if($row['acceptance_act_date']!='' && $row['acceptance_act_date']!='0000-00-00 00:00:00'){
	$aDate = date('d.m.Y', strtotime($row['acceptance_act_date']));
}

echo ' 
<div class="form-group col-md-3">
<label class="lb-sm">pieņemšanas akta dat.</label>
<input type="text" class="form-control datepicker input-xs" placeholder="pieņemšanas akta dat." id="acceptanceDate" name="acceptanceDate" value="'.$aDate.'"  '.$disabled.'>
</div>';

echo ' 
  <div class="form-group col-md-3">
<label class="lb-sm"  for="acceptanceNr">pieņemšanas akta nr. <span class="asterisc">*</span></label>
<input type="text" class="form-control input-xs" id="acceptanceNr" name="acceptanceNr" placeholder="pieņemšanas akta nr."  value="'.$row['acceptance_act_no'].'" '.$disabled.'>
 </div>

<div class="form-group col-md-3">
  <label for="declarationTypeNo">deklarācijas nr. <span class="asterisc">*</span></label>
  <input type="text" class="form-control" name="declarationTypeNo" id="declarationTypeNo" placeholder="deklarācijas nr." value="'.$row['declaration_type_no'].'" '.$disabled.'>
</div>	


 <div class="form-group col-md-3">
  <label class="lb-sm"  for="cargoStatus">kravas status <span class="asterisc">*</span></label>
	<select class="form-control selectpicker btn-group-xs input-xs"  id="cargoStatus" name="cargoStatus"  data-live-search="true" title="kravas status" '.$disabled.'>
	<option value="C"'; if($row['cargo_status']=='C'){echo ' selected';} echo '>C</option>
	<option value="N"'; if($row['cargo_status']=='N'){echo ' selected';} echo '>N</option>
	<option value="EU"'; if($row['cargo_status']=='EU'){echo ' selected';} echo '>EU</option>
	</select>	
 </div>
 


 
 
 <div class="clearfix"></div>
 <div class="form-group col-md-3">
										
 <div class="checkbox">
	 <label><input type="checkbox" id="copyRest" name="copyRest"'; if($row['copyRest']==1){echo ' checked';} echo '>dublēt līnijas vērtības(izņ. seriālo nr.)</label>
 </div>
</div>';  

if($row['scanFinishedBy']>0){
	
	echo '
	<div class="form-group col-md-3" style="float: right;">
		Skenēja: <b>'.returnMeWho($row['scanFinishedBy']).'</b> Laiks: <b>'.date('d.m.Y H:i:s', strtotime($row['scanFinishedDate'])).'</b>
	</div>';

}

 
 
		$allowSubmit=null;	
	    if($row['clientCode']!='' && $row['ownerCode']!='' &&  $row['agreements']!='' &&  $row['resource']!='' &&  $row['application_date']!='0000-00-00 00:00:00' &&  $row['application_no']!='' &&  $row['landingDate']!='0000-00-00 00:00:00' &&  $row['ladingNr']!='' &&  $row['deliveryDate']!='0000-00-00 00:00:00' &&  $row['transport']!='' &&  $row['thisTransport']!='' &&  $row['ship']!='' &&  $row['acceptance_act_no']!='' &&  $row['declaration_type_no']!=''){
			$allowSubmit = 'Y';
		} 
			
		echo '<div class="clearfix"></div><br>';		
		if($isDocNr){

			
			$inCargo = mysqli_query($conn, "
							SELECT SUM(amount) as amount, SUM(assistant_amount) as assistant_amount, SUM(volume) as volume, 
							SUM(tare) as t, SUM(gross) as b, SUM(net) as n, SUM(delta_net) as dn, SUM(cubicMeters) as a, SUM(place_count) AS c,
							SUM(document_net) AS don, 
							productUmo, assistantUmo, productNr, resource, extra_resource FROM cargo_line 
							WHERE docNr='".$isDocNr."' 
							GROUP BY productUmo, assistantUmo, resource") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive">
						<thead>
							<th>pakalpojums</th>
							<th>mērvienība</th>
							<th>daudzums</th>
							<th>palīg mērvienība</th>
							<th>palīg mērvienības daudzums</th>
							<th>dok. neto (kg)</th>
							<th>vietu skaits</th>
							<th>tara (kg)</th>
							<th>bruto (kg)</th>
							<th>neto (kg)</th>
							<th>Δ neto (kg)</th>
							<th>apjoms (m3)</th>
						</thead>
						<tbody>
						';			
			while($icRow = mysqli_fetch_array($inCargo)){				
				echo '

						
							<tr>
								<td>'; 
								if($icRow['extra_resource']=='on'){echo 'Pap. pak. ';} echo $icRow['resource'];
								echo '</td>
								<td>'.$icRow['productUmo'].'</td>
								<td>'.floatval($icRow['amount']).'</td>
								<td>'.$icRow['assistantUmo'].'</td>
								<td>'.floatval($icRow['assistant_amount']).'</td>
								<td>'.floatval($icRow['don']).'</td>
								<td>'.floatval($icRow['c']).'</td>
								<td>'.floatval($icRow['t']).'</td>
								<td>'.floatval($icRow['b']).'</td>
								<td>'.floatval($icRow['n']).'</td>
								<td>'.floatval($icRow['dn']).'</td>
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
		if($GXYrow['senderName']){$sName=$GXYrow['senderName'];}else{$oName=null;}
		if($GXYrow['receiverName']){$rCode=$GXYrow['receiverName'];}else{$rCode=null;}		
		echo '

	  </div>
       <input type="hidden" class="form-control" name="clientName" id="clientName" value="'.$cName.'">
	   <input type="hidden" class="form-control" name="ownerName" id="ownerName" value="'.$oName.'">
	   <input type="hidden" class="form-control" name="senderName" id="senderName" value="'.$sName.'">
	    <input type="hidden" class="form-control" name="cargoCode" id="cargoCode" value="'.$cCode.'">
		<input type="hidden" class="form-control" name="receiverName" id="receiverName" value="'.$rCode.'">

	  ';
	  if(($status==0)){echo '<button type="submit" class="btn btn-default btn-xs" id="saveIt"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';}
	  
	  $getDocNr = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($id)."'");
		$gdr = mysqli_fetch_array($getDocNr);
		
		$docNr = $gdr['docNr'];	
		
		$checkIfLine = mysqli_query($conn, "SELECT id FROM cargo_line WHERE docNr='".$docNr."'");
		$cif = mysqli_num_rows($checkIfLine);
	  
echo '
<a href="/pages/receipt/status.php?id='.$id.'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
    <i class="glyphicon glyphicon-option-vertical"></i> statuss
</a>';	  

	$isProcessed = mysqli_query($conn, "SELECT id FROM cargo_line WHERE docNr='".$docNr."' AND status>0");
	$isP = mysqli_num_rows($isProcessed);

	if(($status==0)&&($isP==0)){echo '<button type="submit" class="btn btn-default btn-xs" id="deleteIt" style="margin-left: 4px;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';}

	echo '<div class="clearfix"></div>';

	$getLastLine = mysqli_query($conn, "SELECT * FROM cargo_line WHERE docNr='".$row['docNr']."' AND extra_resource!='on' ORDER BY id DESC") or die(mysqli_error($conn));
	$gllRow = mysqli_fetch_array($getLastLine);
	
	if($gllRow['thisDate']!='0000-00-00 00:00:00' && $gllRow['thisDate']!=''){$devTime = date('d.m.Y', strtotime($gllRow['thisDate']));}else{$devTime = date('d.m.Y', strtotime($row['deliveryDate']));}
	
	if($gllRow['thisTransport']!=''){$devTransport = $gllRow['thisTransport'];}else{$devTransport = $row['transportNo'];}	
	if($gllRow['ladingNr']!=''){$devLadingNr = $gllRow['ladingNr'];}else{$devLadingNr = $row['ladingNr'];}
	echo '<div class="clearfix"></div>';
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

<th>produkta nr.</th>';

if(in_array($row['clientCode'], $allowedClients)){echo'<th>seriālais nr. (plāns)</th>';}

echo '
<th>seriālais nr. (fakts)</th>

<th>daudzums</th>
<th>mērvienība</th>

<th>palīg mērv. daudzums</th>
<th>palīg mērvienība</th>
<th>dok. neto (kg)</th>
<th>vietu skaits</th>


<th>tara (kg)</th>
<th>bruto (kg)</th>
<th>neto (kg)</th>
<th>&Delta; neto (kg)</th>				
<th nowrap>apjoms (m3)</th>

<th>partijas nr.</th>
<th>noliktava</th>

<th>markas nr.</th>

<th>konteinera tips</th>
<th>pavadzīmes nr.</th>
<th>transporta nr.</th>

<th>deklarācijas nr.</th>

<th>plombes numurs</th>



<th>svēršanas akta nr.</th>
<th>brāķis</th>


				';
				if(($status==0)){echo '<th>darbība</th>';}
			echo '
			    <th></th
			</tr>
		</thead>
		<tbody>';
		
		$lines = mysqli_query($conn, "
			SELECT cargo_line.*, n_items.no_serial, sl.img
			FROM cargo_line 
			LEFT JOIN n_items
			ON cargo_line.productNr=n_items.code
			
			
			LEFT JOIN scanner_lines AS sl
			ON cargo_line.scanId=sl.id
			
			
			WHERE cargo_line.docNr='".$row['docNr']."'
			");		
		$l=0; $noNext = null;
		while($rowL = mysqli_fetch_array($lines)){
			
if($rowL['status']>0){$disabled='disabled';}else{$disabled=null;}						




						
if($rowL['status']==0){

			echo '	<tr class="classlistedit"'; echo '>';

			echo '<td><input type="hidden" name="eLineId[]" value="'.$rowL['id'].'">';
					
?>
<script>

$('#eAmount<?=$rowL['id'];?>').on('keyup change', function(){
	var val = $('#eProductNr<?=$rowL['id'];?> option:selected').attr('data-cnecc');
	var val = encodeURI(val);
	$('#query_sort_e<?=$rowL['id'];?>').load('pages/receipt/query_sort.php?view=eline&input=eAmount&select='+val+'&id=<?=$rowL['id'];?>');
	
});	

$('#eAssistantAmount<?=$rowL['id'];?>').on('keyup change', function(){
	var val = $('#eProductNr<?=$rowL['id'];?> option:selected').attr('data-cnecc');
	var val = encodeURI(val);
	$('#query_sort_e<?=$rowL['id'];?>').load('pages/receipt/query_sort.php?view=eline&input=AeAmount&select='+val+'&id=<?=$rowL['id'];?>');
	
});	

$('#eProductNr<?=$rowL['id'];?>').on('change', function() {
	

	var val = $('#eProductNr<?=$rowL['id'];?> option:selected').attr('data-cnecc');
	var val = encodeURI(val);	

	$("#eGross<?=$rowL['id'];?>").val('0');
	$("#eNet<?=$rowL['id'];?>").val('0');
	$("#eTare<?=$rowL['id'];?>").val('0');
	$("#eCubicMeters<?=$rowL['id'];?>").val('0');

	$('#query_sort_e<?=$rowL['id'];?>').load('pages/receipt/query_sort.php?view=eline&input=all&select='+val+'&id=<?=$rowL['id'];?>');
	$('#query_sort_extra_e<?=$rowL['id'];?>').load('pages/receipt/query_sort_extra_uom.php?view=eline&select='+val+'');
});

$('#eProductNr<?=$rowL['id'];?>').on('change', function() {

	var val = $('#eProductNr<?=$rowL['id'];?> option:selected').attr('data-cnecc');
	var val = encodeURI(val);	

	var id = '<?=$row['agreements'];?>';
	var row = '<?=$rowL['id'];?>';
	$('#resourceNrRetM<?=$rowL['id'];?>').load('pages/receipt/recourse_sort.php?view=multiple&select='+val+'&id='+id+'&row='+row+'');

});

</script>
<?php							
						
					if($rowL['extra_resource']!='on'){
						echo ' 
						
							<select data-width="200px"  name="eProductNr['.$l.']" id="eProductNr'.$rowL['id'].'" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="produkta numurs"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" onchange="try{setCustomValidity(\'\')}catch(e){}" required  '.$disabled.'>
							<option></option>';
						  $selectProducts = mysqli_query($conn, "SELECT * FROM n_items WHERE status='1'");
						  while($rowi = mysqli_fetch_array($selectProducts)){


							  echo '<option value="'.$rowi['code'].'" data-cnecc="'.$rowi['code'].'"';
							  if($rowL['productNr']==$rowi['code']){echo ' selected';}
							  echo '
							   >'.$rowi['code'];

							   if($rowL['import_product_name'] && $rowL['productNr']==$rowi['code']){

									if($rowi['name1'] || $rowi['name2']){
										echo '<br>('.$rowi['name1'].' '.$rowi['name2'].')';
									}else{
										echo '<br>('.$rowL['import_product_name'].')';
									}


							   }else{
								   echo '<br>('.$rowi['name1'].' '.$rowi['name2'].')';
							   }
							   
							   
							   
							   
							   echo '</option>';
						  }
						  
						  echo '
						  </select>';
						}
						
						$reque = null;
						if($rowL['no_serial']!=1){
							$reque =  ' oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required ';
						}

						if($rowL['extra_resource']!='on'){
							?>
							<script>
								$("#eSerialNo<?=$rowL['id'];?>").on('keyup', function() {
									$("#eSerialNoPlan<?=$rowL['id'];?>").val($(this).val());
								});								
							</script>
							<?
							
							$infoTable=$title=null;
							if($rowL['scannedBy']>0){

								$infoTable = '
									<div id="a1'.$rowL['id'].'" class="hidden">
									  <div class="popover-body">
										<table style="width:100%">
										  <tr>
											<td>Skenētājs:</td>
											<td nowrap>'.returnMeWho($rowL['scannedBy']).'</td>	
										  </tr>
										  <tr>
											<td>Laiks:</td>
											<td nowrap>'.date('d.m.Y H:i:s', strtotime($rowL['scannedDate'])).'</td>
										  </tr>
										</table>
									  </div>
									</div>									
								';
								
								
								$title = ' data-toggle="popover" data-trigger="hover" data-popover-content="#a1'.$rowL['id'].'" data-placement="right" ';
							}
							echo $infoTable;							
							
						$emptySerialColor=null;
						if(in_array($row['clientCode'], $allowedClients)){	
						  echo '
						  <td>	 
						  <div class="">
							<input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="seriālais nr. (plāns)" name="eSerialNoPlan['.$l.']" id="eSerialNoPlan'.$rowL['id'].'" onchange="changeEserial($(this).attr(\'id\'));" value="'.$rowL['serialNoPlan'].'" '.$title.' readonly>
						  </div>';
						  
						  //#d9534f sarkans
						  //#f99500 orandžigs
						  
						  if(!$rowL['serialNo'] || $rowL['img']){
							 
							  echo '
							  <style>
									#eSerialNo'.$rowL['id'].'::-webkit-input-placeholder { /* WebKit browsers */
										color:    white;
									}
									#eSerialNo'.$rowL['id'].':-moz-placeholder { /* Mozilla Firefox 4 to 18 */
										color:    white;
									}
									#eSerialNo'.$rowL['id'].'::-moz-placeholder { /* Mozilla Firefox 19+ */
										color:    white;
									}
									#eSerialNo'.$rowL['id'].':-ms-input-placeholder { /* Internet Explorer 10+ */
										color:    white;
									}							  
							  </style>';
							  
							  $emptySerialColor=' background-color: #d9534f; color: white;';
						  }
						  
						  if($rowL['img']){
							  $emptySerialColor=' background-color: #f99500; color: white;';
						  }
						}
						  
						  echo '
						  <td>	 
						  <div class="" style="display: inline-block; float: left;">
							
							
							<div class="input-group">
							<input type="text" class="form-control input-xs" style="min-width: 100px; '.$emptySerialColor.'" placeholder="..seriālais nr. (fakts)" name="eSerialNo['.$l.']" id="eSerialNo'.$rowL['id'].'" onchange="changeEserial($(this).attr(\'id\'));" value="'.$rowL['serialNo'].'" '.$title.' '.$reque.' '.$disabled.'>';
										
							if($rowL['img']){
								
								echo '
								<span class="input-group-btn">
								
									<button class="btn btn-default btn-xs" data-pic="'.$rowL['img'].'" onclick="showPic(event)" style="height: 20px;"><i class="glyphicon glyphicon-camera" style="color: blue;"></i></button>
									
								</span>';
							}
										
							echo '
							</div>
							
							
						  </div>';	
					  
						}else{
							echo '<td><td>';
						}

echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs numbersOnly eAmount" style="min-width: 70px;" placeholder="daudzums" id="eAmount'.$rowL['id'].'"  name="eAmount['.$l.']" value="'.floatval($rowL['amount']).'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
</div>						
';

?>
<script>
$("#eAmount<?=$rowL['id'];?>").on('keyup', function() {
	
	var EitIs = $("#eUnitOfMeasurement<?=$rowL['id'];?>").val();
	
	if(EitIs=='m3'){
		var EcopyVal = $(this).val();
		$("#eCubicMeters<?=$rowL['id'];?>").val(EcopyVal);
	}
	
});
</script>
<?

echo '<td>';
echo '<div id="query_sort_e'.$rowL['id'].'">';
echo '<input type="hidden" name="eUnitOfMeasurement['.$l.']" value="'.$rowL['productUmo'].'" >';
if($rowL['productUmo']){echo $rowL['productUmo'];}
echo '</div>';

if($rowL['extra_resource']!='on'){
echo '<td>
<div class="">
  <input type="text" class="form-control input-xs numbersOnly eAssistantAmount" style="min-width: 70px;" placeholder="palīg mērv. daudzums" id="eAssistantAmount'.$rowL['id'].'"  name="eAssistantAmount['.$l.']" value="'.floatval($rowL['assistant_amount']).'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
</div>						
';
echo '<td>
<div id="query_sort_extra_e'.$rowL['id'].'">';
echo '<input type="hidden" name="eAssistantUnitOfMeasurement['.$l.']" value="'.$rowL['assistantUmo'].'" >';
if($rowL['assistantUmo']){echo $rowL['assistantUmo'];}
echo '</div>';						


echo '<td>
<div class="">
  <input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" placeholder="dok. neto (kg)" id="e_document_net'.$rowL['id'].'"  name="e_document_net['.$l.']" value="'.floatval($rowL['document_net']).'" onkeyup="updateDue($(this).attr(\'id\'))" '.$disabled.'>
</div>						
';


echo '<td>
<div class="">
  <input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" placeholder="vietu skaits" id="e_place_count'.$rowL['id'].'"  name="e_place_count['.$l.']" value="'.floatval($rowL['place_count']).'" '.$disabled.'>
</div>						
';






echo '
<td>
<div id="query_sort2_e'.$rowL['id'].'">
<div class="">
  <input type="text" class="form-control input-xs numbersOnly eTare" style="min-width: 70px;" placeholder="tara (kg)" id="eTare'.$rowL['id'].'" name="eTare['.$l.']" value="'.floatval($rowL['tare']).'" '.$disabled.'>
</div>	
</div>					
';

echo '
<td>
<div id="query_sort3_e'.$rowL['id'].'">
<div class="">
  <input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" placeholder="bruto" id="eGross'.$rowL['id'].'" name="eGross['.$l.']" value="'.floatval($rowL['gross']).'" '.$disabled.'>
</div>
</div>						
';


echo '
<td>
<div id="query_sort4_e'.$rowL['id'].'">
<div class="">
  <input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" placeholder="neto" id="eNet'.$rowL['id'].'" name="eNet['.$l.']" value="'.floatval($rowL['net']).'" onkeyup="updateDue($(this).attr(\'id\'))" '.$disabled.'>
</div>
</div>						
';

echo '<td><div class=""><input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" id="eDeltaNet'.$rowL['id'].'" value="'.floatval($rowL['delta_net']).'" disabled></div>';

echo '
<td>
<div id="query_sort5_e'.$rowL['id'].'">
<div class="">
  <input type="text" class="form-control input-xs numbersOnly" style="min-width: 70px;" placeholder="m3" id="eCubicMeters'.$rowL['id'].'" name="eCubicMeters['.$l.']" value="'.floatval($rowL['cubicMeters']).'" '.$disabled.'>
</div>
</div>						
';	
 
						echo '
						<td>	

						<div class="">
						  <input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="partijas nr." name="eBatchNo['.$l.']" value="'.$rowL['batchNo'].'"  '.$disabled.'>
						</div>';

						
						echo '<td>
						  <select class="form-control selectpicker btn-group-xs input-xs"  name="eLineLocation['.$l.']"  data-live-search="true" title="noliktava" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" onchange="try{setCustomValidity(\'\')}catch(e){}" required '.$disabled.'>
							<option></option>';
						  $selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
						  while($rowl = mysqli_fetch_array($selectLocation)){
							  echo '<option  value="'.$rowl['id'].'"';
							  if($rowL['location']==$rowl['id']){echo ' selected';}
							  echo '>'.$rowl['id'].' - '.$rowl['name'].'</option>';
						  }
							echo '
						  </select>';

						echo '<input type="hidden" name="eThisDate['.$l.']" value="'.date('Y-m-d H:i:s', strtotime($row['deliveryDate'])).'">';


						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="markas nr." name="eLotNr['.$l.']" value="'.$rowL['lot_no'].'" '.$disabled.'>';
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="konteinera tips" name="eContainerType['.$l.']" value="'.$rowL['container_type_no'].'" '.$disabled.'>';



						echo '
						<td>
						<div class="">
						  <input type="text" class="form-control numbersOnly input-xs" style="min-width: 100px;" placeholder="pavadzīmes nr." name="eThisLadingNr['.$l.']" value="';
						  
						  if($rowL['ladingNr']){
							  echo $rowL['ladingNr'];
						  }else{
							  echo $row['ladingNr'];
						  }
						  
						  echo '" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
						</div>';

						echo '
						<td>
						<div class="">
						  <input type="text" class="form-control numbersOnly input-xs" style="min-width: 100px;" placeholder="transporta nr." name="eThisTransport['.$l.']" value="';
						  
						  if($rowL['thisTransport']){
							  echo $rowL['thisTransport'];
						  }else{
							  echo $row['thisTransport'];
						  }
						  
						  echo '" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>
						</div>';
						
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;"  placeholder="deklarācijas nr." name="eDeclarationsType['.$l.']" value="';

						  if($rowL['declaration_type_no']){
							  echo $rowL['declaration_type_no'];
						  }else{
							  echo $row['declaration_type_no'];
						  }
						  
						echo '" '.$disabled.'>';
	
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="plombes numurs" name="eSealNr['.$l.']" value="'.$rowL['seal_no'].'" '.$disabled.'>';


												
						

						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="svēršanas akta nr." name="eWeighingNr['.$l.']" value="'.$rowL['weighing_act_no'].'" '.$disabled.'>';
						
						echo '<td><input type="checkbox" id="eBrack" name="eBrack['.$l.']" onChange="$(this).val(this.checked? \'1\': \'0\');" '; if($rowL['brack']==1){echo ' value="1" checked';}else{echo ' value="0"';} echo '>';						
					}else{
						echo '<td colspan="19">';
					}								

					if($rowL['extra_resource']!='on'){
						if($status==0){
							
							echo '<td nowrap>';
							
							if($rowL['status']==0){
								
							echo '<button class="btn btn-default btn-xs" data-dol="'.$rowL['id'].'" onclick="delLine(event)" id="del'.$rowL['id'].'" style="display: inline-block;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';								
								
							}			
						
							
							if(($rowL['location']=='') && ($rowL['productNr']=='')){$noNext='y';}
						
							if(($rowL['status']==0) && ($rowL['location']!='' && $rowL['productNr']!='') && ($cif>0) && ($allowSubmit=='Y')){
											
											
											if(in_array($row['clientCode'], $allowedClients)){
												
												
													echo '
													<a class="btn btn-default btn-xs" data-aol="'.$rowL['id'].'" id="approveOneLine'.$rowL['id'].'" onclick="approveOneLine(event)" style="display: inline-block;"><i class="glyphicon glyphicon-ok" style="color: blue;"></i> nodot</a>';													
											
												
											}else{
											
												echo '
												<a class="btn btn-default btn-xs" data-aol="'.$rowL['id'].'" id="approveOneLine'.$rowL['id'].'" onclick="approveOneLine(event)" style="display: inline-block;"><i class="glyphicon glyphicon-ok" style="color: blue;"></i> nodot</a>';
											
											}
							}

							if(($rowL['status']==10)){

								echo '<a class="btn btn-default btn-xs" style="margin-right: 2px;" data-col="'.$rowL['id'].'" id="cancelOneLine'.$rowL['id'].'" onclick="cancelOneLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt</a>';	
								
								if($p_edit=='on' && ($allowSubmit=='Y')){
									echo '<a class="btn btn-default btn-xs" data-rol="'.$rowL['id'].'" id="receiveOneLine'.$rowL['id'].'" onclick="receiveOneLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> saņemts</a>';
								}
							}

							if(($rowL['status']>=20)){
								echo returnCargoLineStatus($conn, $rowL['id']);
							}
						}

						}else{
							echo '<td>';
							if($rowL['status']==0){
								
								echo '<button class="btn btn-default btn-xs" data-dol="'.$rowL['id'].'" onclick="delLine(event)" id="del'.$rowL['id'].'" style="display: inline-block;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';								
									
								}							
						}
						
						if($rowL['comment']!=''){ $comColor = ' color: blue;'; }else{ $comColor = ' color : #000;'; }
					    echo '<td>
						  <a href="/pages/receipt/comment.php?id='.$rowL['id'].'&edit='.$id.'" data-toggle="modal" data-target="#showComment" data-remote="false" style="outline: 0; border: none; -moz-outline-style: none; text-decoration : none; '.$comColor.'">
								<i class="glyphicon glyphicon-comment"></i>
						  </a>';						
						
						
					echo '</tr>';
					$l++;
	
}else{
	

			echo '	<tr bgcolor="#eee" class="classlistedit"'; echo '>';

						echo '<td>';

						echo '
							<select data-width="200px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="produkta numurs" disabled>
							<option></option>';
						  $selectProducts = mysqli_query($conn, "SELECT * FROM n_items");
						  while($rowi = mysqli_fetch_array($selectProducts)){
							  echo '<option value="'.$rowi['code'].'" data-cnecc="'.$rowi['code'].'"';
							  if($rowL['productNr']==$rowi['code']){echo ' selected';}
							  echo '
							   >'.$rowi['code'].'  <br>('.$rowi['name1'].' '.$rowi['name2'].')</option>';
						  }
						  
						  echo '
						  </select>';

						if(in_array($row['clientCode'], $allowedClients)){
						  echo '
						  <td>	
  
						  <div class="">
							<input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="seriālais nr. (plāns)" value="'.$rowL['serialNoPlan'].'" disabled>
						  </div>';
						}
						
						  echo '
						  <td>	
  
						  <div class="">
							<input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="seriālais nr. (fakts)" value="'.$rowL['serialNo'].'" disabled>
						  </div>';						  


echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="daudzums" value="'.floatval($rowL['amount']).'" disabled>
</div>						
';

echo '<td>';

if($rowL['productUmo']){echo $rowL['productUmo'];}

echo '<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="palīg mērv. daudzums" value="'.floatval($rowL['assistant_amount']).'" disabled>
</div>						
';
echo '<td>';
if($rowL['assistantUmo']){echo $rowL['assistantUmo'];}

echo '<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="dok. neto (kg)" value="'.floatval($rowL['document_net']).'" disabled>
</div>						
';

echo '<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="vietu skaits" value="'.floatval($rowL['place_count']).'" disabled>
</div>						
';



echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="tara (kg)"  value="'.floatval($rowL['tare']).'" disabled>
</div>						
';	

echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="bruto"  value="'.floatval($rowL['gross']).'" disabled>
</div>						
';

echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="neto"  value="'.floatval($rowL['net']).'" disabled>
</div>						
';	

echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="Δ neto (kg)"  value="'.floatval($rowL['document_net']).'" disabled>
</div>						
';	

echo '
<td>
<div class="">
  <input type="text" class="form-control input-xs" style="min-width: 70px;" placeholder="m3"  value="'.floatval($rowL['cubicMeters']).'" disabled>
</div>						
';						
						  
						echo '
						<td>	

						<div class="">
						  <input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="partijas nr." value="'.$rowL['batchNo'].'" disabled>
						</div>';						
						
						echo '<td>
						  <select class="form-control selectpicker btn-group-xs input-xs"  name="eLineLocation[]"  data-live-search="true" title="noliktava" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" onchange="try{setCustomValidity(\'\')}catch(e){}" required '.$disabled.'>
							<option></option>';
						  $selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
						  while($rowl = mysqli_fetch_array($selectLocation)){
							  echo '<option  value="'.$rowl['id'].'"';
							  if($rowL['location']==$rowl['id']){echo ' selected';}
							  echo '>'.$rowl['id'].' - '.$rowl['name'].'</option>';
						  }
							echo '
						  </select>							
									';					
						
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="" value="'.$rowL['lot_no'].'" disabled>';
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="" value="'.$rowL['container_type_no'].'" disabled>';


						echo '
						<td>
						<div class="">
						  <input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="transporta nr." value="';
						  
						  if($rowL['ladingNr']){
							  echo $rowL['ladingNr'];
						  }else{
							  echo $row['ladingNr'];
						  }
						  
						  echo '" disabled>
						</div>';

						echo '
						<td>
						<div class="">
						  <input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="transporta nr." value="';
						  
						  if($rowL['thisTransport']){
							  echo $rowL['thisTransport'];
						  }else{
							  echo $row['thisTransport'];
						  }
						  
						  echo '" disabled>
						</div>';						
						
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="" value="';
						
						  if($rowL['declaration_type_no']){
							  echo $rowL['declaration_type_no'];
						  }else{
							  echo $row['declaration_type_no'];
						  }						
						
						echo '" disabled>';
						
						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="" value="'.$rowL['seal_no'].'" disabled>';





						echo '<td><input type="text" class="form-control input-xs" style="min-width: 100px;" placeholder="" value="'.$rowL['weighing_act_no'].'" disabled>';




							echo '<td><input type="checkbox"'; if($rowL['brack']==1){echo ' checked';} echo ' disabled>';

							
							echo '<td nowrap>';							

							if(($rowL['status']==10)){

								echo '<a class="btn btn-default btn-xs" style="margin-right: 2px;" data-col="'.$rowL['id'].'" id="cancelOneLine'.$rowL['id'].'" onclick="cancelOneLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt</a>';	
								
								if($p_edit=='on' && ($allowSubmit=='Y')){
									echo '<a class="btn btn-default btn-xs" data-rol="'.$rowL['id'].'" id="receiveOneLine'.$rowL['id'].'" onclick="receiveOneLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> saņemts</a>';
								}
							}

							if(($rowL['status']>=20)){
								echo returnCargoLineStatus($conn, $rowL['id']);
							}
						if($rowL['comment']!=''){ $comColor = ' color: blue;'; }else{ $comColor = ' color : #000;'; }	
					    echo '<td>
						  <a href="/pages/receipt/comment.php?id='.$rowL['id'].'&edit='.$id.'" data-toggle="modal" data-target="#showComment" data-remote="false" style="outline: 0; border: none; -moz-outline-style: none; text-decoration : none; '.$comColor.'">
								<i class="glyphicon glyphicon-comment"></i>
						  </a>';							

						
					echo '</tr>';
	
	
	
}





		}
		echo '<input type="hidden" name="lResult" value="'.$l.'">';

		
		if(($status==0)){
			
		?>
		
		
<script>
$(document).ready(function(){
var appendDate = $("#appendDate").val();
var appendTransport = $("#appendTransport").val(); 

$("#thisDate").val(appendDate);
$("#thisTransport").val(appendTransport);
});

$(document).on("change", function(){
var appendDate = $("#appendDate").val(); 

$("#thisDate").val(appendDate);
});

$(document).keyup(function(){
var appendTransport = $("#appendTransport").val(); 

$("#thisTransport").val(appendTransport);
});
</script>


<script>

$('#amount').on('keyup change input', function(){
	var val = $('#productNr option:selected').attr('data-cncc');
	var val = encodeURI(val);
	$('#query_sort').load('pages/receipt/query_sort_add.php?input=amount&select='+val+'');
});

$('#assistantAmount').on('keyup change input', function(){
	var val = $('#productNr option:selected').attr('data-cncc');
	var val = encodeURI(val);
	$('#query_sort').load('pages/receipt/query_sort_add.php?input=aAmount&select='+val+'');
});	

function prodSelectSimple(val) {
	
	var val = val.value; 
	var val = encodeURI(val);

	$("#gross").val('0');
	$("#net").val('0');
	$("#tare").val('0');
	$("#cubicMeters").val('0');

	$('#query_sort').load('pages/receipt/query_sort_add.php?input=all&select='+val+'');
	$('#query_sort_extra').load('pages/receipt/query_sort_extra_uom.php?select='+val+'');
}


function resourceNrRet(val){
	var val = val.value;
	var id = '<?=$row['agreements'];?>';
	$('.resourceNrRet').load('pages/receipt/recourse_sort.php?view=single&select='+val+'&id='+id+'');
}

$(document).ready(function(){
	$('#extraResource').click(function(){

		if ($(this).is(':checked')){	
			$('#box_resource').load('pages/receipt/recourse_sort.php?view=extrar&agr=<?=$row['agreements'];?>');
			$('.hides').hide();
		}else{
			$('#box_resource').load('pages/receipt/recourse_sort.php?view=extrar&section=org&agr=<?=$row['agreements'];?>');
		
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
			<td '.$tdStyle.' class="hides">
   
				<select data-width="200px" name="productNr" id="productNr" class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="produkta numurs"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" onchange="prodSelectSimple(this);resourceNrRet(this);try{setCustomValidity(\'\')}catch(e){}" >
				';
			  $selectProducts = mysqli_query($conn, "
					SELECT i.code, i.name1, i.name2 
					FROM n_items AS i
					LEFT JOIN agreements_lines AS al
					ON i.code=al.item
					WHERE i.status='1' AND al.deleted='0' AND al.contractNr='".$row['agreements']."' AND al.extra_resource!='on'
					GROUP BY al.item
					");
			  while($rowi = mysqli_fetch_array($selectProducts)){
				  echo '<option  value="'.$rowi['code'].'" data-cncc="'.$rowi['code'].'"';
				  if($GXYrow['copyRest']==1 && $rowi['code']==$gllRow['productNr']){ echo 'selected'; }
				 echo ' >'.$rowi['code'].'  ('.$rowi['name1'].' '.$rowi['name2'].') '.$row['contractNr'].'</option>';
			  }
			  
			  echo '
			  </select>
			  <div id="er1"></div>

			</td>';
			
			if(in_array($row['clientCode'], $allowedClients)){
				echo '
				<td class="hides">
					<div class="">
					  <input style="min-width: 100px;" type="text" class="form-control input-xs" placeholder="seriālais nr. (plāns)" name="serialNoPlan" id="serialNoPlan" readonly>
					</div>				
				</td>';
			}
			
			echo '
			<td class="hides">
				<div class="">
				  <input style="min-width: 100px;" type="text" class="form-control input-xs" placeholder="seriālais nr. (fakts)" name="serialNo" id="serialNo"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" >
				</div>				
			</td>';			

			?>

<script>

$(document).ready(function() {
	
	$('#serialNo').on('keyup', function() {
		$('#serialNoPlan').val($(this).val());
	});	
	
  $('#serialNo').on('change', function() {
    var value = $(this).val();
    $.ajax({
      type: 'post',
      url: 'pages/receipt/serialNoCheck.php?id=<?=$id;?>&serial='+value,
      success: function(r) {
		  console.log('pages/receipt/serialNoCheck.php?id=<?=$id;?>&serial='+value);
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
			url: 'pages/receipt/serialNoCheck.php?id=<?=$id;?>&line='+t+'&serial='+value,
			success: function(r) {
				console.log('pages/receipt/serialNoCheck.php?id=<?=$id;?>&line='+t+'&serial='+value);
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
<td '.$tdStyle.'>
	<div class="">
	  <input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" placeholder="daudzums" name="amount" id="amount"'; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['amount']).'"';} echo ' oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" >
	  <div id="er2"></div>
	</div>				
</td>';



?>
<script>
$('#amount').on('keyup', function() {
	
	var itIs = $('#unitOfMeasurement').val();
	
	if(itIs=='m3'){
		var copyVal = $(this).val();
		$('#cubicMeters').val(copyVal);
	}
	
});	

</script>
<?			

echo '
<td '.$tdStyle.'>
<div id="query_sort">';
if($GXYrow['copyRest']==1){ echo $gllRow['productUmo'].' <input type="hidden" name="unitOfMeasurement" id="unitOfMeasurement" value="'.$gllRow['productUmo'].'" >';}  
  
echo  '
</div>
</td>';

echo '<td '.$tdStyle.' class="hides">
<div class="">
	<input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" placeholder="palīg mērv. daudzums" name="assistantAmount" id="assistantAmount" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['assistant_amount']).'"';} echo '  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" >
  </div>				
';
echo '<td '.$tdStyle.' class="hides">
<div id="query_sort_extra">'; 
if($GXYrow['copyRest']==1){ echo $gllRow['assistantUmo'].' <input type="hidden" name="assistantUnitOfMeasurement" id="assistantUnitOfMeasurement" value="'.$gllRow['assistantUmo'].'" >';}  
echo  '
</div>			
';

echo '<td '.$tdStyle.' class="hides">
<div class="">
	<input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" placeholder="dok. neto (kg)" name="document_net" id="document_net" onkeyup="updateDue(\'0\')" value="'.floatval($gllRow['document_net']).'">
  </div>				
';

echo '<td '.$tdStyle.' class="hides">
<div class="">
	<input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" placeholder="vietu skaits" name="place_count" id="place_count" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['place_count']).'"';} echo ' >
  </div>				
';

echo '
<td '.$tdStyle.' class="hides">
<div id="query_sort2">
	<div class="">
	<input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" id="tare" placeholder="tara (kg)" name="tare" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['tare']).'"';}else{ echo 'value="0"';} echo ' >
	<div id="er2"></div>
	</div>
</div>					
</td>';

echo '

<td '.$tdStyle.' class="hides">
<div id="query_sort3">
	<div class="">
	  <input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" id="gross" placeholder="bruto" name="gross" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['gross']).'"';}else{ echo 'value="0"';} echo ' >
	  <div id="er2"></div>
	</div>	
</div>				
</td>

<td '.$tdStyle.' class="hides">
<div id="query_sort4">
	<div class="">
	  <input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" id="net" placeholder="neto" name="net" onkeyup="updateDue(\'0\')" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['net']).'"';}else{ echo 'value="0"';} echo ' >
	  <div id="er2"></div>
	</div>
</div>					
</td>

<td '.$tdStyle.' class="hides">

	<div class="">
	  <input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" id="deltaNet" placeholder="Δ neto (kg)" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['delta_net']).'"';}else{ echo 'value="0"';} echo ' disabled>
	  <div id="er2"></div>
	</div>
					
</td>

<td '.$tdStyle.' class="hides">
<div id="query_sort5">
	<div class="">
	  <input style="min-width: 70px;" type="text" class="form-control input-xs numbersOnly" id="cubicMeters" placeholder="m3" name="cubicMeters" '; if($GXYrow['copyRest']==1){ echo ' value="'.floatval($gllRow['cubicMeters']).'"';}else{ echo 'value="0"';} echo ' >
	  <div id="er2"></div>
	</div>
</div>				
</td>';			

			echo '


			<td class="hides">
				<div class="">
				  <input style="min-width: 100px;" type="text" class="form-control input-xs" placeholder="partijas nr." name="batchNo" id="batchNo" value="'.$gllRow['batchNo'].'">
				</div>				
			</td>

			<td '.$tdStyle.' class="hides">
			  <select class="form-control selectpicker btn-group-xs input-xs"  name="lineLocation" id="lineLocation"  data-live-search="true" title="noliktava" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" onchange="try{setCustomValidity(\'\')}catch(e){}" >
			  ';
			  $selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location WHERE status=1") or die(mysqli_error($conn));
			  while($rowl = mysqli_fetch_array($selectLocation)){
				  echo '<option  value="'.$rowl['id'].'"';
				  if($row['location']==$rowl['id'] ||  $rowl['id']==$gllRow['location']){echo ' selected';}
				  echo '>'.$rowl['id'].' - '.$rowl['name'].'</option>';
			  }
				echo '
			  </select>	
	  
			</td>';

			echo '<input type="hidden" name="thisDate" value="'.date('Y-m-d H:i:s', strtotime($row['deliveryDate'])).'">';
			echo '<td class="hides"><input style="min-width: 100px;" type="text" class="form-control input-xs" id="lotNr" name="lotNr" placeholder="markas numurs" value="'.$gllRow['lot_no'].'"></td>';


			echo '<td class="hides"><input style="min-width: 100px;" type="text" class="form-control input-xs" id="containerType" name="containerType" placeholder="konteinera tips"  value="'.$gllRow['container_type_no'].'"></td>';

			
			echo '
			<td class="hides">
			<div class="">
			  
			  <input style="min-width: 100px;" type="text" class="form-control input input-xs" name="appendLadingNr" placeholder="pavadzīmes nr." value="';
			  
			  
				  if($devLadingNr){
					  echo $devLadingNr;
				  }else{
					  echo $row['ladingNr'];
				  }			  
			  
			  echo '">
			</div>
			</td>';	

			echo '
			<td class="hides">
			<div class="">
			  
			  <input style="min-width: 100px;" type="text" class="form-control input input-xs" name="appendTransport" id="appendTransport" placeholder="transporta nr." value="';
			  
			  
				  if($row['thisTransport']){
					  echo $row['thisTransport'];
				  }else{
					  echo $devTransport;
				  }			  
			  
			  echo '">
			  <input type="hidden" value="" id="thisTransport" name="thisTransport">
			</div>
			</td>';					


		

			echo '<td class="hides"><input style="min-width: 100px;" type="text" class="form-control input-xs" id="declarationsType" name="declarationsType" placeholder="deklarācijas nr."  value="';
			
			
				  if($rowL['declaration_type_no']){
					  echo $rowL['declaration_type_no'];
				  }else{
					  echo $row['declaration_type_no'];
				  }			  
			
			
			echo '"></td>';

			echo '<td class="hides"><input style="min-width: 100px;" type="text" class="form-control input-xs" id="sealNr" name="sealNr" placeholder="plombes numurs" value="'.$gllRow['seal_no'].'"></td>';





			echo '<td class="hides"><input style="min-width: 100px;" type="text" class="form-control input-xs" id="weighingNr" name="weighingNr" placeholder="svēršanas akta nr."  value="'.$gllRow['weighing_act_no'].'"></td>';

			echo '<td class="hides" class="hides"><input type="checkbox" name="brack"'; if($rowL['brack']==1){echo ' checked';} echo '>';

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
		
		if(($row['status']==0) && ($cif>0) && ($allowSubmit=='Y') && $noNext!='y'){	
		
		
		
			if(in_array($row['clientCode'], $allowedClients)){
				
			
					echo '<button type="submit" class="btn btn-default btn-xs" id="approveLine" data-rowid="'.$row['id'].'" onclick="approveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</button>';												
			
				
			}else{
			
				echo '<button type="submit" class="btn btn-default btn-xs" id="approveLine" data-rowid="'.$row['id'].'" onclick="approveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> nodot</button>';
			
			}		
		
		
		

		}

		if(($row['status']==10)){
			
			
			$getA = mysqli_query($conn, "SELECT actForm FROM agreements WHERE contractNr='".$row['agreements']."'");
			$gAR = mysqli_fetch_array($getA);
			
			$form=null; 
			if($gAR['actForm']){$form = '&form='.$gAR['actForm'];}

		
				echo '<a target="_blank" href="print?id='.$id.''.$form.'" class="btn btn-default btn-xs" style="margin-right: 2px;"><i class="glyphicon glyphicon-print" style="color: silver;"></i> drukāt </a>';
			
			echo '<button type="submit" class="btn btn-default btn-xs" style="margin-right: 2px;" data-cl="'.$row['id'].'" id="cancelLine" onclick="cancelLine(event)"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> atsaukt nodošanu</button>';		
			
			if($p_edit=='on' && ($allowSubmit=='Y')){
				echo '<button type="submit" class="btn btn-default btn-xs" data-rl="'.$row['id'].'" id="receiveLine" onclick="receiveLine(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> saņemts</button>';
			}
		}
	
	
}	

// saņemšanas vēsture
if($view=='history' && !$id){
?>

<script>
function newDoc(val) {
	$('#contenthere').load('/pages/receipt/receipt.php?view=history&id='+val);
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	var search = $('#searchWait').val();
	$('#contenthere').load('/pages/receipt/receipt.php?view=history&client='+val+'&search='+search);
}
</script>
	<script>
	$('#removeFilterCust').on('click', function() {
		var search = $('#searchWait').val();
		$('#contenthere').load('/pages/receipt/receipt.php?view=history&search='+search);
	});
	</script>
	
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#contenthere').load('/pages/receipt/'+url+'&view=history&client=<?=$client;?>');
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
			  <button type="button" class="close" data-dismiss="alert" id="removeFilterCust"><span aria-hidden="true">&times;</span></button>
			  <strong>filtrs pēc klienta:</strong> '.$client.' - '.returnClientName($conn, $client).'
			</div>	
		';}
		
		echo '
			<div class="form-group col-md-2 pull-right">
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			  
			  $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header_receive") or die(mysqli_error($conn));
			 
			  while($rowc = mysqli_fetch_array($selectClient)){
				  if($rowc['clientCode']){
					echo '<option value="'.$rowc['clientCode'].'"';
						if($rowc['clientCode']==$client){ echo ' selected';}
					echo ' >'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
				  }
			  }
			  
			  echo '
			  </select>	
			</div>';		

?>

<script type="text/javascript">

function getStates(value) {
	var client = '<?=$client;?>';
    var search = $.post("/pages/receipt/search.php?view=history", {name:value, client:client},function(data){
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
	
 	
	echo '<div style="float: right; display: inline-block;" ><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt" value="'.$_GET['search'].'"></div></div><div class="clearfix"></div>';
			
	echo '<div id="results">';

	$keepFilter=null;
	if($client){$keepFilter.='client='.$client.'&';}
	
	
	if($_GET['search']){
		$s = mysqli_real_escape_string($conn, $_GET['search']);	
		$search_result = " (
		docNr LIKE '%$s%' || 
		ladingNr LIKE '%$s%' || 
		clientCode LIKE '%$s%' ||
		clientName LIKE '%$s%' || 
		ownerCode LIKE '%$s%' || 
		ownerName LIKE '%$s%' ||
		receiverCode LIKE '%$s%' || 
		receiverName LIKE '%$s%' || 
		cargoCode LIKE '%$s%' ||
		deliveryDate LIKE '%$s%' || 
		deliveryType LIKE '%$s%' || 
		deliveryCode LIKE '%$s%' ||
		location LIKE '%$s%' || 
		createdSource LIKE '%$s%' ||
		thisTransport LIKE '%$s%' ||
		acceptance_act_no LIKE '%$s%'
		) ";
		$searchUrl = 'search='.$s.'&';
		
	}else{
		$searchUrl = null;	
		$search_result = "";	
	}	
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$keepFilter.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 	
	
	$filterClient = null;		
	if($client && $search_result){$filterClient = ' WHERE clientCode="'.$client.'" AND '.$search_result;}
	if(!$client && $search_result){$filterClient = ' WHERE '.$search_result;}
	if($client && !$search_result){$filterClient = ' WHERE clientCode="'.$client.'"';}	
	
	$query = "SELECT * FROM cargo_header_receive ".$filterClient." ORDER BY CASE WHEN scanStatus=200 THEN scanStatus END DESC, deliveryDate ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>pavadzīmes nr.</th>
						<th>dokumenta nr. (iekš.)</th>
						<th>piegādes dat.</th>
						
						<th>pieņemšanas akta nr.</th>
						<th>klienta kods - nosaukums</th>				
						<th>īpašnieka kods - nosaukums</th>
						<th>statuss</th>
						<th>importa datums</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				$trAction = ' onclick="newDoc('.$row['id'].')"';
				if($row['scanStatus']==100){
					$trAction = ' style="background-color: silver;"';
				}

				$show_green=null;
				if($row['scanStatus']==200){
					$show_green = ' style="background-color: #ABEBC6; color: black;"';
				}

				echo '	<tr class="classlistedit" '.$trAction.'>
							<td '.$show_green.'>'.$row['ladingNr'].'
							<td>'.$row['docNr'].'
							<td>';
							if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
							
							echo '
							
							<td>'.$row['acceptance_act_no'].'
							<td>'.$row['clientCode'].' - '.$row['clientName'].'				
							<td>'.$row['ownerCode'].' - '.$row['ownerName'].'
							<td>'.returnCargoStatus($conn, $row['id']).'

							<td>';

							if($row['importDate']!='0000-00-00 00:00:00'){
								echo $row['importDate'];
							}

							echo '							

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
	$GetXY = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$GXYrow = mysqli_fetch_array($GetXY);

	$importedDoc=null;
	if($GXYrow['importDate']!='0000-00-00 00:00:00'){$importedDoc = '<div style="display:inline-block; float: right;">importa datums <b>'.$GXYrow['importDate'].'</b></div>';}

	if($GXYrow['cargoCode']){$cCode=$GXYrow['cargoCode'];}else{$cCode=null;}
	if($GXYrow['docNr']){$isDocNr = $GXYrow['docNr'];}else{$isDocNr = null;}
	echo '<p style="display:inline-block;">vēsture</p> <div id="printchatbox" style="display:inline-block; font-weight: bold;">'.$cCode.'</div> dokumenta nr. (iekš.) <b>'.$GXYrow['docNr'].'</b> '.$importedDoc;

	$result = mysqli_query($conn,"SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$row = mysqli_fetch_array($result);
	$status = $row['status'];
	if($status>0 || $p_edit!='on'){$disabled='disabled';}else{$disabled=null;}
	?>

	
	<?php
	echo '
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
			<label class="lb-sm" for="senderCode">nosūtītāja kods - nosaukums</label>
			
				<select class="form-control selectpicker btn-group-xs input-xs"  id="senderCode" data-live-search="true" title="nosūtītāja kods - nosaukums" disabled>
					<option></option>';
				
				$selectOwner = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, name FROM senders") or die(mysqli_error($conn));
				while($rowo = mysqli_fetch_array($selectOwner)){
					echo '<option value="'.$rowo['Code'].'"';
					if($row['senderCode']==$rowo['Code']){echo ' selected';}
					echo '>'.$rowo['Code'].' - '.$rowo['name'].'</option>';
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
		  
		  <div class="form-group col-md-3">
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
			  <label for="thisTransportEdit">transporta nr.</label>
			  <input type="text" class="form-control" placeholder="transporta nr." value="'.$row['thisTransport'].'" disabled>
			</div>	
			';		  
		  
		  
		  
		  
		  echo '
		  <div class="clearfix"></div>
		  <div class="form-group col-md-3">
			  <label class="lb-sm">kuģis izkraušanai</label>
			  <input type="text" class="form-control input-xs" placeholder="kuģis izkraušanai" value="'.$row['ship'].'" disabled>
		  </div>
		  
		  ';

		  echo ' 
		  <div class="form-group col-md-3">
		  <label class="lb-sm">pieņemšanas akta dat.</label>
		  <input type="text" class="form-control input-xs" placeholder="pieņemšanas akta dat."  value="'.$row['acceptance_act_date'].'" disabled>
		  </div>';		  

echo ' 
<div class="form-group col-md-3">
<label class="lb-sm">pieņemšanas akta nr.</label>
<input type="text" class="form-control input-xs" placeholder="pieņemšanas akta nr."  value="'.$row['acceptance_act_no'].'" disabled>
</div>';


echo '
<div class="form-group col-md-3">
  <label for="declarationTypeNo">deklarācijas nr. <span class="asterisc">*</span></label>
  <input type="text" class="form-control" id="declarationTypeNo" placeholder="deklarācijas nr." value="'.$row['declaration_type_no'].'" disabled>
</div>	


 <div class="form-group col-md-3">
  <label class="lb-sm"  for="cargoStatus">kravas status <span class="asterisc">*</span></label>
	<select class="form-control selectpicker btn-group-xs input-xs"  id="cargoStatus"  data-live-search="true" title="kravas status" disabled>
	<option value="C"'; if($row['cargo_status']=='C'){echo ' selected';} echo '>C</option>
	  <option value="N"'; if($row['cargo_status']=='N'){echo ' selected';} echo '>N</option>
	  <option value="EU"'; if($row['cargo_status']=='EU'){echo ' selected';} echo '>EU</option>
	</select>	
 </div>
';


echo ' 
<div class="clearfix"></div>
<div class="form-group col-md-3">
									  
<div class="checkbox">
   <label><input type="checkbox" id="copyRest" name="copyRest"'; if($row['copyRest']==1){echo ' checked';} echo ' disabled>dublēt līnijas vērtības(izņ. seriālo nr.)</label>
</div>
</div> ';			

		echo '<div class="clearfix"></div>';		
		if($isDocNr){


		
			$inCargo = mysqli_query($conn, "
							SELECT SUM(amount) as amount, SUM(assistant_amount) as assistant_amount, SUM(volume) as volume, 
							SUM(tare) as t, SUM(gross) as b, SUM(net) as n, SUM(cubicMeters) as a, SUM(place_count) AS c,
							SUM(document_net) AS don,
							productUmo, assistantUmo, productNr FROM cargo_line WHERE docNr='".$isDocNr."' GROUP BY productUmo, assistantUmo") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			echo '<br>';
			
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive">
						<thead>
							<th>mērvienība</th>
							<th>daudzums</th>
							<th>palīg mērvienība</th>
							<th>palīg mērvienības daudzums</th>
							<th>dok. neto (kg)</th>
							<th>vietu skaits</th>
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
								<td>'.$icRow['assistantUmo'].'</td>
								<td>'.floatval($icRow['assistant_amount']).'</td>
								<td>'.floatval($icRow['don']).'</td>
								<td>'.floatval($icRow['c']).'</td>
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
		
	
		echo '

	  </div>


	  <div class="clearfix"></div>';
	  
	  echo '
	  <a href="/pages/receipt/status.php?id='.$id.'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
	    	<i class="glyphicon glyphicon-option-vertical"></i> statuss
	  </a>';	  
	  
	  echo '<div class="clearfix"></div>';
	
	$getLastLine = mysqli_query($conn, "SELECT thisDate, thisTransport FROM cargo_line WHERE docNr='".$row['docNr']."' AND extra_resource!='on' ORDER BY id DESC") or die(mysqli_error($conn));
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
				<th>brāķis</th>
				<th>produkta nr</th>
				<th>partijas nr</th>
				<th>noliktava</th>

				<th>markas nr.</th>
				<th>konteinera tips</th>

				<th>transporta nr.</th>

				<th>deklarācijas nr.</th>
				
				<th>plombes numurs</th>

				<th>daudzums (sākuma/reālais)</th>
				<th>mērvienība</th>
				
				<th>tara (kg)</th>
				<th>bruto (kg)</th>
				<th>neto (kg)</th>				
				<th>apjoms (m3)</th>
				<th>plombes numurs</th>
				<th>brāķis</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';
		
		
		$lines = mysqli_query($conn, "SELECT * FROM cargo_line_receive WHERE docNr='".$row['docNr']."'");
		while($rowL = mysqli_fetch_array($lines)){
			
						$getNames = mysqli_query($conn, "SELECT name1, name2, unitOfMeasurement FROM n_items WHERE code='".$rowL['productNr']."'");
						$gNrow = mysqli_fetch_array($getNames);
						
						$getAmount = mysqli_query($conn, "SELECT amount FROM cargo_line WHERE id='".$rowL['id']."' AND status!='40'");
						$gArow = mysqli_fetch_array($getAmount);						
						
						
			echo '	<tr class="toggler" data-prod-cat="'.$rowL['id'].'">
						<td><input type="checkbox"'; if($rowL['brack']==1){echo ' checked';} echo ' disabled>
						<td>'.$rowL['productNr'].'<br>'.$gNrow['name1'].' '.$gNrow['name2'];
						
						echo '
						<td>'.$rowL['batchNo'];				
						echo '<td>'.$rowL['location'].' - '.returnLocationName($conn, $rowL['location']).'
						
						<td>'.$rowL['lot_no'].'
						<td>'.$rowL['container_type_no'].'


						<td>'.$rowL['thisTransport'].'


						<td>'.$rowL['declaration_type_no'];
						
						echo '<td>'.$rowL['seal_no'].'


						<td>'.floatval($rowL['amount']).' / '.floatval($gArow['amount']);
						
						echo '<td>'.$rowL['productUmo'].'</td>';
						
						

						echo '<td>'.floatval($rowL['tare']);
						echo '<td>'.floatval($rowL['gross']);
						echo '<td>'.floatval($rowL['net']);
						echo '<td>'.floatval($rowL['cubicMeters']).'
						      <td>'.$rowL['seal_no'];
						
						if($rowL['comment']!=''){ $comColor = ' color: blue;'; }else{ $comColor = ' color : #000;'; }
					    echo '<td >
						  <a href="/pages/receipt/comment.php?view=history&id='.$rowL['id'].'&edit='.$id.'" data-toggle="modal" data-target="#showComment" data-remote="false" style="outline: 0; border: none; -moz-outline-style: none; text-decoration : none; '.$comColor.'">
								<i class="glyphicon glyphicon-comment"></i>
						  </a>';
						  
					echo '</tr>';
					
					
					
					$getLogs = mysqli_query($conn, "
					SELECT e.type AS type,  e.cargoCode AS cargoCode, e.location AS location, e.amount AS amount, e.status AS status
					FROM item_ledger_entry AS e
					
					WHERE e.docNr='".$rowL['docNr']."' AND e.productNr='".$rowL['productNr']."' AND (e.cargoLine='".$rowL['id']."' OR e.orgLine='".$rowL['id']."') AND e.location='".$rowL['location']."'
					
					
					
					");

					$rowColor = $whatPlace = null;
					while($rowi = mysqli_fetch_array($getLogs)){
						
						if ($rowi['amount'] < 0) {
						  if($rowi['type']=='negative'){ $rowColor = ' bgcolor="#D95C5C"'; $whatPlace = ' no '.$rowi['cargoCode']; }						
						} else {
						  if($rowi['type']=='positive'){ $rowColor = ' bgcolor="#2ECC71"'; $whatPlace = ' uz '.$rowi['cargoCode']; }
						  if($rowi['type']=='negative'){ $rowColor = ' bgcolor="#98FB98"'; $whatPlace = ' no '.$rowi['cargoCode']; }
						}						
						

						echo '<tr '.$rowColor.' class="cat'.$rowL['id'].'" style="display: none;"><td colspan="2"><td nowrap>'.$rowi['location'].' - '.returnLocationName($conn, $rowi['location']).'<td colspan="2"><td colspan="4">'.$rowi['amount'].' '.$whatPlace.'<td colspan="4">
						<td colspan="4">'.returnStatus($rowi['status']).'</tr>';
					}
					
					
		}

		
	echo '</tbody>
	</table></div>';	
	

} // saņemšanas vēstures rindas beidzas

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

<div class="modal fade" id="showComment" tabindex="-1" role="dialog" aria-labelledby="showStatusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  
      <div class="modal-body">
        
      </div>

    </div>
  </div>
</div>			

<script>
// Fill modal with content from link href
$("#showComment").on("show.bs.modal", function(e) {


	
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
	$("#lineLocation").prop('required',true);
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<script>
$(document).ready(function() {
	$('form').on('keyup change', 'input, select, textarea', function(){

		$("#saveIt").css("background","#F1F103");
		
	});
});

$(document).on('dp.change', 'input', function(e) {
	var shown = $(this).attr('dp.shown');
	if (typeof shown != 'undefined' && shown && e.date != e.oldDate)
	$("#saveIt").css("background","#F1F103");
});

$(document).on('dp.show', 'input', function(e) {
	$(this).attr('dp.shown', 1);
});

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});

$(function() {
  $("[data-toggle=popover]").popover({
    html: true,
    content: function() {
      var content = $(this).attr("data-popover-content");
      return $(content).children(".popover-body").html();
    },
    title: function() {
      var title = $(this).attr("data-popover-content");
      return $(title).children(".popover-heading").html();
    }
  });
});

</script>
<?php include_once("../../datepicker.php"); ?>