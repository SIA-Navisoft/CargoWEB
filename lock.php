<?php
session_start();
date_default_timezone_set('Europe/Riga');

if(!isset($_SESSION['login_user']))
{
	echo '<script>location.href="index";</script>' ;
	die(0);  
}

$user_check = $_SESSION['login_user'];

require('inc/s.php');
$result = mysqli_query($conn,"SELECT expire, full_name, email, id, user_group, user_objects, responsible_id FROM user WHERE email='".$user_check."' AND deletedDate = 0");
$r = mysqli_fetch_assoc($result);
$count_r=mysqli_num_rows($result);
mysqli_close($conn);



if(($count_r!="1")||($r['expire']!=='n')||(($r['user_group']!=='w_user')))
{
	header("Location: restriction");
	die(0);
}

$fullname = $r['full_name'];
$myemail = $r['email'];
$myid = $r['id'];
$mygroup = $r['user_group'];
$myobjects = $r['user_objects'];
$myresponsibleid = $r['responsible_id'];

$status_dayname = array( "1" => "pirmdiena", "2" => "otrdiena", "3" => "trešdiena", "4" => "ceturtdiena", "5" => "piektdiena", "6" => "sestdiena", "7" => "svētdiena");
$current_date = date("d.m.Y");
$current_time = date("H:i");
$current_day = $status_dayname[date("N")];


?>