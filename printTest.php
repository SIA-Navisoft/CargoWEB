<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="receipt";



require('inc/s.php');
require('functions/base.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'") or die (mysqli_error($conn));

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}
$id = $view = $eid = null;
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['eid'])){$eid = htmlentities($_GET['eid'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['form'])){$form = htmlentities($_GET['form'], ENT_QUOTES, "UTF-8");}







if(!$view && $form==1){
	
	$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
	$cargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$crow = mysqli_fetch_array($cargo);	
	
	$lastBy = returnMeWho($crow['lastBy']);
	$actNo  = $crow['acceptance_act_no'];
	$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
	
	$cargoStatus = null;
	if($crow['cargo_status']=='C'){
		$cargoStatus = 'EKSPORTS';
	}	
	if($crow['cargo_status']=='N'){
		$cargoStatus = 'TRANZĪTS';
	}
	if($crow['cargo_status']=='EU'){
		$cargoStatus = 'EU';
	}
	
	
	$applicationNo  = $crow['application_no'];
	$landingDate = date('d.m.Y', strtotime($crow['landingDate']));
	$applicationDate = date('d.m.Y', strtotime($crow['application_date']));
	
	$transport = mb_strtoupper($crow['transport'], 'UTF-8');
	$ship  = $crow['ship'];
	
	$linesTransport = mysqli_query($conn, "SELECT GROUP_CONCAT(DISTINCT(thisTransport) separator ', ') AS transports FROM cargo_line WHERE docNr='".$crow['docNr']."'");
	$allT = mysqli_fetch_array($linesTransport);
	
	$thisTransport = $allT['transports'];
	
	
	$linesLocation = mysqli_query($conn, "
					SELECT 
	
					GROUP_CONCAT(
						DISTINCT CONCAT(c.location,' - ',l.name) 
						SEPARATOR '<br>'
					) AS locations
	
					FROM cargo_line AS c
					LEFT JOIN n_location AS l
					ON c.location=l.id
					WHERE c.docNr='".$crow['docNr']."'
				");
	$allL = mysqli_fetch_array($linesLocation);
	
	$locations = $allL['locations'];
	
	
	
	
	$companyWho = getCustInfo($conn, 'Name', $crow['clientCode']);
	
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<title>Akts</title>
	<script src="js/jquery-3.2.1.js"></script>
	</head>
	<body>
	
	
	
	
	<style>
	body {
		margin: 0 auto; 
	}
	page {
		
		background: white;
		display: block;
		margin: 0 auto;
		margin-bottom: 0.5cm;
	}
	page[size="A4"] {  
	
		width: 21cm;
		height: 29.7cm; 
	}
	page[size="A4"][layout="portrait"] {
	
		width: 29.7cm;
		height: 21cm;  
	}
	@media print {
		body, page {
			margin: 0;
			box-shadow: 0;
		}
	}
	
	table, th, td {
			padding: 3px 8px;
			border: 1px solid #666;
			border-collapse: collapse;
		font-size: 13px;
	}
	
	th.invalid, td.invalid {
			border: 1px double #666;
			padding:0 !important;
			margin:0 !important;
	}
	
	#table {
			max-width: 2480px;
			width:100%;
		font-size:	15px;
	}
	
	
	.border-less {
			border-top: 1px solid #FFFFFF;
			border-left: 1px solid #FFFFFF;
	}
	.no-border {
			border: 1px solid #FFFFFF;
	}
	.no-border-all {
			border: 1px double #FFFFFF;
	}
	.side-border {
		border-top: 1px solid #FFFFFF;
		border-bottom: 1px solid #FFFFFF;
			border-left: 1px double #666;
		border-right: 1px solid #666;
	}
	#page td {
		 padding:0; margin:0;
	}
	
	#page {
		 border-collapse: collapse;
	}
	
	.secondt {
		border-collapse: collapse;
		border-style: hidden;
	}
	.center {
			text-align:center;
	}
	.nowrap {
		white-space:nowrap;
	}
	
	table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
	table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
	</style>
	
	
	<page size="A4">
		<div style="">
	<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
	
	<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p> <h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
	PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
	</center></div>
	
		<table width="100%" style="height: 100%;">
			<tr >
				<td class="border-less" >
					<p>TEL <?=$companyPhoneNo;?><br>
					<?=$companyEmail;?></p>
				</td>
				<td width="40%" class="center nowrap">
					ĢENERĀLĀS KRAVAS<br>PIEŅEMŠANAS AKTS
				</td>
				<td width="10%">
					Numurs:<br><br><center><?=$actNo;?></center>
				</td>			
			</tr>
			<tr id="page" width="30%">
				<td width="40%" rowspan="5">
						&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
						&nbsp;&nbsp;vārdā pieņemot kravu:
						<br>
						<br>
						<br>
						<br>
						<br>
						&nbsp;&nbsp;PARAKSTS:<br>
						&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
				</td>		
			</tr>
			<tr>
				<td width="40%">
					Firma / Pieteikuma izdevējs (vārds, uzvārds)
				</td>
				<td width="20%">
					Datums
				</td>	
			</tr>		
			<tr>
				<td height="30px" class="center nowrap" width="40%">
					<?=$companyWho;?>
				</td>
				<td  height="30px" class="center nowrap" width="10%">
					<?=$deliveryDate;?>
				</td>
			</tr>
	
			<tr>
				<td width="40%">
					Pieteikums Nr. / Datums
				</td>
				<td width="10%">
					Eksp / Imp. / Tranzīts&nbsp;
				</td>
			</tr>
	
			<tr>
				<td  height="40px" class="nowrap" width="40%">
				<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$applicationDate;?></div>
				</td>
				<td  height="40px" class="center nowrap" width="10%">
					<?=$cargoStatus;?>
				</td>
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border" width="25%">
							&nbsp;&nbsp;Līnijas aģents / Līnija
							</td>
							<td class="side-border" width="40%">
							&nbsp;&nbsp;Kuģis
							</td>						
	
							<td class="no-border" width="35%">
							&nbsp;&nbsp;Reisa Nr.
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%">					
					&nbsp;&nbsp;Izkraušanas datums
				</td>			
			</tr>
			
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border center nowrap" width="25%">
							----
							</td>
							<td class="side-border center nowrap" width="40%">
							<?=$ship;?>
							</td>						
	
							<td class="no-border center nowrap" width="35%">
							----
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center nowrap">					
				<?=$deliveryDate;?>
				</td>			
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border center nowrap" width="25%">
							Gala osta
							</td>
							<td class="side-border center nowrap" width="40%">
							Operācija
							</td>						
	
							<td class="no-border center nowrap" width="35%">
							Transporta veids
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center nowrap" >					
					Auto/Dzc.platformas Nr.
				</td>			
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
						<table width="100%" style="height:100%;">
						<tr>
							<td class="no-border center nowrap" width="25%">
							----
							</td>
							<td class="side-border center nowrap" width="40%">
								PIEŅEMŠANA
							</td>						
	
							<td class="no-border center nowrap" width="35%">
								<?=$transport;?>
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center ">					
					<?=$thisTransport;?>
				</td>			
			</tr>		
	
			<tr id="page" >
				<td width="70%" colspan="2">
					<table width="100%" id="itable1" class="itable1">
						<tr>
							<td class="no-border center" width="4%">
							B/L<br>Nr.
							</td>
							<td class="side-border center" width="54.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
							Kravas nosaukums
							</td>
							<td class="no-border center" width="6%">
							Vietu<br>skaits
							</td>
							<td class="side-border center" width="20%">
							Brt. svars, <?=$weightFormat;?>
							</td>
							<td class="no-border center" width="15%">
							Net. svars, <?=$weightFormat;?>
							</td>						
						</tr>
						
					</table>
				</td>
				<td width="30%" class="center">
					Piezīmes:
				</td>
				
			</tr>
			
			
			
			
			
			
			<tr id="page" >
				<td width="70%" colspan="2">
					<table width="100%" id="itable2" class="itable2">
	
					<tr>
	<?php
	
		$prodQuery = mysqli_query($conn, "
			SELECT l.thisTransport, l.productNr, l.serialNo,  
			l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.place_count,
			l.gross, l.net, h.transport
			FROM cargo_header AS h
			JOIN cargo_line AS l
			ON h.docNr=l.docNr
			JOIN n_items AS n
			ON l.productNr=n.code
			WHERE h.id='".intval($id)."'
			order by l.productNr, l.thisTransport
		");
		$number = mysqli_num_rows($prodQuery);
		$i = 1;
	
		$place_count = $gross = $net = $lastProd = $lastTransp = null;
		while($pr_row = mysqli_fetch_array($prodQuery)){
			$place_count += $pr_row['place_count'];
			$gross += $pr_row['gross'];
			$net += $pr_row['net'];
	
			
	?>
					
	
					
					<td class="no-border center" width="4%">
												
					</td>
					<td class="side-border center" width="54.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
						
						<?php
						if($lastProd!=$pr_row['productNr']){
							echo $pr_row['name'].'<br>';
						}
						$ltr=ucfirst($pr_row['transport']).' Nr. '.$pr_row['thisTransport'];
						if($lastTransp!=$ltr){
							echo ucfirst($pr_row['transport']).' Nr. '.$pr_row['thisTransport'].'<br>';
						}
						echo $pr_row['serialNo'];
						?>	
	
						<?php if($number==$i){ ?><br><br><br><br>Kopā: <?php } ?>
					</td>
					<td class="no-border center" width="6%" style="vertical-align:top">				
						<?php
						
						 if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
						 if($lastTransp!=$ltr){ echo '<br>'; }						 
							echo $pr_row['place_count'];
						
						?>	
						<?php if($number==$i){ ?><br><br><br><br><?php echo $place_count; } ?>		
					</td>
					<td class="side-border center" width="20%" style="vertical-align:top">				
						<?php
							
							if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							if($lastTransp!=$ltr){ echo '<br>'; }						
							echo floatval($pr_row['gross']);
	
						?>
						<?php if($number==$i){ ?><br><br><br><br><?php echo $gross; } ?>
					</td>
					<td class="no-border center" width="15%" style="vertical-align:top">
						<?php
							
							if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							if($lastTransp!=$ltr){ echo '<br>'; }						
							echo floatval($pr_row['net']);
							
						?>
						<?php if($number==$i){ echo '<br><br><br><br>'.$net; } ?>					
					</td>					
				</tr>
	
	<?php 
	
	$i++;
	$lastProd = $pr_row['productNr'];
	$lastTransp = ucfirst($pr_row['transport']).' Nr. '.$pr_row['thisTransport'];
	 } ?>
	

					</table>
				</td>
				<td width="30%" class="nowrap" style="vertical-align:top">
				<br>
					<center>				
						Visa krava pieņemta<br>noliktavā:<br>
						<?=$locations;?>
						&nbsp;
					</center>
				</td>			
			</tr>		
	
			
			
			
			<tr>
				<td colspan="3">Krava pieņemta saskaņā ar pavaddokumentiem bez pārsvēršanas</td>
			</tr>

			<tr id="page" width="30%">
				<td colspan="3" >
					<table width="100%" rules="all" class="secondt">
	
						<tr>
						<td height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
						<td colspan="2" rowspan="2" height="100"><center><?=$companyWho;?></center><br>
										<br>
										<br>
									
										<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
										<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
										<br>&nbsp;&nbsp;Pilnvarotā persona pieņemot kravu terminālam
										</td>
						</tr>
						<tr>
							<td height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
						</tr>
						<tr>
							
							<td rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
						
							<td rowspan="2" colspan="2">&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
							
						</tr>
					</table>
				</td>
			</tr>			
			
		</table>
		</div>
	</page>
	
	</body>
	</html>	
	
	<?php } 
	


	if(!$view && $form==2){
	
		$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
		
		
		$cargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
		$crow = mysqli_fetch_array($cargo);	
		
		$lastBy = returnMeWho($crow['lastBy']);
		$actNo  = $crow['acceptance_act_no'];
		$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
		
		$cargoStatus = null;
		if($crow['cargo_status']=='C'){
			$cargoStatus = 'EKSPORTS';
		}	
		if($crow['cargo_status']=='N'){
			$cargoStatus = 'TRANZĪTS';
		}
		if($crow['cargo_status']=='EU'){
			$cargoStatus = 'EU';
		}
		
		
		$applicationNo  = $crow['application_no'];
		$landingDate = date('d.m.Y', strtotime($crow['landingDate']));
		$applicationDate = date('d.m.Y', strtotime($crow['application_date']));
		
		$transport = mb_strtoupper($crow['transport'], 'UTF-8');
		$ship  = $crow['ship'];
		
		$linesTransport = mysqli_query($conn, "SELECT GROUP_CONCAT(DISTINCT(thisTransport) separator ', ') AS transports FROM cargo_line WHERE docNr='".$crow['docNr']."'");
		$allT = mysqli_fetch_array($linesTransport);
		
		$thisTransport = $allT['transports'];
		
		
		$linesLocation = mysqli_query($conn, "
						SELECT 
		
						GROUP_CONCAT(
							DISTINCT CONCAT(c.location,' - ',l.name) 
							SEPARATOR '<br>'
						) AS locations
		
						FROM cargo_line AS c
						LEFT JOIN n_location AS l
						ON c.location=l.id
						WHERE c.docNr='".$crow['docNr']."'
					");
		$allL = mysqli_fetch_array($linesLocation);
		
		$locations = $allL['locations'];
		
			$getShips = mysqli_query($conn, "
				SELECT l.declaration_type_no, l.declaration_type_no as declaration_type_no2
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
	
				WHERE c.id='".intval($id)."'
			") or die(mysqli_error($conn));
			
		$shipsIn = $shipsOut = $decNo = array();
		while($ships = mysqli_fetch_array($getShips)){
			
				$decNo[] = $ships['declaration_type_no'];
				$decNo[] = $ships['declaration_type_no2'];
	
		}
		
		$decNo = array_unique($decNo);
		
	
		$issue_declaration_type_no = implode(',<br>', $decNo);
		
		
		$companyWho = getCustInfo($conn, 'Name', $crow['clientCode']);
		
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>		
		
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
		
		table, th, td {
				padding: 3px 8px;
				border: 1px solid #666;
				border-collapse: collapse;
			font-size: 13px;
		}
		
		th.invalid, td.invalid {
				border: 1px double #666;
				padding:0 !important;
				margin:0 !important;
		}
		
		#table {
				max-width: 2480px;
				width:100%;
			font-size:	15px;
		}
		
		
		.border-less {
				border-top: 1px solid #FFFFFF;
				border-left: 1px solid #FFFFFF;
		}
		.no-border {
				border: 1px solid #FFFFFF;
		}
		.no-border-all {
				border: 1px double #FFFFFF;
		}
		.side-border {
			border-top: 1px solid #FFFFFF;
			border-bottom: 1px solid #FFFFFF;
				border-left: 1px double #666;
			border-right: 1px solid #666;
		}
		#page td {
			 padding:0; margin:0;
		}
		
		#page {
			 border-collapse: collapse;
		}
		
		.secondt {
			border-collapse: collapse;
			border-style: hidden;
		}
		.center {
				text-align:center;
		}
		.nowrap {
			white-space:nowrap;
		}
		
		table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
		table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
		
		
	#wrapper_1 {
		float:left;
		text-align: center;
		width: 50%;
		height: 100%;
	}
	
	#wrapper_2 {
		float:right;
		text-align: center;
		width: 50%;
		height: 100%;
	}
	
	.seperator {
	  height: 100%;
	  width: 1px;
	  background: #666;
	  margin: 0 auto;
	  top: 0;
	  bottom: 0;
	  position: absolute;
	  left: 50%;
	}
	
	.container {
	  position: relative;
	  height: 100%;
	}
	
	.clearfix {
	  clear: both;
	}	
		</style>
		
		
		<page size="A4">
			<div style="">
		<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
		
		<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
		PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
		</center></div>
		
		
			<table width="100%" style="height:100%;">
				<tr >
					<td class="border-less" >
						<p>TEL <?=$companyPhoneNo;?><br>
						<?=$companyEmail;?></p>
					</td>
					<td width="40%" class="center nowrap">
						ĢENERĀLĀS KRAVAS<br>PIEŅEMŠANAS AKTS
					</td>
					<td width="10%">
						Numurs:<br><br><center><?=$actNo;?></center>
					</td>			
				</tr>
				<tr id="page" width="30%">
					<td width="40%" rowspan="5">
							&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
							&nbsp;&nbsp;vārdā pieņemot kravu:
							<br>
							<br>
							<br>
							<br>
							<br>
							&nbsp;&nbsp;PARAKSTS:<br>
							&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
					</td>		
				</tr>
				<tr>
					<td width="40%">
						Firma / Pieteikuma izdevējs (vārds, uzvārds)
					</td>
					<td width="20%">
						Datums
					</td>	
				</tr>		
				<tr>
					<td height="30px" class="center nowrap" width="40%">
						<?=$companyWho;?>
					</td>
					<td  height="30px" class="center nowrap" width="10%">
						<?=$deliveryDate;?>
					</td>
				</tr>
		
				<tr>
					<td width="40%">
						Pieteikums Nr. / Datums
					</td>
					<td width="10%">
						Eksp / Imp. / Tranzīts&nbsp;
					</td>
				</tr>
		
				<tr>
					<td  height="40px" class="nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$applicationDate;?></div>
					</td>
					<td  height="40px" class="center nowrap" width="10%">
						<?=$cargoStatus;?>
					</td>
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border" width="25%">
								&nbsp;&nbsp;Līnijas aģents / Līnija
								</td>
								<td class="side-border" width="40%">
								&nbsp;&nbsp;Kuģis
								</td>						
		
								<td class="no-border" width="35%">
								&nbsp;&nbsp;Reisa Nr.
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%">					
						&nbsp;&nbsp;Izkraušanas datums
					</td>			
				</tr>
				
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
								<?=$ship;?>
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								----
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
					<?=$deliveryDate;?>
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								Gala osta
								</td>
								<td class="side-border center nowrap" width="40%">
								Operācija
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								Transporta veids
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap" >					
						Auto/Dzc.platformas Nr.
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
						<table width="100%" style="height:100%;">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
									PIEŅEMŠANA
								</td>						
		
								<td class="no-border center nowrap" width="35%">
									<?=$transport;?>
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center ">					
						<?=$thisTransport;?>
					</td>			
				</tr>		
		
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable1" class="itable1">
							<tr>
								<td class="no-border center" width="7%">
								Nr. p/k
								</td>
								<td class="side-border center"  colspan="2" width="35.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
								Kravas nosaukums
								</td>
								<td class="no-border center" width="22.5%">
								
								</td>
								<td class="side-border center" width="20%">
								Brt. svars, T
								</td>
								<td class="no-border center" width="15%">
								
								</td>						
							</tr>
							
						</table>
					</td>
					<td width="30%" class="center">
						Piezīmes:
					</td>
					
				</tr>
				
				
				
				
				
				
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable2" class="itable2">
		
						<tr>
		<?php
		
			$prodQuery = mysqli_query($conn, "
				SELECT l.thisTransport, l.productNr, l.serialNo,  
				l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.place_count,
				l.gross, l.net, l.batchNo, h.transport, l.amount, l.resource, nr.name as resource_name
				FROM cargo_header AS h
				JOIN cargo_line AS l
				ON h.docNr=l.docNr
				JOIN n_items AS n
				ON l.productNr=n.code
				
				JOIN n_resource AS nr
				ON l.resource=nr.id			
				
				WHERE h.id='".intval($id)."'
				order by l.productNr, l.thisTransport
			") or die (mysqli_error($conn));
			$number = mysqli_num_rows($prodQuery);
			$i = 1;
		
			$place_count = $gross = $net = $lastProd = $lastTransp = null;
			$batchNo = array();
			$batchNoA = array();
			while($pr_row = mysqli_fetch_array($prodQuery)){
				$place_count += $pr_row['place_count'];
				$amount += $pr_row['amount'];
				$gross += $pr_row['gross'];
				$net += $pr_row['net'];
		
				
		?>
						
		
						
						<td class="no-border center" width="7%">
							<?php
							
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								
								echo $i.'<br>';
								array_push($batchNo, $pr_row['batchNo']);
								$batchNoA[$pr_row['batchNo']] += $pr_row['amount'];
								if($number==$i){ ?><br><br><br><br><br><?php echo ''; } 
							
							?>					
						</td>
						<td class="side-border center" colspan="2" width="35.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
														
							<?php 
							if($lastProd!=$pr_row['productNr']){ echo '<br>'; }

							echo $pr_row['productNr']; 
							?>		
							
							
							
							
						</td>
						<td class="no-border center" width="22.5%" style="vertical-align:top">					

							<?php		
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }						
								echo $pr_row['resource_name'];
							?>							

						</td>
						<td class="side-border center" width="20%" style="vertical-align:top">				
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
												
								echo floatval($pr_row['amount']);
		
							?>
							
							
							<?php if($number==$i){ ?><br><br><?php 
							
							$goal = count(array_count_values($batchNo));
							if($goal>0){
								for($k=0; $k<=$goal; $k++){
									echo '<br>';
								}
							}						
							
							echo $amount; } ?>
						</td>
						<td class="no-border center" width="15%" style="vertical-align:top">
						<? if($lastProd!=$pr_row['productNr']){ echo '<br>'; } ?>
						bez pārsvēršanas
						</td>					
					</tr>
		
		<?php 
		
		$i++;
		$lastProd = $pr_row['productNr'];
		$lastTransp = ucfirst($pr_row['transport']).' Nr. '.$pr_row['thisTransport'];
		 } ?>
		
	
						</table>
					</td>
					<td width="30%" class="nowrap" style="vertical-align:top">
					<br>
						<center>				
							Visa krava pieņemta<br>noliktavā:<br>
							<?=$locations;?>
							<br>
							<?=$issue_declaration_type_no;?>
							&nbsp;
						</center>
					</td>			
				</tr>		
		
				
				
				
				<tr>
					<td colspan="3">Krava pieņemta saskaņā ar pavaddokumentiem bez pārsvēršanas</td>
				</tr>
	
				<tr id="page" width="30%">
					<td colspan="3" >
						<table width="100%" rules="all" class="secondt">
		
							<tr>
							<td height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
							<td colspan="2" rowspan="2" height="100"><center><?=$companyWho;?></center><br>
											<br>
											<br>
										
											<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
											<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
											<br>&nbsp;&nbsp;Pilnvarotā persona pieņemot kravu terminālam
											</td>
							</tr>
							<tr>
								<td height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
							</tr>
							<tr>
								
								<td rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
							
								<td rowspan="2" colspan="2">&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
								
							</tr>
						</table>
					</td>
				</tr>			
				
			</table>
			</div>
		</page>
		
		</body>
		</html>	
		
		<?php }


	if(!$view && $form==4){
	
	$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
	$cargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$crow = mysqli_fetch_array($cargo);	
	
	$lastBy = returnMeWho($crow['lastBy']);
	$actNo  = $crow['acceptance_act_no'];
	$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
	
	$cargoStatus = null;
	if($crow['cargo_status']=='C'){
		$cargoStatus = 'EKSPORTS';
	}	
	if($crow['cargo_status']=='N'){
		$cargoStatus = 'TRANZĪTS';
	}
	if($crow['cargo_status']=='EU'){
		$cargoStatus = 'EU';
	}
	
	
	$applicationNo  = $crow['application_no'];
	$landingDate = date('d.m.Y', strtotime($crow['landingDate']));
	$applicationDate = date('d.m.Y', strtotime($crow['application_date']));
	
	$transport = mb_strtoupper($crow['transport'], 'UTF-8');
	$ship  = $crow['ship'];
	
	$linesTransport = mysqli_query($conn, "SELECT GROUP_CONCAT(DISTINCT(thisTransport) separator ', ') AS transports FROM cargo_line WHERE docNr='".$crow['docNr']."'");
	$allT = mysqli_fetch_array($linesTransport);
	
	$thisTransport = $allT['transports'];
	
	
	$linesLocation = mysqli_query($conn, "
					SELECT 
	
					GROUP_CONCAT(
						DISTINCT CONCAT(c.location,' - ',l.name) 
						SEPARATOR '<br>'
					) AS locations
	
					FROM cargo_line AS c
					LEFT JOIN n_location AS l
					ON c.location=l.id
					WHERE c.docNr='".$crow['docNr']."'
				");
	$allL = mysqli_fetch_array($linesLocation);
	
	$locations = $allL['locations'];
	
	
	
	
	$companyWho = getCustInfo($conn, 'Name', $crow['clientCode']);
	
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<title>Akts</title>
			<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
				
			<style>
			body {
				margin: 0 auto; 
			}
			page {
				
				background: white;
				display: block;
				margin: 0 auto;
				margin-bottom: 0.5cm;
			}
			page[size="A4"] {  
			
				width: 21cm;
				height: 29.7cm; 
			}
			page[size="A4"][layout="portrait"] {
			
				width: 29.7cm;
				height: 21cm;  
			}
			@media print {
				body, page {
					margin: 0;
					box-shadow: 0;
				}
			}
			
			table, th, td {
					padding: 3px 8px;
					border: 1px solid #666;
					border-collapse: collapse;
				font-size: 13px;
			}
			
			th.invalid, td.invalid {
					border: 1px double #666;
					padding:0 !important;
					margin:0 !important;
			}
			
			#table {
					max-width: 2480px;
					width:100%;
				font-size:	15px;
			}
			
			
			.border-less {
					border-top: 1px solid #FFFFFF;
					border-left: 1px solid #FFFFFF;
			}
			.no-border {
					border: 1px solid #FFFFFF;
			}
			.no-border-all {
					border: 1px double #FFFFFF;
			}
			.side-border {
				border-top: 1px solid #FFFFFF;
				border-bottom: 1px solid #FFFFFF;
					border-left: 1px double #666;
				border-right: 1px solid #666;
			}
			#page td {
				 padding:0; margin:0;
			}
			
			#page {
				 border-collapse: collapse;
			}
			
			.secondt {
				border-collapse: collapse;
				border-style: hidden;
			}
			.center {
					text-align:center;
			}
			.nowrap {
				white-space:nowrap;
			}
			
			table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
			table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
			</style>
		
		
			<page size="A4">
				<div style="">
					<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
			
					<div style="padding-left: 140px;">
						<center>
							<p style="display: inline-block;"></p>
							<h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
							PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
						</center>
					</div>
			
					<table width="100%" style="height: 100%;">
						<tr >
							<td class="border-less" >
								<p>TEL <?=$companyPhoneNo;?><br>
								<?=$companyEmail;?></p>
							</td>
							<td width="40%" class="center nowrap">
								<b>ĢENERĀLĀS KRAVAS<br>PIEŅEMŠANAS AKTS</b>
							</td>
							<td width="10%">
								Numurs:<br><br><center><b><?=$actNo;?></b></center>
							</td>			
						</tr>
						<tr id="page" width="30%">
							<td width="40%" rowspan="5">
									&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
									&nbsp;&nbsp;vārdā pieņemot kravu:
									<br>
									<br>
									<br>
									<br>
									<br>
									&nbsp;&nbsp;PARAKSTS:<br>
									&nbsp;&nbsp;VĀRDS, UZVĀRDS: <b><?=$lastBy;?></b>
							</td>		
						</tr>
						<tr>
							<td width="40%">
								Firma / Pieteikuma izdevējs (vārds, uzvārds)
							</td>
							<td width="20%">
								Pieņemšanas datums
							</td>	
						</tr>		
						<tr>
							<td height="30px" class="center nowrap" width="40%">
								<b><?=$companyWho;?></b>
							</td>
							<td  height="30px" class="center nowrap" width="10%">
								<b><?=$deliveryDate;?></b>
							</td>
						</tr>
				
						<tr>
							<td width="40%">
								Pieteikums Nr. / Datums
							</td>
							<td width="10%">
								Eksp / Imp. / Tranzīts&nbsp;
							</td>
						</tr>
				
						<tr>
							<td  height="40px" class="nowrap" width="40%">
							<b><?=$applicationNo;?></b> <div style="display: inline-block; margin-left: 70px;"><?=$applicationDate;?></div>
							</td>
							<td  height="40px" class="center nowrap" width="10%">
								<?=$cargoStatus;?>
							</td>
						</tr>
				
						<tr id="page" width="30%">
							<td width="70%" colspan="2">
								<table width="100%">
									<tr>
										<td class="no-border" width="25%">
										&nbsp;&nbsp;Līnijas aģents / Līnija
										</td>
										<td class="side-border" width="40%">
										&nbsp;&nbsp;Kuģis
										</td>						
				
										<td class="no-border" width="35%">
										&nbsp;&nbsp;Reisa Nr.
										</td>						
									</tr>
								</table>
							</td>
							<td width="30%">					
								&nbsp;&nbsp;Izkraušanas datums
							</td>			
						</tr>
						
						<tr id="page" width="30%">
							<td width="70%" colspan="2">
								<table width="100%">
									<tr>
										<td class="no-border center nowrap" width="25%">
										----
										</td>
										<td class="side-border center nowrap" width="40%">
										<?=$ship;?>
										</td>						
				
										<td class="no-border center nowrap" width="35%">
										----
										</td>						
									</tr>
								</table>
							</td>
							<td width="30%" class="center nowrap">					
							<?=$deliveryDate;?>
							</td>			
						</tr>
				
						<tr id="page" width="30%">
							<td width="70%" colspan="2">
								<table width="100%">
									<tr>
										<td class="no-border center nowrap" width="25%">
										Gala osta
										</td>
										<td class="side-border center nowrap" width="40%">
										Operācija
										</td>						
				
										<td class="no-border center nowrap" width="35%">
										Transporta veids
										</td>						
									</tr>
								</table>
							</td>
							<td width="30%" class="center nowrap" >					
								Auto/Dzc.platformas Nr.
							</td>			
						</tr>
				
						<tr id="page" width="30%">
							<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
									<table width="100%" style="height:100%;">
									<tr>
										<td class="no-border center nowrap" width="25%">
										----
										</td>
										<td class="side-border center nowrap" width="40%">
											<b>PIEŅEMŠANA</b>
										</td>						
				
										<td class="no-border center nowrap" width="35%">
											<b><?=$transport;?></b>
										</td>						
									</tr>
								</table>
							</td>
							<td width="30%" class="center ">					
								
							</td>			
						</tr>		
				
						<tr id="page" >
							<td width="70%" colspan="2">
								<table width="100%" id="itable1" class="itable1">
									<tr>
										<td class="no-border center" width="4%">
										B/L<br>Nr.
										</td>
										<td class="side-border center" width="40.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
										Kravas nosaukums
										</td>
										<td class="no-border center" width="20%">
										Auto/Dzc.platformas Nr.
										</td>
										<td class="side-border center" width="20%">
										Brt. svars, <?=$weightFormat;?>
										</td>
										<td class="no-border center" width="15%">
										Net. svars, <?=$weightFormat;?>
										</td>						
									</tr>
									
								</table>
							</td>
							<td width="30%" class="center">
								Piezīmes:
							</td>
							
						</tr>
						
						
						
						
						
						
						<tr id="page" >
							<td width="70%" colspan="2">
								<table width="100%" id="itable2" class="itable2">
				
								
									<?php
								
									$prodQuery = mysqli_query($conn, "
										SELECT l.thisTransport, l.productNr,  
										CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr,
										l.gross, l.net, nr.name as resource_name
										FROM cargo_header AS h
										JOIN cargo_line AS l
										ON h.docNr=l.docNr
										JOIN n_items AS n
										ON l.productNr=n.code
										
											JOIN n_resource AS nr
											ON l.resource=nr.id				
										
										WHERE h.id='".intval($id)."'
										order by l.productNr, l.thisTransport
									");
									$number = mysqli_num_rows($prodQuery);
									$i = 1;
								
									$gross = $net = $lastProd = $lastThisTransport = $lastProdName = $whileDataA =  $whileDataB =  $whileDataC =  $whileDataD = null;
									$whileDataAf =  $whileDataBf =  $whileDataCf =  $whileDataDf = null;
									$extraBr = 0;

									$tableArr = [];
									while($pr_row = mysqli_fetch_array($prodQuery)){
										
										$gross += $pr_row['gross'];
										$net += $pr_row['net'];
										$lastProdName = $pr_row['name'];
										
										?>
															
																		
											
											<?php
											if($lastProd!=$pr_row['productNr']){ $whileDataA .= '<br>'; }
											if($lastProd!=$pr_row['productNr']){
												$whileDataA .= '<b>'.$pr_row['name'].'</b><br>';
												
												$actDocumentAmmounts = null;
												$actDocumentAmmounts = actDocumentAmmounts($conn, $id, $pr_row['productNr']);

												$isLines = null;
												$isLines = $actDocumentAmmounts['max'];
												$isLines = $isLines - 1;

													if($actDocumentAmmounts['gross']){
														$whileDataA .= $actDocumentAmmounts['gross'];
														$isLines = $isLines - 1;
														$tableArr[$pr_row['name']]['A'][] = $actDocumentAmmounts['gross'];
														
													}else{
														
													}
													if($actDocumentAmmounts['net']){
														$whileDataA .= $actDocumentAmmounts['net'];
														$isLines = $isLines - 1;
													
														$tableArr[$pr_row['name']]['A'][] = $actDocumentAmmounts['net'];
													}else{
														
													}

												 if($isLines>0){
													$whileDataA .= str_repeat("<br>", $isLines);
												 }

											}else{

												if($pr_row['gross']>0 || $pr_row['net']>0){
													
												}

											}
											
											?>	
						
											<?php 
												if($number==$i){ 
													$addBr = null;
													if($extraBr==0){
														
													}else{
														
													}
													$whileDataAf .= $addBr.'Kopā:'; 
												} 
											?>
														
											<?php
											 
											 if($lastProd!=$pr_row['productNr']){ 
												$whileDataB .= '<br>';
											 }
											 if($lastProdName==$pr_row['name']){ 
												$whileDataB .= '<b>'.$pr_row['thisTransport'].'</b><br>';
												$tableArr[$pr_row['name']]['B'][] = '<b>'.$pr_row['thisTransport'].'</b>';


											 }else{
												 $whileDataB .= '<br>';
											 }
											 
											 if($lastProdName==$pr_row['name'] && !$pr_row['thisTransport']){
												
											 }
												
												if($lastProd!=$pr_row['productNr']){ $whileDataC .= '<br>'; }
																	
												if($pr_row['gross']>0){ 
													$whileDataC .= '<b>'.floatval($pr_row['gross']).'</b><br>';
													$tableArr[$pr_row['name']]['C'][] = '<b>'.floatval($pr_row['gross']).'</b>';
												}
											?>
											
											<?php if($number==$i && $gross!=0){ $whileDataCf .= '<b>'.$gross.'</b>'; } ?>
									
										
											<?php
												
												if($lastProd!=$pr_row['productNr']){ $whileDataD .= '<br>'; }
																	
												if($pr_row['net']>0){ 
													$whileDataD .= '<b>'.floatval($pr_row['net']).'</b><br>'; 
													$tableArr[$pr_row['name']]['D'][] = '<b>'.floatval($pr_row['net']).'</b>';
												}
												
											?>
											<?php if($number==$i && $net!=0){ $whileDataDf .= '<b>'.$net.'</b>'; } ?>					
								
				
								<?php 
				
										$i++;
										$lastProd = $pr_row['productNr'];
										$lastThisTransport = $pr_row['thisTransport'];
										
									 } 
									 
									 if($whileDataA || $whileDataB || $whileDataC || $whileDataD){

									 }

									 
									 if(COUNT($tableArr)>0){

										foreach($tableArr AS $key => $val){


											echo '<tr>';

												
													echo '<td class="no-border center" width="4%"></td>';
													echo '<td class="side-border center" width="40.6%" style="vertical-align:top"><br>';
														echo '<b>'.$key.'</b><br>';
														foreach($val['A'] AS $aKey => $aData){
															if(isset($aData)){
																echo $aData.'<br>';
															}
														}
													echo '</td>';
													echo '<td class="no-border center" width="20%" style="vertical-align:top"><br>';
														foreach($val['B'] AS $bKey => $bData){
															echo $bData.'<br>';
														}
													echo '</td>';
													echo '<td class="side-border center" width="20%" style="vertical-align:top"><br>';
														foreach($val['C'] AS $cKey => $cData){
															echo $cData.'<br>';
														}
													echo '</td>';
													echo '<td class="no-border center" width="15%" style="vertical-align:top"><br>';
														foreach($val['D'] AS $dKey => $dData){
															echo $dData.'<br>';
														}
													echo '</td>';
																							
											echo '</tr>';

										}

									 }
									 

									 if($whileDataAf || $whileDataBf || $whileDataCf || $whileDataDf){
										echo '
										<tr>
											<td class="no-border center" width="4%">
												<br>
											</td>

											<td class="side-border center" width="40.6%" style="vertical-align:top">								
												<br>'.$whileDataAf.'
											</td>

											<td class="no-border center" width="20%" style="vertical-align:top">
												<br>'.$whileDataBf.'
											</td>

											<td class="side-border center" width="20%" style="vertical-align:top">				
												<br>'.$whileDataCf.'
											</td>
											<td class="no-border center" width="15%" style="vertical-align:top">
												<br>'.$whileDataDf.'
											</td>					
										</tr>';
									 }
									 
									 ?>
				

								</table>
							</td>
							<td width="30%" class="nowrap" style="vertical-align:top">
							<br>
								<center>				
									Visa krava pieņemta<br>noliktavā:<br>
									<?=$locations;?>
									&nbsp;
								</center>
							</td>			
						</tr>		
				
						
						
						
						<tr>
							<td colspan="3"><b>Krava pieņemta saskaņā ar pavaddokumentiem.<b><br><b>Pretenzijas par kravu, daudzumu unkvalitāti tiek pieņemtas tikai pieņemšanas.</b></td>
						</tr>

						<tr id="page" width="30%">
							<td colspan="3" >
								<table width="100%" rules="all" class="secondt">
									
									<tr>
										<td align="center">PILNVARAS DATI</td>
										
										<td colspan="2" rowspan="3" height="100">
													&nbsp;PIEZĪMES
													<br><br>
													<center><?=$companyWho;?></center><br>
													<br><br>
													
												
													<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
													<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
										</td>
										
									</tr>
									<tr><td height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td></tr>
									<tr>
										<td height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
									</tr>
									<tr>
										
										<td rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
									
										<td rowspan="2" colspan="2">&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
										
									</tr>
								</table>
							</td>
						</tr>			
						
					</table>
				</div>
			</page>
		
		</body>
	</html>	
		
	<?php 
}		
	
