<?php 
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="additional_services";


require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

$id = null;
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');


$query = mysqli_query($conn, "SELECT * FROM additional_services_header WHERE id='".intval($id)."'") or die (mysqli_error($conn));
$row = mysqli_fetch_array($query);

$status = $row['status'];


	if($status==0){
		$sta = 'ienākusi';
	}
	if($status==10){
		$sta = 'ienākusi (nodota)';
	}
	if($status==20){
		$sta = 'saņemta';
	}
	if($status==30){
		$sta = 'saņemta (nodota)';
	}
	if($status==40){
		$sta = 'izsniegta';
	}	

echo 'kravas status: <b>'.$sta.'</b> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
?>
				