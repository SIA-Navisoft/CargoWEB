<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="stock";


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

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['edituom'])){$edituom = htmlentities($_GET['edituom'], ENT_QUOTES, "UTF-8");}


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}
?>

<?php if($p_edit=='on'){ ?>
	<script>
	$(document).ready(function () {
		$('#add_product').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/stock/post.php?r=addProduct',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('#submitbtn').html("gaidiet...");
					$("#submitbtn").prop("disabled",true);

				},			
				success: function (data) {
				
					$('#stock').load('/pages/stock/stock.php?res='+data);
					
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
		$('#edit_product').on('submit', function(e) {
			e.preventDefault();

			if (confirm('UZMANĪBU! Mainot pamat mērvienību tiks dzēstas visas papild mērvienības.')) {

				$.ajax({
					url : '/pages/stock/post.php?r=editProduct',
					type: "POST",
					data: $(this).serializeArray(),
					beforeSend: function(){
						
						$('#submitbtn').html("gaidiet...");
						$("#submitbtn").prop("disabled",true);

					},			
					success: function (data) {

						$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id=<?=urlencode($id);?>&res='+data);
						
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
	$(document).ready(function () {
		$('#add_additional').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/stock/post.php?r=add_additional',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('#additionalbtn').html("gaidiet...");
					$("#additionalbtn").prop("disabled",true);

				},			
				success: function (data) {
				
					$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id=<?=urlencode($id);?>&res='+data);
					
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
	function productDel(event) {
		event.preventDefault();

		var val = $(event.target).data("pd");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=deleteProduct&id='+val+'',
			beforeSend: function(){
				$('#pdel'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#pdel"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#stock').load('/pages/stock/stock.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>	  
	function productRet(event) {
		event.preventDefault();

		var val = $(event.target).data("pr");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=returnProduct&id='+val+'',
			beforeSend: function(){
				$('#pret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#pret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#stock').load('/pages/stock/stock.php?res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>	
	
	<script>	  
	function euomDel(event) {
		event.preventDefault();

		var val = $(event.target).data("ed");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=deleteEuom&id='+val+'',
			beforeSend: function(){
				$('#uomdel'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#uomdel"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id=<?=urlencode($id);?>&res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>	  
	function euomRet(event) {
		event.preventDefault();

		var val = $(event.target).data("er");
		
		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=returnEuom&id='+val+'',
			beforeSend: function(){
				$('#uomret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#uomret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id=<?=urlencode($id);?>&res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	</script>

	<script>
	function newDoc(val) {
		var val = encodeURI(val);
		
		$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id='+val+'');
	}
	</script>	
	
	<script>
	function uomDoc(val) {
		var val = encodeURI(val);
		varmid = '<?=$id;?>';
		var varmid = encodeURI(varmid);
		
		$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id='+varmid+'&edituom='+val);
	}
	</script>

	<script>
	$(document).ready(function(){
		$('.classlist').click(function(){
			$('#stock').load('/pages/stock/stock.php<?=$glpage;?>');
		});
	})
	</script>

<script>
	$(document).ready(function(){
		$('.classresource').click(function(){
			$('#stock').load('/pages/stock/resources.php');
		});
	})
</script>

<script>
	$(document).ready(function(){
		$('.classlocation').click(function(){
			$('#stock').load('/pages/stock/settings.php?view=locations');
		});
	})
</script>

	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#stock').load('/pages/stock/'+url);
			event.preventDefault();
		});
	});
	</script>
	
<?php
//skats uz ierakstiem
if (!$view){?>
	
	<?php

	require('../../inc/s.php');

	echo '<div id="stock">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				
				echo '<a class="btn btn-default classlocation"  ><i class="glyphicon glyphicon-object-align-bottom" style="color: #00B5AD"  title="noliktavu katalogs"></i></a>
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
			
			if ($res=='added'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">pievienots!</div></div>';}
			
			
			if ($res=='error' ||  $res=='productCodeExists' || $res=='productCodeDoesNotExists'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
			  
					
			if ($res=="restored"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">atjaunots!</div></div>';}
			if ($res=="deleted"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">dzēsts!</div></div>';}			
			
		echo '</div>';
		
		echo '<p style="display:inline-block;">preču katalogs</p>';

	if($p_edit=='on'){	
		?>
		<script>

			$('#addCode').bind('keyup',function(){
				$(this).val($(this).val().replace(/[^0-9a-z ]/gi, ""))
			});
		</script>
		<?php
		echo '
		<form id="add_product">
			
			<div class="form-group col-md-3">
			  <label>kods ';
			if($res=='productCodeExists'){echo '<span class="label label-danger">kods jau eksistē</span>';}
			echo '</label>			
			  <input type="text" title="Atļauti: cipari un latīņu burti." class="form-control input-xs" name="addCode" id="addCode" placeholder="kods" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>			  
			</div>
			
			<div class="form-group col-md-3">
			  <label>nosaukums</label>			
			  <input type="text" class="form-control input-xs" name="addName" placeholder="nosaukums" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required>			  
			</div>

			<div class="form-group col-md-3">
			<label for="addUom">pamat mērvienība</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addUom" id="addUom"  data-live-search="true" title="pamat mērvienība">';
			  $selectType = mysqli_query($conn, "SELECT code FROM unit_of_measurement WHERE status='1'") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectType)){
				  echo '<option value="'.$rowt['code'].'">'.$rowt['code'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			
			<div class="form-group col-md-3">
			<label for="helpUom">palīg mērvienība</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="helpUom" id="helpUom"  data-live-search="true" title="palīg mērvienība">';
			  $selectHelpUom = mysqli_query($conn, "SELECT code FROM unit_of_measurement WHERE status='1'") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectHelpUom)){
				  echo '<option value="'.$rowt['code'].'">'.$rowt['code'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';			
			
			echo '

			<div class="clearfix"></div>
			
			<div class="form-group col-md-3">
			  <label>svītrkods</label>			
			  <input type="text" class="form-control input-xs" name="addBarcode" placeholder="svītrkods">		  
			</div>

			
			<div class="form-group col-md-3">
			<label for="addUom">projekts</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addProject" id="addProject"  data-live-search="true" title="projekts">';
			  $selectProject = mysqli_query($conn, "SELECT Code, Name FROM n_projects") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectProject)){
				  echo '<option value="'.$rowt['Code'].'">'.$rowt['Code'].' ('.$rowt['Name'].')</option>';
			  }
			  
			  echo '
			  </select>	
			</div>

			<div class="form-group col-md-3">										
				<div class="checkbox">
					<label><input type="checkbox" id="noSerial" name="noSerial">seriālais nr. nav obligāts</label>
				</div>
			</div> 			

			<div class="clearfix"></div><br>
			<button type="submit" id="submitbtn" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button> 			
		</form>';		
	}		
		
?>


<script type="text/javascript">

function getStates(value) {
    var search = $.post("search.php", {name:value},function(data){
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
	
echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div><div class="clearfix"></div>'; 		
	
echo '<div id="results">';					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM n_items ORDER BY status DESC, code ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>
						<th>pamat mērvienība</th>
						<th>palīg mērvienība</th>
						
						<th>svītrkods</th>
						<th>nosaukums</th>
						<th>projekts</th>
						<th>seriālais nr. nav obligāts</th>
						<th>pēdējās aktivitātes veica</th>';

						if($p_edit=='on'){ echo '<th>darbība</th>'; }

					echo '	
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				?>
				<tr class="classlistedit" onclick="newDoc('<?=$row['code'];?>')">
				<?php
				echo '

							<td>'.$row['code'].'
							<td>'.$row['unitOfMeasurement'];
							echo '<td>'.$row['assistantUmo'];

							echo '
							<td>'.$row['barCode'].'
							<td>'.$row['name1'].'
							<td>'.$row['project_code'];
							if($row['project_name']!=''){echo ' ('.$row['project_name'].')';}
							
							echo '<td>';
							if($row['no_serial']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}							
							
							echo '<td>';
							
							if($row['editedBy']>0){
								echo returnMeWho($row['editedBy']).' ('.date('d.m.Y H:i:s', strtotime($row['editedDate'])).')';
							}else{
								echo returnMeWho($row['createdBy']).' ('.date('d.m.Y H:i:s', strtotime($row['createdDate'])).')';
							}

						if($p_edit=='on'){	
							echo '<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" data-pd="<?=$row['code'];?>" id="pdel<?=$row['code'];?>" onclick="productDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" data-pr="<?=$row['code'];?>" id="pret<?=$row['code'];?>" onclick="productRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}
		echo '</div>';
		
}

if($view=='edit'){

	require('../../inc/s.php');

	echo '<div id="stock">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				
				echo '<a class="btn btn-default classlocation"  ><i class="glyphicon glyphicon-object-align-bottom" style="color: #00B5AD"  title="noliktavu katalogs"></i></a> 
				
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
			
			if ($res=='added'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">pievienots!</div></div>';}
			
			
			if ($res=='error' ||  $res=='productCodeExists' || $res=='productCodeDoesNotExists'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
			  
					
			if ($res=="restored"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">atjaunots!</div></div>';}
			if ($res=="deleted"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">dzēsts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">labot preci</p><br>';

		$code = mysqli_real_escape_string($conn, $id);
		$getDetails = mysqli_query($conn, "SELECT code, unitOfMeasurement, assistantUmo, barCode, name1, defWeight, defVolume, project_code, no_serial FROM n_items WHERE code='".$code."'");
		$rowDet = mysqli_fetch_array($getDetails);
			
	if($p_edit=='on'){	
		echo '<form id="edit_product">';
		$disabled='';
	}else{
		$disabled='disabled';
	}
			
		echo '	
			<div class="form-group col-md-3">
			  <label>kods</label>			
			  <input type="text" class="form-control input-xs" name="addCode" placeholder="kods" value="'.$rowDet['code'].'" readonly>		  
			</div>
			
			
			<div class="form-group col-md-3">
			  <label>nosaukums</label>			
			  <input type="text" class="form-control input-xs" name="addName" placeholder="nosaukums" value="'.$rowDet['name1'].'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')" required '.$disabled.'>			  
			</div>			
			';
			
			echo '
			<div class="form-group col-md-3">
			<label for="addUom">pamat mērvienība</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addUom" id="addUom"  data-live-search="true" title="pamat mērvienība" '.$disabled.'>';
			  $selectType = mysqli_query($conn, "SELECT code FROM unit_of_measurement WHERE status='1'") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectType)){
				  echo '<option value="'.$rowt['code'].'"';
				  if($rowt['code']==$rowDet['unitOfMeasurement']){echo ' selected';}
				  echo '>'.$rowt['code'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
			
			echo '
			<div class="form-group col-md-3">
			<label for="helpUom">palīg mērvienība</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="helpUom" id="helpUom"  data-live-search="true" title="palīg mērvienība" '.$disabled.'>';
			  $selectHelpUom = mysqli_query($conn, "SELECT code FROM unit_of_measurement WHERE status='1'") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectHelpUom)){
				  echo '<option value="'.$rowt['code'].'"';
				  if($rowt['code']==$rowDet['assistantUmo']){echo ' selected';}
				  echo '>'.$rowt['code'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';			
			
			echo '

			<div class="clearfix"></div>

			
			<div class="form-group col-md-3">
			  <label>svītrkods</label>			
			  <input type="text" class="form-control input-xs" name="addBarcode" placeholder="svītrkods" value="'.$rowDet['barCode'].'" '.$disabled.'>		  
			</div>
	

			<div class="form-group col-md-3">
			<label for="addUom">projekts</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addProject" id="addProject"  data-live-search="true" title="projekts" '.$disabled.'>';
			  $selectProject = mysqli_query($conn, "SELECT Code, Name FROM n_projects") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectProject)){
				  echo '<option value="'.$rowt['Code'].'"';
				  	if($rowDet['project_code']==$rowt['Code']){ echo 'selected';}
				  echo ' >'.$rowt['Code'].' ('.$rowt['Name'].')</option>';
			  }
			  
			  echo '
			  </select>	
			</div>
			
			<div class="form-group col-md-3">										
				<div class="checkbox">
					<label><input type="checkbox" id="noSerial" name="noSerial" '; if($rowDet['no_serial']==1){echo 'checked';} echo '>seriālais nr. nav obligāts</label>
				</div>
			</div> 				
			
			<div class="clearfix"></div><br>';
			
	if($p_edit=='on'){		
			echo '
			
			<button type="submit" id="submitbtn" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button> 			
		</form>';		





echo '<hr>';
echo '<p style="display:inline-block;">pievienot papildus mērvienību</p>';	
	
		
		echo '
		
		<form id="add_additional">';

			if($edituom){

			  $selectEuom = mysqli_query($conn, "SELECT id, uom, amount, weight, for_extra_uom, convert_from FROM additional_uom WHERE id='".intval($edituom)."'") or die(mysqli_error($conn));
			  $selUom = mysqli_fetch_array($selectEuom);			
				
			}
			
			echo '
			<div class="form-group col-md-1">
				<div class="checkbox">
					<label><input type="checkbox" name="forExtraUom"'; if($selUom['for_extra_uom']==1){echo ' checked';} echo '> palīg mērvienībai</label>
				</div>				
			</div>			
			';


			echo '
			<div class="form-group col-md-3">
			<label for="addUomAdd">papildus mērvienība</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addUomAdd" id="addUomAdd"  data-live-search="true" title="papildus mērvienība">';
			  
			  if($edituom){
				  echo '<option value="'.$selUom['uom'].'" selected>'.$selUom['uom'].'</option>';
			  }			  
			  
			  $selectType = mysqli_query($conn, "				
				
				SELECT a.code

				FROM unit_of_measurement AS a
				WHERE (
							(  
								a.code!='KG' AND 
								NOT EXISTS(
										SELECT NULL 
										FROM additional_uom AS b 
										WHERE a.code = b.uom 
										AND b.productNr='".$rowDet['code']."' 
										AND b.status='1'	
								) 
									  
							) OR (
								  
									  a.code='KG' AND (SELECT COUNT(c1.uom) FROM additional_uom AS c1 WHERE c1.productNr='".$rowDet['code']."' AND c1.status='1' AND c1.uom='KG') < 2
								  
								  
							)
						)	  
				AND a.status='1' 


			  
			  ") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectType)){

				  echo '<option value="'.$rowt['code'].'"';
				  if($edituom){ if($rowt['code']==$selUom['uom']){echo ' selected';} }
				  echo '>'.$rowt['code'].'</option>';
				  
			  }
			  
			  echo '
			  </select>	
			</div>';			

			echo '
			<div class="form-group col-md-3">
			  <label>skaits pamat mērvienībā</label>			
			  <input type="text" class="form-control input-xs" name="addCodeAdd" placeholder="skaits pamat mērvienībā" '; if($edituom){ echo ' value="'.floatval($selUom['amount']).'"';}  echo '>		  
			</div>';			


			$types=array("10" => "bruto (kg)", "20" => "neto (kg)", "30" => "apjoms (m3)", "50" => "vietu skaits");

			echo '
			<div class="form-group col-md-3">
			<label for="addWeight">tips</label>
			  <select class="form-control selectpicker btn-group-xs form-xs"  name="addWeight" id="addWeight"  data-live-search="true" title="tips">
				  <option></option>';
				  
				foreach($types AS $type=>$name){

					if($edituom){
						$checkFreeType = mysqli_query($conn, "SELECT id FROM additional_uom WHERE productNr='".mysqli_real_escape_string($conn, $id)."' AND weight='".$type."' AND id!='".$selUom['id']."' AND status='1'");
						$cFTrow = mysqli_num_rows($checkFreeType);

						if($cFTrow==0){
							echo '<option value="'.$type.'"'; if($selUom['weight']==$type){ echo ' selected';}  echo '>'.$name.'</option>';
						}
						
					}else{
						$checkFreeType = mysqli_query($conn, "SELECT id FROM additional_uom WHERE productNr='".mysqli_real_escape_string($conn, $id)."' AND weight='".$type."' AND status='1'");
						$cFTrow = mysqli_num_rows($checkFreeType);

						if($cFTrow==0){
							echo '<option value="'.$type.'">'.$name.'</option>';
						}
						
					}
					
				}  

				echo '
			  </select>	
			</div>';			

			
			if(($selUom['weight']==10 || $selUom['weight']==20 || $selUom['weight']==30) || (!$edituom)){
				echo '
				<div class="form-group col-md-2">
					<div class="checkbox">
						<label><input type="checkbox" name="convertFrom"'; if($selUom['convert_from']==1){echo ' checked';} echo '> lietot rēķinā</label>
					</div>				
				</div>';
			}			
						
			echo '
			  <input type="hidden" name="productCodeAdd" value="'.$id.'">		  			
			';
			
			echo '<div class="clearfix"></div><br>';
			if(!$edituom){
				echo '<button type="submit" id="additionalbtn" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>';
			}else{
				echo '<input type="hidden" name="uuom" value="'.$edituom.'">';
				echo '<button type="submit" id="additionalbtn" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';
			}
		echo '
		</form>';	
	}
if($id){		
	
	$rec_limit_uom = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to_uom = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query_uom = "SELECT * FROM additional_uom WHERE productNr='".mysqli_real_escape_string($conn, $id)."' AND base='0' AND status='1'";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu_uom, $result_all, $resultGL7_uom) = pageing_menu($offset, $rec_limit_uom, $max_pages, $link_to_uom, $page, $conn, $query_uom);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu_uom;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7_uom = mysqli_num_rows($resultGL7_uom);  //IEGŪST SKAITU 	
	
		if ($count_GL7_uom!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>palīg mērvienībai</th>
						<th>papildus mērvienība</th>
						<th>skaits pamat mērvienībā</th>
						<th>tips</th>
						<th>lietot rēķinā</th>
						<th>pēdējās aktivitātes veica</th>';

						if($p_edit=='on'){ echo '<th>darbība</th>';}

						echo '	
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7_uom)){

				?>
				<tr <?php if($p_edit=='on'){ ?> class="classlistedit" onclick="uomDoc('<?=$row['id'];?>')" <?php } ?> >
				<?php
				echo '

							<td>';

								if($row['for_extra_uom']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}

							echo '
							<td>'.$row['uom'].'
							<td>'.floatval($row['amount']);


							echo '<td>';

							if($row['weight']==10){
								echo 'bruto (kg)';
							}
							if($row['weight']==20){
								echo 'neto (kg)';
							}
							if($row['weight']==30){
								echo 'apjoms (m3)';
							}
					
							if($row['weight']==50){
								echo 'vietu skaits';
							}																				

							
						echo '<td>';	
						
							if($row['weight']==10 && $row['convert_from']==1){
								echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';
							}
							if($row['weight']==20 && $row['convert_from']==1){
								echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';
							}
							if($row['weight']==30 && $row['convert_from']==1){
								echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';
							}						
							

							echo '<td>';
							
							if($row['editedBy']>0){
								echo returnMeWho($row['editedBy']).' ('.date('d.m.Y H:i:s', strtotime($row['editedDate'])).')';
							}else{
								echo returnMeWho($row['createdBy']).' ('.date('d.m.Y H:i:s', strtotime($row['createdDate'])).')';
							}							
							
							
						if($p_edit=='on'){	
							echo '
							<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" data-ed="<?=$row['id'];?>" id="uomdel<?=$row['id'];?>" onclick="euomDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" data-er="<?=$row['id'];?>" id="uomret<?=$row['id'];?>" onclick="euomRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';
			
		}else{
			echo '<br><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav pievienota neviena papildus mērvienība!';
		}	
	
}	
echo '<hr>';	
	



	
?>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("search.php", {name:value},function(data){
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
	
echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div><div class="clearfix"></div>'; 					
	
echo '<div id="results">';					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM n_items ORDER BY status DESC, code ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>
						<th>pamat mērvienība</th>
						<th>palīg mērvienība</th>
						
						<th>svītrkods</th>
						<th>nosaukums</th>
						<th>projekts</th>
						<th>seriālais nr. nav obligāts</th>
						<th>pēdējās aktivitātes veica</th>';
						if($p_edit=='on'){ echo '<th>darbība</th>'; }

						echo '
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				?>
				<tr class="classlistedit" onclick="newDoc('<?=$row['code'];?>')">
				<?php
				echo '

							<td>'.$row['code'].'
							<td>'.$row['unitOfMeasurement'];
							echo '<td>'.$row['assistantUmo'];

							echo '
							<td>'.$row['barCode'].'
							<td>'.$row['name1'];

							echo '
							<td>'.$row['project_code'];
							if($row['project_name']!=''){echo ' ('.$row['project_name'].')';}
							
							echo '<td>';
							if($row['no_serial']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}

							echo '<td>';
							
							if($row['editedBy']>0){
								echo returnMeWho($row['editedBy']).' ('.date('d.m.Y H:i:s', strtotime($row['editedDate'])).')';
							}else{
								echo returnMeWho($row['createdBy']).' ('.date('d.m.Y H:i:s', strtotime($row['createdDate'])).')';
							}

						if($p_edit=='on'){
							echo '<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" data-pd="<?=$row['code'];?>" id="pdel<?=$row['code'];?>" onclick="productDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" data-pr="<?=$row['code'];?>" id="pret<?=$row['code'];?>" onclick="productRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';
			mysqli_close($conn);
		}	
	echo '</div>';
}
?>

</div>

<script>
setTimeout(function() {
    $('#hideMessage').fadeOut('fast');
}, 3000);
</script>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>


<script>
$(document).ready(function() {
	$('form').on('keyup change', 'input, select, textarea', function(){

		$("#submitbtn").css("background","#F1F103");
		
	});
});

$(document).on('dp.change', 'input', function(e) {
	var shown = $(this).attr('dp.shown');
	if (typeof shown != 'undefined' && shown && e.date != e.oldDate)
	$("#submitbtn").css("background","#F1F103");
});

$(document).on('dp.show', 'input', function(e) {
	$(this).attr('dp.shown', 1);
});

</script>