//forma 3 VTO ČUGUNS Auto/Dzc.platformas Nr.
if(!$view && $form==3){
	
	$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
	$cargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE id='".intval($id)."'");
	$crow = mysqli_fetch_array($cargo);	
	
	$lastBy = returnMeWho($crow['lastBy']);
	$actNo  = $crow['acceptance_act_no'];
	$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
	$actDate = date('d.m.Y', strtotime($crow['acceptance_act_date']));
	
	$cargoStatus = null;
	if($crow['cargo_status']=='C'){
		$cargoStatus = 'EKSPORTS';
	}	
	if($crow['cargo_status']=='N'){
		$cargoStatus = 'TRANZĪTS';
	}
	if($crow['cargo_status']=='EU'){
		$cargoStatus = 'EU';
	}
	
	
	$applicationNo  = $crow['application_no'];
	$landingDate = date('d.m.Y', strtotime($crow['landingDate']));
	$applicationDate = date('d.m.Y', strtotime($crow['application_date']));
	
	$transport = mb_strtoupper($crow['transport'], 'UTF-8');
	$ship  = $crow['ship'];
	
	$linesTransport = mysqli_query($conn, "SELECT GROUP_CONCAT(DISTINCT(thisTransport) separator ', ') AS transports FROM cargo_line WHERE docNr='".$crow['docNr']."'");
	$allT = mysqli_fetch_array($linesTransport);
	
	$thisTransport = $allT['transports'];
	
	
	$linesLocation = mysqli_query($conn, "
					SELECT 
	
					GROUP_CONCAT(
						DISTINCT CONCAT(c.location,' - ',l.name) 
						SEPARATOR '<br>'
					) AS locations
	
					FROM cargo_line AS c
					LEFT JOIN n_location AS l
					ON c.location=l.id
					WHERE c.docNr='".$crow['docNr']."'
				");
	$allL = mysqli_fetch_array($linesLocation);
	
	$locations = $allL['locations'];
	
		$getShips = mysqli_query($conn, "
			SELECT l.declaration_type_no, l.declaration_type_no as declaration_type_no2
			FROM cargo_header AS c
			LEFT JOIN cargo_line AS l
			ON c.docNr=l.docNr

			WHERE c.id='".intval($id)."'
		") or die(mysqli_error($conn));
		
	$shipsIn = $shipsOut = $decNo = array();
	while($ships = mysqli_fetch_array($getShips)){
		
			$decNo[] = $ships['declaration_type_no'];
			$decNo[] = $ships['declaration_type_no2'];

	}
	
	$decNo = array_unique($decNo);
	

	$issue_declaration_type_no = implode(',<br>', $decNo);
	
	
	$companyWho = getCustInfo($conn, 'Name', $crow['ownerCode']);
	
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<title>Akts</title>
	<script src="js/jquery-3.2.1.js"></script>
	</head>
	<body>
	
	
	<style>
	body {
		margin: 0 auto; 
	}
	page {
		
		background: white;
		display: block;
		margin: 0 auto;
		margin-bottom: 0.5cm;
	}
	page[size="A4"] {  
	
		width: 21cm;
		height: 29.7cm; 
	}
	page[size="A4"][layout="portrait"] {
	
		width: 29.7cm;
		height: 21cm;  
	}
	@media print {
		body, page {
			margin: 0;
			box-shadow: 0;
		}
	}
	
	table, th, td {
			padding: 3px 8px;
			border: 1px solid #666;
			border-collapse: collapse;
		font-size: 13px;
	}
	
	th.invalid, td.invalid {
			border: 1px double #666;
			padding:0 !important;
			margin:0 !important;
	}
	
	#table {
			max-width: 2480px;
			width:100%;
		font-size:	15px;
	}
	
	
	.border-less {
			border-top: 1px solid #FFFFFF;
			border-left: 1px solid #FFFFFF;
	}
	.no-border {
			border: 1px solid #FFFFFF;
	}
	.no-border-all {
			border: 1px double #FFFFFF;
	}
	.side-border {
		border-top: 1px solid #FFFFFF;
		border-bottom: 1px solid #FFFFFF;
			border-left: 1px double #666;
		border-right: 1px solid #666;
	}
	#page td {
		 padding:0; margin:0;
	}
	
	#page {
		 border-collapse: collapse;
	}
	
	.secondt {
		border-collapse: collapse;
		border-style: hidden;
	}
	.center {
			text-align:center;
	}
	.nowrap {
		white-space:nowrap;
	}
	
	table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
	table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
	
	
