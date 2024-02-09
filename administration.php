<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="administration";
$page_header="portāla uzstādījumi";
$page_icon="basic settings";

include('functions/base.php');

$view = $action = $section = $id = $query = null;
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['erz'])){$erz = htmlentities($_GET['erz'], ENT_QUOTES, "UTF-8");}	

// ADD, EDIT or DELETE
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["source"])){$source = null;}else{$source = htmlentities($_POST['source'], ENT_QUOTES, "UTF-8");}
	
	if ($source=='edit') {
		$password_expire_range = htmlentities($_POST['password_expire_range'], ENT_QUOTES, "UTF-8");
		$last_passwords = htmlentities($_POST['last_passwords'], ENT_QUOTES, "UTF-8");
		$password_expire = htmlentities($_POST['password_expire'], ENT_QUOTES, "UTF-8");
		if($password_expire!='on'){$password_expire = 0;}else{$password_expire = 1;}

		
		$password_expire_range = intval($password_expire_range);
		$last_passwords = intval($last_passwords);
		$password_expire = intval($password_expire);		
		
		require('inc/s.php');
		mysqli_query($conn, "UPDATE global_settings SET password_expire='$password_expire', password_expire_range='$password_expire_range', last_passwords='$last_passwords'");
		mysqli_close($conn);
	
		header("Location: ".$page_file."?res=done");
		die(0); 
	}
	
	if($source=='editMail'){
		$email_host = htmlentities($_POST['email_host'], ENT_QUOTES, "UTF-8");
		$email_port = htmlentities($_POST['email_port'], ENT_QUOTES, "UTF-8");
		$host_username = htmlentities($_POST['host_username'], ENT_QUOTES, "UTF-8");
		$host_password = htmlentities($_POST['host_password'], ENT_QUOTES, "UTF-8");


		require('inc/s.php');
		$email_host = mysqli_real_escape_string($conn, $email_host);
		$email_port = mysqli_real_escape_string($conn, $email_port);
		$host_username = mysqli_real_escape_string($conn, $host_username);
		$host_password = mysqli_real_escape_string($conn, $host_password);		
		
		
		mysqli_query($conn, "UPDATE global_settings SET email_host='$email_host', email_port='$email_port', host_username='$host_username', host_password='$host_password'");
		mysqli_close($conn);
	
		header("Location: ".$page_file."?section=2&res=done");
		die(0); 		
	}
}
	


include('header.php');
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

<?php

//datu labošana

	//pamatdatu labošana
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT * FROM global_settings");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		mysqli_close($conn);
		if(!$section){
	?>
		<form class="form-horizontal" name="edit" method="POST" action="">
		<input type='hidden' name='source' value='edit'/>
			
			
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
								<a class="btn btn-default" href="<?=$page_file;?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD" title="parole"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=2"><i class="glyphicon glyphicon-lock" style="color: #00B5AD"  title="epasts"></i></a> 
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
							?>
						
						</div>				
					
						<h4>PORTĀLA UZSTĀDĪJUMI</h4>
						
						<span class="button-checkbox">
							<button type="button" class="btn" data-color="success">paroles termiņš (izslēgts/ieslēgts)</button>
							<input type="checkbox" class="hidden"  name="password_expire"
									<?php
									require('inc/s.php');
									if(global_settings($conn, 'password_expire')==1){echo ' checked="checked"';}
									mysqli_close($conn);
									?>								
							>
						</span>																					
													
						<div class="form-group">
							<div class="col-md-3">
								<small>paroles termiņš (mēnešos)</small>
								<input type="text" class="form-control" name="password_expire_range" value="<?=$row['password_expire_range']?>">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-3">
								<small>paroles nedrīks atkārtoties</small>
								<input type="text" class="form-control" name="last_passwords" value="<?=$row['last_passwords']?>">
							</div>
						</div>
						
							
												
						<a class="btn btn-default btn-sm" aria-label="Left Align" href="welcome" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>

						<a class="btn btn-default btn-sm" aria-label="Left Align" href="javascript:document.edit.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
						</a>					
			
			
		</form>	
<?php
}
if($section==2){
?>
		<form class="form-horizontal" name="editMail" method="POST" action="">
		<input type='hidden' name='source' value='editMail'/>			
					
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
								<a class="btn btn-default" href="<?=$page_file;?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD" title="parole"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=2"><i class="glyphicon glyphicon-lock" style="color: #00B5AD"  title="epasts"></i></a> 
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
							?>
						
						</div>	
						<h4>ĒPASTA UZSTĀDĪJUMI</h4>							

						<div class="form-group">
							<div class="col-md-3">
								<small>hosts</small>
								<input type="text" class="form-control" name="email_host" value="<?=$row['email_host']?>">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-3">
								<small>ports</small>
								<input type="text" class="form-control" name="email_port" value="<?=$row['email_port']?>">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-3">
								<small>lietotājs</small>
								<input type="text" class="form-control" name="host_username" value="<?=$row['host_username']?>">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-3">
								<small>parole</small>
								<input type="text" class="form-control" name="host_password" value="<?=$row['host_password']?>">
							</div>
						</div>						
													
						<a class="btn btn-default btn-sm" aria-label="Left Align" href="welcome" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>

						<a class="btn btn-default btn-sm" aria-label="Left Align" href="javascript:document.editMail.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
						</a>
							
		</form>	
<?php		
}
?>

</div>
</div>
</div>
</div>
</div>	


<script>
var fileExtentionRange = '.jpg .jpeg .png .gif';

$(document).on('change', '.btn-file :file', function() {
    var input = $(this);

    if (navigator.appVersion.indexOf("MSIE") != -1) { // IE
        var label = input.val();

        input.trigger('fileselect', [ 1, label, 0 ]);
    } else {
        var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        var numFiles = input.get(0).files ? input.get(0).files.length : 1;
        var size = input.get(0).files[0].size;

        input.trigger('fileselect', [ numFiles, label, size ]);
    }
});

$('.btn-file :file').on('fileselect', function(event, numFiles, label, size) {
    $('#capture').attr('name', 'capture'); // allow upload.

    var postfix = label.substr(label.lastIndexOf('.'));
    if (fileExtentionRange.indexOf(postfix.toLowerCase()) > -1) {
        $('#_attachmentName').val(label);
    } else {
        alert('allowed file type：' + fileExtentionRange + '');

        $('#capture').removeAttr('name'); // cancel upload file.
    }
});

</script>


<?php include("footer.php"); ?>