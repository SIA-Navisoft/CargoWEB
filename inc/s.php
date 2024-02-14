<?php

$DBServer = "cwdb.sl-cargoweb.svc.cluster.local";
$DBPort = "3306";
$DBUser = "userP1O";
$DBPass = "ybiWxp3S50dWWsHG";
$DBName = "cwdb";

$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName, $DBPort);
if ($conn->connect_error) {
	echo "Database connection failed: " . $conn->connect_error, E_USER_ERROR;
}
mysqli_set_charset($conn,"utf8");

$resultCI = mysqli_query($conn,"SELECT * FROM company_information WHERE id='1'") or die (mysqli_error($conn));
$rowCI = mysqli_fetch_array($resultCI);
$companyName     = $rowCI['Name'];
$companyAddress  = $rowCI['Address'];
$companyCity     = $rowCI['City'];
$companyCountry  = $rowCI['Country'];
$companyPostCode = $rowCI['Post Code']; 
$companyPhoneNo  = $rowCI['Phone No.'];
$companyFaxNo    = $rowCI['Fax No.'];
$companyEmail    = $rowCI['E-mail'];
$companyWEB      = $rowCI['Web'];
$portalName      = $rowCI['portalName'];
$item_per_page = 5;

//ALL PAGES LIMITS FOR TABLES
$a_p_l = 10;  
$i_p_l = 100;



// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL & ~E_NOTICE);
error_reporting(0);
?>