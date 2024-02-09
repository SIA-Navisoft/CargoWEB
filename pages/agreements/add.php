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

if($p_edit!='on'){ echo "<script type=\"text/javascript\">window.location.href='../../welcome';</script>"; die(0); } //header("Location: ../../welcome");die(0);}

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

<script>
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#agreements').load('/pages/agreements/add.php?aStatus=<?=$aStatus;?>&aStatus=<?=$aStatus;?>');
    });
})
</script>

<script>
function newDoc(val) {
	var val = encodeURI(val);
	$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id='+val+'');
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
	
	$idR=findLastRow("agreements"); 

	require('../../inc/s.php');
	$resultIdr = mysqli_query($conn, "SELECT contractNr FROM agreements WHERE id='".intval($idR)."'") or die(mysqli_error($conn));
	$rowIdr = mysqli_fetch_array($resultIdr);
	$idR = $rowIdr['contractNr'];

?>
<script>
$(document).ready(function () {
    $('#addAgreement').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({						
			url : '/pages/agreements/post.php?r=addAgreement',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {

				var productNr = $("#productNr").val();
				var amount = $("#amount").val();
				$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=<?=$aStatus;?>&id='+data);
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>

<?php
//skats uz ierakstiem


	require('../../inc/s.php');

	echo '<div id="agreements">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default active classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="pievienot"></i></a>
			</div>';
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

	if($aStatus=='on'){$eQue = " WHERE (dateTo!='0000-00-00 00:00:00' OR dateTo IS NOT NULL) AND dateTo<CURDATE() AND deleted='0'";}else{$eQue = " WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND deleted='0'";}

	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?aStatus='.$aStatus.'&page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM agreements ".$eQue." ORDER BY customerNr DESC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo '<div style="display: inline-block;">'.$page_menu.'</div>'; //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><tbody>';   

			while($row = mysqli_fetch_array($resultGL7)){
				
				if($row['contractNr']==$id){$bg = ' style="background-color: #337ab7; color: white;"';}else{$bg = null;}
				?>
				<tr onclick="newDoc('<?=$row['contractNr'];?>')" <?=$bg;?>>
				<?php
				echo '	

							<td>'.$row['contractNr'].'
							<td>'.$row['customerNr'].'

						</tr>';
			}
			
			echo '</tbody></table></div></div>';
			mysqli_close($conn);
		}

echo '</div>';

if (!$view){
	require('../../inc/s.php');
echo '
<div class="col-sm-9 col-md-6 col-lg-9"> 
 
    <div class="panel panel-default"> 
        <div class="panel-body"> 

					<form id="addAgreement">
							<div class="form-row">
								
									<div class="form-group col-md-3">
									<label for="customer">klients</label>
										<select class="form-control selectpicker btn-group-xs"  id="customer" name="customer"  data-live-search="true" title="klients">';
										
										$selectCustomers = mysqli_query($conn, "SELECT Code, Name FROM n_customers") or die(mysqli_error($conn));
										while($rowc = mysqli_fetch_array($selectCustomers)){
											echo '<option  value="'.$rowc['Code'].'">'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
										}
										
										echo '
										</select>	
									</div>	
							
									<div class="form-group col-md-3">
										<label for="outerNr">ārējais līg. nr.</label>
										<input type="text" class="form-control" id="outerNr" name="outerNr" placeholder="ārējais līg. nr.">
									</div>
						

						
									<div class="form-group col-md-3">
									<label for="dateFrom">no</label>
									<input type="text" class="form-control datepicker" id="dateFrom" name="dateFrom">
									</div>

									<div class="form-group col-md-3">
										<label for="dateTo">līdz</label>
										<input type="text" class="form-control datepicker" id="dateTo" name="dateTo">
									</div>	
									<div class="clearfix"></div>
									<div class="form-group col-md-3">
										<label for="freeDays">bezmaksas glabāšana</label>
										<input type="text" class="form-control" id="freeDays" name="freeDays" placeholder="dienu skaits">
									</div>

									
									<div class="form-group col-md-3">
									<label for="actForm">akta forma</label>
										<select class="form-control selectpicker btn-group-xs"  id="actForm" name="actForm"  data-live-search="true" title="akta forma">
										
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
										
										</select>	
									</div>									
									
									
									<div class="form-group col-md-3">
										
										<div class="checkbox">
											<label><input type="checkbox" id="forRelease" name="forRelease"> aprēķināt glabāšanu visā posmā</label>
										</div>
									</div>	

									<div class="form-group col-md-3">
										
										<div class="checkbox">
											<label><input type="checkbox" id="useScan" name="useScan"> lietot skenēšanā</label>
										</div>
									</div>																			

							</div>
							<div class="clearfix"></div>
							<button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>
					</form>

        </div>
    </div>

</div>
';
}
?>

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
<?php include_once("../../datepicker.php"); ?>
