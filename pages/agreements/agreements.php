<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="agreements";


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

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('../../functions/base.php');


if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  
if(!empty($_GET['page_a'])) {$page_a = filter_input(INPUT_GET, 'page_a', FILTER_VALIDATE_INT);if(false === $page_a) {$page_a = 1;}}else{$page_a = 1;}  //IEGŪSTAM LAPAS NUMURU

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['line'])){$line = htmlentities($_GET['line'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['aStatus'])){$aStatus = htmlentities($_GET['aStatus'], ENT_QUOTES, "UTF-8");}


if($aStatus==''){$aStatus='off';}

if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}
?>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#agreements').load('/pages/agreements/agreements.php<?=$glpage;?>&aStatus=<?=$aStatus;?>');
    });
})
</script>

<?php if($p_edit=='on'){ ?>
<script>
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#agreements').load('/pages/agreements/add.php?aStatus=<?=$aStatus;?>&aStatus=<?=$aStatus;?>');
    });
})
</script>
<?php } ?>

<script>
function newDoc(val) {
	var val = encodeURI(val);
	$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id='+val+'');
}
</script>

<script>
function editDoc(val) {
	var val = encodeURI(val);
	$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id=<?=$id;?>&line='+val+'');
}
</script>

<script>
function changeStatus(val) {
	var val = encodeURI(val);
	$('#agreements').load('/pages/agreements/agreements.php?aStatus='+val+'<?=$elpage;?>');
}
</script>

<script>
$(function() {
    $(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		
		$('#agreements').load('/pages/agreements/'+url);
		event.preventDefault();
    });
});
</script>					

<?php

if($p_edit=='on'){

if($line!=''){
	$formPost = '#agreement_line_edit';
	$formUrl = '/pages/agreements/post.php?r=edit&id='.$id.'&line='.$line;
}else{
	$formPost = '#agreement_line';
	$formUrl = '/pages/agreements/post.php?r=add&id='.$id;
}

?>

<script>
$(document).ready(function () {
    $('<?=$formPost;?>').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '<?=$formUrl;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {
				var productNr = $("#productNr").val();
				var amount = $("#amount").val();
				$('#agreements').load('/pages/agreements/agreements.php?view=edit&aStatus=<?=$aStatus;?>&id=<?=$id;?>&res=done');
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

	if (confirm('Tiešām vēlaties izdzēst šo līniju?')) {
		
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/agreements/action.php?action=deleteLine&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				$('#agreements').load('/pages/agreements/agreements.php?view=edit&aStatus=<?=$aStatus;?>&id=<?=$id;?>&res=del');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});

	}
}	  
</script>


<?php } ?>

