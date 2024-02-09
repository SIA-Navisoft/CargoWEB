<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="stock";


require('inc/s.php');
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

include('functions/base.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;} 

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}

$user_group_array = array("w_user" => "NNVT", "w_partner" => "");


if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}

	require('inc/s.php');
	
	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);	
	$search = " WHERE (code LIKE '%$s%' || barCode LIKE '%$s%' || name1 LIKE '%$s%' || unitOfMeasurement LIKE '%$s%') ORDER BY status DESC, code ASC";
	$searchUrl = 'search='.$s.'&';
		
	}else{
	$searchUrl = null;	
	$search = " ORDER BY status DESC, code ASC";
		
	}
	
	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM n_items ".$search."";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   		
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU
	
	
	
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>
						<th>pamat mērvienība</th>
						<th>svītrkods</th>
						<th>nosaukums</th>
						<th>projekts</th>
						<th>seriālais nr. nav obligāts</th>	
						<th>pēdējās aktivitātes veica</th>					
						<th>darbība</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($resultGL7)){
				?>
				<tr class="classlistedit" onclick="newDoc('<?=$row['code'];?>')">
				<?php
				echo '	

							<td>'.$row['code'].'
							<td>'.$row['unitOfMeasurement'].'
							<td>'.$row['barCode'].'
							<td>'.$row['name1'].'
							<td>'.$row['project_code'];
							if($row['project_name']!=''){echo ' ('.$row['project_name'].')';}
							
							echo '<td>';
							if($row['no_serial']==1){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}							
							
							echo '<td>';
							
							if($row['editedBy']>0){
								echo returnMeWho($row['editedBy']).' ('.date('d.m.Y H:i:s', strtotime($row['editedDate'])).')';
							}else{
								echo returnMeWho($row['createdBy']).' ('.date('d.m.Y H:i:s', strtotime($row['createdDate'])).')';
							}

							echo '
							<td>';
						
							if($row['status']==1){
								?>
								<a class="btn btn-default btn-xs" id="pdel<?=$row['code'];?>" onclick="productDel('<?=$row['code'];?>')"><i class="glyphicon glyphicon-erase" style="color: red"  title="dzēst"></i> dzēst</a>
								<?php
							}
							if($row['status']==0){
								?>
								<a class="btn btn-default btn-xs" id="pret<?=$row['code'];?>" onclick="productRet('<?=$row['code'];?>')"><i class="glyphicon glyphicon-refresh" style="color: #3BC0DE"  title="atjaunot"></i> atjaunot</a>
								<?php
							}
							
							echo '

						</tr>';
			}
			
			echo '</tbody></table></div>';
			
			
		}else{
			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>
						<th>pamat mērvienība</th>
						<th>svītrkods</th>
						<th>nosaukums</th>
						<th>projekts</th>
						<th>seriālais nr. nav obligāts</th>
						<th>pēdējās aktivitātes veica</th>						
						<th>darbība</th>
					</tr></thead><tbody>
					<td colspan="5"><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</td>
					</tbody></table></div>';					
		}
?>