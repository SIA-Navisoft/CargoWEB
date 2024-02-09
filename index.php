<?php 
require_once('functions/base.php');
require('inc/s.php');
$maint = 'off';
if($maint=='on'){
	header("Location: maintenance");
	die(0);
}
session_start();
$_SESSION['mys_user']=NULL;
$_SESSION['mys_company']=NULL;

if(isset($_SESSION['attempt']) ) $attempt=$_SESSION['attempt']+1; else  $attempt=1;

if($attempt>3) {
require_once __DIR__ . '/recaptcha2/autoload.php';
$siteKey = '6LeHyCAUAAAAACeYsXdQShGyqxSAG0VKexLS7rWC';
$secret = '6LeHyCAUAAAAANe9Gyo8nA3ACOROmWa3fxPyxtRm';
}
$captcha='ok';

$CAPTCHA_STYLE='#000000';
if (isset($_POST['g-recaptcha-response']) && $attempt>3) {

    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
// Make the call to verify the response and also pass the user's IP address
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    if ($resp->isSuccess()){
// If the response is a success, that's it!
$captcha='ok';

    } else {
// If it's not successful, then one or more error codes will be returned.
$CAPTCHA_STYLE='#FF0000';
$captcha='';

    } // isSuccess ???

} // g-recaptcha-response

$section=null;
if($_SERVER["REQUEST_METHOD"] == "POST"  && $captcha=='ok'){
	if($attempt <= 3) $captcha='ok';

		require('inc/s.php');
		session_start();
		$myusername=htmlentities($_POST['userName'], ENT_QUOTES, "UTF-8");
		$mypassword=htmlentities($_POST['passWord'], ENT_QUOTES, "UTF-8");
				
		$result = mysqli_query($conn,"SELECT id, longpass, user_group FROM user WHERE email='".$myusername."'");
		$r = mysqli_fetch_assoc($result);
		$salt = substr($r['longpass'], 0, 64);
		$hash = $salt . $mypassword;
		for ( $i = 0; $i < 100000; $i ++ ) {
		  $hash = hash('sha256', $hash);
		}
		$hash = $salt . $hash;
		$user_ip = $_SERVER['REMOTE_ADDR']; 
		$agent = $_SERVER["HTTP_USER_AGENT"];
		if ($hash == $r['longpass']  && $captcha=='ok'){
			
		if(global_settings($conn, 'password_expire')==1){
			// check if password expired
			$result_p = mysqli_query($conn,"SELECT * FROM user_passwords_logs WHERE user='".$myusername."' ORDER BY id DESC");
			
			$r_p = mysqli_fetch_array($result_p);
			
			$today = date('Y-m-d H:i:s');
			$last_day = date('Y-m-d H:i:s', $r_p['date_end']);
 
		}

		if(($last_day<$today) && (global_settings($conn, 'password_expire')==1)){
				header("location: password_expired?view=expired&user=".$myusername."");
				die(0); 
		}else{			
			
			$_SESSION['login_user']=$myusername;
			//user stat input

			$form_data = array(
			'comp_id' => 'NNVT',
			'ip' => $user_ip,
			'user_name' => $myusername,
			'ok' => 'y',
			'browser' => $agent
			);
			insertNewRows("user_stat", $form_data);

			header("location: welcome");
			die(0);

			
		}	
		}else{
			//user stat input
			
				$form_data = array(
				'comp_id' => 'NNVT',
				'ip' => $user_ip,
				'user_name' => $myusername,
				'ok' => 'n',
				'browser' => $agent
				);
				insertNewRows("user_stat", $form_data);
			
			//user stat end	
			$_SESSION['attempt']=$attempt;
				header("location: error");
			die(0); 
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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/login.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/bootstrap.min.js"></script>
 
</head>
<body onLoad="document.loginForm.userName.focus()">
<script>
	function pressed(e) {
		if ( (window.event ? event.keyCode : e.which) == 13) { 
			document.forms[0].submit();
		}
	}
</script>

    <div class="container">
		<div class="row">
		  <div class="card card-frame">
			<div class="card card-container">
				<form class="form-signin" name="loginForm" method="POST" action="index" onkeydown="pressed(event)">
					<center><span style="font-weight: lighter; font-size: 1.8em; color: white;"><b><?=$companyName;?></b></span><br>
					<span style="font-weight: lighter; font-size: 1.6em; color: white;">selfservice</span></center><br>
					
					<div class="required-field-block">
						<div class="inner-addon left-addon">
							<i class="small glyphicon glyphicon-user"></i>		
							<input type="email" placeholder="Username" class="form-control" name="userName">
							<div class="required-icon">
								<div class="text">*</div>
							</div>
						</div>
					</div>

					<div class="required-field-block">
						<div class="inner-addon left-addon">
							<i class="small glyphicon glyphicon-lock"></i>		
							<input type="password" placeholder="Password" class="form-control" name="passWord">
							<div class="required-icon">
								<div class="text">*</div>
							</div>
						</div>
					</div>		

					<?php if($attempt > 3) { ?>
					<center>
					<div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
					<script type="text/javascript"
							src="https://www.google.com/recaptcha/api.js?hl=lv">
					</script>
					<br>
					</center>          
					<?php } ?>  
					
					<center>
						<ul class="nav "> 
							<li role="presentation"><a href="javascript:document.loginForm.submit()" class="log-in"><i class="glyphicon glyphicon-circle-arrow-right btn-lg"></i><br>Pieslēgties</a></li> 
						</ul>					
					</center>
					
						
				</form>

			</div>
			<a href="recover_passw" class="forgot-password">
				aizmirsi paroli?
			</a>		
		  </div>
		</div>  
    </div>
</body>
</html>