<?php
//skats uz ierakstiem


	require('../../inc/s.php');

	echo '<div id="agreements">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
			if($p_edit=='on'){ echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="pievienot"></i></a>';}
			echo '</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">līgumu reģistrs</p>';
        echo '<div class="clearfix"></div>';
?>

<script type="text/javascript">

function searchInAgr(value) {
	var tid = '<?=$id;?>';
	var lid = '<?=$line;?>';
	var aStatus = '<?=$aStatus;?>';	
    var search = $.post("/pages/agreements/search.php?search=agr", {name:value, id:tid, line:lid, aStatus:aStatus},function(data){
        $("#sAgr").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>

<script type="text/javascript">

function searchInLines(value) {
	var tid = '<?=$id;?>';
	var lid = '<?=$line;?>';
    var search = $.post("/pages/agreements/search.php?search=lines", {name:value, id:tid, line:lid},function(data){
        $("#sLines").html(data);		
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

echo '<div class="col-sm-3 col-md-6 col-lg-3" style="border-right: 1px solid #eee;">';

echo '

<div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;">
	<input type="text" id="searchWait" class="form-control input-xs" onkeyup="searchInAgr(this.value)" placeholder="meklēt" value="'.$s.'">
</div>

<div style="display: inline-block; float: right;">';
	
	if($aStatus=='on'){$sOff = "'off'"; $aChecked = 'aktīvie';}
	if($aStatus=='off'){$sOff = "'on'"; $aChecked = 'neaktīvie';}
	
    echo '
	<a class="btn btn-default btn-xs" onclick="changeStatus('.$sOff.')">'.$aChecked.'</a>
</div>

';

echo '<div id="sAgr">';	

	if($aStatus=='on'){$eQue = " WHERE (dateTo!='0000-00-00 00:00:00' AND dateTo IS NOT NULL) AND dateTo<CURDATE() OR status='20' AND deleted='0'";}else{$eQue = " WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0'";}

	$rec_limit = $a_p_l; $offset=0; $max_pages = 3;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?aStatus='.$aStatus.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM agreements ".$eQue."  ORDER BY customerNr DESC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo '<div style="display: inline-block;">'.$page_menu.'</div>'; //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><tbody>';   

			while($row = mysqli_fetch_array($resultGL7)){
				
				if($row['contractNr']==$id){$bg = ' style="background-color: #337ab7; color: white;"';}else{$bg = null;}
				?>
				<tr onclick="newDoc('<?=$row['contractNr'];?>')" <?=$bg;?>>
				<?php
				echo '	

							<td>'.$row['contractNr'].'
							<td>'.$row['customerNr'].' ('.$row['customerName'].')

						</tr>';
			}
			
			echo '</tbody></table></div></div>';
			mysqli_close($conn);
		}

echo '</div>';

if (!$view){
echo '
<div class="col-sm-9 col-md-6 col-lg-9"> 
 
    <div class="panel panel-default" style="border-left: 5px solid #00B5AD;"> 
        <div class="panel-body"> 
            <h4>Līgumu pārvaldība</h4> 
            <p>Pa kreisi nospiežot uz jebkuru līgumu šeit parādīsies līguma detaļas.</p> 
        </div>
    </div>

</div>
';
}else{

	require('../../inc/s.php');

	echo '
<div class="col-sm-9 col-md-6 col-lg-9"> 	


 
    <div class="panel panel-default"> 
        <div class="panel-body">';

		if($id){

			$getInfoAgr = mysqli_query($conn, "SELECT customerNr, outerNr, freeDays, dateFrom, dateTo, status, prepareAll, customerName, actForm, useScan FROM agreements WHERE contractNr='".mysqli_real_escape_string($conn, $id)."'");
			$giaRow = mysqli_fetch_array($getInfoAgr) or die(mysqli_error($conn));
			

			echo '<p style="display:inline-block;">līgums: <b>'.$id.' ('.$giaRow['customerName'].')</b>&nbsp</p>';

			if($giaRow['status']==20){$disabled='disabled';}else{$disabled=null;}		
			?>
<?php if($p_edit=='on'){ ?>
			<script>
			$(document).ready(function () {
				$('#editAgreement').on('submit', function(e) {
					e.preventDefault();
					
					$.ajax({
									url : '/pages/agreements/post.php?r=editAgreement',
						type: "POST",
						data: $(this).serializeArray(),
						beforeSend: function(){
							$('#savebtn').html("gaidiet...");
							$("#savebtn").prop("disabled",true);
						},			
						success: function (data) {
							var productNr = $("#productNr").val();
							var amount = $("#amount").val();
							
							$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id=<?=$id;?>');
						},
						error: function (jXHR, textStatus, errorThrown) {
							alert(errorThrown);
						}
					});
				});
			});
			</script>
<?php } ?>
<script>	  
	function agrApprove(event) {
		event.preventDefault();
		
		var val = $(event.target).data("app");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/agreements/action.php?action=agrApprove&id='+val+'',
			beforeSend: function(){
				$('#agrApp').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#agrApp").prop("disabled",true);
			},				
			success: function (result) {
				$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id=<?=$id;?>');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
</script>

<script>	  
	function agrClose(event) {
		event.preventDefault();
		
		var val = $(event.target).data("clo");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/agreements/action.php?action=agrClose&id='+val+'',
			beforeSend: function(){
				$('#agrClo').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#agrClo").prop("disabled",true);
			},				
			success: function (result) {
				$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id=<?=$id;?>');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
</script>

<script>	  
	function agrUnlock(event) {
		event.preventDefault();
		
		var val = $(event.target).data("unl");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/agreements/action.php?action=agrUnlock&id='+val+'',
			beforeSend: function(){
				$('#agrUnl').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#agrUnl").prop("disabled",true);
			},				
			success: function (result) {
				$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id=<?=$id;?>');
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
</script>

<script>	  
	function agrDelete(event) {
		event.preventDefault();
		
		if (confirm('Tiešām vēlaties izdzēst šo līgumu?')) {
			var val = $(event.target).data("unl");

			$.ajax({
				global: false,
				type: 'GET',
				url: '/pages/agreements/action.php?action=agrDelete&id='+val+'',
				beforeSend: function(){
					$('#agrUnl').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
					$("#agrUnl").prop("disabled",true);
				},				
				success: function (result) {
					$('#agreements').load('/pages/agreements/agreements.php');
				},
				error: function (request, status, error) {
					serviceError();
				}
			});
		}
	}	  
</script>

			<?php			


		}

		if($p_edit=='on'){
			echo '<form id="editAgreement">';
			$disabled='';
		}else{
			$disabled= ' disabled';
		}
							echo '
							<div class="form-row">
									<input type="hidden" name="contractNr" value="'.$id.'">
									<div class="form-group col-md-3">
									<label for="customer">klients</label>
										<select class="form-control selectpicker btn-group-xs"  id="customer" name="customer"  data-live-search="true" title="klients" '.$disabled.'>';
										
										$selectCustomers = mysqli_query($conn, "SELECT Code, Name FROM n_customers") or die(mysqli_error($conn));
										while($rowc = mysqli_fetch_array($selectCustomers)){
											echo '<option  value="'.$rowc['Code'].'"';
												if($giaRow['customerNr']==$rowc['Code']){echo 'selected';}
											echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
										}
										
										echo '
										</select>	
									</div>	
							
									<div class="form-group col-md-3">
										<label for="outerNr">ārējais līg. nr.</label>
										<input type="text" class="form-control" id="outerNr" name="outerNr" placeholder="ārējais līg. nr." value="'.$giaRow['outerNr'].'" '.$disabled.'>
									</div>
						

						
									<div class="form-group col-md-3">
									<label for="dateFrom">no</label>
									<input type="text" class="form-control datepicker" id="dateFrom" name="dateFrom" value="';
											if($giaRow['dateFrom']!='' && $giaRow['dateFrom']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($giaRow['dateFrom']));}
										echo '">
									</div>

									<div class="form-group col-md-3">
										<label for="dateTo">līdz</label>
										<input type="text" class="form-control datepicker" id="dateTo" name="dateTo" value="';
											if($giaRow['dateTo']!='' && $giaRow['dateTo']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($giaRow['dateTo']));}
										echo '" '.$disabled.'>
									</div>
									<div class="clearfix"></div>
									<div class="form-group col-md-3">
										<label for="freeDays">bezmaksas glabāšana</label>
										<input type="text" class="form-control" id="freeDays" name="freeDays" placeholder="dienu skaits" value="'.$giaRow['freeDays'].'" '.$disabled.'>
									</div>										

									<div class="form-group col-md-3">
									<label for="actForm">akta forma</label>
										<select class="form-control selectpicker btn-group-xs"  id="actForm" name="actForm"  data-live-search="true" title="akta forma" '.$disabled.'>
										
											<option value="1"'; if($giaRow['actForm']==1){ echo 'selected'; } echo '>1</option>
											<option value="2"'; if($giaRow['actForm']==2){ echo 'selected'; } echo '>2</option>
											<option value="3"'; if($giaRow['actForm']==3){ echo 'selected'; } echo '>3</option>
											<option value="4"'; if($giaRow['actForm']==4){ echo 'selected'; } echo '>4</option>
										
										</select>	
									</div>										
									
									
									<div class="form-group col-md-3">
										
										<div class="checkbox">
											<label><input type="checkbox" id="forRelease" name="forRelease"'; if($giaRow['prepareAll']==1){echo ' checked';} echo $disabled.'> aprēķināt glabāšanu visā posmā</label>
										</div>
									</div>

									<div class="form-group col-md-3">
										
										<div class="checkbox">
											<label><input type="checkbox" id="useScan" name="useScan"'; if($giaRow['useScan']==1){echo ' checked';} echo $disabled.'> lietot skenēšanu</label>
										</div>
									</div>									

							</div>
							<div class="clearfix"></div>';
							
			if($p_edit=='on'){
							if($id){

								if($giaRow['status']==0 || $giaRow['status']==10 && $disabled!='disabled'){

									echo '<button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>&nbsp;';

								}

								if($giaRow['status']==0 && $disabled!='disabled'){									

									echo '<a class="btn btn-default btn-xs" data-app="'.$id.'" id="agrApp" onclick="agrApprove(event)"><i class="glyphicon glyphicon-ok" style="color: green;"></i> apstiprināt</a>';
									
								}								

								if($giaRow['status']==10 && $disabled!='disabled'){

									echo '<a class="btn btn-default btn-xs" data-clo="'.$id.'" id="agrClo" onclick="agrClose(event)"><i class="glyphicon glyphicon-lock" style="color: red;"></i> slēgt</a>';

								}

								if($giaRow['status']==20){

									echo '<a class="btn btn-default btn-xs" data-unl="'.$id.'" id="agrUnl" onclick="agrUnlock(event)"><i class="glyphicon glyphicon-lock" style="color: green;"></i> atslēgt</a>';

								}

								$checkLines = mysqli_query($conn, "SELECT id FROM agreements_lines WHERE contractNr='".mysqli_real_escape_string($conn, $id)."' AND deleted='0'");
								
								if(mysqli_num_rows($checkLines)==0){

									echo ' <a class="btn btn-default btn-xs" data-unl="'.$id.'" id="agrDel" onclick="agrDelete(event)"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</a>';

								}								

							}

					echo '		
					</form>';
			}
			
		


			echo '
			<div style="display:inline-block; float: right;">
				<div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;">
					<input type="text" id="searchWait" class="form-control input-xs" onkeyup="searchInLines(this.value)" placeholder="meklēt" value="'.$s.'">
				</div>
			</div>
			<div class="clearfix"></div><br>'; 
				
			echo '<div id="sLines">';
			$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
			$link_to = $page_file.'?view=edit&id='.$id.'&line='.$line.'&page_a='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
			$query = "SELECT * FROM agreements_lines WHERE contractNr='".mysqli_real_escape_string($conn, $id)."' AND deleted='0' ORDER BY id DESC";  //NEPIECIEŠAMAIS VAICĀJUMS
			
			list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page_a, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 
		
			echo '<div style="display: inline-block;">'.$page_menu.'</div>';		
				
			$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 
						
				if ($count_GL7>0){
		
					echo '
					<div class="table-responsive">';

					if($p_edit=='on'){
						if($line!=''){
							echo '<form id="agreement_line_edit">';
						}else{
							echo '<form id="agreement_line">';
						}
					}
						

						echo '
							<table class="table table-hover table-responsive">
								
								<thead>
									<th>palīg pak.
									<th>pakalpojums
									<th>glab.
									<th nowrap>pakalpojuma nos.
									<th style="min-width: 150px;">prece
									<th>mērvienība
									
									<th>atbilstība
									<th>garums
									
									<th>tarifs
									<th>datums no
									<th>datums līdz';
									if($p_edit=='on' && $giaRow['status']<20 && ($giaRow['dateTo']=='0000-00-00 00:00:00' ||  date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d'))){echo '<th>dzēst';}
								echo '	
								</thead>
								<tbody>';   
			
								while($row = mysqli_fetch_array($resultGL7)){

									if($row['id']==$line){$bgcol = ' style="background-color: #337ab7; color: white;"';}else{$bgcol = null;}
									echo '
									<tr'; if($p_edit=='on' && $giaRow['status']<20 && ($giaRow['dateTo']=='0000-00-00 00:00:00' || date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d'))){ echo ' onclick="editDoc('.$row['id'].')"';} echo $bgcol.'>		<td>';
										if($row['extra_resource']=='on'){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
										echo '
										<td>'.$row['service'].' ('.returnResourceName($conn, $row['service']).')
										<td>';
										if($row['keeping']=='on'){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
										echo '
										<td>'.$row['service_name'].'
										<td>';

										if($row['item']){echo $row['item'].' ('.returnProductName($conn, $row['item']).')';}

										echo '
										<td>';
										
										if($row['uom']){echo $row['uom'].' ('.returnUomName($conn, $row['uom']).')';}

										
										echo '<td>'.$row['productMatch'];
										echo '<td>'.$row['productLength'];
										
										
										echo '
										<td>'.floatval($row['tariffs']).'
										<td>';
										if($row['dateFrom']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['dateFrom']));}
										echo '<td>';
										if($row['dateTo']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['dateTo']));}

										if($p_edit=='on' && $giaRow['status']<20 && ($giaRow['dateTo']=='0000-00-00 00:00:00' || date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d'))){
											echo '<td><button class="btn btn-default btn-xs" data-dol="'.$row['id'].'" onclick="delLine(event)" id="del'.$row['id'].'" style="display: inline-block;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';
										}								
										
									echo '
									</tr>';

								}

								if($p_edit=='on' && $giaRow['status']<20 && (date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d') || $giaRow['dateTo']=='0000-00-00 00:00:00')){

									if($line){

										$line = mysqli_real_escape_string($conn, $line);
										$selectLineInfo = mysqli_query($conn, "SELECT * FROM agreements_lines WHERE id='".$line."' AND deleted='0'");
										$l_row = mysqli_fetch_array($selectLineInfo);
										
										$resource = html_entity_decode($l_row['service'], ENT_QUOTES, "UTF-8");
										
										$product = $l_row['item'];
										$uom = $l_row['uom'];
										$tariffs = ' value="'.floatval($l_row['tariffs']).'"';

										if($l_row['dateFrom']!='0000-00-00 00:00:00'){ $dateFrom = ' value="'.date('d.m.Y', strtotime($l_row['dateFrom'])).'"';}else{$dateFrom=null;}
										if($l_row['dateTo']!='0000-00-00 00:00:00'){$dateTo = ' value="'.date('d.m.Y', strtotime($l_row['dateTo'])).'"';}else{$dateTo = null;}


										$serviceName = ' value="'.$l_row['service_name'].'"';

										$keeping = $l_row['keeping'];
										$extraResource = $l_row['extra_resource'];

									}else{
										$resource = $product = $tariffs = $keeping = $extraResource = null;
									}

									echo '							
									<tr>
										<td><input type="checkbox" id="extraResource" name="extraResource"'; if($extraResource=='on'){echo ' checked';} echo ' >
										<td>
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="pakalpojums" name="resource">';
											require('../../inc/s.php');	
											$selectResource = mysqli_query($conn, "SELECT * FROM n_resource");
											while($rowi = mysqli_fetch_array($selectResource)){
												echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
													if($resource!='' && $resource==$rowi['id']){ echo ' selected';}
												echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>
										<td><input type="checkbox" name="keeping"'; if($keeping=='on'){echo ' checked';} echo '>
										<td><input type="text" class="form-control" name="serviceName" placeholder="pakalpojuma nosaukums" '.$serviceName.'>

										<td>';
										
										if($extraResource!='on'){
											echo '
											<select data-width="150px" class="form-control selectpicker btn-group-xs product"  data-live-search="true" title="prece" name="product" id="product">';
											
											$selectProducts = mysqli_query($conn, "SELECT * FROM n_items WHERE status='1'");
											while($rowi = mysqli_fetch_array($selectProducts)){
												echo '<option value="'.$rowi['code'].'"';
													if($product!='' && $product==$rowi['code']){ echo ' selected';}
												echo '>'.$rowi['code'].' ('.$rowi['name1'].' '.$rowi['name2'].')</option>';
											}
											
											echo '
											</select>';
										}
										
										

										
										
										echo '
										<td>
										
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="mērvienība" name="uom">';
												
											$selectUOM = mysqli_query($conn, "SELECT code, name FROM unit_of_measurement");
											while($rowi = mysqli_fetch_array($selectUOM)){
												echo '<option value="'.$rowi['code'].'"';
													if($uom!='' && $uom==$rowi['code']){ echo ' selected';}
												echo '>'.$rowi['code'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>';

										echo '<td><input type="text" class="form-control input-xs" style="min-width: 80px;" name="addMatch" placeholder="atbilstība" value="'.$l_row['productMatch'].'">	';
										echo '<td><input type="text" class="form-control input-xs" style="min-width: 80px;" name="addLength" placeholder="garums" value="'.$l_row['productLength'].'">';											
											
										echo '
										<td><input type="text" class="form-control numbersOnly" name="tariffs" placeholder="tarifs" style="min-width: 80px;" '.$tariffs.'>											

										<td><input type="text" class="form-control datepicker" style="min-width: 80px;" name="dateFrom" '.$dateFrom.'>	

										<td><input type="text" class="form-control datepicker" style="min-width: 80px;" name="dateTo" '.$dateTo.'>

										<td>';

											if($line!=''){
												echo '<button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';
											}else{
												echo '<button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>';
											}

									echo '	
									</tr>';
								}

								echo '	
								</tbody>
							</table>';


						
							if($p_edit=='on' && $giaRow['status']<20 && date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d')){
								echo '</form>';
							}


					echo '	
					</div>';
					mysqli_close($conn);

				}else{

					echo '<div><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</div>';


					if($p_edit=='on' && $giaRow['status']<20 && (date('Y-m-d', strtotime($giaRow['dateTo']))>=date('Y-m-d') || $giaRow['dateTo']=='0000-00-00 00:00:00')){
						if($line!=''){
							echo '<form id="agreement_line_edit">';
						}else{
							echo '<form id="agreement_line">';
						}
					

					echo '
					<div class="table-responsive">
						<form id="agreement_line">
							<table class="table table-hover table-responsive">
								
								<thead>
									<th>palīg pak.
									<th>pakalpojums
									<th>glab.
									<th nowrap>pakalpojuma nos.
									<th style="min-width: 150px;">prece
									<th>mērvienība
									<th>tarifs
									<th>datums no
									<th>datums līdz
									<th>
								</thead>
								<tbody>
									<tr>
										<td><input type="checkbox" id="extraResource" name="extraResource"'; if($extraResource=='on'){echo ' checked';} echo ' >
										<td>

											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="pakalpojums" name="resource">';
											require('../../inc/s.php');	
											$selectResource = mysqli_query($conn, "SELECT * FROM n_resource");
											while($rowi = mysqli_fetch_array($selectResource)){
												echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>
										<td><input type="checkbox" name="keeping">
										<td><input type="text" class="form-control" name="serviceName" placeholder="pakalpojuma nosaukums">

										<td>
										
											<select data-width="150px" class="form-control selectpicker btn-group-xs product"  data-live-search="true" title="prece" name="product">';
											
											$selectProducts = mysqli_query($conn, "SELECT * FROM n_items WHERE status='1'");
											while($rowi = mysqli_fetch_array($selectProducts)){
												echo '<option value="'.$rowi['code'].'">'.$rowi['code'].' ('.$rowi['name1'].' '.$rowi['name2'].')</option>';
											}
											
											echo '
											</select>

										<td>

											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="uom" name="uom">';
											require('../../inc/s.php');	
											$selectUOM = mysqli_query($conn, "SELECT code, name FROM unit_of_measurement");
											while($rowi = mysqli_fetch_array($selectUOM)){
												echo '<option value="'.$rowi['code'].'">'.$rowi['code'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>												

										<td><input type="text" class="form-control numbersOnly" name="tariffs" placeholder="tarifs" style="min-width: 80px;">

										<td><input type="text" class="form-control datepicker" name="dateFrom" style="min-width: 80px;">	

										<td><input type="text" class="form-control datepicker" name="dateTo" style="min-width: 80px;">

										<td><button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>

									</tr>								
								</tbody>
							</table>
						</form>';
					}	
					echo '	
					</div>'; 							
				}

			echo '		
			</div>
		</div>
	
	</div>
	</div>
	';
}
?>

<script>

$("#extraResource").click(function() {
    if($(this).is(":checked")) {
        $(".product").hide();
    } else {
        $(".product").show();
    }
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
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<script>

	$("select[name='resource']").on("change", function() {

		var selected = $(this).find('option:selected');
        var extra = selected.data('name'); 

		$("input[name='serviceName']").val(extra);
	});

</script>	
<?php include_once("../../datepicker.php"); ?>
