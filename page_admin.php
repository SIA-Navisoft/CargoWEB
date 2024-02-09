<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');
$page_file="page_admin";
require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'  AND (u_rights.p_view = 'on' OR u_rights.p_edit = 'on')");
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
include('header.php');

$currenttime=date('Y-m-d H:i:s');
$month_short_array = array( "1" => "jan", "2" => "feb", "3" => "mar", "4" => "apr", "5" => "mai", "6" => "jūn", "7" => "jūl", "8" => "aug", "9" => "sep", "10" => "okt", "11" => "nov", "12" => "dec");

$view = $action = $section = $id = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['query'])){$query = htmlentities($_GET['query'], ENT_QUOTES, "UTF-8");}

// ADD, EDIT or DELETE
if(($_SERVER["REQUEST_METHOD"] == "POST")&&($p_edit=='on')){
	if (empty($_POST["source"])){$source = null;}else{$source = htmlentities($_POST['source'], ENT_QUOTES, "UTF-8");}

	if ($source=='addTable') {
		$table_file=htmlentities($_POST['table_file'], ENT_QUOTES, "UTF-8");
		$table_header=htmlentities($_POST['table_header'], ENT_QUOTES, "UTF-8");
		
		require('inc/s.php');
		
		$form_data = array(
		'table_file' => $table_file,
		'table_header' => $table_header,
		'createdBy' => $myid
		);
		insertNewRows("setup_tables", $form_data);		
		
		
		mysqli_close($conn);
		header("Location: ".$page_file."?view=viewTables");
		die(0); 
	}
	
	if ($source=='addPage') {
		$page_file=htmlentities($_POST['page_file'], ENT_QUOTES, "UTF-8");
		$page_header=htmlentities($_POST['page_header'], ENT_QUOTES, "UTF-8");
		$page_table=htmlentities($_POST['page_table'], ENT_QUOTES, "UTF-8");
		$page_icon=htmlentities($_POST['page_icon'], ENT_QUOTES, "UTF-8");
		
		require('inc/s.php');
		
		$form_data = array(
		'page_file' => $page_file,
		'page_header' => $page_header,
		'page_table' => $page_table,
		'page_icon' => $page_icon,
		'createdBy' => $myid
		);
		insertNewRows("setup_pages", $form_data);			
		
		$page_header="administrēšanas panelis";
		$page_table="setup_pages";
		$page_file="page_admin";
		header("Location: ".$page_file."");
		die(0); 
		mysqli_close($conn);
	}

}

?>
<style>
.col-centered{
    float: none;
    margin: 0 auto;
}
</style> 
<div class="container-fluid">
<div class="row">

  <div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 
				
					<div class="page-header" style="margin-top: -5px;">
					
				<?php if ($p_edit=='on'){ ?>

							<div class="btn-group btn-group-sm" role="group" aria-label="Small button group"> 
								<a class="btn btn-default <?php if(!$view){echo ' active';}?>" href="<?=$page_file?>"><i class="glyphicon glyphicon-list-alt" style="color: #00B5AD"></i></a>
								<a class="btn btn-default <?php if($view=='viewTables'){echo ' active';}?>" href="<?=$page_file?>?view=viewTables"><i class="glyphicon glyphicon-list-alt" style="color: #00B5AD"></i></a>
								<a class="btn btn-default <?php if($view=='showAllstat'){echo ' active';}?>" href="<?=$page_file?>?view=showAllstat"><i class="glyphicon glyphicon-list-alt" style="color: #00B5AD"></i></a>
								<a class="btn btn-default <?php if($view=='statistics'){echo ' active';}?>" href="<?=$page_file?>?view=statistics"><i class="glyphicon glyphicon-stats" style="color: #00B5AD"></i></a>
								<a class="btn btn-default <?php if($view=='user_rights'){echo ' active';}?>" href="<?=$page_file?>?view=user_rights"><i class="glyphicon glyphicon-list-alt" style="color: #00B5AD"></i></a>
								
								
								<div class="btn-group" role="group">
									<a class="btn btn-sm btn-default <?php if($view=='addPage' || $view=='addTable'){echo ' active';} ?>" id="dropdownMenu1" data-toggle="dropdown">
									  <i class="glyphicon glyphicon-plus" style="color: #00B5AD"></i>
									</a>

								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
									<li><a href="<?=$page_file?>?view=addPage">pievienot lapu</a></li>
									<li><a href="<?=$page_file?>?view=addTable">pievienot tabulu</a></li>
								  </ul>
								</div>



								
								
							</div>

				<?php }else{ ?>
							<a class="btn btn-default" href="<?=$page_file?>?view=statistics"><i class="glyphicon glyphicon-stats" style="color: #00B5AD"></i></a>
				<?php } ?>		
					</div>				
				
