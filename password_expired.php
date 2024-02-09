<?php
require_once('functions/base.php');
include('inc/s.php');
date_default_timezone_set('Europe/Riga');
$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

$warning_msg=$view=$key=$user=$passw=null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['key'])){$key = htmlentities($_GET['key'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['user'])){$user = htmlentities($_GET['user'], ENT_QUOTES, "UTF-8");}

if ((!$view)){header("Location: restriction.php");die(0);}
	
if(isset($_POST['passw'])){
	$pass = $_POST['passw'];
	$old_pass = $_POST['old_passw'];
	$warning_msg = $errortext = $errorcounter = null;
	if(!preg_match('/[A-Z]/', $pass)){$errortext.="<li>neviens burts nav lielais";$errorcounter++;}
	if(!preg_match('/[a-z]/', $pass)){$errortext.="<li>neviens burts nav mazais";$errorcounter++;}
	if(!preg_match('/[0-9]/', $pass)){$errortext.="<li>nav neviens cipars";$errorcounter++;}
	if(!preg_match('/[^0-9a-zA-z]/', $pass)){$errortext.="<li>nav neviens simbols";$errorcounter++;}
	if(strlen($pass)<8){$errortext.="<li>parolei ir jābūt vismaz no 8 simboliem";}
	if($_POST['passw']!=$_POST['passw_again']){$errortext.="<li>jaunās paroles nesakrīt";}
	if (($errorcounter>0)||(strlen($pass)<8)){
		$warning_msg="Parole netiks akceptēta!<ul>".$errortext."</ul>";
		$user=htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
		$view=null;
	}
}

?>
<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1,IE=9" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$companyName;?></title>
	
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/guest_footer.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/bootstrap.min.js"></script>
 
</head>
<body onLoad="document.validation.passw.focus()">

	<nav class="navbar navbar-default navbar-sm navbar-inverse navbar-static-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="index"><i class="glyphicon glyphicon-home"></i></a>
		  <p class="navbar-text" style="color: white;"><b><?=$companyName;?></b> selfservice</p>
        </div>
      </div>
   </nav>


    <footer class="footer" style="margin-top: -20px; background-color: #A1CF64;">
      <div class="container">
		<div style="width:100%;">

				<div class="container vertical-divider">
				  <div class="column one-third" style="color: white;">
						<span style="font-weight: lighter; font-size: 2em;">reģistrēšanās : registration : регистрация</span><br /><br />
						<span style="font-weight: lighter; font-size: 1.6em;">Jums ir jānomaina esošā parole!</span>
				  </div>
				  <div class="column two-thirds"  style="color: white;">
				<span style="font-weight: lighter; font-size: 1.8em;">
							<?=$current_day?><br/>
							<?=$current_date?></span><br/>
				  </div>
				</div>		
				
		</div>
	 </div>       
    </footer>	

	
	
		<div style="margin-left: 20px; margin-top: 20px;">

		
		
		</div>


	
</body>
</html>	




<!DOCTYPE html>
<html>


<body onLoad="document.validation.passw.focus()">



	<?
	if (($user)&&($view!="validation")){
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT id FROM user WHERE email='$user'");
		if (!$result){die("Attention! Query to show fields failed..");}
		$row = mysqli_fetch_assoc($result);
		$count = mysqli_num_rows($result);
		if (($count==1)){
	?>	

		<div class="column">
			<div class="col-md-6">
			<?php
			if (!$warning_msg){
				echo '	<div class="alert alert-info" role="alert"><b>'.$companyName.' SELFSERVICE</b> portāla lietotājiem ir nepieciešama droša parole:
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
				echo '	<div class="alert alert-danger" role="alert">'.$warning_msg.'</div>';
			}?>
			</div>
			
			
		</div>		
		
		<div class="col-md-6">
			<form class="form-horizontal" name='validation' method='post' action='password_expired.php?view=validation'>
			<input type='hidden' name='user' value='<?=$user ?>'>
			
	
			
			<div class="form-group">
				<div class="col-md-6">
					<small>lietotāja id (epasts)</small>
					<div class="inner-addon left-addon search-icon">
					
						<i class="glyphicon glyphicon-user "></i>
						<input type="text" class="form-control" disabled="disabled"  placeholder="<?=$user?>">
					</div>						
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6">			
					<div class="required-field-block">
					<small>vecā parole</small>
						<div class="inner-addon left-addon">
						
							<i class="small glyphicon glyphicon-lock"></i>		
							<input type="password" class="form-control" placeholder="Old password" name="old_passw">
							<div class="required-icon">
								<div class="text">*</div>
							</div>
						</div>
					</div>	
				</div>
			</div>	
			
			<div class="form-group">
				<div class="col-md-6">			
					<div class="required-field-block">
					<small>lietotāja parole</small>
						<div class="inner-addon left-addon">
						
							<i class="small glyphicon glyphicon-lock"></i>		
							<input type="password" class="form-control" placeholder="Password" name="passw">
							<div class="required-icon">
								<div class="text">*</div>
							</div>
						</div>
					</div>	
				</div>
			</div>	

			<div class="form-group">
				<div class="col-md-6">			
					<div class="required-field-block">
					<small>lietotāja parole vēlreiz</small>
						<div class="inner-addon left-addon">
						
							<i class="small glyphicon glyphicon-lock"></i>		
							<input type="password" class="form-control" placeholder="Password again" name="passw_again">
							<div class="required-icon">
								<div class="text">*</div>
							</div>
						</div>
					</div>	
				</div>
			</div>				
			
			
			<a class="btn btn-default btn-sm" aria-label="Left Align" href="javascript:document.validation.submit()" style="margin-bottom: 1px;">
			  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> SAGLABĀT
			</a>			
			
			</form>
		</div>

			
		<?php
		} else {
			echo '	<div style="margin-left: 20px; margin-top: 20px;">
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Izmantotais atjaunošanas kods ir nederīgs!<br/>
						Sazinieties ar '.$companyName.' portāla administratoru.
					</span>
					</div>';
		}
		mysqli_close($conn);
	}
	?>

	<?php
	if ($view=='validation'){
		$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
		$pass = htmlentities($_POST['passw'], ENT_QUOTES, "UTF-8");
		$pass_again = htmlentities($_POST['passw_again'], ENT_QUOTES, "UTF-8");
		if ((!$user)){header("Location: restriction.php");	die(0);}
		require('inc/s.php');	
		
		
		$result = mysqli_query($conn,"SELECT id, longpass FROM user WHERE email='$user'");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row=mysqli_fetch_array($result);
		$count=mysqli_num_rows($result);
		if (($count==1) && ($pass==$pass_again)){
			
				$result_p = mysqli_query($conn,"SELECT * FROM user_passwords_logs WHERE user='".$user."' ORDER BY id DESC LIMIT ".global_settings($conn, 'last_passwords')."") or die(mysqli_error($conn));
				$error = null;
				if(mysqli_num_rows($result_p)>0){
					while($r_p = mysqli_fetch_array($result_p)){

							$salt = substr($r_p['password'], 0, 64);
							$hash = $salt . $pass;
							for ( $i = 0; $i < 100000; $i ++ ) {
							  $hash = hash('sha256', $hash);
							}
							$hash = $salt . $hash;
							
							if($r_p['password']==$hash){
								$error = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
							}
							if($row['longpass']==$hash){
								$error = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
							}							
					}
				}
				
				$error_o = null;
				if(mysqli_num_rows($result_p)==0){
					// PĀRBAUDA VAI VECĀ PAROLE SAKRĪT AR IEVADĪTO
					$old = mysqli_query($conn,"SELECT longpass FROM user WHERE email='".$user."' ORDER BY id DESC");
					$r_o = mysqli_fetch_assoc($old);
					
					$salt_i = substr($r_o['longpass'], 0, 64);
					$hash_i = $salt_i . $old_pass;
					for ( $i = 0; $i < 100000; $i ++ ) {
						$hash_i = hash('sha256', $hash_i);
					}
					$hash_i = $salt_i . $hash_i;	
						
					if($r_o['longpass']!=$hash_i){
						$error_o = 'Vecā parole nesakrīt!<br>';
					}
				}
			  
				// JA NAV VĒSTURES PAROĻU LOGOS PĀRBAUDA VAI ESOŠĀ PAROLE NESAKRĪT AR JAUNO 
				$error_first_time = null;
				if(mysqli_num_rows($result_p)==0){
					
					$salt = substr($r_o['longpass'], 0, 64);
					$hash = $salt . $pass;
					for ( $i = 0; $i < 100000; $i ++ ) {
						$hash = hash('sha256', $hash);
					}
					$hash = $salt . $hash;	
				
					if($r_o['longpass']==$hash){
					    $error_first_time = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
					}			  
				}			  
		
		if(($error) || ($error_o) || ($error_first_time)){
	
			  
			echo '	<div  style="margin-left: 20px; margin-top: 20px;">
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						'.$error_first_time.' '.$error.' '.$error_o.' 
					</span>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Lai mēģinātu vēlreiz, dodies uz <a href="password_expired.php?view=expired&user='.$user.'" style="color: #2593CD;">paroles maiņas lapu</a>.
					</span>				
					</div>';			
		}else{
			
			mysqli_query($conn, "UPDATE user SET longpass='$hash', recovery='' WHERE email='$user' LIMIT 1");
			
			
			$form_data = array(
			'longpass' => $hash,
			'recovery' => ''					
			);
			updateSomeRow("user", $form_data, "WHERE email='$user' LIMIT 1");			
			
			$range = global_settings($conn, 'password_expire_range');
			$exipre = date('Y-m-d H:i:s', strtotime('+'.$range.' months'));
			$exipre = strtotime($exipre);

			$form_data = array(
			'user' => $user,
			'password' => $hash,
			'date_made' => time(),
			'date_end' => $exipre
			);
			insertNewRows("user_passwords_logs", $form_data);			
			
			echo '	<div style="margin-left: 20px; margin-top: 20px;">
					<h1>Parole ir uzstādīta!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Lai pieslēgtos, dodies uz <a href="index" style="color: #2593CD;">autorizācijas lapu</a>.
					</span>
					</div>';			
		}			

		}else{
			echo '	<div  style="margin-left: 20px; margin-top: 20px;">
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Jaunās paroles nesakrīt!<br> 
					</span>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Lai mēģinātu vēlreiz, dodies uz <a href="password_expired.php?view=expired&user='.$user.'" style="color: #2593CD;">paroles maiņas lapu</a>.
					</span>				
					</div>';
		}

	mysqli_close($conn);
	}
	?>

</body>
</html>