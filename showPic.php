<?
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="receipt";

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];

require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (isset($_GET['filekey'])){$filekey = htmlentities($_GET['filekey'], ENT_QUOTES, "UTF-8");}

$query = mysqli_query($conn,"SELECT type, big, content FROM scanner_lines_images WHERE filekey = '".$filekey."'");

$count = mysqli_num_rows($query);

if($count>=1){
?>
<style>
.zoom {
  transition: transform .2s;
}

.zoom:hover {
  -ms-transform: scale(1.5);
  -webkit-transform: scale(1.5);
  transform: scale(1.5); 
}
</style>
<?	
	$row = mysqli_fetch_assoc($query);
	echo "<img class='zoom' src='data:".$row['type'].";base64,".base64_encode($row['content'])."' style='width:500px;height:600px;'><br /><br />";	
	
	
}else{
	echo '<i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> Šāds attēls nepastāv.';
}