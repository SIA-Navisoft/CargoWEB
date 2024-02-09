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
$id = $view = $eid = null;
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['eid'])){$eid = htmlentities($_GET['eid'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['form'])){$form = htmlentities($_GET['form'], ENT_QUOTES, "UTF-8");}


if($myid!=29){
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=prepare_attachment_".$from."_".$to.".xls");
}

echo '<style>
		.noborders > td{
			border-color: transparent;
		border-bottom-style: hidden;
		border-right-style: hidden;
		border-left-style: hidden;
		}
	  </style>';

echo '<table border="1">
		<thead>
			<tr>
				<td>Pieņemšanas datums</td>
				<td>Pieņemšanas akta Nr.</td>
				<td>Vagona Nr.</td>
				<td>tonnas</td>
				<td>Marka LOT</td>
				<td>Dzelzceļa pavadzīmes Nr.</td>
				<td>Deklarācijas Nr.</td>
				<td>Izkraušanas datums</td>
				<td>Vagonu skaits</td>
				<td>Izdošanas datums</td>
				<td>Izdošanas dokuments</td>				
				<td>M/k nosaukums</td>
			</tr>
		</thead>
		<tbody>';

				$query = mysqli_query($conn, "
						SELECT l.activityDate, l.thisTransport, l.amount, l.batchNo, l.ladingNr, l.declaration_type_no, l.place_count, h.acceptance_act_date, 
						h.acceptance_act_no,
						d.issueDate, d.issuance_id, d.transport_name
						FROM cargo_line AS l

						LEFT JOIN cargo_header AS h
						ON l.docNr=h.docNr

						LEFT JOIN issuance_doc AS d
						ON l.issuance_id=d.issuance_id
						
						

						WHERE l.issuance_id='".$id."'
				");

				if(mysqli_num_rows($query)>0){

					$totAmount=$totPlaceCount=null;
					while($row = mysqli_fetch_array($query)){
						$totAmount+=floatval($row['amount']);
						$totPlaceCount+=$row['place_count'];
						
						echo '
						<tr>
							<td>'.date('d.m.Y', strtotime($row['acceptance_act_date'])).'</td>
							<td>'.$row['acceptance_act_no'].'</td>
							<td>'.$row['thisTransport'].'</td>
							<td align="right" style="vnd.ms-excel.numberformat:0.00">'.floatval($row['amount']).'</td>
							<td align="right" style="vnd.ms-excel.numberformat:0">'.$row['batchNo'].'</td>
							<td>'.$row['ladingNr'].'</td>
							<td>'.$row['declaration_type_no'].'</td>
							<td>'.date('d.m.Y', strtotime($row['activityDate'])).'</td>
							<td align="right" style="vnd.ms-excel.numberformat:0">'.$row['place_count'].'</td>
							<td>'.date('d.m.Y', strtotime($row['issueDate'])).'</td>
							<td>manifests</td>
							<td>'.$row['transport_name'].'</td>
						</tr>';
					}

					if($totAmount>0 || $totPlaceCount>0){
						echo '
						<tr class="noborders">
							<td colspan="3"></td>
							<td align="right" style="vnd.ms-excel.numberformat:0.000">'.$totAmount.'</td>
							<td colspan="4"></td>
							<td align="right" style="vnd.ms-excel.numberformat:0">'.$totPlaceCount.'</td>
							<td colspan="3"></td>
						</tr>
						';
					}
				}else{
					echo '<tr><td colspan="12">nav neviena ieraksta!</td></tr>';
				}

			echo '
		</tbody>
	  </table>';
	
	
	
	?>

