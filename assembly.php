<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="assembly";


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
        $('#contenthere').load('/pages/assembly/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/assembly/assembly.php<?=$glpage;?>');
    });
})
</script>

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
$(function() {
	$(".paging").delegate("a", "click", function() {	
		var url = $(this).attr('href');
		
		$('#contenthere').load('/pages/assembly/'+url);
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
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				
			echo '<a class="btn btn-default classadd"  ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>';
				
			echo '</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">darbības</p>';

		echo '
			<div class="form-group col-md-4 pull-right">
			  <select id="xxx" class="form-control selectpicker btn-group-xs input-xs" data-live-search="true" title="filtrēt pēc klienta" onchange="selClient(this)">';
			  
			   $selectClient = mysqli_query($conn, "SELECT DISTINCT(clientCode) AS clientCode, clientName FROM cargo_header WHERE (status='20')") or die(mysqli_error($conn));
			  
			  while($rowc = mysqli_fetch_array($selectClient)){
				  echo '<option value="'.$rowc['clientCode'].'">'.$rowc['clientCode'].' '.$rowc['clientName'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			<div class="clearfix"></div>
		';	
					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT *,
	(select COUNT(id) from cargo_line WHERE docNr=cargo_header.docNr AND action='27') AS acount FROM cargo_header WHERE (status='20' || rowSent='1') ORDER BY acount DESC, deliveryDate";  //NEPIECIEŠAMAIS VAICĀJUMS
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

?>

</div>
</div>
</div>
</div>
</div>
</div>


<?php include("footer.php"); ?>