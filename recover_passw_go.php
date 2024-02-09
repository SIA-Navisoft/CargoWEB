<?
date_default_timezone_set('Europe/Riga');
$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

include('functions/base.php');

session_start();
if(!isset($_SESSION['acc_email'])){
	header("Location: recover_passw");
	die(0);
}else{
	$Err = $acc_email = $ip = $agent = $action = "";
	$acc_email=$_SESSION['acc_email'];
	$ip = $_SERVER['REMOTE_ADDR']; 
	$agent = $_SERVER["HTTP_USER_AGENT"];
	session_destroy();
	
	include('inc/s.php');
	$result = mysqli_query($conn,"SELECT id, email FROM user WHERE email='".$acc_email."' AND expire='n'");
	$total_usr = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);
	$acc_email = $acc_code = null;
	$acc_email = $row['email'];
	$acc_code = $row['id'];
	if(!$row['id']){
		$Err = "<p>Paroles atjaunošana nav izdevusies, jo lietotājs ar šādu id <b> ".$row['email']." </b> mūsu datubāzē neeksistē.<br/><br/>Ja uzskatat, ka ir notikusi kļūda, sazinieties ar mūsu speciālistiem.</p>"; 
		$action = 'netika';
	}

	if (!$Err){

			$key_code=md5(uniqid(rand()));	
			//sending email
			
			
			if (filter_var($acc_email, FILTER_VALIDATE_EMAIL)) {
				require_once "Mail.php";
				
				$from = "scales@nnvt.lv";
				$url="http://192.168.15.248/first_time?user=". $acc_email ."&key=". $key_code;
				$to = $acc_email;
				$subject = "NNVT SELFSERVICE PORTAL: paroles atjaunošana";
				$type = "text/html; charset=utf-8";
				
				$body="<p style='font-size:10pt; font-family: Segoe UI, Arial, Verdana;'>Jums tika veikta paroles atjaunošana <b>NNVT SELFSERVICE</b> portālam.<br/><br/>
				Lai uzstādītu jauno paroli, dodaties šeit: <a href='$url'>$url</a></span></p>\r\n";
				$body.="<p style='font-size:10pt; font-family: Segoe UI, Arial, Verdana;'><br/>Jūsu lietotājvārds ir:<span style='color:blue;'> $acc_email</span><br/></p>\r\n";
				$body.="<p style='font-size:8pt; font-family: Segoe UI, Arial, Verdana;'><br/>Neskaidrību un jautājumu gadījumā sazinieties ar NNVT.</p>";
				
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <scales@nnvt.lv>' . "\r\n";
				
				
				include('inc/m.php');
				$headers = array ('From' => $from,'To' => $to,'Content-Type' => $type,'Subject' => $subject);
				$smtp = Mail::factory(
					'smtp',array (
						'host' => $host,
						'auth' => true,
						'username' => $username,
						'password' => $password,
						'port' => $port					
					)
				);
				$mail = $smtp->send($to, $headers, $body);
				if (PEAR::isError($mail)) {
					echo($mail->getMessage());
				}
				
				$form_data = array(
				'sender' => $from,
				'receiver' => $to,
				'topic' => 'SELFSERVICE: paroles atjaunošana',
				'createdSource' => 'selfservice'
				);
				insertNewRows("web_messages", $form_data);				
				
				$Err.= "<p>Uz ".$acc_email." tika nosūtīts uzaicinājums pievienoties portālam. Lai aktivizētu lietotāju, sekojiet norādēm, kas tika nosūtītas.</p>"; 
				
				$action = 'tika';
				
				$form_data = array(
				'recovery' => $key_code					
				);
				updateSomeRow("user", $form_data, "WHERE id = '$acc_code' LIMIT 1");				
				
			} else {
				$Err = "<p>Paroles atjaunošana nav izdevusies, jo lietotāja id <b> ".$acc_email." </b> nevaram validēt kā epasta adresi.<br/><br/>Ja uzskatat, ka ir notikusi kļūda, sazinieties ar mūsu speciālistiem.</p>"; 
				$action = 'netika';
			}
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
			2. Pieteikuma forma <?=$action?> apstrādāta.<br/><?=$Err?><br/>
		</span>
			<?if($action=='tika'){
				echo '<a class="btn btn-success" href="index" >UZ SĀKUMU</a>';
			}else{
				echo '<a class="btn btn-danger" href="recover_passw" >MĒĢINĀT VĒLREIZ</a>';
			}?>		

		</div><br>


	
</body>
</html>	
