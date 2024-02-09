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


if(!empty($_GET['m'])) {$m = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT);if(false === $m) {$m = 1;}}else{$m = 1;}  
if(!empty($_GET['c'])) {$c = filter_input(INPUT_GET, 'c', FILTER_VALIDATE_INT);if(false === $c) {$c = 1;}}else{$c = 1;}  
if(!empty($_GET['t'])) {$t = filter_input(INPUT_GET, 't', FILTER_VALIDATE_INT);if(false === $t) {$t = 1;}}else{$t = 1;}  //IEGŪSTAM LAPAS NUMURU
 
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}

if($page){$glpage = '?page='.$page;}else{$glpage = null;}
?>

	<script>
	$(document).ready(function(){
		$('.classlist').click(function(){
			$('#cargoType').load('/pages/stock/stock.php<?=$glpage;?>');
		});
	})
	
	$(document).ready(function(){
		$('.settingslang').click(function(){
			$("#pleaseWait").toggle();
			$('#cargoType').load('/pages/stock/settings.php?view=languages');
		});
	})	
	
	$(document).ready(function(){
		$('.settingsreceiver').click(function(){
			$("#pleaseWait").toggle();
			$('#cargoType').load('/pages/stock/settings.php?view=receiver');
		});
	})

	$(document).ready(function(){
		$('.settingssender').click(function(){
			$("#pleaseWait").toggle();
			$('#cargoType').load('/pages/stock/settings.php?view=sender');
		});
	})		
	</script>

	<script>
	$(function() {
		$(".paging").delegate("a", "click", function(event) {	
			var url = $(this).attr('href');
			
			$('#cargoType').load('/pages/stock/'+url);
			event.preventDefault();
		});
	});
	</script>	

<script>
$(document).ready(function(){
    $('.settingslist').click(function(){
		$("#pleaseWait").toggle();
        $('#cargoType').load('/pages/stock/settings.php');
    });
})
</script>


