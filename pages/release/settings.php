<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


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
if (isset($_GET['do'])){$do = htmlentities($_GET['do'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}




require('../../inc/s.php');
?>


<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/release/release.php<?=$glpage;?>');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#contenthere').load('/pages/release/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classhistory').click(function(){
        $('#contenthere').load('/pages/release/release.php?view=history');
    });
})
</script>
<?php $idR=findLastRow("issuance_doc"); ?>
	<script>
	$(document).ready(function () {
		$('#issuanceDoc').on('submit', function(e) {			
			e.preventDefault();
		
			var searchWait = $('#searchWait').val();
			var searchWait = encodeURI(searchWait);

			$.ajax({
				url : '/pages/release/post.php?r=issuanceDoc',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('.actionLine').html("gaidiet...");
					$(".actionLine").prop("disabled",true);

				},			
				success: function (data) {
					var mainCargo = $('#mainCargo').val();
					
					$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$idR+1;?>');
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>

<form id="issuanceDoc">
  <div class="form-row">
  
	<?php
	
	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-sm" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
			<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>
			<a class="btn btn-default classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>
		</div>';
		if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
		if ($res=="del"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">izdzēsts!</div></div>';}
		if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
		
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';
			  
	echo '</div>';	
		
	?>
	
    <div class="form-group col-md-3">
      <label for="transport">transports</label>
      <input type="text" class="form-control" name="transport" id="transport">
    </div>		

  </div>
  
  <div class="clearfix"></div>
  <button type="submit" class="btn btn-default" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>
</form>  


  