#wrapper_1 {
    float:left;
	text-align: center;
	width: 50%;
	height: 100%;
}

#wrapper_2 {
    float:right;
	text-align: center;
	width: 50%;
	height: 100%;
}

.seperator {
  height: 100%;
  width: 1px;
  background: #666;
  margin: 0 auto;
  top: 0;
  bottom: 0;
  position: absolute;
  left: 50%;
}

.container {
  position: relative;
  height: 100%;
}

.clearfix {
  clear: both;
}	
	</style>
	
	
	<page size="A4">
		<div style="">
	<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
	
	<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
	PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
	</center></div>
	
	
		<table width="100%" style="height:100%;">
			<tr >
				<td class="border-less" >
					<p>TEL <?=$companyPhoneNo;?><br>
					<?=$companyEmail;?></p>
				</td>
				<td width="40%" class="center nowrap">
					ĢENERĀLĀS KRAVAS<br>PIEŅEMŠANAS AKTS
				</td>
				<td width="10%">
					Numurs:<br><br><center><?=$actNo;?></center>
				</td>			
			</tr>
			<tr id="page" width="30%">
				<td width="40%" rowspan="5">
						&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
						&nbsp;&nbsp;vārdā pieņemot kravu:
						<br>
						<br>
						<br>
						<br>
						<br>
						&nbsp;&nbsp;PARAKSTS:<br>
						&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
				</td>		
			</tr>
			<tr>
				<td width="40%">
					Firma / Pieteikuma izdevējs (vārds, uzvārds)
				</td>
				<td width="20%">
					Datums
				</td>	
			</tr>		
			<tr>
				<td height="30px" class="center nowrap" width="40%">
					<?=$companyWho;?>
				</td>
				<td  height="30px" class="center nowrap" width="10%">
					<?php echo $actDate; ?>
				</td>
			</tr>
	
			<tr>
				<td width="40%">
					Pieteikums Nr. / Datums
				</td>
				<td width="10%">
					Eksp / Imp. / Tranzīts&nbsp;
				</td>
			</tr>
	
			<tr>
				<td  height="40px" class="nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$applicationDate;?></div>
				</td>
				<td  height="40px" class="center nowrap" width="10%">
					<?=$cargoStatus;?>
				</td>
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border" width="25%">
							&nbsp;&nbsp;Līnijas aģents / Līnija
							</td>
							<td class="side-border" width="40%">
							&nbsp;&nbsp;Kuģis
							</td>						
	
							<td class="no-border" width="35%">
							&nbsp;&nbsp;Reisa Nr.
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%">					
					&nbsp;&nbsp;Izkraušanas datums
				</td>			
			</tr>
			
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border center nowrap" width="25%">
							----
							</td>
							<td class="side-border center nowrap" width="40%">
							
							----
							</td>						
	
							<td class="no-border center nowrap" width="35%">
							----
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center nowrap">					
				<?=$deliveryDate;?>
				</td>			
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2">
					<table width="100%">
						<tr>
							<td class="no-border center nowrap" width="25%">
							Gala osta
							</td>
							<td class="side-border center nowrap" width="40%">
							Operācija
							</td>						
	
							<td class="no-border center nowrap" width="35%">
							Transporta veids
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center nowrap" >					
					Auto/Dzc.platformas Nr.
				</td>			
			</tr>
	
			<tr id="page" width="30%">
				<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
					<table width="100%" style="height:100%;">
						<tr>
							<td class="no-border center nowrap" width="25%">
							----
							</td>
							<td class="side-border center nowrap" width="40%">
								PIEŅEMŠANA
							</td>						
	
							<td class="no-border center nowrap" width="35%">
								<?=$transport;?>
							</td>						
						</tr>
					</table>
				</td>
				<td width="30%" class="center" id="transports">					
                    
				</td>			
			</tr>		
	
			<tr id="page" >
				<td width="70%" colspan="2">
					<table width="100%" id="itable1" class="itable1">
						<tr>
							<td class="no-border center" width="4%">
							LOT
							</td>
							<td class="side-border center"  colspan="2" width="61.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
							Kravas nosaukums
							</td>

							<td class="side-border center" width="20%">
							Brt. svars, m3
							</td>
							<td class="no-border center" width="15%">
                                
							</td>						
						</tr>
						
					</table>
				</td>
				<td width="30%" class="center">
					Piezīmes:
				</td>
				
			</tr>
			
			
			
			
			
			
			<tr id="page" >
				<td width="70%" colspan="2">
					<table width="100%" id="itable2" class="itable2">
	
					<tr>
	<?php
	
		$prodQuery = mysqli_query($conn, "
			SELECT l.thisTransport, l.productNr, l.serialNo,  
			l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.place_count,
			l.gross, l.net, l.batchNo, h.transport, l.amount, l.resource, nr.name as resource_name, l.ladingNr
			FROM cargo_header AS h
			JOIN cargo_line AS l
			ON h.docNr=l.docNr
			JOIN n_items AS n
			ON l.productNr=n.code
			
			JOIN n_resource AS nr
			ON l.resource=nr.id			
			
			WHERE h.id='".intval($id)."'
		") or die (mysqli_error($conn));
		$number = mysqli_num_rows($prodQuery);
		$i = 1;
	
		$place_count = $gross = $net = $lastProd = $lastTransp = null;
		$batchNo = array();
        $batchNoA = array();
        $transports = [];
		while($pr_row = mysqli_fetch_array($prodQuery)){
			$place_count += $pr_row['place_count'];
			$amount += $pr_row['amount'];
			$gross += $pr_row['gross'];
			$net += $pr_row['net'];
	
			
	?>
					
	
					
					<td class="no-border center" width="4%">
						<?php
						
							if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							
							echo $pr_row['batchNo'];
							array_push($batchNo, $pr_row['batchNo']);
							$batchNoA[$pr_row['batchNo']] += $pr_row['amount'];
							if($number==$i){ ?><br><br><br><br><br><?php echo ''; } 
						
						?>					
					</td>
					<td class="side-border center" colspan="2" width="61.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
				
<?php

									if($lastProd!=$pr_row['productNr']){
										echo '<div style="width: 100%;">';
											echo $pr_row['name'].'<br>';
										echo '</div>';
									}

?>	
				
<div class="container">

									<?php
									echo 'Pavadzīmes Nr. '.$pr_row['ladingNr'];

									$ltr=$pr_row['thisTransport'];
									if($lastTransp!=$ltr){
										$transports[] = $pr_row['thisTransport'];
                                    }									
									?>	


    <script>
        $('#transports').html('<?=implode(', ', array_unique($transports));?>');
    </script>

	<div class="clearfix">

	</div>

</div>						
						
						
						
						
						
						
						
					</td>
 
					<td class="side-border center" width="20%" style="vertical-align:top">				
						<?php
							
							if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
												
							echo floatval($pr_row['amount']);
	
						?>
						
						
						<?php if($number==$i){ ?><br><br><?php 
						
						$goal = count(array_count_values($batchNo));
						if($goal>0){
							for($k=0; $k<=$goal; $k++){
								echo '<br>';
							}
						}						
						
						echo $amount; } ?>
					</td>
					<td class="no-border center" width="15%" style="vertical-align:top">
											
					</td>					
				</tr>
	
	<?php 
	
	$i++;
	$lastProd = $pr_row['productNr'];
	$lastTransp = ucfirst($pr_row['transport']).' Nr. '.$pr_row['thisTransport'];
	 } ?>
	

					</table>
				</td>
				<td width="30%" class="nowrap" style="vertical-align:top">
				<br>
					<center>				
						Visa krava pieņemta<br>noliktavā:<br>
						<?=$locations;?>
						<br>
						<?=$issue_declaration_type_no;?>
						&nbsp;
					</center>
				</td>			
			</tr>		
	
			
			
			
			<tr>
				<td colspan="3">Krava pieņemta saskaņā ar pavaddokumentiem.<br>Pretenzijas par kravu daudzumu un kvalitāti tiek pieņemtas tikai pieņemšanas laikā.</td>
			</tr>

			<tr id="page" width="30%">
				<td colspan="3" >
					<table width="100%" rules="all" class="secondt">
	
						<tr>
							<td colspan="2" align="center">PILNVARAS DATI</td>

							<td colspan="2" rowspan="3" height="100">&nbsp;&nbsp;PIEZĪMES<br><br>&nbsp;&nbsp;<?=$companyWho;?><br>
								<br>
								<br>
							
								<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
								<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
								<br>&nbsp;&nbsp;Pilnvarotā persona pieņemot kravu terminālam
							</td>
						</tr>
						
						<tr>
							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
						</tr>
						
						<tr>
							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
						</tr>
					

					

						<tr>
							
							<td colspan="2" rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
						
							<td rowspan="2" >&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
							
						</tr>
						
					</table>
				</td>
			</tr>			
			
		</table>
		</div>
	</page>
	
	</body>
	</html>	
	
	<?php } 		

