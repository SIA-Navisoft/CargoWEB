<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="stock";


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

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU
if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}

$user_group_array = array("w_user" => "NNVT", "w_partner" => "");


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}
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
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#contenthere').load('/pages/stock/add.php');
    });
})
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
function newDoc(val) {
	var val = encodeURI(val);
	$('#stock').load('/pages/stock/stock.php?view=edit<?=$elpage;?>&id='+val+'');
}
</script>

<script>
function selClient(val) {
	var val = val.value;
	$('#contenthere').load('/pages/stock/stock.php?client='+val+'');
}
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
if (!$view){

	require('inc/s.php');

	echo '<div id="stock">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>'; 
				
				echo '<a class="btn btn-default classlocation"  ><i class="glyphicon glyphicon-object-align-bottom" style="color: #00B5AD"  title="noliktavu katalogs"></i></a>
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">preču katalogs</p>';

	if($p_edit=='on'){	
?>
		<script>
		$(document).ready(function(){
			$('#addCode').bind('keyup',function(){
				$(this).val($(this).val().replace(/[^0-9a-z ]/gi, ""));
			});
		});
		</script>
<?php		
		echo '
		<form id="add_product">
			
			<div class="form-group col-md-3">
			  <label>kods</label>			
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

	echo '<div style="display: inline-block;">'.$page_menu.'</div>'; //IZVADA TABULU AR LAPĀM			
	
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
							echo '
							<td>';
						
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
			
			echo '</tbody></table></div></div>';
			mysqli_close($conn);
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