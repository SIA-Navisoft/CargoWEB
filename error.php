<?php
require('inc/s.php');
date_default_timezone_set('Europe/Riga');
$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");

$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];
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


    <footer class="footer" style="margin-top: -20px; background-color: #d9534f;">
      <div class="container">
		<div style="width:100%;">
			

				<div class="container vertical-divider">
				  <div class="column one-third" style="color: white;">
						<span style="font-weight: lighter; font-size: 2em;">kļūda : error : ошибка</span><br /><br />
						<span style="font-weight: lighter; font-size: 1.6em;">nepareizs lietotāja vārds vai parole!</span>
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
	
<a class="btn btn-danger"href="index" style="margin-left: 30px; margin-top: 30px;">ATKĀRTOT</a>

	
</body>
</html>	