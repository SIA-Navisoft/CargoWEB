<?php 
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="movement";


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

$id = $edit = $view = null;
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['edit'])){$edit = htmlentities($_GET['edit'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');


if(!$view){
?>
<script>
$(document).ready(function () {
    $('#add_comment').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '/pages/receipt/post.php?r=addComment&id=<?=$id;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#comSubmit').html("gaidiet...");
				$("#comSubmit").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				var amount = $("#amount").val();
				$('#contenthere').load('/pages/receipt/receipt.php?view=edit&id=<?=$edit;?>&res=done');
				
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
		$('#showComment').modal('hide');
		$('.modal-backdrop').remove();
    });
});
</script>
<?php

$query = mysqli_query($conn, "SELECT comment FROM cargo_line WHERE id='".intval($id)."'") or die (mysqli_error($conn));
$row = mysqli_fetch_array($query);


echo '
<form id="add_comment">
	
		<label>komentārs</label> <button style="float: right;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<textarea class="form-control" rows="5" name="comment">'.$row['comment'].'</textarea>
		<br>
		<button type="submit" id="comSubmit" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>
</form>
';
}

if($view=='history'){
	
$query = mysqli_query($conn, "SELECT comment FROM cargo_line WHERE id='".intval($id)."'") or die (mysqli_error($conn));
$row = mysqli_fetch_array($query);

echo '
		<label>komentārs</label> <button style="float: right;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		<textarea class="form-control" rows="5" readonly>'.$row['comment'].'</textarea>
';
	
}
?>



				