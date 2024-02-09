<?
require_once('functions/base.php');
include('inc/s.php');
date_default_timezone_set('Europe/Riga');
$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

$warning_msg=$view=$key=$user=$passw=$newPassword2=null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['key'])){$key = htmlentities($_GET['key'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['user'])){$user = htmlentities($_GET['user'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['error'])){$error = htmlentities($_GET['error'], ENT_QUOTES, "UTF-8");}

if ((!$view)&&(!$key)){header("Location: restriction");die(0);}
	
if(isset($_POST['passw'])){
	$newPassword2 = $_POST['newPassword2'];
	$pass = $_POST['passw'];
	$warning_msg = $errortext = $errorcounter = null;
	if($pass != $newPassword2){$errortext.="<li>paroles nesakrīt";$errorcounter++;}
	if(!preg_match('/[A-Z]/', $pass)){$errortext.="<li>neviens burts nav lielais";$errorcounter++;}
	if(!preg_match('/[a-z]/', $pass)){$errortext.="<li>neviens burts nav mazais";$errorcounter++;}
	if(!preg_match('/[0-9]/', $pass)){$errortext.="<li>nav neviens cipars";$errorcounter++;}
	if(!preg_match('/[^0-9a-zA-z]/', $pass)){$errortext.="<li>nav neviens simbols";$errorcounter++;}
	if(strlen($pass)<8){$errortext.="<li>parolei ir jābūt vismaz no 8 simboliem";}
	if (($errorcounter>0)||(strlen($pass)<8)){
		$warning_msg="Parole netiks akceptēta!<ul>".$errortext."</ul>";
		$user=htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
		$key=htmlentities($_POST['key'], ENT_QUOTES, "UTF-8");
		$view=null;
	}
}
if(isset($_GET['error'])){
	$warning_msg = $errortext = $errorcounter = null;
	if($_GET['error'] != ''){$errortext.="<li>paroles nesakrīt";$errorcounter++;}

	if ($_GET['error'] != ''){
		$warning_msg="Parole netiks akceptēta!<ul>".$errortext."</ul>";

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
<body>

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
						<span style="font-weight: lighter; font-size: 1.6em;">Jums ir jāuzstāda sākotnējā parole!</span>
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

	<?
	if (($user)&&($key)&&($view!="validation")){
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT id FROM user WHERE email='$user' AND recovery='$key'");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		$count = mysqli_num_rows($result);
		if (($count==1)&&(strlen($key)==32)){
	?>	

		<div class="col-md-6">
			<?
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
			
	
		<div class="col-md-6">
			<form class="form-horizontal" name='validation' method='post' action='first_time.php?view=validation'>
			<input type='hidden' name='user' value='<? echo $user ?>'>
			<input type='hidden' name='key' value='<? echo $key ?>'>
			
			
			
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
					<small>atkārtot paroli</small>
						<div class="inner-addon left-addon">
						
							<i class="small glyphicon glyphicon-lock"></i>		
							<input type="password" class="form-control" placeholder="Password" name="newPassword2">
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
			
		<?
		} else {
			echo '	
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Izmantotais atjaunošanas kods ir nederīgs!<br/>
						Sazinieties ar '.$companyName.' SELFSERVICE portāla administratoru.
					</span';
		}
		mysqli_close($conn);
	}
	?>

	<?
	if ($view=='validation'){
		$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8");
		$key = htmlentities($_POST['key'], ENT_QUOTES, "UTF-8");
		$pass = htmlentities($_POST['passw'], ENT_QUOTES, "UTF-8");
		$newPassword2 = htmlentities($_POST['newPassword2'], ENT_QUOTES, "UTF-8");
		
		if ((!$key)||(!$user)){header("Location: restriction.php");	die(0);}
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT id, longpass FROM user WHERE email='$user' AND recovery='$key'");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row=mysqli_fetch_array($result);
		$count=mysqli_num_rows($result);
		if($pass == $newPassword2){
		if (($count==1)&&(strlen($key)==32)){
			$salt = hash('sha256', uniqid(mt_rand(), true) . 'mainīgais' . strtolower($user));
			$hash = $salt . $pass;
			for ( $i = 0; $i < 100000; $i ++ ) {$hash = hash('sha256', $hash);}
			$hash = $salt . $hash;
		
		$error = null;
		if(global_settings($conn, 'password_expire')==1){	
			// PĀRBAUDA VAI PAROLE NAV STARP PĒDĒJĀM 3 LIETOTAJĀM
			$result_p = mysqli_query($conn,"SELECT * FROM user_passwords_logs WHERE user='".$user."' ORDER BY id DESC LIMIT ".global_settings($conn, 'last_passwords')." ") or die(mysqli_error($conn));			
			if(mysqli_num_rows($result_p)>0){
				while($r_p = mysqli_fetch_array($result_p)){

						$salt_e = substr($r_p['password'], 0, 64);
						$hash_e = $salt_e . $pass;
						for ( $i = 0; $i < 100000; $i ++ ) {
						  $hash_e = hash('sha256', $hash_e);
						}
						$hash_e = $salt_e . $hash_e;
						
						if($r_p['password']==$hash_e){
							$error = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
						}
						if($row['longpass']==$hash_e){
							$error = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
						}						
				}
			}	
			
			$error_first_time = null;
			if(mysqli_num_rows($result_p)==0){

				$old = mysqli_query($conn,"SELECT longpass FROM user WHERE email='".$user."' LIMIT 1");
				$r_o = mysqli_fetch_assoc($old);
				
				$salt_o = substr($r_o['longpass'], 0, 64);
				$hash_o = $salt_o . $pass;
				for ( $i = 0; $i < 100000; $i ++ ) {
					$hash_o = hash('sha256', $hash_o);
				}
				$hash_o = $salt_o . $hash_o;	

				if($r_o['longpass']==$hash_o){
					$error_first_time = 'Parole nedrīkst sakrist ar pēdējām '.global_settings($conn, 'last_passwords').' parolēm!<br>';
				}			  
			
			}			
			
		}
		
		if((($error) || ($error_first_time)) && (global_settings($conn, 'password_expire')==1)){
	
			  
			echo '	
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						'.$error_first_time.' '.$error.'
					</span>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Lai mēģinātu vēlreiz, dodies uz <a href="first_time?user='.$user.'&key='.$key.'" style="color: #2593CD;">paroles maiņu</a>.
					</span>';			
		}else{			

			$form_data = array(
			'longpass' => $hash,
			'recovery' => ''
			);
			updateSomeRow("user", $form_data, "WHERE email='$user' AND recovery='$key' LIMIT 1");
			
			if(global_settings($conn, 'password_expire')==1){
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
				
			}
			
			echo '	
					<h1>Parole ir uzstādīta!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Lai pieslēgtos, dodies uz <a href="index" style="color: #2593CD;">autorizācijas lapu</a>.
					</span>';
		}
		}else{
			echo '	
					<h1>Kļūda!</h1>
					<span style="font-weight: lighter; font-size: 1.6em;">
						Izmantotais atjaunošanas kods ir nederīgs!<br/>
						Sazinieties ar '.$companyName.' SELFSERVICE portāla administratoru.
					</span>
					';
		}
		}else
		{

			
			header("Location: first_time?user=".$user."&key=".$key."&error=wrong");
			die(0);
			}
		

	mysqli_close($conn);
	}
	?>		
		
		</div><br>


	
</body>
</html>	















