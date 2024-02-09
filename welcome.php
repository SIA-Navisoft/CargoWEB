<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');
$page_header="galvenā izvēlne";
$page_file="main";

if (($mygroup!="w_user")){header("Location: restriction");die(0);}

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];


include('functions/base.php');
include('header.php');
webStatisticsPages($myemail, $myid, $_POST["source"], $_GET['view'], $_GET["action"]);

function isMobileDevice() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

?>

<style>
.texto_grande {
    font-size: 1.0rem;
    color: #00B5AD;
} 
#icone_grande {
    font-size: 1.8rem;
    color: #fff;
} 

</style>


<div class="container-fluid ">
	<div class="row">
	
<?php



if ($mygroup=="w_user"){
	require('inc/s.php');
	
	
	if(isMobileDevice()){
		
		
		$result = mysqli_query($conn,"	SELECT u_rights.page_name, s_pages.page_header, s_pages.page_icon, s_pages.page_file
										FROM setup_pages AS s_pages
										JOIN user_rights AS u_rights
										ON u_rights.page_name = s_pages.page_file
										WHERE u_rights.user_id = '".$myid."' AND (u_rights.p_view = 'on' OR u_rights.p_edit = 'on') AND s_pages.for_mobile=1 AND u_rights.p_sequence>'0'
										ORDER BY u_rights.p_sequence");
		if (!$result){die("Attention! Query to show fields failed. M1");}
		$count_rows = mysqli_num_rows($result);
		
			while($row = mysqli_fetch_assoc($result)){
			
				echo '
					 <div class="col-md-2" style="padding: 10px 10px 10px 10px;">
						<a class="btn btn-block btn-xs btn-default" href="'.$row['page_file'].'">
							<i class="btn-xs '.$row['page_icon'].'" id="icone_grande" style="color: #00B5AD;"></i> <br>
							<span class="texto_grande">'.ucfirst($row['page_header']).'</span></a>
					  </div> 			
				';			
							
			}		
		
		
		$result = mysqli_query($conn,"	SELECT u_rights.page_name, s_pages.page_header, s_pages.page_icon, s_pages.page_file
										FROM setup_pages AS s_pages
										JOIN user_rights AS u_rights
										ON u_rights.page_name = s_pages.page_file
										WHERE u_rights.user_id = '".$myid."' AND (u_rights.p_view = 'on' OR u_rights.p_edit = 'on') AND s_pages.for_mobile=1  AND (u_rights.p_sequence='0' OR u_rights.p_sequence='')
										ORDER BY s_pages.page_header");
		if (!$result){die("Attention! Query to show fields failed. M2");}
		$count_rows2 = mysqli_num_rows($result);

			while($row = mysqli_fetch_assoc($result)){
			
				echo '
					 <div class="col-md-2" style="padding: 10px 10px 10px 10px;">
						<a class="btn btn-block btn-xs btn-default" href="'.$row['page_file'].'" style=" color: blue;">
							<i class="btn-xs '.$row['page_icon'].'" id="icone_grande" style="color: #00B5AD;"></i> <br>
							<span class="texto_grande">'.ucfirst($row['page_header']).'</span></a>
					  </div> 			
				';			
							
			}

			if($count_rows==0 && $count_rows2==0){
				echo ' <i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
			}

		mysqli_close($conn);
			
			
			
	} else {	
		
		$result = mysqli_query($conn,"	SELECT u_rights.page_name, s_pages.page_header, s_pages.page_icon, s_pages.page_file
										FROM setup_pages AS s_pages
										JOIN user_rights AS u_rights
										ON u_rights.page_name = s_pages.page_file
										WHERE u_rights.user_id = '".$myid."' AND (u_rights.p_view = 'on' OR u_rights.p_edit = 'on') AND u_rights.page_name<>'page_my_incidents' AND s_pages.for_mobile=0 AND u_rights.p_sequence>'0'
										ORDER BY u_rights.p_sequence");
		if (!$result){die("Attention! Query to show fields failed.");}
		$count_rows = mysqli_num_rows($result);
			
			while($row = mysqli_fetch_assoc($result)){
		
				echo '
					 <div class="col-md-2" style="padding: 10px 10px 10px 10px;">
						<a class="btn btn-block btn-xs btn-default" href="'.$row['page_file'].'">
							<i class="btn-xs '.$row['page_icon'].'" id="icone_grande" style="color: #00B5AD;"></i> <br>
							<span class="texto_grande">'.ucfirst($row['page_header']).'</span></a>
					  </div> 			
				';			
							
			}		
		
		
		$result = mysqli_query($conn,"	SELECT u_rights.page_name, s_pages.page_header, s_pages.page_icon, s_pages.page_file
										FROM setup_pages AS s_pages
										JOIN user_rights AS u_rights
										ON u_rights.page_name = s_pages.page_file
										WHERE u_rights.user_id = '".$myid."' AND (u_rights.p_view = 'on' OR u_rights.p_edit = 'on') AND u_rights.page_name<>'page_my_incidents' AND s_pages.for_mobile=0  AND (u_rights.p_sequence='0' OR u_rights.p_sequence='')
										ORDER BY s_pages.page_header");
		if (!$result){die("Attention! Query to show fields failed.");}
		$count_rows = mysqli_num_rows($result);
			
			while($row = mysqli_fetch_assoc($result)){
			
				echo '
					 <div class="col-md-2" style="padding: 10px 10px 10px 10px;">
						<a class="btn btn-block btn-xs btn-default" href="'.$row['page_file'].'" style=" color: blue;">
							<i class="btn-xs '.$row['page_icon'].'" id="icone_grande" style="color: #00B5AD;"></i> <br>
							<span class="texto_grande">'.ucfirst($row['page_header']).'</span></a>
					  </div> 			
				';			
							
			}

		mysqli_close($conn);
		
	}	
	
	
}
?>	
	
	</div>
</div>

<?php include("footer.php"); ?>