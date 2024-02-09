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

$page_file="resources";

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
        $('#stock').load('/pages/stock/stock.php<?=$glpage;?>&aStatus=<?=$aStatus;?>');
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

<?php if($p_edit=='on'){ ?>
<script>
$(document).ready(function () {
	$('#editResource').on('submit', function(e) {
			e.preventDefault();
	
			$.ajax({
					url : '/pages/stock/post.php?r=editResource',
					type: "POST",
					data: $(this).serializeArray(),
					beforeSend: function(){
						$('#savebtn').html("gaidiet...");
						$("#savebtn").prop("disabled",true);
					},			
					success: function (data) {
							console.log(data);
							$('#stock').load('/pages/stock/resources.php?res=done');
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


	require('../../inc/s.php');

	echo '<div id="stock">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default active classresource" ><i class="glyphicon glyphicon-wrench" style="color: #00B5AD" title="resursu katalogs"></i></a>
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
			  	  
		echo '</div>';
		
		echo '<p style="display:inline-block;">resursu katalogs</p>';
        
?>

<script type="text/javascript">

function searchInAgr(value) {

    var search = $.post("/pages/stock/search.php?doSearch=resource", {name:value},function(data){
        $("#sResource").html(data);		
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


echo '
<div style="float: right; display: inline-block;" >
	<div id="showWait" style="display: inline-block;"></div>
	<div style="display: inline-block;">
	<input type="text" id="searchWait" class="form-control input-xs" onkeyup="searchInAgr(this.value)" placeholder="meklēt" value="'.$s.'">
	</div>
</div>';

echo '<div id="sResource">';	




	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "
		SELECT r.id, r.name, rd.from, rd.to 
		FROM n_resource AS r
		LEFT JOIN resource_data AS rd
		ON r.id=rd.resource_id
		";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo '<div style="display: inline-block;">'.$page_menu.'</div>';			
	
	$count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			if($p_edit=='on'){

				echo '<form id="editResource">';
			
			}

			echo '
							<div class="table-responsive">
									<table class="table table-hover table-responsive">
									<thead>
										<tr>
											<th>resurs
											<th>no
											<th>uz
										</tr>
									</thead>
									<tbody>';   
			$r=0;
			while($row = mysqli_fetch_array($resultGL7)){
			$r++;


				echo '
				<input type="hidden" name="resource_id[]" value="'.$row['id'].'">
				<tr>				

							<td>'.$row['id'].' ('.$row['name'].')
							<td>';

						if($p_edit=='on'){

							echo '
							<select class="form-control selectpicker btn-group-xs" name="from[]" data-live-search="true" title="no">';
							echo '<option value=""> </option>';
							$selectTypeT = mysqli_query($conn, "
							SELECT id, transport FROM transport
							WHERE status=1 ORDER BY transport") or die(mysqli_error($conn));
							while($rowt = mysqli_fetch_array($selectTypeT)){
								echo '<option value="'.$rowt['id'].'"';
								if($rowt['id']==$row['from']){ echo ' selected';}
							 echo ' >'.$rowt['transport'].'</option>';
							}
							
							echo '
							</select>';

						}else{

							$selectTypeT = mysqli_query($conn, "SELECT transport FROM transport WHERE id='".$row['from']."'") or die(mysqli_error($conn));
							$rowt = mysqli_fetch_array($selectTypeT);
							echo $rowt['transport'];

						}

							echo '<td>';

						if($p_edit=='on'){
							
							echo '
							<select class="form-control selectpicker btn-group-xs" name="to[]" data-live-search="true" title="uz">';
							echo '<option value=""> </option>';
							$selectTypeT = mysqli_query($conn, "
							SELECT id, transport FROM transport
							WHERE status=1 ORDER BY transport") or die(mysqli_error($conn));
							while($rowt = mysqli_fetch_array($selectTypeT)){
								echo '<option value="'.$rowt['id'].'"';
								if($rowt['id']==$row['to']){ echo ' selected';}
							 echo ' >'.$rowt['transport'].'</option>';
							}
							
							echo '
							</select>';

						}else{

							$selectTypeT = mysqli_query($conn, "SELECT transport FROM transport WHERE id='".$row['to']."'") or die(mysqli_error($conn));
							$rowt = mysqli_fetch_array($selectTypeT);
							echo $rowt['transport'];

						}

							echo '	

						</tr>';
			}
			echo '<input type="hidden" name="result" value="'.$r.'">';
			echo '</tbody></table></div>';
				
			if($p_edit=='on'){

			echo '
			<button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>

			</form>';

			}

			echo '
			</div>';
			mysqli_close($conn);
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

