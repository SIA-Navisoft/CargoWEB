<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


require('../../inc/s.php');
include('../../functions/base.php');

if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
$id = intval($_GET['id']);


if (isset($_GET['s'])){$s = htmlentities($_GET['s'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceId'])){$issuanceId = htmlentities($_GET['issuanceId'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['issuanceStatus'])){$issuanceStatus = htmlentities($_GET['issuanceStatus'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['acceptanceActNo'])){$acceptanceActNo = htmlentities($_GET['acceptanceActNo'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}

if (isset($_POST['idtdelta'])){$idtdelta = htmlentities($_POST['idtdelta'], ENT_QUOTES, "UTF-8");}
if (isset($_POST['value'])){$value = htmlentities($_POST['value'], ENT_QUOTES, "UTF-8");}

$query = mysqli_query($conn, "SELECT * FROM cargo_line WHERE id='".intval($idtdelta)."'");
$row = mysqli_fetch_array($query);


$start_amount = floatval($row['amount']);

echo $value.' '.$start_amount;