<?php
//skats uz lapām
if (!$view){?>

		<i class="text file outline icon"></i>
		<div class="content">
		<div class="header">Lapas (<?countDataAll($page_table)?>)</div>
		<p>Pēdējās izmaiņas veiktas </i> <?lastData($page_table)?></p>
		</div>


	<?php
	require('inc/s.php');
	$result = mysqli_query($conn,"SELECT id, page_file, page_header, page_table, page_icon, createdBy, createdDate FROM {$page_table} ORDER BY page_file");
	if (!$result){die("Query to show fields failed");}
	
	echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>';
	echo '		<th>web lapa</th>
				<th>virsraksts</th>
				<th>galvenā tabula</th>
				<th>ikona</th>
				<th>izveidoja</th>';
	echo '	</tr></thead><tbody>';
	
	while($row = mysqli_fetch_array($result)){
		echo '	<tr onclick="window.location=\''.$row['page_file'].'\'">
					<td>'.$row['page_file'].'
					<td>'.$row['page_header'].'
					<td>'.$row['page_table'].'
					<td>'.$row['page_icon'].'
					<td>'.$row['createdDate'].'
				</tr>';
	}
	
	echo '</tbody></table></div>';
	mysqli_close($conn);
}


if ($view=='showAllstat'){
	require('inc/s.php');
	echo '	Informācija par datubāzes tabulām<br><br/>
		<div class="table-responsive"><table class="table table-hover table-responsive">';
		$i=null;
	$result = mysqli_query($conn,"
		SELECT table_name, table_rows
		FROM INFORMATION_SCHEMA.TABLES
		WHERE TABLE_SCHEMA = 'portalrbssk'");
	while($row = mysqli_fetch_array($result)){
		$i++;
		echo '	<tr>
					<td>'.$i.'
					<td>'.$row['table_name'].'
					<td>'.$row['table_rows'].'
				</tr>';
	}
	
	echo '
		</table></div>
		
			Informācija par php failiem<br/>
			<div class="table-responsive"><table class="table table-hover table-responsive">';
		$i=null;
		$dir = "../";
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if (is_dir('../'.$filename.'/')){
				$i++;
				echo '<tr><td>'.$i.'<td>'.strtoupper($filename).'<td><tr>';
					$dir2 = "../".$filename."/";
					$dh2  = opendir($dir2);
					while (false !== ($filename2 = readdir($dh2))) {
						if (($filename2!='.')||($filename2!='..')){
							$i++;
							echo '<tr><td>'.$i.'<td>'.strtoupper($filename).'<td>'.$filename2.'<tr>';
						}
					}
			}
		}
		
		
		echo '</table></div>';

}
//datubāzes statistika