if($view=='izs'){ //IZSNIEGŠAMA
	
	if($form==1){

		$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
		$cargo = mysqli_query($conn, "
				SELECT c.lastBy, i.issuance_act_no, c.deliveryDate, i.application_no, i.applicationDate, i.transport_name, i.transport, c.ship, c.docNr, i.clientCode,
				i.issueDate, i.date, i.createdBy, i.editedBy
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
		$crow = mysqli_fetch_array($cargo);	
	
	
		$issueDate = date('d.m.Y', strtotime($crow['issueDate']));
		$idate = date('d.m.Y', strtotime($crow['date']));
	
		if($crow['editedBy']==0){
			$lastBy = returnMeWho($crow['createdBy']);
		}
		if($crow['editedBy']>0){
			$lastBy = returnMeWho($crow['editedBy']);
		}	
	
		$actNo  = $crow['issuance_act_no'];
		$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
		
		$applicationNo  = $crow['application_no'];
		$landingDate = date('d.m.Y', strtotime($crow['applicationDate']));
		
		$transport = mb_strtoupper($crow['transport'], 'UTF-8');		
		
		$getShips = mysqli_query($conn, "
				SELECT i.transport_name, c.ship, l.issue_declaration_type_no
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
			
		$shipsIn = $shipsOut = $decNo = array();
		while($ships = mysqli_fetch_array($getShips)){
			
			if($ships['ship']){
				$shipsIn[] = $ships['ship'];
			}
			if($ships['transport_name']){
				$shipsOut[] = $ships['transport_name'];
			}
			
			if($ships['issue_declaration_type_no']){
				$decNo[] = $ships['issue_declaration_type_no'];
			}
		}
		
		$shipsIn = array_unique($shipsIn);
		$shipsOut = array_unique($shipsOut);
		$decNo = array_unique($decNo);
		
		if(COUNT($shipsIn)>0){
			$ship = implode(',', $shipsIn);
		}else{
			$ship = implode(',', $shipsOut);
		}	
		
		$ship = null;
		$ship = $crow['transport_name'];
		
		
		$linesTransport = mysqli_query($conn, "
				SELECT GROUP_CONCAT(DISTINCT(c.issue_thisTransport) separator '<br>') AS transports 
				FROM cargo_line AS c
				LEFT JOIN issuance_doc AS i
				ON c.issuance_id=i.issuance_id
				WHERE c.issuance_id='".$id."'			
				");
		$allT = mysqli_fetch_array($linesTransport);
		
		$issue_header_info = mysqli_query($conn, "
				SELECT thisTransport, declaration_type_no
				FROM issuance_doc
				WHERE issuance_id='".$id."'			
				");
		$ihi = mysqli_fetch_array($issue_header_info);
	
		if($ihi['thisTransport']){
			$thisTransport = $ihi['thisTransport'];
		}else{
			$thisTransport = $allT['transports'];
		}
		
		if(COUNT($decNo)>0){
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}
		}else{
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}		
		}
		
		$linesLocation = mysqli_query($conn, "
						SELECT 
		
						GROUP_CONCAT(
							DISTINCT CONCAT(c.location,' - ',l.name) 
							SEPARATOR '<br>'
						) AS locations,
						GROUP_CONCAT(DISTINCT(c.cargo_status) separator ',') AS cargo_status
						
		
						FROM cargo_line AS c
						LEFT JOIN n_location AS l
						ON c.location=l.id
	
						LEFT JOIN issuance_doc AS i
						ON c.issuance_id=i.issuance_id
						WHERE c.issuance_id='".$id."'
					") or die (mysqli_error($conn));
		$allL = mysqli_fetch_array($linesLocation);
		
		$locations = $allL['locations'];
		$cargo_status = $allL['cargo_status'];
		
		$companyWho = getCustInfo($conn, 'Name', $crow['clientCode']);
		
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
		
		
		
		
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
		
		table, th, td {
				padding: 3px 8px;
				border: 1px solid #666;
				border-collapse: collapse;
			font-size: 13px;
		}
		
		th.invalid, td.invalid {
				border: 1px double #666;
				padding:0 !important;
				margin:0 !important;
		}
		
		#table {
				max-width: 2480px;
				width:100%;
			font-size:	15px;
		}
		
		
		.border-less {
				border-top: 1px solid #FFFFFF;
				border-left: 1px solid #FFFFFF;
		}
		.no-border {
				border: 1px solid #FFFFFF;
		}
		.no-border-all {
				border: 1px double #FFFFFF;
		}
		.side-border {
			border-top: 1px solid #FFFFFF;
			border-bottom: 1px solid #FFFFFF;
				border-left: 1px double #666;
			border-right: 1px solid #666;
		}
		#page td {
			 padding:0; margin:0;
		}
		
		#page {
			 border-collapse: collapse;
		}
		
		.secondt {
			border-collapse: collapse;
			border-style: hidden;
		}
		.center {
				text-align:center;
		}
		.nowrap {
			white-space:nowrap;
		}
		
		table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
		table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
		</style>
		
		
		<page size="A4">
			<div style="">
		<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
		
		<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
		PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
		</center></div>
		
			<table width="100%">
				<tr >
					<td class="border-less" >
						<p>TEL <?=$companyPhoneNo;?><br>
						<?=$companyEmail;?></p>
					</td>
					<td width="40%" class="center nowrap">
						ĢENERĀLĀS KRAVAS<br>IZDOŠANAS AKTS
					</td>
					<td width="10%">
						Numurs:<br><br><center><?=$actNo;?></center>
					</td>			
				</tr>
				<tr id="page" width="30%">
					<td width="40%" rowspan="5">
							&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
							&nbsp;&nbsp;vārdā izdodot kravu:
							<br>
							<br>
							<br>
							<br>
							<br>
							&nbsp;&nbsp;PARAKSTS:<br>
							&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
					</td>		
				</tr>
				<tr>
					<td width="40%">
						Firma / Pieteikuma izdevējs (vārds, uzvārds)
					</td>
					<td width="20%">
						Datums
					</td>	
				</tr>		
				<tr>
					<td height="30px" class="center nowrap" width="40%">
						<?=$companyWho;?>
					</td>
					<td  height="30px" class="center nowrap" width="10%">
						<?=$idate;?>
					</td>
				</tr>
		
				<tr>
					<td width="40%">
						Pieteikums Nr. / Datums
					</td>
					<td width="10%">
						Eksp / Imp. / Tranzīts&nbsp;
					</td>
				</tr>
		
				<tr>
					<td  height="40px" class="center nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$landingDate;?></div>
					</td>
					<td  height="40px" class="center nowrap" width="10%">
						<?php
						
							$cs = explode(',', $cargo_status);
							$vas = array();
							foreach($cs as $va){
								if($va=='C'){
									array_push($vas, 'EKSPORTS'); //echo 'EKSPORTS';
								}
	
								if($va=='N'){
									array_push($vas, 'TRANZĪTS'); //echo 'TRANZĪTS';
								}

								if($va=='EU'){
									array_push($vas, 'EU'); //echo 'EU';
								}

							}
							$vas = implode(', ', $vas);
							echo $vas;
						
						?>
					</td>
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border" width="25%">
								&nbsp;&nbsp;Līnijas aģents / Līnija
								</td>
								<td class="side-border" width="40%">
								&nbsp;&nbsp;Kuģis
								</td>						
		
								<td class="no-border" width="35%">
								&nbsp;&nbsp;Reisa Nr.
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%">					
						&nbsp;&nbsp;Iekraušanas datums
					</td>			
				</tr>
				
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
								<?=$ship;?>
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								----
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
					<?=$issueDate;?>
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								Gala osta
								</td>
								<td class="side-border center nowrap" width="40%">
								Operācija
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								Transporta veids
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap" >					
						Auto/Dzc.platformas Nr.
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
									IZDOŠANA
								</td>						
		
								<td class="no-border center nowrap" width="35%">
									<?=$transport;?>
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
						<?=$thisTransport;?>
					</td>			
				</tr>		
		
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable1" class="itable1">
							<tr>
								<td class="no-border center" width="4%">
								B/L<br>Nr.
								</td>
								<td class="side-border center" width="54.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
								Kravas nosaukums
								</td>
								<td class="no-border center" width="6%">
								Vietu<br>skaits
								</td>
								<td class="side-border center" width="20%">
								Brt. svars, <?=$weightFormat;?>
								</td>
								<td class="no-border center" width="15%">
								Net. svars, <?=$weightFormat;?>
								</td>						
							</tr>
							
						</table>
					</td>
					<td width="30%" class="center">
						Piezīmes:
					</td>
					
				</tr>
			
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable2" class="itable2">
		
						<tr>
		<?php
		
			$prodQuery = mysqli_query($conn, "
				SELECT l.issue_thisTransport, l.productNr, l.serialNo,  
				l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.issue_place_count,
				l.issueGross, l.issueNet, i.transport, h.transport AS recive_transport, l.thisTransport, h.deliveryDate, l.issue_declaration_type_no
				FROM cargo_header AS h
				JOIN cargo_line AS l
				ON h.docNr=l.docNr
				JOIN n_items AS n
				ON l.productNr=n.code
	
				JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
				order by h.deliveryDate DESC, l.productNr, l.thisTransport, l.serialNo
				
			") or die(mysqli_error($conn));
			$number = mysqli_num_rows($prodQuery);
			$i = 1;
		
			$place_count = $gross = $net = $lastProd = $lastTransp = $lastName = null;
			while($pr_row = mysqli_fetch_array($prodQuery)){
				$place_count += $pr_row['issue_place_count'];
				$gross += $pr_row['issueGross'];
				$net += $pr_row['issueNet'];
		
				
		?>
							
						<td class="no-border center" width="4%">
													
						</td>
						<td class="side-border center" width="54.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
							
							<?php
							
							if($lastProd!=$pr_row['productNr']){
								echo $pr_row['name'].'<br>';
							}	
							$ltr=ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')';
							if($lastTransp!=$ltr){
								echo ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')<br>';
							}							
								
							
	
							echo $pr_row['serialNo'];
							?>	
		
							<?php if($number==$i){ ?><br><br><br><br>Kopā: <?php } ?>
						</td>
						<td class="no-border center" width="6%" style="vertical-align:top;">				
							<?php
							 
							 if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							 if($lastTransp!=$ltr){ echo '<br>'; }		
							 if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }						 
								
								if($pr_row['issue_place_count']>0){
									echo $pr_row['issue_place_count'];
								}else{
									echo ' ';
								}
							
							 
							if($number==$i){ 
								echo '<br><br><br><br>';
								
								if($place_count>0){
									echo $place_count;
								}else{
									echo ' ';
								}									
							} 
							?>		
						</td>
						<td class="side-border center" width="20%" style="vertical-align:top;">				
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }							
								
								if($pr_row['issueGross']>0){
									echo floatval($pr_row['issueGross']);
								}else{
									echo ' ';
								}
		
								if($number==$i){ 
									echo '<br><br><br><br>';
									if($gross>0){
										echo $gross;
									}else{
										echo ' ';
									}
								}
							?>
						</td>
						<td class="no-border center" width="15%" style="vertical-align:top;">
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }
								
								if($pr_row['issueNet']>0){
									echo floatval($pr_row['issueNet']);
								}else{
									echo ' ';
								}
								
							?>
							<?php 
								if($number==$i){ 
									echo '<br><br><br><br>'; 
									if($net>0){
										echo $net;
									}else{
										echo ' ';
									}
								} 
							?>					
						</td>					
					</tr>
		
		<?php 
		
		$i++;
		$lastProd = $pr_row['productNr'];
		$lastTransp = ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')';
		$lastName = $pr_row['name'];
		 } ?>
		
						</table>
					</td>
					<td width="30%" class="nowrap" style="vertical-align:top">
					<br>
						<center>
	
							<br><?=$issue_declaration_type_no;?><br><br>
						
							Visa krava izdota<br>noliktavā:<br>
							<?=$locations;?>
							&nbsp;
						</center>
					</td>			
				</tr>		
	
				<tr>
					<td colspan="3">Krava izdota saskaņā ar pavaddokumentiem bez pārsvēršanas</td>
				</tr>
	
				<tr id="page" width="30%">
					<td colspan="3" >
						<table width="100%" rules="all" class="secondt">
		
							<tr>
							<td height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
							<td colspan="2" rowspan="2" height="100"><center><?=$companyWho;?></center><br>
											<br>
											<br>
										
											<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
											<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
											<br>&nbsp;&nbsp;Pilnvarotā persona izdodot kravu terminālam
											</td>
							</tr>
							<tr>
								<td height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
							</tr>
							<tr>
								
								<td rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
							
								<td rowspan="2" colspan="2">&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
								
							</tr>
						</table>
					</td>
				</tr>		
				
			</table>
			</div>
		</page>
				
		</body>
		</html>			
		<?php
	}

	if($form==2){

		$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
		$cargo = mysqli_query($conn, "
				SELECT c.lastBy, i.issuance_act_no, c.deliveryDate, i.application_no, i.applicationDate, i.transport_name, i.transport, c.ship, c.docNr, i.clientCode,
				i.issueDate, i.date, i.createdBy, i.editedBy
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
		$crow = mysqli_fetch_array($cargo);	
	
	
		$issueDate = date('d.m.Y', strtotime($crow['issueDate']));
		$idate = date('d.m.Y', strtotime($crow['date']));
	
		if($crow['editedBy']==0){
			$lastBy = returnMeWho($crow['createdBy']);
		}
		if($crow['editedBy']>0){
			$lastBy = returnMeWho($crow['editedBy']);
		}	
	
		$actNo  = $crow['issuance_act_no'];
		$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
		
		$applicationNo  = $crow['application_no'];
		$landingDate = date('d.m.Y', strtotime($crow['applicationDate']));
		
		$transport = mb_strtoupper($crow['transport'], 'UTF-8');		
		
		$getShips = mysqli_query($conn, "
				SELECT i.transport_name, c.ship, l.issue_declaration_type_no
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
			
		$shipsIn = $shipsOut = $decNo = array();
		while($ships = mysqli_fetch_array($getShips)){
			
			if($ships['ship']){
				$shipsIn[] = $ships['ship'];
			}
			if($ships['transport_name']){
				$shipsOut[] = $ships['transport_name'];
			}
			
			if($ships['issue_declaration_type_no']){
				$decNo[] = $ships['issue_declaration_type_no'];
			}
		}
		
		$shipsIn = array_unique($shipsIn);
		$shipsOut = array_unique($shipsOut);
		$decNo = array_unique($decNo);	
		
		if(COUNT($shipsIn)>0){
			$ship = implode(',', $shipsIn);
		}else{
			$ship = implode(',', $shipsOut);
		}	
		
		
		$linesTransport = mysqli_query($conn, "
				SELECT GROUP_CONCAT(DISTINCT(c.issue_thisTransport) separator '<br>') AS transports 
				FROM cargo_line AS c
				LEFT JOIN issuance_doc AS i
				ON c.issuance_id=i.issuance_id
				WHERE c.issuance_id='".$id."'			
				");
		$allT = mysqli_fetch_array($linesTransport);
		
		$issue_header_info = mysqli_query($conn, "
				SELECT thisTransport, declaration_type_no
				FROM issuance_doc
				WHERE issuance_id='".$id."'			
				");
		$ihi = mysqli_fetch_array($issue_header_info);
	
		if($ihi['thisTransport']){
			$thisTransport = $ihi['thisTransport'];
		}else{
			$thisTransport = $allT['transports'];
		}
		
		if(COUNT($decNo)>0){
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}
		}else{
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}		
		}
		
		$linesLocation = mysqli_query($conn, "
						SELECT 
		
						GROUP_CONCAT(
							DISTINCT CONCAT(c.location,' - ',l.name) 
							SEPARATOR '<br>'
						) AS locations,
						GROUP_CONCAT(DISTINCT(c.cargo_status) separator ',') AS cargo_status
						
		
						FROM cargo_line AS c
						LEFT JOIN n_location AS l
						ON c.location=l.id
	
						LEFT JOIN issuance_doc AS i
						ON c.issuance_id=i.issuance_id
						WHERE c.issuance_id='".$id."'
	
					") or die (mysqli_error($conn));
		$allL = mysqli_fetch_array($linesLocation);
		
		$locations = $allL['locations'];
		$cargo_status = $allL['cargo_status'];
		
		$companyWho = getCustInfo($conn, 'Name', $crow['clientCode']);
		
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
		
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
		
		table, th, td {
				padding: 3px 8px;
				border: 1px solid #666;
				border-collapse: collapse;
			font-size: 13px;
		}
		
		th.invalid, td.invalid {
				border: 1px double #666;
				padding:0 !important;
				margin:0 !important;
		}
		
		#table {
				max-width: 2480px;
				width:100%;
			font-size:	15px;
		}
		
		
		.border-less {
				border-top: 1px solid #FFFFFF;
				border-left: 1px solid #FFFFFF;
		}
		.no-border {
				border: 1px solid #FFFFFF;
		}
		.no-border-all {
				border: 1px double #FFFFFF;
		}
		.side-border {
			border-top: 1px solid #FFFFFF;
			border-bottom: 1px solid #FFFFFF;
				border-left: 1px double #666;
			border-right: 1px solid #666;
		}
		#page td {
			 padding:0; margin:0;
		}
		
		#page {
			 border-collapse: collapse;
		}
		
		.secondt {
			border-collapse: collapse;
			border-style: hidden;
		}
		.center {
				text-align:center;
		}
		.nowrap {
			white-space:nowrap;
		}
		
		table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
		table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
		</style>
		
		
		<page size="A4">
			<div style="">
		<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
		
		<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
		PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
		</center></div>
		
			<table width="100%">
				<tr >
					<td class="border-less" >
						<p>TEL <?=$companyPhoneNo;?><br>
						<?=$companyEmail;?></p>
					</td>
					<td width="40%" class="center nowrap">
						ĢENERĀLĀS KRAVAS<br>IZDOŠANAS AKTS
					</td>
					<td width="10%">
						Numurs:<br><br><center><?=$actNo;?></center>
					</td>			
				</tr>
				<tr id="page" width="30%">
					<td width="40%" rowspan="5">
							&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
							&nbsp;&nbsp;vārdā izdodot kravu:
							<br>
							<br>
							<br>
							<br>
							<br>
							&nbsp;&nbsp;PARAKSTS:<br>
							&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
					</td>		
				</tr>
				<tr>
					<td width="40%">
						Firma / Pieteikuma izdevējs (vārds, uzvārds)
					</td>
					<td width="20%">
						Datums
					</td>	
				</tr>		
				<tr>
					<td height="30px" class="center nowrap" width="40%">
						<?=$companyWho;?>
					</td>
					<td  height="30px" class="center nowrap" width="10%">
						<?=$idate;?>
					</td>
				</tr>
		
				<tr>
					<td width="40%">
						Pieteikums Nr. / Datums
					</td>
					<td width="10%">
						Eksp / Imp. / Tranzīts&nbsp;
					</td>
				</tr>
		
				<tr>
					<td  height="40px" class="center nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$landingDate;?></div>
					</td>
					<td  height="40px" class="center nowrap" width="10%">
						<?php
						
							$cs = explode(',', $cargo_status);
							$vas = array();
							foreach($cs as $va){
								if($va=='C'){
									array_push($vas, 'EKSPORTS'); //echo 'EKSPORTS';
								}
	
								if($va=='N'){
									array_push($vas, 'TRANZĪTS'); //echo 'TRANZĪTS';
								}

								if($va=='EU'){
									array_push($vas, 'EU'); //echo 'EU';
								}

							}
							$vas = implode(', ', $vas);
							echo $vas;
						
						?>
					</td>
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border" width="25%">
								&nbsp;&nbsp;Līnijas aģents / Līnija
								</td>
								<td class="side-border" width="40%">
								&nbsp;&nbsp;Kuģis
								</td>						
		
								<td class="no-border" width="35%">
								&nbsp;&nbsp;Reisa Nr.
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%">					
						&nbsp;&nbsp;Iekraušanas datums
					</td>			
				</tr>
				
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
								<?=$ship;?>
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								----
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
					<?=$issueDate;?>
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								Gala osta
								</td>
								<td class="side-border center nowrap" width="40%">
								Operācija
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								Transporta veids
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap" >					
						Auto/Dzc.platformas Nr.
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
									IZDOŠANA
								</td>						
		
								<td class="no-border center nowrap" width="35%">
									<?=$transport;?>
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
						<?=$thisTransport;?>
					</td>			
				</tr>		
		
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable1" class="itable1">
							<tr>
								<td class="no-border center" width="4%">
								B/L<br>Nr.
								</td>
								<td class="side-border center" width="54.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
								Kravas nosaukums
								</td>
								<td class="no-border center" width="6%">
								Vietu<br>skaits
								</td>
								<td class="side-border center" width="20%">
								Brt. svars, <?=$weightFormat;?>
								</td>
								<td class="no-border center" width="15%">
								Net. svars, <?=$weightFormat;?>
								</td>						
							</tr>
							
						</table>
					</td>
					<td width="30%" class="center">
						Piezīmes:
					</td>
					
				</tr>
			
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable2" class="itable2">
		
						<tr>
		<?php
		
			$prodQuery = mysqli_query($conn, "
				SELECT l.issue_thisTransport, l.productNr, l.serialNo,  
				l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.issue_place_count,
				l.issueGross, l.issueNet, i.transport, h.transport AS recive_transport, l.thisTransport, h.deliveryDate, l.issue_declaration_type_no
				FROM cargo_header AS h
				JOIN cargo_line AS l
				ON h.docNr=l.docNr
				JOIN n_items AS n
				ON l.productNr=n.code
	
				JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
				order by h.deliveryDate DESC, l.productNr, l.thisTransport, l.serialNo
				
			") or die(mysqli_error($conn));
			$number = mysqli_num_rows($prodQuery);
			$i = 1;
		
			$place_count = $gross = $net = $lastProd = $lastTransp = $lastName = null;
			while($pr_row = mysqli_fetch_array($prodQuery)){
				$place_count += $pr_row['issue_place_count'];
				$gross += $pr_row['issueGross'];
				$net += $pr_row['issueNet'];
		
				
		?>
							
						<td class="no-border center" width="4%">
													
						</td>
						<td class="side-border center" width="54.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
							
							<?php
							if($lastProd!=$pr_row['productNr']){
								echo $pr_row['name'].'<br>';
							}	
							$ltr=ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')';
							if($lastTransp!=$ltr){
								echo ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')<br>';
							}							
								
							
	
							echo $pr_row['serialNo'];
							?>	
		
							<?php if($number==$i){ ?><br><br><br><br>Kopā: <?php } ?>
						</td>
						<td class="no-border center" width="6%" style="vertical-align:top;">				
							<?php
							 
							 if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							 if($lastTransp!=$ltr){ echo '<br>'; }		
							 if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }						 
							
							 if($pr_row['issue_place_count']>0){
								 echo $pr_row['issue_place_count'];
							 }else{
								 echo '';
							 }
							
							 if($number==$i){ 
								echo '<br><br><br><br>';
								if($place_count>0){
									echo $place_count;
								}else{
									echo ' ';
								}
							 } 
							?>		
						</td>
						<td class="side-border center" width="20%" style="vertical-align:top;">				
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }							
								
								if($pr_row['issueGross']>0){
									echo floatval($pr_row['issueGross']);
								}else{
									echo ' ';
								}
								if($number==$i){ 
									echo '<br><br><br><br>'; 
									if($gross>0){
										echo $gross;
									}else{
										echo ' ';
									}
								} 
							?>
						</td>
						<td class="no-border center" width="15%" style="vertical-align:top;">
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }
								
								if($pr_row['issueNet']>0){
									echo floatval($pr_row['issueNet']);
								}else{
									echo ' ';
								}
								
								if($number==$i){ 
									echo '<br><br><br><br>';
									if($net>0){
										echo $net;
									}else{
										echo ' ';
									}
								} 
							?>					
						</td>					
					</tr>
		
		<?php 
		
		$i++;
		$lastProd = $pr_row['productNr'];
		$lastTransp = ucfirst($pr_row['recive_transport']).' Nr. '.$pr_row['thisTransport'].' ('.date('d.m.Y', strtotime($pr_row['deliveryDate'])).')';
		$lastName = $pr_row['name'];
		 } ?>
		
						</table>
					</td>
					<td width="30%" class="nowrap" style="vertical-align:top">
					<br>
						<center>
	
							<br><?=$issue_declaration_type_no;?><br><br>
						
							Visa krava izdota<br>noliktavā:<br>
							<?=$locations;?>
							&nbsp;
						</center>
					</td>			
				</tr>		
	
				<tr>
					<td colspan="3">Krava izdota saskaņā ar pavaddokumentiem bez pārsvēršanas</td>
				</tr>
	
				<tr id="page" width="30%">
					<td colspan="3" >
						<table width="100%" rules="all" class="secondt">
		
							<tr>
							<td height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
							<td colspan="2" rowspan="2" height="100"><center><?=$companyWho;?></center><br>
											<br>
											<br>
										
											<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
											<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
											<br>&nbsp;&nbsp;Pilnvarotā persona izdodot kravu terminālam
											</td>
							</tr>
							<tr>
								<td height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>
							</tr>
							<tr>
								
								<td rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
							
								<td rowspan="2" colspan="2">&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
								
							</tr>
						</table>
					</td>
				</tr>		
				
			</table>
			</div>
		</page>
				
		</body>
		</html>			
		<?php
	}
























	if($form==3){

		$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
		$cargo = mysqli_query($conn, "
				SELECT c.lastBy, i.issuance_act_no, c.deliveryDate, i.application_no, i.applicationDate, i.transport_name, i.transport, c.ship, c.docNr, i.clientCode,
				i.issueDate, i.date, i.createdBy, i.editedBy, c.ownerCode
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
		$crow = mysqli_fetch_array($cargo);	
	
	
		$issueDate = date('d.m.Y', strtotime($crow['issueDate']));
		$idate = date('d.m.Y', strtotime($crow['date']));
	
		if($crow['editedBy']==0){
			$lastBy = returnMeWho($crow['createdBy']);
		}
		if($crow['editedBy']>0){
			$lastBy = returnMeWho($crow['editedBy']);
		}	
	
		$actNo  = $crow['issuance_act_no'];
		$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
		
		$applicationNo  = $crow['application_no'];
		$landingDate = date('d.m.Y', strtotime($crow['applicationDate']));
		
		$transport = mb_strtoupper($crow['transport'], 'UTF-8');		
		
		$getShips = mysqli_query($conn, "
				SELECT i.transport_name, c.ship, l.issue_declaration_type_no
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
			
		$shipsIn = $shipsOut = $decNo = array();
		while($ships = mysqli_fetch_array($getShips)){
			
			if($ships['ship']){
				$shipsIn[] = $ships['ship'];
			}
			if($ships['transport_name']){
				$shipsOut[] = $ships['transport_name'];
			}
			
			if($ships['issue_declaration_type_no']){
				$decNo[] = $ships['issue_declaration_type_no'];
			}
		}
		
		$shipsIn = array_unique($shipsIn);
		$shipsOut = array_unique($shipsOut);
		$decNo = array_unique($decNo);
		
		if(COUNT($shipsIn)>0){
			$ship = implode(',', $shipsIn);
		}else{
			$ship = implode(',', $shipsOut);
		}	
		
		$ship = null;
		$ship = $crow['transport_name'];
		
		$linesTransport = mysqli_query($conn, "
				SELECT GROUP_CONCAT(DISTINCT(c.issue_thisTransport) separator '<br>') AS transports 
				FROM cargo_line AS c
				LEFT JOIN issuance_doc AS i
				ON c.issuance_id=i.issuance_id
				WHERE c.issuance_id='".$id."'			
				");
		$allT = mysqli_fetch_array($linesTransport);
		
		$issue_header_info = mysqli_query($conn, "
				SELECT thisTransport, declaration_type_no
				FROM issuance_doc
				WHERE issuance_id='".$id."'			
				");
		$ihi = mysqli_fetch_array($issue_header_info);
	
		if($ihi['thisTransport']){
			$thisTransport = $ihi['thisTransport'];
		}else{
			$thisTransport = $allT['transports'];
		}
		
		if(COUNT($decNo)>0){
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}
		}else{
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}		
		}
		
		$linesLocation = mysqli_query($conn, "
						SELECT 
		
						GROUP_CONCAT(
							DISTINCT CONCAT(c.location,' - ',l.name) 
							SEPARATOR '<br>'
						) AS locations,
						GROUP_CONCAT(DISTINCT(c.cargo_status) separator ',') AS cargo_status
						
		
						FROM cargo_line AS c
						LEFT JOIN n_location AS l
						ON c.location=l.id
	
						LEFT JOIN issuance_doc AS i
						ON c.issuance_id=i.issuance_id
						WHERE c.issuance_id='".$id."'
	
					") or die (mysqli_error($conn));
		$allL = mysqli_fetch_array($linesLocation);
		
		$locations = $allL['locations'];
		$cargo_status = $allL['cargo_status'];
		
		$companyWho = getCustInfo($conn, 'Name', $crow['ownerCode']);
		
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
		
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
		
		table, th, td {
				padding: 3px 8px;
				border: 1px solid #666;
				border-collapse: collapse;
			font-size: 13px;
		}
		
		th.invalid, td.invalid {
				border: 1px double #666;
				padding:0 !important;
				margin:0 !important;
		}
		
		#table {
				max-width: 2480px;
				width:100%;
			font-size:	15px;
		}
		
		
		.border-less {
				border-top: 1px solid #FFFFFF;
				border-left: 1px solid #FFFFFF;
		}
		.no-border {
				border: 1px solid #FFFFFF;
		}
		.no-border-all {
				border: 1px double #FFFFFF;
		}
		.side-border {
			border-top: 1px solid #FFFFFF;
			border-bottom: 1px solid #FFFFFF;
				border-left: 1px double #666;
			border-right: 1px solid #666;
		}
		#page td {
			 padding:0; margin:0;
		}
		
		#page {
			 border-collapse: collapse;
		}
		
		.secondt {
			border-collapse: collapse;
			border-style: hidden;
		}
		.center {
				text-align:center;
		}
		.nowrap {
			white-space:nowrap;
		}
		
		table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
		table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
		</style>
		
		
		<page size="A4">
			<div style="">
		<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
		
		<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
		PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
		</center></div>
		
			<table width="100%" style="height:100%;">	
				<tr >
					<td class="border-less" >
						<p>TEL <?=$companyPhoneNo;?><br>
						<?=$companyEmail;?></p>
					</td>
					<td width="40%" class="center nowrap">
						ĢENERĀLĀS KRAVAS<br>IZDOŠANAS AKTS
					</td>
					<td width="10%">
						Numurs:<br><br><center><?=$actNo;?></center>
					</td>			
				</tr>
				<tr id="page" width="30%">
					<td width="40%" rowspan="5">
							&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
							&nbsp;&nbsp;vārdā izdodot kravu:
							<br>
							<br>
							<br>
							<br>
							<br>
							&nbsp;&nbsp;PARAKSTS:<br>
							&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
					</td>		
				</tr>
				<tr>
					<td width="40%">
						Firma / Pieteikuma izdevējs (vārds, uzvārds)
					</td>
					<td width="20%">
						Datums
					</td>	
				</tr>		
				<tr>
					<td height="30px" class="center nowrap" width="40%">
						<?=$companyWho;?>
					</td>
					<td  height="30px" class="center nowrap" width="10%">
						<?=$idate;?>
					</td>
				</tr>
		
				<tr>
					<td width="40%">
						Pieteikums Nr. / Datums
					</td>
					<td width="10%">
						Eksp / Imp. / Tranzīts&nbsp;
					</td>
				</tr>
		
				<tr>
					<td  height="40px" class="nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$landingDate;?></div>
					</td>
					<td  height="40px" class="center nowrap" width="10%">
						<?php
						
							$cs = explode(',', $cargo_status);
							$vas = array();
							foreach($cs as $va){
								if($va=='C'){
									array_push($vas, 'EKSPORTS'); //echo 'EKSPORTS';
								}
	
								if($va=='N'){
									array_push($vas, 'TRANZĪTS'); //echo 'TRANZĪTS';
								}

								if($va=='EU'){
									array_push($vas, 'EU'); //echo 'EU';
								}

							}
							$vas = implode(', ', $vas);
							echo $vas;
						
						?>
					</td>
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border" width="25%">
								&nbsp;&nbsp;Līnijas aģents / Līnija
								</td>
								<td class="side-border" width="40%">
								&nbsp;&nbsp;Kuģis
								</td>						
		
								<td class="no-border" width="35%">
								&nbsp;&nbsp;Reisa Nr.
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%">					
						&nbsp;&nbsp;Iekraušanas datums
					</td>			
				</tr>
				
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
								<?=$ship;?>
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								----
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
					<?=$issueDate;?>
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								Gala osta
								</td>
								<td class="side-border center nowrap" width="40%">
								Operācija
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								Transporta veids
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap" >					
						Auto/Dzc.platformas Nr.
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
						<table width="100%" style="height:100%;">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
									IZDOŠANA
								</td>						
		
								<td class="no-border center nowrap" width="35%">
									<?=$transport;?>
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
						vagona sarakstu<br>skatīt pielikumā
					</td>			
				</tr>		
		
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable1" class="itable1">
							<tr>
								<td class="no-border center" width="4%">
								B/L<br>Nr.
								</td>
								<td class="side-border center" width="54.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
								Kravas nosaukums
								</td>
								<td class="no-border center" width="6%">
								Partija
								</td>
								<td class="side-border center" width="20%">
								Brt. svars, T
								</td>
								<td class="no-border center" width="15%">
								
								</td>						
							</tr>
							
						</table>
					</td>
					<td width="30%" class="center">
						Piezīmes:
					</td>
					
				</tr>
			
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable2" class="itable2">
		
						<tr>
		<?php
		
			$prodQuery = mysqli_query($conn, "
				SELECT 

				(
					SELECT COUNT(li.issue_thisTransport)
					FROM cargo_line AS li
					WHERE li.issuance_id=l.issuance_id AND li.batchNo=l.batchNo 

				) AS tTtotal,

				(
					SELECT SUM(li.issueAmount)
					FROM cargo_line AS li
					WHERE li.issuance_id=l.issuance_id AND li.batchNo=l.batchNo 

				) AS issueAmount,
				
				 l.productNr, l.serialNo,  l.batchNo, l.issue_resource,
				l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.issue_place_count,
				l.issueGross, i.transport, h.transport AS recive_transport, l.thisTransport, h.deliveryDate, l.issue_declaration_type_no
				FROM cargo_header AS h

				JOIN cargo_line AS l
				ON h.docNr=l.docNr

				JOIN n_items AS n
				ON l.productNr=n.code
	
				JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				
				WHERE l.issuance_id='".$id."'
				GROUP BY l.batchNo, l.issue_resource
				order by h.deliveryDate DESC, l.productNr, l.thisTransport, l.serialNo

				
			") or die(mysqli_error($conn));
			$number = mysqli_num_rows($prodQuery);
			$i = 1;
		
			$place_count = $gross = $net = $lastProd = $lastTransp = $lastName = null;
			while($pr_row = mysqli_fetch_array($prodQuery)){
				$place_count += $pr_row['issue_place_count'];
				$gross += $pr_row['issueGross'];
				$net += $pr_row['issueAmount'];
		
				
		?>
							
						<td class="no-border center" width="4%">
													
						</td>

						<td class="side-border center" width="54.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
							
							<?php

							if($lastProd!=$pr_row['productNr']){
								echo $pr_row['name'].'<br>';
							}	
							$ltr=$pr_row['tTtotal'].' vag';
							if($lastTransp!=$ltr){
								echo $pr_row['tTtotal'].' vag';
							}							

							?>	
		
							
						</td>

						<td class="no-border center" width="6%" style="vertical-align:top;">				
							<?php
							 
							 if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
							 if($lastTransp!=$ltr){ echo '<br>'; }		
							 if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }						 
								
								echo $pr_row['batchNo'];
							
							?>	
								
						</td>

						<td class="side-border center" width="20%" style="vertical-align:top;">	
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }
								
								if($pr_row['issueAmount']>0){
									echo floatval($pr_row['issueAmount']);
								}else{
									echo ' ';
								}
								
								if($number==$i){ 
									echo '<br><br><br><br>'; 
									if($net>0){
										echo $net;
									}else{
										echo ' ';
									}
								} 
							?>					
						</td>

						<td class="no-border center" width="15%" style="vertical-align:top;">
									
							<?php
								
								if($lastProd!=$pr_row['productNr']){ echo '<br>'; }
								if($lastTransp!=$ltr){ echo '<br>'; }	
								if($lastName!='' && $lastName!=$pr_row['name']){ echo '<br>'; }							
								echo $pr_row['issue_resource'];
		
							?>
							
						</td>

	

					</tr>
		
		<?php 
		
			$i++;
			$lastProd = $pr_row['productNr'];
			$lastTransp = $pr_row['tTtotal'].' vag';
			$lastName = $pr_row['name'];
		 } ?>
		
						</table>
					</td>
					<td width="30%" class="nowrap" style="vertical-align:top">
					<br>
						<center>
	
							
						
							Visa krava izdota<br>noliktavā:<br>
							<?=$locations;?>
							&nbsp;
						</center>
					</td>			
				</tr>		






				<tr id="page" width="30%">
				<td colspan="3" >
					<table width="100%" rules="all" class="secondt">


		 				<tr>
						 	<td colspan="2" align="center">&nbsp;</td>
							 <td rowspan="2" align="center"><center><?=$companyWho;?></center></td>
						</tr> 

						<tr>
							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
						
							
		 				</tr>




						<tr> 

							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>

							<td>
								
								<br>
							
								<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
								<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
								<br>&nbsp;&nbsp;Pilnvarotā persona pieņemot kravu terminālam
							</td>
						</tr>
						
					
					

					

						<tr>
							
							<td colspan="2" rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
						
							<td rowspan="2" >&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
							
						</tr>
						
					</table>
				</td>
			</tr>

			</table>
			</div>
		</page>
				
		</body>
		</html>			
		<?php
	}

	if($form==4){

		$weightFormat = getSettingUOM($conn, 'receipt_report_uom');	
	
	
		$cargo = mysqli_query($conn, "
				SELECT c.lastBy, i.issuance_act_no, c.deliveryDate, i.application_no, i.applicationDate, i.transport_name, i.transport, c.ship, c.docNr, i.clientCode,
				i.issueDate, i.date, i.createdBy, i.editedBy, c.ownerCode
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
		$crow = mysqli_fetch_array($cargo);	
	
	
		$issueDate = date('d.m.Y', strtotime($crow['issueDate']));
		$idate = date('d.m.Y', strtotime($crow['date']));
	
		if($crow['editedBy']==0){
			$lastBy = returnMeWho($crow['createdBy']);
		}
		if($crow['editedBy']>0){
			$lastBy = returnMeWho($crow['editedBy']);
		}	
	
		$actNo  = $crow['issuance_act_no'];
		$deliveryDate = date('d.m.Y', strtotime($crow['deliveryDate']));
		
		$applicationNo  = $crow['application_no'];
		$landingDate = date('d.m.Y', strtotime($crow['applicationDate']));
		
		$transport = mb_strtoupper($crow['transport'], 'UTF-8');		
		
		$getShips = mysqli_query($conn, "
				SELECT i.transport_name, c.ship, l.issue_declaration_type_no
				FROM cargo_header AS c
				LEFT JOIN cargo_line AS l
				ON c.docNr=l.docNr
				LEFT JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
			");
			
		$shipsIn = $shipsOut = $decNo = array();
		while($ships = mysqli_fetch_array($getShips)){
			
			if($ships['ship']){
				$shipsIn[] = $ships['ship'];
			}
			if($ships['transport_name']){
				$shipsOut[] = $ships['transport_name'];
			}
			
			if($ships['issue_declaration_type_no']){
				$decNo[] = $ships['issue_declaration_type_no'];
			}
		}
		
		$shipsIn = array_unique($shipsIn);
		$shipsOut = array_unique($shipsOut);
		$decNo = array_unique($decNo);
	
		
		if(COUNT($shipsIn)>0){
			$ship = implode(',', $shipsIn);
		}else{
			$ship = implode(',', $shipsOut);
		}	
		
		
		$linesTransport = mysqli_query($conn, "
				SELECT GROUP_CONCAT(DISTINCT(c.issue_thisTransport) separator '<br>') AS transports 
				FROM cargo_line AS c
				LEFT JOIN issuance_doc AS i
				ON c.issuance_id=i.issuance_id
				WHERE c.issuance_id='".$id."'			
				");
		$allT = mysqli_fetch_array($linesTransport);
		
		$issue_header_info = mysqli_query($conn, "
				SELECT thisTransport, declaration_type_no, cargo_status, transport_name
				FROM issuance_doc
				WHERE issuance_id='".$id."'			
				");
		$ihi = mysqli_fetch_array($issue_header_info);
	
		$transport_name = $ihi['transport_name'];
	
		if($ihi['thisTransport']){
			$thisTransport = $ihi['thisTransport'];
		}else{
			$thisTransport = $allT['transports'];
		}
		
		if(COUNT($decNo)>0){
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}
		}else{
			if($ihi['declaration_type_no']){
				$issue_declaration_type_no = $ihi['declaration_type_no'];
			}else{
				$issue_declaration_type_no = implode(',<br>', $decNo);
			}		
		}
		
		$linesLocation = mysqli_query($conn, "
						SELECT 
	
						GROUP_CONCAT(
							DISTINCT CONCAT(c.location,' - ',l.name) 
							SEPARATOR '<br>'
						) AS locations,
						GROUP_CONCAT(DISTINCT(c.cargo_status) separator ',') AS cargo_status
						
		
						FROM cargo_line AS c
						LEFT JOIN n_location AS l
						ON c.location=l.id
	
						LEFT JOIN issuance_doc AS i
						ON c.issuance_id=i.issuance_id
						WHERE c.issuance_id='".$id."'

					") or die (mysqli_error($conn));
		$allL = mysqli_fetch_array($linesLocation);
		
		$locations = $allL['locations'];
		$cargo_status = $allL['cargo_status'];
		

		$companyWho = getCustInfo($conn, 'Name', $crow['ownerCode']);
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
		
	
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
		
		table, th, td {
				padding: 3px 8px;
				border: 1px solid #666;
				border-collapse: collapse;
			font-size: 13px;
		}
		
		th.invalid, td.invalid {
				border: 1px double #666;
				padding:0 !important;
				margin:0 !important;
		}
		
		#table {
				max-width: 2480px;
				width:100%;
			font-size:	15px;
		}
		
		
		.border-less {
				border-top: 1px solid #FFFFFF;
				border-left: 1px solid #FFFFFF;
		}
		.no-border {
				border: 1px solid #FFFFFF;
		}
		.no-border-all {
				border: 1px double #FFFFFF;
		}
		.side-border {
			border-top: 1px solid #FFFFFF;
			border-bottom: 1px solid #FFFFFF;
				border-left: 1px double #666;
			border-right: 1px solid #666;
		}
		#page td {
			 padding:0; margin:0;
		}
		
		#page {
			 border-collapse: collapse;
		}
		
		.secondt {
			border-collapse: collapse;
			border-style: hidden;
		}
		.center {
				text-align:center;
		}
		.nowrap {
			white-space:nowrap;
		}
		
		table.itable1  { width: 100%;table-layout:fixed;word-wrap:normal; }
		table.itable2  { width: 100%;table-layout:fixed;word-wrap:normal; }
		</style>
		
		
		<page size="A4">
			<div style="">
		<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -95px;">
		
		<div style="padding-left: 140px;"><center><p style="display: inline-block;"></p><h1 style="display: inline-block;">STENA LINE PORTS VENTSPILS, AS</h1><br>
		PLOSTA IELA 7, VENTSPILS, LATVIJA, LV-3601<br><br><br>
		</center></div>
		
			<table width="100%" style="height:100%;">	
				<tr >
					<td class="border-less" >
						<p>TEL <?=$companyPhoneNo;?><br>
						<?=$companyEmail;?></p>
					</td>
					<td width="40%" class="center nowrap">
						ĢENERĀLĀS KRAVAS<br>IZDOŠANAS AKTS
					</td>
					<td width="10%">
						Numurs:<br><br><center><?=$actNo;?></center>
					</td>			
				</tr>
				<tr id="page" width="30%">
					<td width="40%" rowspan="5">
							&nbsp;&nbsp;STENA LINE PORTS VENTSPILS<br>
							&nbsp;&nbsp;vārdā izdodot kravu:
							<br>
							<br>
							<br>
							<br>
							<br>
							&nbsp;&nbsp;PARAKSTS:<br>
							&nbsp;&nbsp;VĀRDS, UZVĀRDS: <?=$lastBy;?>
					</td>		
				</tr>
				<tr>
					<td width="40%">
						Firma / Pieteikuma izdevējs (vārds, uzvārds)
					</td>
					<td width="20%">
						Datums
					</td>	
				</tr>		
				<tr>
					<td height="30px" class="center nowrap" width="40%">
						<?=$companyWho;?>
					</td>
					<td  height="30px" class="center nowrap" width="10%">
						<?=$issueDate;?>
					</td>
				</tr>
		
				<tr>
					<td width="40%">
						Pieteikums Nr. / Datums
					</td>
					<td width="10%">
						Eksp / Imp. / Tranzīts&nbsp;
					</td>
				</tr>
		
				<tr>
					<td  height="40px" class="nowrap" width="40%">
					<?=$applicationNo;?> <div style="display: inline-block; margin-left: 70px;"><?=$landingDate;?></div>
					</td>
					<td  height="40px" class="center nowrap" width="10%">
						<?php
						
							$cargoSt = $ihi['cargo_status'];
						
							if($cargoSt=='C'){ echo 'EKSPORTS'; }
							if($cargoSt=='N'){ echo 'TRANZĪTS'; }
							if($cargoSt=='EU'){ echo 'EU'; }

						?>
					</td>
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border" width="25%">
								&nbsp;&nbsp;Līnijas aģents / Līnija
								</td>
								<td class="side-border" width="40%">
								&nbsp;&nbsp;Kuģis
								</td>						
		
								<td class="no-border" width="35%">
								&nbsp;&nbsp;Reisa Nr.
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%">					
						&nbsp;&nbsp;Iekraušanas datums
					</td>			
				</tr>
				
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
								<?=$transport_name;?>
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								----
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
						<?=$idate;?> - <?=$issueDate;?>
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2">
						<table width="100%">
							<tr>
								<td class="no-border center nowrap" width="25%">
								Gala osta
								</td>
								<td class="side-border center nowrap" width="40%">
								Operācija
								</td>						
		
								<td class="no-border center nowrap" width="35%">
								Transporta veids
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap" >					
						Auto/Dzc.platformas Nr.
					</td>			
				</tr>
		
				<tr id="page" width="30%">
					<td width="70%" colspan="2" style="padding: 0; height:100%" valign="top">
						<table width="100%" style="height:100%;">
							<tr>
								<td class="no-border center nowrap" width="25%">
								----
								</td>
								<td class="side-border center nowrap" width="40%">
									IZDOŠANA
								</td>						
		
								<td class="no-border center nowrap" width="35%">
									NO NOLIKTAVAS
								</td>						
							</tr>
						</table>
					</td>
					<td width="30%" class="center nowrap">					
						----
					</td>			
				</tr>		
		

				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable1" class="itable1">
							<tr>
								<td class="no-border center" width="4%">
								B/L<br>Nr.
								</td>
								<td class="side-border center" width="40.6%"> <?php // bija 55% nomainīts lai līnijas saietu; ?>	
								Kravas nosaukums
								</td>
								<td class="no-border center" width="20%">
								Vietu skaits
								</td>
								<td class="side-border center" width="20%">
								Brt. svars, T
								</td>
								<td class="no-border center" width="15%">
									Net. svars, <br>tonnās
								</td>						
							</tr>
							
						</table>
					</td>
					<td width="30%" class="center">
						Piezīmes:
					</td>
					
				</tr>
			
				<tr id="page" >
					<td width="70%" colspan="2">
						<table width="100%" id="itable2" class="itable2">
		
						<tr>
		<?php
		
			$prodQuery = mysqli_query($conn, "
				SELECT 

				(
					SELECT COUNT(li.issue_thisTransport)
					FROM cargo_line AS li
					WHERE li.issue_resource=l.issue_resource AND li.productNr=l.productNr AND li.issuance_id='".$id."'

				) AS tTtotal,

				(
					SELECT SUM(li.issueAmount)
					FROM cargo_line AS li
					WHERE li.issue_resource=l.issue_resource AND li.productNr=l.productNr AND li.issuance_id='".$id."'

				) AS issueAmount,
				
				(
					SELECT SUM(li.amount)
					FROM cargo_line AS li
					WHERE li.issue_resource=l.issue_resource AND li.productNr=l.productNr AND li.issuance_id='".$id."'

				) AS amount,

				 l.productNr, l.serialNo,  l.batchNo, l.issue_resource,
				l.thisDate, CONCAT(COALESCE(n.name1,''), ' ', COALESCE(n.name2,'')) AS name, h.docNr, l.issue_place_count,
				l.issueGross, i.transport, h.transport AS recive_transport, l.thisTransport, h.deliveryDate, l.issue_declaration_type_no
				FROM cargo_header AS h
				JOIN cargo_line AS l
				ON h.docNr=l.docNr
				JOIN n_items AS n
				ON l.productNr=n.code
	
				JOIN issuance_doc AS i
				ON l.issuance_id=i.issuance_id
				WHERE l.issuance_id='".$id."'
				GROUP BY l.productNr, l.issue_resource
				order by h.deliveryDate DESC, l.productNr, l.thisTransport, l.serialNo

				
			") or die(mysqli_error($conn));
			
			$number = mysqli_num_rows($prodQuery);
			$i = 1;
		
			$place_count = $gross = $net = $lastProd = $lastTransp = $lastName = null;
			while($pr_row = mysqli_fetch_array($prodQuery)){
				$place_count += $pr_row['issue_place_count'];
				$gross += $pr_row['issueGross'];
				$net += $pr_row['issueAmount'];
		
				
		?>
							
						<td class="no-border center" width="4%">
										
						</td>

						<td class="side-border center" width="40.6%" style="vertical-align:top">	<?php // bija 55% nomainīts lai līnijas saietu; ?>								
							
							<?php

							if($lastProd!=$pr_row['productNr']){
								echo '<br>'.$pr_row['name'].'<br>';
							}	

							?>	
		
							
						</td>

						<td class="no-border center" width="20%" style="vertical-align:top;">				
							<?php

								echo '<br>'.$pr_row['issue_resource'].'<br>';
							
							?>	
								
						</td>

						<td class="side-border center" width="20%" style="vertical-align:top;">	
							<?php
								
								if($pr_row['issueAmount']>0){
									echo '<br>'.floatval($pr_row['issueAmount']).' <br>';
								}else{
									echo '<br> <br>';
								}
								
								if($number==$i){ 
									echo '<br><br><br><br>'; 
									if($net>0){
										echo $net;
									}else{
										echo ' ';
									}
								} 
							?>					
						</td>

						<td class="no-border center" width="15%" style="vertical-align:top;">
							
						</td>

					</tr>
		
		<?php 
		
			$i++;
			$lastProd = $pr_row['productNr'];
			
			$lastName = $pr_row['name'];
		 } ?>
		
						</table>
					</td>
					<td width="30%" class="nowrap" style="vertical-align:top">
					<br>
						<center>
	
							
						
							Visa krava izdota<br>noliktavā:<br>
							<?=$locations;?>
							&nbsp;
						</center>
					</td>			
				</tr>		

		 		<tr>
				 	<td colspan="6">
		 				<b>
						 	Krava izdota saskaņā ar pavaddokumentiem.<br>
		 					Pretenzijas par kravu, daudzumu un kvalitāti tiek pieņemtas tikai izdošanas laikā.
						</b>
					</td>
				</tr>
				<tr id="page" width="30%">
				<td colspan="3" >
					<table width="100%" rules="all" class="secondt">


		 				<tr>
						 	<td colspan="2" align="center">&nbsp;PILNVARAS DATI</td>

							 <td rowspan="3">
							 	&nbsp;PIEZĪMES
								 <br><br><br><center><?=$companyWho;?></center><br><br>
							
								<div style="display: inline-block;">&nbsp;&nbsp;Vārds, Uzvārds</div>  
								<div style="display: inline-block; margin-left: 110px;">&nbsp;Paraksts</div>
								
							</td>							 
							
						</tr> 

						<tr>
							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvarotā persona - vārds, uzvārds:<br><br><br></td>
						
							
		 				</tr>



						<tr> 

							<td colspan="2" height="50">&nbsp;&nbsp;Pilnvaras Nr./izdevējs/izdošanas datums:<br><br><br></td>

							
						</tr>
						
					
					

					

						<tr>
							
							<td colspan="2" rowspan="2" height="50">&nbsp;&nbsp;Pases dati (Pases Nr., izdevējs):<br><br><br></td>
						
							<td rowspan="2" >&nbsp;&nbsp;MUITAS PIEZĪMES<br><br><br></td>
							
						</tr>
						
					</table>
				</td>
			</tr>

	
				
			</table>
			</div>
		</page>
				
		</body>
		</html>			
		<?php
	}














	































} //IZSNIEGŠANA

if($view=='tpage'){
?>
<!DOCTYPE html>
<html>
<head>
<title>Tālmaņa Lapa</title>

<style>
body {
  margin: 0 auto; 
}
page {
	
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
}
page[size="A4"] {  

  width: 21cm;
  height: 29.7cm; 
}
page[size="A4"][layout="portrait"] {

  width: 29.7cm;
  height: 21cm;  
}
@media print {
  body, page {
    margin: 0;
    box-shadow: 0;
  }
}

.tSame {
	 width: 33.33333333333333%;
	 font-size: 0.9em;
}
.tBorder {
  border-left: 1px solid #666;
   border-spacing: 0px;
}
.aBorder {
  border: 1px solid #666;
  border-spacing: 0px;
}
.center {
	text-align: center;
}
.pagebreak { 
	margin: 15px;
	page-break-before: always; 
}

</style>
</head>
<body >
<?php

if($eid){
	$getInfo = mysqli_query($conn, "SELECT transport, date, time_from, time_to, place, brigade FROM issuance_doc WHERE issuance_id='".mysqli_real_escape_string($conn, $eid)."'");
	if(mysqli_num_rows($getInfo)>0){
		$gIrow = mysqli_fetch_array($getInfo);
		$transport = $gIrow['transport'];
		$date = $gIrow['date'];
		$time_from = $gIrow['time_from'];
		$time_to = $gIrow['time_to'];
		$place = $gIrow['place'];
		$brigade = $gIrow['brigade'];
	}else{
		$transport = NULL;
		$date = NULL;
		$time_from = NULL;
		$time_to = NULL;
		$place = NULL;
		$brigade = NULL;
	}
	
}

?>
<page size="A4">
	<div style="margin: 0px 15px 0px 15px;">
	<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -100px;">

	<div style="padding-left: 140px; text-transform: uppercase; ">
		<center> 
			<div style=" font-size: 1.9em;"><b>STENA LINE PORTS VENTSPILS, AS</b></div>
		</center>
		<center>	
			<div style="display: inline-block; font-size: 1.9em;"><b>tālmaņa lapa-c</b></div>
			<div style="display: inline-block; font-size: 1.4em; float: right; margin-top: 10px;"><b>iekraušana</b></div>
		</center>
	</div>
	<br><br>

	<style>
	.item {
	  display: flex;
	  justify-content: space-between;
	}

	.descripcion {
	  overflow: hidden;
	  white-space: nowrap;
	}

	.descripcion:after {
	  content: "...................................................."
	}

	.whitespace {
		white-space: nowrap;
	}
	</style>

	<div class="item" style="max-width: 90%;">

		<div class="descripcion">Kuģis</div>

		<div class="descripcion"><?=$transport;?></div>

		<div class="descripcion">Datums</div>

		<div class="descripcion"><?=$date;?></div>

	</div>


	<div style="display: inline-block; max-width: 45%;">

		<div class="item" style="">

			<div class="descripcion">Darbs no</div>

			<div class="descripcion"><?=$time_from;?></div>

			<div class="descripcion">līdz</div>

			<div class="descripcion"><?=$time_to;?></div>

		</div>

		<div class="item">

			<div class="descripcion">Brigāde</div>

			<div class="descripcion"><?=$brigade;?></div>

		</div>

		<div class="item">

			<div class="whitespace">Iekraušanas vieta: rūme</div>

			<div class="descripcion"></div>

			<div class="descripcion"><?=$place;?></div>

			<div class="whitespace"> klājs (pasvītrot)</div>

		</div>

	</div>

	<div style="display: inline-block; border: 1px solid #666; width: 410px;">
		Aizpildīja:<br>
		<div style="max-width: 80%;">

			<div class="item">

				<div class="descripcion"></div>

				<div class="whitespace"><?=returnMeWho($myid);?></div>

				<div class="descripcion"></div>

			</div>	
		
			<div style="text-align: center;">(vārds, uzvārds)</div>
		</div>
	</div>
	<br><br>

	<table border="1" class="tSame aBorder" style="float:left;">
		<thead>
			<tr>
				<td class="center">Nr.<br>p.k.</td>
				<td class="center">Marka</td>
				<td class="center">Pakas Nr.</td>
				<td class="center">M3</td>
			</tr>
		</thead>	
		<tbody>


		
	<?php 

	if($id){
		$getCard = mysqli_query($conn, "SELECT docNr FROM cargo_header WHERE id='".intval($id)."'");
		if(mysqli_num_rows($getCard)>0){
			$gCrow = mysqli_fetch_array($getCard);
			$docNr = " AND docNr='".$gCrow['docNr']."' AND status='40'";
		}else{
			$docNr = NULL;
		}
		
	}

	$count = mysqli_query($conn, "SELECT batchNo FROM cargo_line WHERE issuance_id='".mysqli_real_escape_string($conn, $eid)."' ".$docNr." ORDER BY id ASC");

	$lines = mysqli_num_rows($count);

	if($lines>0){
			for($i=1; $i<=$lines; $i++) { 
			
	$row = mysqli_fetch_array($count);		
			?>	


			<tr>
				<td class="center"> <?=$i;?></td>
				<td> </td>
				<td> <?=$row['batchNo'];?></td>
				<td> </td>
			</tr>
	<?php
	  if (($i % 34) == 0) {
		echo '<tr><td class="center" colspan="4">KOPĀ M3</td></tr>';
	  }
	  if($i==$lines){
		 echo '<tr><td align="center" colspan="4" >KOPĀ M3</td></tr>';
		 echo '<tr><td colspan="4" >iekrauto paku skaits: '.$i.'</td></tr>';
		 echo '<tr><td colspan="4">PAVISAM KOPĀ M3</td></tr>';	 
	  }
	?>	





	<?php

	  if (($i % 34) == 0) {
		echo '
		</tbody>
	</table>';
	if($i>=100){ echo '<br><div class="pagebreak"> </div>'; }
	echo '
	<table border="1" class="tSame aBorder" style="float:left;">
		<thead>
			<tr>
				<td class="center">Nr.<br>p.k.</td>
				<td class="center">Marka</td>
				<td class="center">Pakas Nr.</td>
				<td class="center">M3</td>
			</tr>
		<thead>	
		<tbody>	
		';
	  }

	 } 
	 
	}else{
		echo 'Nau neviena ieraksta.';
	}


	?>

		</tbody>
	</table> 

		<div style="clear: both;"></div>
		
		<div style="margin-top: 10px;">
		
			<table>
				<tbody>
					<tr>
						<td><b>Tālmanis</b></td>
						<td>__________<?=returnMeWho($myid);?>__________</td>
						<td>&nbsp;<b>Paraksts</b>___________________________</td>
					</tr>
					<tr>
						<td></td>
						<td class="center">(vārds, uzvārds)</td>
						<td></td>
					</tr>	
				</tbody>
			</table>
		
		</div>
	</div>	
</page>

<script>
    window.print();
</script>

</body>
</html>	

<?php } 



if($view=='additional_services'){
	?>	
		<!DOCTYPE html>
		<html>
		<head>
		<title>Akts</title>
		<script src="js/jquery-3.2.1.js"></script>
		</head>
		<body>
		
		
		
		
		<style>
		body {
			margin: 0 auto; 
		}
		page {
			
			background: white;
			display: block;
			margin: 0 auto;
			margin-bottom: 0.5cm;
		}
		page[size="A4"] {  
		
			width: 21cm;
			height: 29.7cm; 
		}
		page[size="A4"][layout="portrait"] {
		
			width: 29.7cm;
			height: 21cm;  
		}
		@media print {
			body, page {
				margin: 0;
				box-shadow: 0;
			}
		}
	
		</style>
	<?php
		require('inc/s.php');
		$lines = mysqli_query($conn, "
						SELECT ash.*, GROUP_CONCAT(DISTINCT(IF(asl.thisTransport='', null, asl.thisTransport))) AS allTransports
						FROM additional_services_header ash
						
						LEFT JOIN additional_services_line asl
						ON ash.docNr=asl.docNr
						
						WHERE ash.id='".intval($id)."'
						
					");
		$row = mysqli_fetch_array($lines) or die(mysqli_error($conn));
		
		$companyWho = getCustInfo($conn, 'Name', $row['clientCode']);
		
		$month_array = array( "1" => "janvāris", "2" => "februāris", "3" => "marts", "4" => "aprīlis", "5" => "maijs", "6" => "jūnijs", "7" => "jūlijs", "8" => "augusts", "9" => "septembris", "10" => "oktobris", "11" => "novembris", "12" => "decembris");
		
		$month = date('n', strtotime($row['deliveryDate']));
		
	?>	
		<page size="A4">

			<center>
				<div style="display: inline-block; height: 80px; ">
					<img src="images/SL.png" alt="Logo" height="62" width="170" style="margin-bottom: -120px">
				</div>
				
				<div style="height: 80px; padding-left: 80px; vertical-align: middle; display: inline-block; margin-bottom: -150px">
						<p style="display: inline-block;"></p><h1 style="display: inline-block;"><u>STENA LINE PORTS VENTSPILS, AS</u></h1>
				</div>
				<div style="height: 100px;"></div>
				<p>Vispārējas formas</p>
				<p>AKTS Nr. <?=$row['acceptance_act_no'];?></p>
				<p><?php echo date('Y', strtotime($row['deliveryDate'])).'.gada '.date('d', strtotime($row['deliveryDate'])).'.'.$month_array[$month]; ?></p>
				<br><br>
			</center>	
				
			<div style="margin: 90px; 0px; 90px; 0px;">	
				<b>Sastādīts šādu personu klātbūtnē:</b><br>
				<?php
					if($row['home_delegate']){echo 'SIA "NNVT" pārstāvis: '.$row['home_delegate'].'<br>';}
					if($row['client_delegate']){echo $companyWho.' pārstāvis: '.$row['client_delegate'].'<br>';}
				?><br>
				
				<b>Kravas nosaukums:</b> <?=$row['client_product'];?> <?=$row['cargo_name'];?><br><br>
				
				<b><?=ucfirst($row['transport']);?> Nr.:</b> <?=$row['allTransports'];?><br><br>
				
				<b>Pavadzīmes, konosamenta Nr.:</b> <?=$row['ladingNr'];?><br><br>
				
				<b>Akta sastādīšanas cēloņa apstākļu apraksts:</b> <br><?=$row['description'];?><br><br>
				
				
				<b>Veiktie darbi:</b><br>
				<?php 
				$jobsDone = mysqli_query($conn, "
					SELECT asl.*, nr.name
					FROM additional_services_line asl
					LEFT JOIN n_resource nr
					ON asl.resource=nr.id
					WHERE docNr='".$row['docNr']."'
					ORDER BY asl.id
				");
				while($roz = mysqli_fetch_array($jobsDone)){
					
					if($roz['resource']){
						echo 'Pakalpojums: '.$roz['resource'].' ('.$roz['name'].') ';
					}
					if($roz['serialNo']){
						echo 'Seriālais Nr.: '.$roz['serialNo'].' ';
					}
					if($roz['productUmo']){
						echo 'Mērvienība: '.$roz['productUmo'].' ';
					}
					if($roz['amount']>0){
						echo 'Daudzums: '.floatval($roz['amount']).' ';
					}
					echo $roz['comment'].'<br><br>';
				}
				
				?>
				<br><br>
				
				
				<div style="display: inline-block; float: left;"><b>Paraksti:</b></div>
				<div style="display: inline-block; float: right;">
				<?php
					if($row['home_delegate']){echo $row['home_delegate'].'<br><br>';}
					if($row['client_delegate']){echo $row['client_delegate'].'<br>';}
				?>
				</div><br>
			</div>	
				
		</page>
		
		
	</html>	
	<script>
		window.print();
	</script>
	<?php	
	}
	
	
	
	?>

