<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="additional_services";


require('inc/s.php');
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

include('functions/base.php');
include('header.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU

if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>

<style>
.col-centered{
    float: none;
    margin: 0 auto;
}
</style> 
<div class="container-fluid">
<div class="row">

  <div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 



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
$(document).ready(function(){
    $('.classhistory').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/additional_services/additional_services.php?view=history');
    });
})
</script>

<script>
function newDoc(val) {
	$("#pleaseWait").toggle();
	$('#contenthere').load('/pages/additional_services/additional_services.php?view=edit&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$("#pleaseWait").toggle();
	$('#contenthere').load('/pages/additional_services/additional_services.php?client='+val+'');
}
</script>	

	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			$("#pleaseWait").toggle();
			$('#contenthere').load('/pages/additional_services/'+url);
			event.preventDefault();
		});
	});
	</script>						
				
<?php
//skats uz ierakstiem
if (!$view){?>
	
	<?php

	require('inc/s.php');

	echo '<div id="contenthere">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
				';
				
				echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
				echo '<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>';
				
			echo '</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">papildu pakalpojumi</p>';

		echo '
			<div class="form-group col-md-2 pull-right" style="display: inline-block;">
			  <select class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			   $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM additional_services_header WHERE (status='0' OR status='10')") or die(mysqli_error($conn));
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
?>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("/pages/additional_services/search.php", {name:value},function(data){
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
	
echo ' <div style="float: right; display: inline-block;" ><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control  input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div><div class="clearfix"></div>'; 	

			
	
	echo '<div id="results">';					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(SELECT SUM(amount) FROM additional_services_line WHERE additional_services_line.docNr=additional_services_header.docNr) as vienibas,
	
	(SELECT thisTransport FROM additional_services_line WHERE additional_services_line.docNr=additional_services_header.docNr LIMIT 1) AS lineThisTransport
	 FROM additional_services_header WHERE (status='0' || status='10')";  //NEPIECIEŠAMAIS VAICĀJUMS
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

?>
<div id="pleaseWait" style="display: none;">
    <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<?php include("footer.php"); ?>