if ($view=="statistics"){
	require('inc/s.php');
	if ($id){
			if (filter_var($id, FILTER_VALIDATE_EMAIL)){
						$result_name = mysqli_query($conn,"
							SELECT full_name, user_group FROM user WHERE email LIKE '%".$id."%' LIMIT 1");
						$row_name = mysqli_fetch_array($result_name);
						$usr_name = ' - '.$row_name['full_name'].' ('.$row_name['user_group'].'/user)';
			}
			if (is_numeric($id)){
						$result_name = mysqli_query($conn,"
							SELECT full_name, user_group FROM worker WHERE phone LIKE '%".$id."%' LIMIT 1");
						$row_name = mysqli_fetch_array($result_name);
						$usr_name = ' - '.$row_name['full_name'].' ('.$row_name['user_group'].'/worker)';
			}
			echo '	<div class="table-responsive"><table class="table table-hover table-responsive">
						<tr><td><a href="?view=statistics"><i class="red remove icon"></i></a> '.$id.' '.$usr_name.'
						</tr>
					</table></div>
					';
	}
			if (!$action){$action=1;}
			if (!$section){$section=1;}
			
			$history='-'.($action*$section*7).' days';
			$history_t='-'.(($action-1)*$section*7).' days';
			
			if ($section=='1'){$s1="active red";}
			if ($section=='2'){$s2="active red";}
			if ($section=='4'){$s4="active red";}
			
			echo '
			<a href="?view=statistics&action='.($action+1).'&id='.$id.'&section='.($section).'"><i class="teal angle left icon"></i></a> Pieslēgumi no '.date('Y-m-d', strtotime($history)).' līdz '.date('Y-m-d', strtotime($history_t)).' 
			<a href="?view=statistics&action='.($action-1).'&id='.$id.'&section='.($section).'"><i class="teal right angle icon"></i></a><br><br>
			<div class="table-responsive"><table class="table table-hover table-responsive"><tr><td><div class="btn-group btn-group-sm" role="group">
						<a class="btn btn-default btn-sm '; if($section=='1'){echo ' active';} echo '" href="?view=statistics&id='.$id.'&section=1">NEDĒĻA</a>
						<a class="btn btn-default btn-sm '; if($section=='2'){echo ' active';} echo '" href="?view=statistics&id='.$id.'&section=2">2 NEDĒĻAS</a>
						<a class="btn btn-default btn-sm '; if($section=='4'){echo ' active';} echo '" href="?view=statistics&id='.$id.'&section=4">4 NEDĒĻAS</a>
					</div>
			</tr></table></div>
			<br/>';
			
			//PORTĀLA NOSLODZES GRAFIKS
					echo '	<script src="../js/chart.js"></script>';
					
					$month_name=$i=null;
										
					$row_p=date('Y-m-d', strtotime($history));
					while($i <= ($section*7)){
						
						if (is_float($i/$section)){
							$month_name=$month_name.'"",';
						}else{
							$month_name=$month_name.'"'.date("j", strtotime($row_p)).'.'.$month_short_array[date("n", strtotime($row_p))].'",';
						}
						
						$result_count = mysqli_query($conn,"
							SELECT COUNT(id) AS counter, ok
							FROM user_stat
							WHERE DATE_FORMAT(datums, '%Y-%m-%d') = '".$row_p."' AND user_name LIKE '%".$id."%'
							GROUP BY ok");
						if (!$result_count){die("Query to show fields failed");}
						$y_water=2;
						$row_count_h=0;
						$row_count_c=0;
						while($row_count = mysqli_fetch_array($result_count)){
							if ($row_count['ok']=="n"){$row_count_h=$row_count['counter'];}
							if ($row_count['ok']=="y"){$row_count_c=$row_count['counter'];}									
							++$y_water;
						}
						$row_count_h=$row_count_h+0;
						$row_count_c=$row_count_c+0;
						$counter_water_h=$counter_water_h.''.$row_count_h.',';
						$counter_water_c=$counter_water_c.''.$row_count_c.',';	
						
						++$i;
						
						$row_p = date('Y-m-d', strtotime("+1 days", strtotime($row_p)));
					}
						if ($i>1){
							$counter=substr($counter, 0, -1);
							$month_name=substr($month_name, 0, -1);
							$counter_water_c=substr($counter_water_c, 0, -1);
							$counter_water_h=substr($counter_water_h, 0, -1);
							
							echo '	<div id="canvas-holder" style="max-width:100%;">
										<canvas id="canvas_line" height="60px;"></canvas>
									</div>
									<script>
										var lineChartData = {
											labels : ['.$month_name.'],
											datasets : [
												
												{
													label: "veiksmīgi",
													fillColor : "rgba(217, 92, 92,0.1)",
													strokeColor : "rgba(217, 92, 92,0.8)",
													pointColor : "rgba(217, 92, 92,1)",
													pointStrokeColor : "#fff",
													pointHighlightFill : "#fff",
													pointHighlightStroke : "rgba(217, 92, 92,1)",
													data : ['.$counter_water_h.']
												},
												{
													label: "neveiksmīgi",
													fillColor : "rgba(151,187,205,0.1)",
													strokeColor : "rgba(151,187,205,0.8)",
													pointColor : "rgba(151,187,205,1)",
													pointStrokeColor : "#fff",
													pointHighlightFill : "#fff",
													pointHighlightStroke : "rgba(151,187,205,1)",
													data : ['.$counter_water_c.']
												}
											]

										}
									</script>
									<script>
										window.onload = function(){
												var ctx2 = document.getElementById("canvas_line").getContext("2d");
												window.myLine = new Chart(ctx2).Line(lineChartData, {
													responsive: true, showTooltips: false
												});											
											}
									</script>
							';
						}
	
?>



<?php	
	
	
			echo '	<div class="row">
			<div class="col-xs-6 col-sm-4">
						Pēdējās pieslēgšanās portālam<br/>';
						$result3 = mysqli_query($conn,"SELECT datums, user_name, ok, comp_id FROM user_stat WHERE user_name LIKE '%".$id."%' ORDER BY datums desc LIMIT 20");
						$count_rows3 = mysqli_num_rows($result3);
						echo '	<div class="table-responsive"><table class="table table-hover table-responsive">';
						while($row3 = mysqli_fetch_array($result3)){
							echo '	<tr onclick="window.location=\''.$row['page_file'].'?view=statistics&id='.$row3['user_name'].'\'">
										<td>'.$row3['user_name'].'<td>'.$row3['comp_id'].'<td>'.$row3['datums'].'<td>';
										if ($row3['ok']!='y'){echo '<i class="red warning icon"></i>';}else{echo '<i class="green ok sign icon"></i>';}
							echo '	</tr>';
						}
						echo '</table></div>
			</div>
			
			<div class="col-xs-6 col-sm-4">
						TOP 20 aktīvākie lietotāji<br/>';
						$result3 = mysqli_query($conn,"SELECT count(id) AS counter, user_name, comp_id FROM user_stat WHERE ok='y' AND user_name LIKE '%".$id."%' GROUP BY user_name ORDER BY counter DESC LIMIT 20");
						$count_rows3 = mysqli_num_rows($result3);
						echo '	<div class="table-responsive"><table class="table table-hover table-responsive">';
						while($row3 = mysqli_fetch_array($result3)){
							echo '<tr onclick="window.location=\''.$row['page_file'].'?view=statistics&id='.$row3['user_name'].'\'"><td>'.$row3['user_name'].'<td>'.$row3['comp_id'].'<td>'.$row3['counter'].'</tr>';
						}
						echo '</table></div>';
				echo '
			</div>
			<div class="col-xs-6 col-sm-4">
						Aktīvākās lietotāju grupas<br/>';
						$result3 = mysqli_query($conn,"SELECT count(id) AS counter, SUBSTR(user_name, INSTR(user_name, '@') + 1) AS domain, comp_id FROM user_stat WHERE ok='y' AND user_name LIKE '%".$id."%' GROUP BY SUBSTR(user_name, INSTR(user_name, '@') + 1) ORDER BY counter DESC LIMIT 20");
						$count_rows3 = mysqli_num_rows($result3);
						echo '	<div class="table-responsive"><table class="table table-hover table-responsive">';
						while($row3 = mysqli_fetch_array($result3)){
							echo '<tr onclick="window.location=\''.$row['page_file'].'?view=statistics&id='.$row3['domain'].'\'"><td>'.$row3['domain'].'<td>'.$row3['comp_id'].'<td>'.$row3['counter'].'</tr>';
						}
						echo '</table></div>';
				echo '
			</div>
			</div>
		</div>';

}

//skats uz datubāzes ierakstiem
if ($view=="viewTables"){?>

		<i class="lab icon"></i>
		<div class="content">
		<div class="header">Tabulas (<?countDataAll('setup_tables')?>)</div>
		<p>Pēdējās izmaiņas veiktas </i> <?lastData('setup_tables')?></p>
	    </div>


	<?php
	require('inc/s.php');
	$result = mysqli_query($conn,"SELECT id, table_file, table_header, createdDate FROM setup_tables ORDER BY table_file");
	if (!$result){die("Query to show fields failed");}
	
	echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>';
	echo '		<th>tabula</th>
				<th>apraksts</th>
				<th>izveidoja</th>
				<th align="center">ieraksti</th>';
	echo '	</tr></thead><tbody>';
	
	while($row = mysqli_fetch_array($result)){
		echo '	<tr onclick="window.location=\'?view=tableDetails&tablename='.$row['table_file'].'\'">
					<td>'.$row['table_file'].'
					<td>'.$row['table_header'].'
					<td>'.$row['createdDate'].'
					<td align="center">';
						countDataAll($row['table_file']);
		echo '	</tr>';
	}
	
	echo '</tbody></table></div>';
	mysqli_close($conn);
}

//datu labošana
if ($view=='addPage'){
?>
	<form class="form-horizontal" name="addPage" method="POST" action="" style="width:100%">
		<input type='hidden' name='source' value='addPage'/>


					<h4>TABULAS DATI</h4>

						<div class="form-group">
							<div class="col-md-3">
								<small>lapas virsraksts</small>
								<input type="text" class="form-control" name="page_header">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>php faila nosaukums</small>
								<input type="text" class="form-control" name="page_file">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>lapas ikona</small>
								<input type="text" class="form-control" name="page_icon">
							</div>
						</div>						
					
						<div class="col-md-3">
							<div class="form-group">
							  <small for="sel1">lapas galvenā tabula</small>
							  <select class="form-control" id="sel1" name="page_table">
								<?
								require('inc/s.php');
								$result = mysqli_query($conn,"SELECT table_file FROM setup_tables ORDER BY table_file");
								if (!$result){die("Query to show fields failed");}
					
								while($row = mysqli_fetch_array($result)){
									echo '	<option>'.$row['table_file'].'</option>';
								}
								mysqli_close($conn);
								?>
							  </select>
							</div>				
						</div>					
			
						<div class="clearfix"></div>
						<a class="btn btn-default btn-sm" aria-label="Left Align" href="<?=$page_file?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>

						<a class="btn btn-default btn-sm" aria-label="Left Align" href="javascript:document.addPage.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> pievienot
						</a>									
						
	</form>
<?php
}

//datu labošana
if ($view=='addTable'){
?>
	<form class="form-horizontal" name="addTable" method="POST" action="" style="width:100%">
		<input type='hidden' name='source' value='addTable'/>


					<h4>TABULAS DATI</h4>
					
						<div class="form-group">
							<div class="col-md-3">
								<small>datubāze</small>
								<input type="text" class="form-control" name="table_file">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>nosaukums</small>
								<input type="text" class="form-control" name="table_header">
							</div>
						</div>						
						
						<a class="btn btn-default btn-sm" aria-label="Left Align" href="<?=$page_file?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>

						<a class="btn btn-default btn-sm" aria-label="Left Align" href="javascript:document.addTable.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
						</a>							

	</form>
<?php
}

//datu labošana
if ($view=='tableDetails'){
	if (isset($_GET['tablename'])){$tablename = htmlentities($_GET['tablename'], ENT_QUOTES, "UTF-8");}else{header("Location: ".$page_file."");die(0);}
	echo '	
				<i class="lab icon"></i>
				<div class="content">
					<div class="header">Tabula: '.$tablename.'</div>
				</div>';
			
	//lapu sadalīšana
	require('inc/s.php');
	$result = mysqli_query($conn,"SELECT id FROM {$tablename}");
	if (!$result){die("Query to show fields failed");}
	$rec_count = mysqli_num_rows($result);
	$rec_limit = 100;
	$offset=0;
	
	if ($rec_count>$rec_limit){
		$page = 1;  $total_pages=ceil($rec_count/$rec_limit); $max_pages = 10;
		
		if(!empty($_GET['page'])) {
			$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
			if(false === $page) {$page = 1;}
		}
		if ($page>$total_pages){$page=$total_pages;}
		$offset = ($page - 1) * $rec_limit;
		
		echo '	<nav aria-label="Page navigation">
					<ul class="pagination">';
				if ($page == 1){$status='disabled';$page_back=$page;}else{$status=null;$page_back=$page-1;}
				
				echo '
					<li class="'.$status.'">
					  <a href="?tablename='.$tablename.'&view='.$view.'&page=1" aria-label="Previous">
						<span aria-hidden="true"><i class="glyphicon glyphicon-chevron-left"></i></span>
					  </a>
					</li>
					<li class="'.$status.'">
					  <a href="?tablename='.$tablename.'&view='.$view.'&page='.$page_back.'"" aria-label="Previous">
						<span aria-hidden="true"><i class="glyphicon glyphicon-menu-left"></i></span>
					  </a>
					</li>					
					';

				$u=null;
				for($i=1; $i<=$total_pages; $i++){
					if ($i==$page){$status='active';}else{$status=null;}
					if (($page<($i+5))&&($u<$max_pages)){
						echo '	<li class="'.$status.'"><a href="?tablename='.$tablename.'&view='.$view.'&page='.$i.'">'.$i.'</a></li>';
						++$u;
					}
				}					
					
					
				if ($page == $total_pages){$status='disabled';$page_front=$page;}else{$status=null;$page_front=$page+1;}
				echo '
					<li class="'.$status.'">
					  <a href="?tablename='.$tablename.'&view='.$view.'&page='.$page_front.'" aria-label="Next">
						<span aria-hidden="true"><i class="glyphicon glyphicon-menu-right"></i></span>
					  </a>
					</li>
					<li class="'.$status.'">
					  <a href="?tablename='.$tablename.'&view='.$view.'&page='.$total_pages.'" aria-label="Next">
						<span aria-hidden="true"><i class="glyphicon glyphicon-chevron-right"></i></span>
					  </a>
					</li>					
				';
		
		echo '  </ul>
			</nav>';
	}


	showData($tablename, $offset, $rec_limit);
}


//lietotāji lapām
if ($view=="user_rights"){
	require('inc/s.php');
	$result = mysqli_query($conn,"SELECT id, page_file, page_header, page_table, page_icon, createdBy, createdDate FROM {$page_table} ORDER BY page_file");
	if (!$result){die("Query to show fields failed");}
	
	echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>';
	echo '		<th>web lapa</th>
				<th>virsraksts</th>
				<th>lietošana</th>
				<th>administrēšana</th>';
	echo '	</tr></thead><tbody>';
	
	while($row = mysqli_fetch_array($result)){
		echo '	<tr>
					<td>'.$row['page_file'].'
					<td>'.$row['page_header'].'
					<td>';
					$result2 = mysqli_query($conn,"SELECT user_id FROM user_rights WHERE page_name = '".$row['page_file']."' AND p_view ='on'");
					if (!$result2){die("Attention! Query to show fields failed.");}
					while ($row2 = mysqli_fetch_array($result2)){
						echo returnMeWho($row2['user_id']).'<br/>';
					}
			echo 	'<td>';
			
					$result3 = mysqli_query($conn,"SELECT user_id FROM user_rights WHERE page_name = '".$row['page_file']."' AND p_edit ='on'");
					if (!$result3){die("Attention! Query to show fields failed.");}
					while ($row3 = mysqli_fetch_array($result3)){
						echo returnMeWho($row3['user_id']).'<br/>';
					}

		echo '	</tr>';
	}
	
	echo '</tbody></table></div>';
	mysqli_close($conn);
}
?>

</div>	</div> </div> </div> </div>	
	


<script>
var fileExtentionRange = '.xls .xlsx .csv';

$(document).on('change', '.btn-file :file', function() {
    var input = $(this);

    if (navigator.appVersion.indexOf("MSIE") != -1) { // IE
        var label = input.val();

        input.trigger('fileselect', [ 1, label, 0 ]);
    } else {
        var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        var numFiles = input.get(0).files ? input.get(0).files.length : 1;
        var size = input.get(0).files[0].size;

        input.trigger('fileselect', [ numFiles, label, size ]);
    }
});

$('.btn-file :file').on('fileselect', function(event, numFiles, label, size) {
    $('#capture').attr('name', 'capture'); // allow upload.

    var postfix = label.substr(label.lastIndexOf('.'));
    if (fileExtentionRange.indexOf(postfix.toLowerCase()) > -1) {
        $('#_attachmentName').val(label);
    } else {
        alert('allowed file type：' + fileExtentionRange + '');

        $('#capture').removeAttr('name'); // cancel upload file.
    }
});

</script>

<?php include("footer.php"); ?>