<?php if($p_edit=='on'){ ?>
<script>
$(document).ready(function () {
    $('#send_settings').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/stock/post.php?r=rru',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				$('#cargoType').load('/pages/stock/settings.php?res=done');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>
<?php 
} 

//skats uz ierakstiem
if($view=='sender'){
require('../../inc/s.php');

	if($id){ $form = '#edit_sender'; $formUrl = 'editSender';}else{ $form = '#add_sender'; $formUrl = 'addSender';}
?>
	
	<script>
	$(document).ready(function () {
		$('<?=$form;?>').on('submit', function(e) {
			e.preventDefault();
			
			$.ajax({
				url : '/pages/stock/post.php?r=<?=$formUrl;?>',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('#submitbtn').html("gaidiet...");
					$("#submitbtn").prop("disabled",true);

				},			
				success: function (data) {
	
					$('#cargoType').load('/pages/stock/settings.php?view=sender<?php if($id){ echo '&id='.$id; } ?>');
					
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	
	function countryDoc(val) {
		$('#cargoType').load('/pages/stock/settings.php?view=sender<?=$elpage;?>&id='+val+'');
	}	
	

	function senderDel(event) {
		event.preventDefault();
		
		var val = $(event.target).data("sdel");


		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=deleteSenderCode&id='+val+'',
			beforeSend: function(){
				$('#cdel'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cdel"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/stock/settings.php?view=sender&res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  
	  
	function senderRet(event) {
		event.preventDefault();
		
		var val = $(event.target).data("sret");

		$.ajax({
			global: false,
			type: 'GET',
			url: '/pages/stock/action.php?action=returnSenderCode&id='+val+'',
			beforeSend: function(){
				$('#cret'+val+'').html("<i class=\"glyphicon glyphicon-ban-circle\" style=\"color: red;\"></i> gaidiet...");
				$("#cret"+val+"").prop("disabled",true);
			},				
			success: function (result) {
				console.log(result);
				$('#cargoType').load('/pages/stock/settings.php?view=sender&res='+result);
			},
			error: function (request, status, error) {
				serviceError();
			}
		});
	}	  	
	
	</script>
<?

	echo '<div id="cargoType">';									
		echo '<div class="page-header" style="margin-top: -5px;">
		  
			<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
				<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
				<a class="btn btn-default settingslist" ><i class="glyphicon glyphicon-wrench" style="color: #00B5AD" title="uzstādījumi"></i></a>
				<a class="btn btn-default settingslang" ><i class="glyphicon glyphicon-globe" style="color: #00B5AD" title="valodu uzstādījumi"></i></a>
				<a class="btn btn-default settingsreceiver" ><i class="glyphicon glyphicon-road" style="color: #00B5AD" title="saņēmēju uzstādījumi"></i></a>
				<a class="btn btn-default active settingssender" ><i class="glyphicon glyphicon-road icon-flipped" style="color: #00B5AD" title="nosūtītāju uzstādījumi"></i></a>
			</div>';
			if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
			
			if ($res=='added'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">pievienots!</div></div>';}
			
			
			if ($res=='error' ||  $res=='productCodeExists' || $res=='productCodeDoesNotExists'){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
			  
					
			if ($res=="restored"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">atjaunots!</div></div>';}
			if ($res=="deleted"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">dzēsts!</div></div>';}
			  	  
		echo '</div>';
		
		$cantEdit = null;
		echo '<p>nosūtītāju katalogs ';
			if($id){echo '<b>'.$id.'</b> labošana'; $cantEdit = ' readonly';}
		echo '</p>';


		$code = mysqli_real_escape_string($conn, $id);
		$getDetails = mysqli_query($conn, "SELECT Code, name FROM senders WHERE Code='".$code."'");
		$rowDet = mysqli_fetch_array($getDetails);
		
	if($p_edit=='on'){	

		if($id){
			echo '<form id="edit_sender">';
		}else{
			echo '<form id="add_sender">';
		}
			
			
			echo '
			<div class="form-group col-md-3">
			  <label>nosūtītāja kods</label>			
			  <input type="text" class="form-control input-xs" name="addCode" placeholder="nosūtītāja kods" value="'.$rowDet['Code'].'" '.$cantEdit.'>		  
			</div>
			<div class="form-group col-md-3">
			  <label>nosūtītāja nosaukums</label>			
			  <input type="text" class="form-control input-xs" name="addName" placeholder="nosūtītāja nosaukums" value="'.$rowDet['name'].'" oninvalid="this.setCustomValidity(\'šim laukam jābūt aizpildītam\')" oninput="setCustomValidity(\'\')">		  
			</div>

	
			<div class="clearfix"></div><br>
			<button type="submit" id="submitbtn" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button> 			
		</form>';

	}

					
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM senders ORDER BY status DESC, Code ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menuz, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menuz;   //IZVADA TABULU AR LAPĀM			
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 	
	
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>saņēmēja kods</th>
						<th>saņēmēja nosaukums</th>';

						if($p_edit=='on'){ echo '<th>darbība</th>'; }

					echo '	
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){

				?>
				<tr <?php if($p_edit=='on'){ ?> onclick="countryDoc('<?=$row['Code'];?>')" <?php } ?> >
				<?php
				echo '

							<td>'.$row['Code'].'
							<td>'.$row['name'];


						if($p_edit=='on'){	
							echo '<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" data-sdel="<?=$row['Code'];?>" id="sdel<?=$row['Code'];?>" onclick="senderDel(event)"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" data-sret="<?=$row['Code'];?>" id="sret<?=$row['Code'];?>" onclick="senderRet(event)"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}	
						}	
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';		

		
	echo '</div>';	
	
		}

	
}


mysqli_close($conn);	
?>

</div>



<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<script>
$(document).ready(function() {
	$('form').on('keyup change', 'input, select, textarea', function(){

		$("#savebtn").css("background","#F1F103");
	
	});
});

$(document).on('dp.change', 'input', function(e) {
	var shown = $(this).attr('dp.shown');
	if (typeof shown != 'undefined' && shown && e.date != e.oldDate)
	$("#savebtn").css("background","#F1F103");
});

$(document).on('dp.show', 'input', function(e) {
	$(this).attr('dp.shown', 1);
});

</script>