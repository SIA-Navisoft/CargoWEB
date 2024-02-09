<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="settings";
$page_header="lietotāja uzstādījumi";
$page_icon="basic settings";
$page_table="user";

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

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
		$full_name = htmlentities($_POST['full_name'], ENT_QUOTES, "UTF-8");
		$phone = htmlentities($_POST['phone'], ENT_QUOTES, "UTF-8");
		
		require('inc/s.php');
		
		$form_data = array(
		'full_name' => $full_name,
		'phone' => $phone,
		'editedBy' => $myid,
		'editedDate' => $currenttime
			
		);
		updateSomeRow("user", $form_data, "WHERE id = '$myid'");			
		
		mysqli_close($conn);
	
		header("Location: ".$page_file."?res=done");
		die(0); 
	}
	
	if ($source=='editPassword') {
		$currentPassword = htmlentities($_POST['currentPassword'], ENT_QUOTES, "UTF-8");
		$newPassword = htmlentities($_POST['newPassword'], ENT_QUOTES, "UTF-8");
		$newPassword2 = htmlentities($_POST['newPassword2'], ENT_QUOTES, "UTF-8");

	if(isset($_POST['newPassword'])){	
		$pass = $_POST['newPassword'];

		$warning_msg = $errortext = $errorcounter = $erz = null;
		if(!preg_match('/[A-Z]/', $pass)){$erz .= 1 .','; $errortext.="<li>neviens burts nav lielais";$errorcounter++;}
		if(!preg_match('/[a-z]/', $pass)){$erz .= 2 .','; $errortext.="<li>neviens burts nav mazais";$errorcounter++;}
		if(!preg_match('/[0-9]/', $pass)){$erz .= 3 .','; $errortext.="<li>nav neviens cipars";$errorcounter++;}
		if(!preg_match('/[^0-9a-zA-z]/', $pass)){$erz .= 4 .','; $errortext.="<li>nav neviens simbols";$errorcounter++;}
		if(strlen($pass)<8){$erz .= 5 .','; $errortext.="<li>parolei ir jābūt vismaz no 8 simboliem";}
		if (($errorcounter>0)||(strlen($pass)<8)){
			$warning_msg="Parole netiks akceptēta!<ul>".$errortext."</ul>";
			echo '<br><br><br>';
			echo $warning_msg;
			header("Location: ".$page_file."?section=2&erz=".$erz."");
			die(0);
		}

	}		
		
		if ((!$newPassword)||(!$newPassword2)||(!$currentPassword)){
			header("Location: ".$page_file."?section=2&res=error1");
			die(0);
		}
		
		if ($newPassword!==$newPassword2){
			header("Location: ".$page_file."?section=2&res=error2");
			die(0);
		}
		
		require('inc/s.php');
		
		$result = mysqli_query($conn,"SELECT longpass FROM user WHERE email='".$myemail."'");
		if (!$result){die("Query to show fields failed");}
		$countrows = mysqli_num_rows($result);
		if(($count_r!="1")||($r['expire']!=='n')){
			header("Location: restriction");
			die(0);
		}else{
			
			
			
			
			
			
			$r = mysqli_fetch_assoc($result);
			$salt = substr($r['longpass'], 0, 64);
			$hash = $salt . $currentPassword;
			for ( $i = 0; $i < 100000; $i ++ ) {
			  $hash = hash('sha256', $hash);
			}
			$hash = $salt . $hash;
 			
			if (($hash == $r['longpass'])&&($newPassword===$newPassword2)){
				
				$salt = hash('sha256', uniqid(mt_rand(), true) . 'mainīgais' . strtolower($myemail));
				$hash = $salt . $newPassword;
				for ( $i = 0; $i < 100000; $i ++ ) {$hash = hash('sha256', $hash);}
				$hash = $salt . $hash;
				


		
		$error = null;
		if(global_settings($conn, 'password_expire')==1){	
			// PĀRBAUDA VAI PAROLE NAV STARP PĒDĒJĀM xx LIETOTAJĀM
			$result_p = mysqli_query($conn,"SELECT * FROM user_passwords_logs WHERE user='".$myemail."' ORDER BY id DESC LIMIT ".global_settings($conn, 'last_passwords')."") or die(mysqli_error($conn));			
			if(mysqli_num_rows($result_p)>0){
				while($r_p = mysqli_fetch_array($result_p)){

						$salt_e = substr($r_p['password'], 0, 64);
						$hash_e = $salt_e . $newPassword;
						for ( $i = 0; $i < 100000; $i ++ ) {
						  $hash_e = hash('sha256', $hash_e);
						}
						$hash_e = $salt_e . $hash_e;
						
						if($r_p['password']==$hash_e){
							$error = 6;
							//'Parole nedrīkst sakrist ar pēdējām trim parolēm!<br>';
						}
						if($r['longpass']==$hash){
							$error = 6;
						}						
						
				}
			}	
			
			$error_first_time = null;
			if(mysqli_num_rows($result_p)==0){

				$old = mysqli_query($conn,"SELECT longpass FROM user WHERE email='".$myemail."'");
				$r_o = mysqli_fetch_assoc($old);
				
				$salt_o = substr($r_o['longpass'], 0, 64);
				$hash_o = $salt_o . $newPassword;
				for ( $i = 0; $i < 100000; $i ++ ) {
					$hash_o = hash('sha256', $hash_o);
				}
				$hash_o = $salt_o . $hash_o;	
			
				if($r_o['longpass']==$hash_o){
					$error_first_time = 7;
					//'Parole nedrīkst sakrist ar pēdējām trim parolēm!<br>';
				}			  
			
			}			
			
		}
		
		if((($error) || ($error_first_time)) && (global_settings($conn, 'password_expire')==1)){
			
			if($error){ $erz = 6;}
			if($error_first_time){ $erz = 7;}
			
			header("Location: ".$page_file."?section=2&erz=".$erz."");
			die(0);			  
				
		}else{					
				
				$form_data = array(
				'longpass' => $hash,
				'recovery' => ''				
				);
				updateSomeRow("user", $form_data, "WHERE email='$myemail' LIMIT 1");				

				$range = global_settings($conn, 'password_expire_range');
				$exipre = date('Y-m-d H:i:s', strtotime('+'.$range.' minutes'));
				$exipre = strtotime($exipre);

				$form_data = array(
				'user' => $myemail,
				'password' => $hash,
				'date_made' => time(),
				'date_end' => $exipre
				);
				insertNewRows("user_passwords_logs", $form_data);				
				
		}	
				

				mysqli_close($conn);
				header("Location: ".$page_file."?section=2&res=done");
				die(0);
			}else{
				header("Location: ".$page_file."?section=2&res=error3");
				die(0);
			}
		}
	}
	
	if($source=='editSequence'){
			
			$records = htmlentities($_POST['records'], ENT_QUOTES, "UTF-8");
			require('inc/s.php');
			
			for ($i=0; $i <= $records; $i++) {	
 
				$sequence = htmlentities($_POST['sequence'][$i], ENT_QUOTES, "UTF-8");
				$p_sequence = htmlentities($_POST['p_sequence'][$i], ENT_QUOTES, "UTF-8");
			
				if(intval($sequence) && $p_sequence){

					$form_data = array(
					'p_sequence' => intval(abs($sequence))					
					);
					updateSomeRow("user_rights", $form_data, "WHERE page_name = '".mysqli_real_escape_string($conn, $p_sequence)."' AND user_id='".intval($myid)."'");					
					
				}
							
			}
			mysqli_close($conn);
			header("Location: ".$page_file."?section=3&res=done");
			die(0);						
	}
	
}
	
