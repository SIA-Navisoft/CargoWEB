<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="cargo_setup";


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

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('functions/base.php');
include('header.php');

if(!empty($_GET['m'])) {$m = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT);if(false === $m) {$m = 1;}}else{$m = 1;}  
if(!empty($_GET['c'])) {$c = filter_input(INPUT_GET, 'c', FILTER_VALIDATE_INT);if(false === $c) {$c = 1;}}else{$c = 1;}  
if(!empty($_GET['tr'])) {$t = filter_input(INPUT_GET, 'tr', FILTER_VALIDATE_INT);if(false === $tr) {$tr = 1;}}else{$tr = 1;}
if(!empty($_GET['t'])) {$t = filter_input(INPUT_GET, 't', FILTER_VALIDATE_INT);if(false === $t) {$t = 1;}}else{$t = 1;}  //IEGŪSTAM LAPAS NUMURU

$user_group_array = array("w_user" => "NNVT", "w_partner" => "");


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
	function typeDel(event) {
		event.preventDefault();
		
		var val = $(event.target).data("td");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=deleteType&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

<script>	  
	function transportDel(event) {
		event.preventDefault();
		
		var val = $(event.target).data("td");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=deleteTransport&id='+val+'',
			beforeSend: function(){
				$('#del'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#del"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	

<script>	  
	function transportRet(event) {
		event.preventDefault();
		
		var val = $(event.target).data("tr");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=returnTransport&id='+val+'',
			beforeSend: function(){
				$('#ret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#ret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

<script>	  
	function typeRet(event) {
		event.preventDefault();
		
		var val = $(event.target).data("tr");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=returnType&id='+val+'',
			beforeSend: function(){
				$('#ret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#ret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>	  
	function codeDel(event) {
		event.preventDefault();

		var val = $(event.target).data("cd");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=deleteCode&id='+val+'',
			beforeSend: function(){
				$('#cdel'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cdel"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>	  
	function codeRet(event) {
		event.preventDefault();
		
		var val = $(event.target).data("cr");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=returnCode&id='+val+'',
			beforeSend: function(){
				$('#cret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>				

	<script>
	$(document).ready(function () {
		$('#add_type').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/cargo_setup/post.php?r=cargoSetupType',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$("#changebtn").on('click',function(e) {
					$('#changebtn').html("gaidiet...");
					$("#changebtn").prop("disabled",true);
					});
				},			
				success: function (data) {
					$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+data);
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
		$('#add_code').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/cargo_setup/post.php?r=cargoSetupCode',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$("#changebtn").on('click',function(e) {
					$('#changebtn').html("gaidiet...");
					$("#changebtn").prop("disabled",true);
					});
				},			
				success: function (data) {
					$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+data);
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
		$('#add_transport').on('submit', function(e) {
			e.preventDefault();

			$.ajax({
				url : '/pages/cargo_setup/post.php?r=add_transport',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$("#changebtn").on('click',function(e) {
					$('#changebtn').html("gaidiet...");
					$("#changebtn").prop("disabled",true);
					});
				},			
				success: function (data) {
					$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+data);
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
		$('#add_uom').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/cargo_setup/post.php?r=addUomCode',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$("#changebtn").on('click',function(e) {
					$('#changebtn').html("gaidiet...");
					$("#changebtn").prop("disabled",true);
					});
				},			
				success: function (data) {
					$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+data);
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>	
	<script>	  
	function uomDel(event) {
		event.preventDefault();
		
		var val = $(event.target).data("ud");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=deleteUomCode&id='+val+'',
			beforeSend: function(){
				$('#udel'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#udel"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>	  
	function uomRet(event) {
		event.preventDefault();
		
		var val = $(event.target).data("ur");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/cargo_setup/action.php?action=returnUomCode&id='+val+'',
			beforeSend: function(){
				$('#uret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#uret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>
	<script>
	function uomDoc(val) {
		$('#cargoType').load('/pages/cargo_setup/cargo_setup.php?view=edit<?=$elpage;?>&id='+val+'');
	}
	</script>	
	
	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#cargoType').load('/pages/cargo_setup/'+url);
			event.preventDefault();
		});
	});
	</script>	
<script>
$(document).ready(function(){
    $('.settingslist').click(function(){
		$("#pleaseWait").toggle();
        $('#cargoType').load('/pages/cargo_setup/settings.php');
    });
})

$(document).ready(function(){
    $('.settingslang').click(function(){
		$("#pleaseWait").toggle();
        $('#cargoType').load('/pages/cargo_setup/settings.php?view=languages');
    });
})

$(document).ready(function(){
	$('.settingsreceiver').click(function(){
		$("#pleaseWait").toggle();
		$('#cargoType').load('/pages/cargo_setup/settings.php?view=receiver');
	});
})

$(document).ready(function(){
	$('.settingssender').click(function(){
		$("#pleaseWait").toggle();
		$('#cargoType').load('/pages/cargo_setup/settings.php?view=sender');
	});
})

$(document).ready(function(){
	$('.settingsDestination').click(function(){
		$("#pleaseWait").toggle();
		$('#cargoType').load('/pages/cargo_setup/settings.php?view=destination');
	});
})
</script>	
<?php
//skats uz ierakstiem
if (!$view){?>
	
	<?php

	require('inc/s.php');

	echo '<div id="cargoType">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
				<a class="btn btn-default settingslist" ><i class="glyphicon glyphicon-wrench" style="color: #00B5AD" title="uzstādījumi"></i></a>
				<a class="btn btn-default settingslang" ><i class="glyphicon glyphicon-globe" style="color: #00B5AD" title="valodu uzstādījumi"></i></a>	
				<a class="btn btn-default settingsreceiver" ><i class="glyphicon glyphicon-road" style="color: #00B5AD" title="saņēmēju uzstādījumi"></i></a>
				<a class="btn btn-default settingssender" ><i class="glyphicon glyphicon-road icon-flipped" style="color: #00B5AD" title="nosūtītāju uzstādījumi"></i></a>	
				
				
				<a class="btn btn-default settingsDestination" ><i class="glyphicon glyphicon-download-alt" style="color: #00B5AD" title="galamērķi"></i></a>

			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p>kravu uzstādījumi</p>';

	echo '
<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-body">';
			
			if($p_edit=='on'){	
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					tipa pievienošana
				</div>
				<form id="add_code">
					<label>kods</label>
					<div class="form-inline">			  
					<input type="text" class="form-control input-xs" name="addCode" placeholder="kods" style="display:inline-block;"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>
					<button type="submit" class="btn btn-default btn-xs" style="display:inline-block;"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button> 
					</div>	
				</form>';
			}else{
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					kodi
				</div>';
			}

			echo '<div class="clearfix"></div>';	
					
	$rec_limitc = $a_p_l; $offsetc=0; $max_pagesc = 8;  //DEFINĒJAM LIMITUS
	$link_toc = $page_file.'?c='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$queryc = "SELECT * FROM cargo_code ORDER BY status DESC, code ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menuc, $result_allc, $resultGL7c) = pageing_menu($offsetc, $rec_limitc, $max_pagesc, $link_toc, $c, $conn, $queryc);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menuc;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7c = mysqli_num_rows($resultGL7c) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7c!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>';
						if($p_edit=='on'){ echo '<th>darbība</th>'; }
					echo '
					</tr></thead><tbody>';
			while($rowc = mysqli_fetch_array($resultGL7c)){

				echo '	<tr class="classlistedit">


							<td>'.$rowc['code'];
							
						if($p_edit=='on'){
							echo '<td>';
						
							if($rowc['status']==1){
								echo '<a class="btn btn-default btn-xs" data-cd="'.$rowc['id'].'" id="cdel'.$rowc['id'].'" onclick="codeDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>';
							}
							if($rowc['status']==0){
								echo '<a class="btn btn-default btn-xs" data-cr="'.$rowc['id'].'" id="cret'.$rowc['id'].'" onclick="codeRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>';
							}
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';
			
		}else{
			echo '<i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> neviens kods nav pievienots.';
		}
		
echo '
			</div>
		</div>



		
		
		




</div>



<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-body">';
		
			if($p_edit=='on'){	
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					transporta pievienošana
				</div>
				<form id="add_transport">
					<label>transports</label>
					<div class="form-inline">			  
					<input type="text" class="form-control input-xs" name="add_transport" placeholder="transports" style="display:inline-block;"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>
					<button type="submit" class="btn btn-default btn-xs" style="display:inline-block;"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button> 
					</div>	
				</form>';
			}else{
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					transporti
				</div>';
			}	

			echo '<div class="clearfix"></div>';
	
					
	$t_rec_limit = 5; $t_offset=0; $t_max_pages = 8;  //DEFINĒJAM LIMITUS
	$t_link_to = $page_file.'?tr='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$t_query = "SELECT * FROM transport ORDER BY status DESC, transport ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($t_page_menu, $result_all, $t_resultGL7) = pageing_menu($t_offset, $t_rec_limit, $t_max_pages, $t_link_to, $tr, $conn, $t_query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $t_page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$t_count_GL7 = mysqli_num_rows($t_resultGL7);  //IEGŪST SKAITU 					
		if ($t_count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>tips</th>';
						if($p_edit=='on'){ echo '<th>darbība</th>'; }
					echo '
					</tr></thead><tbody>';
			while($t_row = mysqli_fetch_array($t_resultGL7)){

				echo '	<tr class="classlistedit">


							<td>'.$t_row['transport'];

						if($p_edit=='on'){	
							echo '<td>';
						
							if($t_row['status']==1){
								echo '<a class="btn btn-default btn-xs" data-td="'.$t_row['id'].'" id="del'.$t_row['id'].'" onclick="transportDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>';
							}
							if($t_row['status']==0){
								echo '<a class="btn btn-default btn-xs" data-tr="'.$t_row['id'].'" id="ret'.$t_row['id'].'" onclick="transportRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>';
							}
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';

		}else{
			echo '<i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> neviens transports nav pievienots.';
		}
		
		

		echo '
			</div>
		</div>
</div>




<div class="clearfix"></div>
';	

		// mērvienības pievienošana
		echo '
<p>preces uzstādījumi</p>		
<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-body">';

			if($p_edit=='on'){			
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					mērvienības pievienošana
				</div>
				<form id="add_uom">

				<div class="form-group col-md-3">
					<label for="addUom">mērvienība</label>			
					<input type="text" class="form-control input-xs" name="addUom" id="addUom" placeholder="mērvienība" style="display:inline-block;"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>
				</div>

				<div class="form-group col-md-5">
					<label for="addName">nosaukums</label>			
					<input type="text" class="form-control input-xs" name="addName" id="addName" placeholder="nosaukums" style="display:inline-block;"  oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>
				</div>

				<div class="form-group col-md-4">	
				<label for="NAVuom">NAV kods</label>			
					<select class="form-control selectpicker btn-group-xs" name="NAVuom" id="NAVuom"  data-live-search="true" title="NAV kods">';	
						echo '<option value=""></option>';
						$selectuom = mysqli_query($conn, "SELECT code, description FROM n_unit_of_measurement") or die(mysqli_error($conn));
						while($rowi = mysqli_fetch_array($selectuom)){
							echo '<option value="'.$rowi['code'].'">'.$rowi['code']; if($rowi['description']){ echo ' ('.$rowi['description'].')'; } echo '</option>';
						}
					
					echo '
					</select>
				</div>

				<div class="clearfix"></div><br>
					<button type="submit" class="btn btn-default btn-xs" style="display:inline-block;"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button> 			
				</form>';
			}else{
				echo '
				<div class="page-header" style="margin-top: -5px;">		
					mērvienības
				</div>';
			}

		echo '<div class="clearfix"></div>';
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 8;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?m='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT m.*, nm.description
			  FROM unit_of_measurement AS m
			  LEFT JOIN n_unit_of_measurement AS nm
			  ON m.NAVcode=nm.code
			  ORDER BY m.status DESC, m.code ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menum, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $m, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menum;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>mērvienība</th>
						<th>nosaukums</th>
						<th>NAV kods</th>';
						if($p_edit=='on'){ echo '<th>darbība</th>'; }
					echo '	
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){
				?>
				<tr <?php if($p_edit=='on'){ ?> class="uomedit" onclick="uomDoc('<?=$row['code'];?>')" <?php } ?> >
				<?php
				echo '


							<td>'.$row['code'].'
							<td>'.$row['name'].'
							<td>'.$row['NAVcode'];
							if($row['description']){echo ' ('.$row['description'].')';}

						if($p_edit=='on'){
							echo '<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" data-ud="<?=$row['code'];?>" id="udel<?=$row['code'];?>" onclick="uomDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" data-ur="<?=$row['code'];?>" id="uret<?=$row['code'];?>" onclick="uomRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}	
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';

		}else{
			echo '<i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> neviens kods nav pievienots.';
		}
echo '
			</div>
		</div>
</div>
';			

}
mysqli_close($conn);
?>

</div>
</div>
</div>
</div>
</div>
</div>


<?php include("footer.php"); ?>