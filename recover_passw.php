<?php
require('inc/s.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache");

date_default_timezone_set('Europe/Riga');
$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

// define variables and set to empty values
$acc_email = $emailErr = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if (empty($_POST["acc_email"])){
		$emailErr = "<li>Nav norādīts lietotāja id</li>";
	}else{
		$acc_email = htmlentities($_POST['acc_email'], ENT_QUOTES, "UTF-8");
	}

	$error = $emailErr;

	
	if (!$error){
		session_start();
		$_SESSION['acc_email'] = $acc_email;
		header("Location: recover_passw_go");  
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
	
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/guest_footer.css">
  <script src="js/jquery-3.2.1.js"></script>
  <script src="js/bootstrap.min.js"></script>
 
</head>
<body  onLoad="document.registration.company_name.focus()">

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
						<span style="font-weight: lighter; font-size: 1.6em;">Paroles atjaunošana</span>
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
			<span style="font-weight: lighter; font-size: 1.6em;">
			1. Lai atjaunotu lietotāja paroli, aizpildiet pieteikuma formu.
			</span>
			
			<p><? if ($error){ echo 'Reģistrēšana neizdevās, konstatējām sekojošas kļūdas:<ul>'.$error .'</ul>';}?></p>
			
			<form class="ui form" name='registration' method='post' action='recover_passw'>
				
           
				<label style="font-size: 0.875em;">lietotāja id (epasts)</label>
                <div class="inner-addon left-addon search-icon" style="width:300px;">
				
                    <i class="glyphicon glyphicon-user "></i>
                    <input type="text" name="acc_email"  placeholder="<?=$acc_email?>" class="form-control search-box">
                </div>
           			
			<br><br>	
			
				<a class="btn btn-success" href='javascript:document.registration.submit()' style="text-decoration: none;">ATJAUNOT PAROLI</a>	
				<a class="btn btn-danger" href="index" style=" text-decoration: none;">ATCELT</a>
			
			</form>			
	</div><br>

</body>
</html>	