if ($action=='deletePicture') {
	if (isset($_GET['filekey'])){$filekey = htmlentities($_GET['filekey'], ENT_QUOTES, "UTF-8");}
	if ($filekey){
		require('inc/s.php');
		
		$form_data = array(
		'img' => ''				
		);
		updateSomeRow("user", $form_data, "WHERE id = '".$myid."' LIMIT 1");		
		
		deleteSomeRow("user_images", "WHERE filekey = '".$filekey."'");
		mysqli_close($conn);
	}
	header("Location: ".$page_file."?section=1");
	die(0); 
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
	if (!$section){
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT email, full_name, phone FROM user WHERE id = ".$myid." LIMIT 1");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		mysqli_close($conn);
	?>
		<form role="form" class="form-horizontal" name="edit" method="POST" action="">
		<input type='hidden' name='source' value='edit'/>
												
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-xs" role="group" aria-label="Small button group"> 
								<a class="btn btn-default active" href="<?=$page_file;?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD" title="pamatdati"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=2"><i class="glyphicon glyphicon-lock" style="color: #00B5AD"  title="parole"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=3"><i class="glyphicon glyphicon-wrench" style="color: #00B5AD"  title="lapu sakārtojums"></i></a> 								
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
							?>
						
						</div>						
							
						<h4>LIETOTĀJA DATI</h4>

						<div class="form-group">
							<div class="col-md-3">
								<small>lietotāja id</small>
								<input type="text" class="form-control" name="email" disabled="disabled" value="<?=$row['email']?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>vārds, uzvārds</small>
								<input type="text" class="form-control" name="full_name" value="<?=$row['full_name']?>">
							</div>
							<div class="col-md-3">
								<small>tālrunis</small>
								<input type="text" class="form-control" name="phone" value="<?=$row['phone']?>">
							</div>
						</div>

						<br>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.edit.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
						</a>

		</form>
	<?php
	}
	//parole
	if ($section=='2'){
	
	?>
		<form role="form" class="form-horizontal" name="editPassword" method="POST" action="">
		<input type='hidden' name='source' value='editPassword'/>

		
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-xs" role="group" aria-label="Small button group"> 
								<a class="btn btn-default" href="<?=$page_file;?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD" title="pamatdati"></i></a> 
								<a class="btn btn-default active"  href="<?=$page_file;?>?section=2"><i class="glyphicon glyphicon-lock" style="color: #00B5AD"  title="parole"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=3"><i class="glyphicon glyphicon-wrench" style="color: #00B5AD"  title="lapu sakārtojums"></i></a> 								
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
								
								if ($erz){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-danger">KĻŪDA!</div></div>';}

							?>
						
						</div>		

						<h4>LIETOTĀJA PAROLE</h4>
						
<div class="main container">

			<div class="col-md-7">
			<?
			if (!$erz){
				echo '	<div class="alert alert-info" role="alert">
							<b>'.$companyName.'</b> portāla lietotājiem ir nepieciešama droša parole:
								<ol>
									<li>parolē jāizmanto visi no sekojošiem nosacījumiem:
								<ul>
									<li>lielie burti (ABC..)
									<li>mazie burti (abc...)
									<li>cipari (123..)
									<li>simboli (#$%...)
								</ul>
									<li>minimālais paroles garums ir 8 simboli
								 </ol>
							
						</div>';
			}else{
				
				if($erz){
					echo '<div class="alert alert-danger" role="alert">';
					$array = explode(',',$erz);
					echo '	</i>Parole netika akceptēta!<ul>';
					require('inc/s.php');
					foreach($array AS $value){

						if($value==1){echo "<li>neviens burts nav lielais</li>";}
						if($value==2){echo "<li>neviens burts nav mazais</li>";}
						if($value==3){echo "<li>nav neviens cipars</li>";}
						if($value==4){echo "<li>nav neviens simbols</li>";}
						if($value==5){echo "<li>parolei ir jābūt vismaz no 8 simboliem</li>";}
						if($value==6){echo "<li>Parole nedrīkst sakrist ar pēdējām ".global_settings($conn, 'last_passwords')." parolēm!</li>";}
						if($value==7){echo "<li>Parole nedrīkst sakrist ar pēdējām ".global_settings($conn, 'last_passwords')." parolēm!</li>";}
					}
					echo '</ul></div></div>';
				}
			}?>	

			</div>
			<div class="clearfix"></div>
						

			<div class="form-group">
				<div class="col-md-3">
					<small>patreizējā parole</small>
					<input type="password" class="form-control" name="currentPassword">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<small>jaunā parole</small>
					<input type="password" class="form-control" name="newPassword">
				</div>
				<div class="col-md-3">
					<small>atkārtot jauno paroli</small>
					<input type="password" class="form-control" name="newPassword2">
				</div>
			</div>

			<br>
			<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.editPassword.submit()" style="margin-bottom: 1px;">
			  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
			</a>			

</div>								

		</form>
<?php
	}
	
	
	
if($section==3){
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT email, full_name, phone FROM user WHERE id = ".$myid." LIMIT 1");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		mysqli_close($conn);
	?>
		<form role="form" class="form-horizontal" name="editSequence" method="POST" action="">
		<input type='hidden' name='source' value='editSequence'/>
												
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-xs" role="group" aria-label="Small button group"> 
								<a class="btn btn-default" href="<?=$page_file;?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD" title="pamatdati"></i></a> 
								<a class="btn btn-default"  href="<?=$page_file;?>?section=2"><i class="glyphicon glyphicon-lock" style="color: #00B5AD"  title="parole"></i></a> 
								<a class="btn btn-default active"  href="<?=$page_file;?>?section=3"><i class="glyphicon glyphicon-wrench" style="color: #00B5AD"  title="lapu sakārtojums"></i></a> 								
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success">saglabāts!</div></div>';}
							?>
						
						</div>						
							
						<h4>LAPU IZKĀRTOJUMS</h4>
<?php
			echo '<table class="table table-bordered"><tbody>
						<td>lapa</td>
						<td>kārtas numurs</td>
					</tr>';
			require('inc/s.php');
			$showApages = mysqli_query($conn, "
			SELECT r.p_sequence AS p_sequence, p.page_header AS page_header, r.page_name AS page_name
			FROM user_rights AS r
			JOIN setup_pages AS p
			ON r.page_name=p.page_file
	
			WHERE r.user_id='".$myid."' AND r.p_view='on'");			
			
			$r = null;
			while($row = mysqli_fetch_array($showApages)){
			echo '<input type="hidden" name="p_sequence['.$r.']" value="'.$row['page_name'].'">';
				echo '	<tr>
							<td style="text-transform: uppercase;">'.$row['page_header'].'				
				
							<td>
							<div class="form-group">
								<div class="col-md-3">
									<input type="number" class="form-control" name="sequence['.$r.']" value="'.$row['p_sequence'].'">
								</div>
							</div>							
	

						</tr>';
			$r++;			
			}
			echo '<input type="hidden" name="records" value="'.$r.'">';
			echo '</tbody></table>';
?>			
						<br>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.editSequence.submit()" style="margin-bottom: 1px;">
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