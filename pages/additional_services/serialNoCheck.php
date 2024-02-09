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

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);



$id = $serial = $line = null;
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['serial'])){$serial = htmlentities($_GET['serial'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['line'])){$line = htmlentities($_GET['line'], ENT_QUOTES, "UTF-8");}
$exLine = null;
if($line){$exLine = " AND l.id!='".intval($line)."'";}

require('../../inc/s.php');
$query =  mysqli_query($conn, "
        SELECT h.id 
        FROM additional_services_header AS h 
        LEFT JOIN additional_services_line AS l
        ON h.docNr=l.docNr
        WHERE h.id='".intval($id)."' AND l.serialNo='".$serial."' ".$exLine."");


if(mysqli_num_rows($query)>=1){
    echo 'error';
}else{
    echo 'done';
}        
