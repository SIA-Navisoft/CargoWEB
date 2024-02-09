<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="agreements";


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

                <script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#agreements').load('/pages/agreements/agreements.php?aStatus=off');
    });
})
</script>

<?php if($p_edit=='on'){ ?>
	<script>
	$(document).ready(function(){
		$('.classadd').click(function(){
			$('#agreements').load('/pages/agreements/add.php?aStatus=off');
		});
	})
	</script>
<?php } ?>

<script>
function newDoc(val) {
	var val = encodeURI(val);
	$('#agreements').load('/pages/agreements/agreements.php?view=edit<?=$elpage;?>&aStatus=off&id='+val+'');
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
//skats uz ierakstiem
if (!$view){

	require('inc/s.php');

	echo '<div id="agreements">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
            <a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>';
            if($p_edit=='on'){echo '<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="pievienot"></i></a>';}
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
    var search = $.post("/pages/agreements/search.php?search=agr", {name:value, id:tid, line:lid},function(data){
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





    <div class="col-sm-3 col-md-6 col-lg-4">



    </div>
    <div class="col-sm-9 col-md-6 col-lg-8">
 


    </div>


<?php	

echo '<div class="col-sm-3 col-md-6 col-lg-3" style="border-right: 1px solid #eee;">';

echo '

<div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;">
	<input type="text" id="searchWait" class="form-control input-xs" onkeyup="searchInAgr(this.value)" placeholder="meklēt" value="'.$s.'">
</div>

<div style="display: inline-block; float: right;">';

    $sOff = "'on'";
    echo '
    <a class="btn btn-default btn-xs" onclick="changeStatus('.$sOff.')">neaktīvie</a>
    
</div>


';

echo '<div id="sAgr">';	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM agreements WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<'20' AND deleted='0' ORDER BY customerNr DESC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo '<div style="display: inline-block;">'.$page_menu.'</div>';			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><tbody>';   

			while($row = mysqli_fetch_array($resultGL7)){
				?>
				<tr onclick="newDoc('<?=$row['contractNr'];?>')">
				<?php
				echo '	

							<td>'.$row['contractNr'].'
							<td>'.$row['customerNr'].' ('.$row['customerName'].')

						</tr>';
			}
			
			echo '</tbody></table></div></div>';
			mysqli_close($conn);
		}
}
echo '</div>';


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

?>

</div>
</div>
</div>
</div>
</div>
</div>


<?php include("footer.php"); ?>