<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="page_users";

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

ob_start();

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
$view = $action = $section = $id = $query = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['query'])){$query = htmlentities($_GET['query'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}

$user_group_array = array("w_user" => "Money Express Credit", "w_partner" => "");

// ADD, EDIT or DELETE
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["source"])){$source = null;}else{$source = htmlentities($_POST['source'], ENT_QUOTES, "UTF-8");}
	
	
	if ($source=='editUserRights') {
		$id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");
		$totalrows = htmlentities($_POST['totalrows'], ENT_QUOTES, "UTF-8");
		
		require('inc/s.php');
		
		if ($totalrows){
			for ($i=1; $i <= $totalrows; ++$i) {
				
				$page = htmlentities($_POST['page'][$i], ENT_QUOTES, "UTF-8");
				$view = htmlentities($_POST['view'][$i], ENT_QUOTES, "UTF-8");
				$edit = htmlentities($_POST['edit'][$i], ENT_QUOTES, "UTF-8");
				
				$result = mysqli_query($conn,"SELECT id FROM user_rights WHERE user_id = '".$id."' AND page_name = '".$page."'");
				if (!$result){die("Attention! Query to show fields failed.");}
				$row = mysqli_fetch_assoc($result);
				$count_rows = mysqli_num_rows($result);
				if ($count_rows=="0"){
					
					$form_data = array(
					'user_id' => $id,
					'page_name' => $page,
					'p_view' => $view,
					'p_edit' => $edit,
					'createdBy' => $myid
					);
					insertNewRows("user_rights", $form_data);					
					
				} else {
					
					$form_data = array(
					'p_view' => $view,
					'p_edit' => $edit,
					'editedBy' => $myid,
					'editedDate' => $currenttime					
					);
					updateSomeRow("user_rights", $form_data, "WHERE id = '".$row['id']."' LIMIT 1");					
					
				}
			}
			$res="done";
		}
		
		
		
		mysqli_close($conn);
		header("Location: ".$page_file."?view=edit&section=2&id=".$id."&res=".$res."");
		die(0); 
	}
	
	
	if ($source=='edit') {
		$full_name = htmlentities($_POST['full_name'], ENT_QUOTES, "UTF-8");
		$phone = htmlentities($_POST['phone'], ENT_QUOTES, "UTF-8");
		$id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");
		$user_objects = htmlentities($_POST['user_objects'], ENT_QUOTES, "UTF-8");
		
		
		include('inc/s.php');
		
		$form_data = array(
		'full_name' => $full_name,
		'phone' => $phone,
		'editedBy' => $myid,
		'editedDate' => $currenttime,
		'user_objects' => $user_objects
		);
		updateSomeRow("user", $form_data, "WHERE id = '$id'");		
		
		mysqli_close($conn);
	
		header("Location: ".$page_file."?view=edit&id=".$id."&res=done");
		die(0); 
	}
	
	if ($source=='addUser') {
		$full_name = htmlentities($_POST['full_name'], ENT_QUOTES, "UTF-8");
		$phone = htmlentities($_POST['phone'], ENT_QUOTES, "UTF-8");
		$email = htmlentities($_POST['email'], ENT_QUOTES, "UTF-8");


		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT id FROM {$page_table} WHERE email='".$email."'");
		if (!$result){die("Query to show fields failed");}
		$countrows = mysqli_num_rows($result);
		if ($countrows>0){
			mysqli_close($conn);
			header("Location: ".$page_file."?view=addUser&res=error");
			die(0);
		}else{
					
			$form_data = array(
			'email' => $email,
			'full_name' => $full_name,
			'phone' => $phone,
			'createdBy' => $myid,
			'user_group' => 'w_user',
			'expire' => 'y'
			);
			insertNewRows($page_table, $form_data);			
			
			$result = mysqli_query($conn,"SELECT id FROM {$page_table} WHERE email = '{$email}' LIMIT 1");
			if (!$result){die("Attention! Query to show fields failed.");}
			$row = mysqli_fetch_assoc($result);
			
			mysqli_close($conn);
			header("Location: ".$page_file."?view=edit&id=".$row['id']."&res=done");
			die(0);
		}
	}
	
	
	if ($source=='addPartner') {
		$full_name = htmlentities($_POST['full_name'], ENT_QUOTES, "UTF-8");
		$phone = htmlentities($_POST['phone'], ENT_QUOTES, "UTF-8");
		$email = htmlentities($_POST['email'], ENT_QUOTES, "UTF-8");
		$user_objects = htmlentities($_POST['user_objects'], ENT_QUOTES, "UTF-8");


		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT id FROM {$page_table} WHERE email='".$email."'");
		if (!$result){die("Query to show fields failed");}
		$countrows = mysqli_num_rows($result);
		if ($countrows>0){
			mysqli_close($conn);
			header("Location: ".$page_file."?view=addPartner&res=error");
			die(0);
		}else{
			
			$form_data = array(
			'email' => $email,
			'full_name' => $full_name,
			'phone' => $phone,
			'createdBy' => $myid,
			'user_group' => 'w_partner',
			'user_objects' => $user_objects,
			'expire' => 'y'
			);
			insertNewRows($page_table, $form_data);			
			
			$result = mysqli_query($conn,"SELECT id FROM {$page_table} WHERE email = '{$email}' LIMIT 1");
			if (!$result){die("Attention! Query to show fields failed.");}
			$row = mysqli_fetch_assoc($result);
			
			mysqli_close($conn);
			header("Location: ".$page_file."?view=edit&id=".$row['id']."&res=done");
			die(0);
		}
	}
	
}
	
if ($action=='invite'){
	if ($id){
		$recovery_code=md5(uniqid(rand()));
		require('inc/s.php');
		
		$form_data = array(
		'invitedDate' => $currenttime,
		'expire' => 'n',
		'invitedBy' => $myid,
		'recovery' => $recovery_code
		);
		updateSomeRow("user", $form_data, "WHERE id='".$id."' LIMIT 1");		
		
		$result = mysqli_query($conn,"SELECT email FROM user WHERE id='".$id."' LIMIT 1");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		$email = $row['email'];
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			//sending email
			require_once "Mail.php";
			
			$from = "scales@nnvt.lv";
			$url="http://192.168.15.248/first_time?user=". $email ."&key=". $recovery_code;
			$to = $email;
			$cc = $bcc = null;
			$subject = "NNVT SELFSERVICE PORTAL: aicinājums pievienoties";
			$type = "text/html; charset=utf-8";
			
			$body="<p style=\"font-size:10pt; font-family: Segoe UI, Arial, Verdana;\">Jums tiek nosūtīts uzaicinājums pievienoties <b>NNVT SELFSERVICE portālam</b>.<br/><br/>
			Lai pievienotos un uzstādītu sākotnējo paroli, dodaties šeit: <a href=\"$url\">$url</a></p>\r\n";
			$body.="<p style=\"font-size:10pt; font-family: Segoe UI, Arial, Verdana;\"><br/>Jūsu lietotājvārds ir:<span style=\"color:blue;\"> $email</span><br/></p>\r\n";
			$body.="<p style=\"font-size:8pt; font-family: Segoe UI, Arial, Verdana;\"><br/>Neskaidrību un jautājumu gadījumā sazinieties ar NNVT.</p>";
			
			// new mail
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <scales@nnvt.lv>' . "\r\n";
			
			include('inc/m.php');
			
			$headers = array ('From' => $from,'To' => $to,'Content-Type' => $type,'Subject' => $subject);
			$smtp = Mail::factory(
				'smtp',array (
					'host' => $host,
					'auth' => true,
					'username' => $username,
					'password' => $password,
					'port' => $port					
				)
			);
			$mail = $smtp->send($to, $headers, $body);
			if (PEAR::isError($mail)) {
				echo($mail->getMessage());
			}			

			webmessage($myemail, $to, $subject, $body, 'page users');

			header("Location: ".$page_file."?view=edit&id=".$id."&res=invited");
			die(0); 
		}
	}

}

if ($action=='delete') {
	if ($id){
		require('inc/s.php');
		
		$form_data = array(
		'expire' => 'y',
		'deletedBy' => $myid,
		'deletedDate' => $currenttime
		);
		updateSomeRow("user", $form_data, "WHERE id = '$id' LIMIT 1");		
		
		mysqli_close($conn);
	}
	$action=null;
}

if ($action=='restore') {
	if ($id){
		require('inc/s.php');
		
		$form_data = array(
		'expire' => 'n',
		'deletedBy' => '',
		'deletedDate' => ''
		);
		updateSomeRow("user", $form_data, "WHERE id = '$id' LIMIT 1");		
		
		mysqli_close($conn);
	}
	$action=null;
}

if ($action=='deletePicture') {
	if (isset($_GET['filekey'])){$filekey = htmlentities($_GET['filekey'], ENT_QUOTES, "UTF-8");}
	if (($id)&&($filekey)){
		require('inc/s.php');
		
		$form_data = array(
		'img' => ''
		);
		updateSomeRow("user", $form_data, "WHERE id = '$id' LIMIT 1");		
		
		deleteSomeRow("user_images", "WHERE filekey = '".$filekey."'");
		mysqli_close($conn);
		
		mysqli_close($conn);
	}
	header("Location: ".$page_file."?view=edit&section=1&id=".$id."");
	die(0); 
}
?>

<nav class="navbar navbar-default" style="margin-top: -20px; border-radius: 0px;">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?=$page_file?>" style="text-transform: uppercase;"><?=$row['page_header']?></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
      <ul class="nav navbar-nav">
        <li><a href="<?=$page_file?>">AKTĪVIE LIETOTĀJI</a></li>
		<li><a href="<?=$page_file?>?view=recycle">IZDZĒSTIE LIETOTĀJI</a></li>
		<li><a href="<?=$page_file?>?view=access">SADAĻU LIETOTĀJI</a></li>

		<?php 	if ($p_edit=='on'){ ?>
		<li><a href="<?=$page_file?>?view=addUser"><i class="glyphicon glyphicon-plus"></i></a></li>
		<?php } ?>	
      </ul>

      <ul class="nav navbar-nav navbar-right">
        
        <li>
		  <form class="navbar-form navbar-left" role="search" name='search' action='' method='GET'>
			<div class="form-group">
				<?php if($view){ echo '<input type="hidden" name="view" value="'.$view.'">'; } ?>
				<input type="search" name='query' class="form-control" placeholder="Meklēt..">
			</div>
			<button type="submit" class="btn btn-default btn-xs">Meklēt</button>
		 </form>		  
        </li>
      </ul>
    </div>
  </div>
</nav>

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





<?php
//skats uz lapām
if ($view=='access'){

	echo '
	<div class="page-header" style="margin-top: -5px;">
	  <h4>lietotāju piekļuve pa sadaļām</h4>
	</div>	
';

	
	require('inc/s.php');
	
	$result = mysqli_query($conn, "SELECT id, page_file, page_header, page_icon FROM setup_pages WHERE page_file!='agreements' AND  page_file<>'page_admin' AND page_file<>'movement' AND page_file<>'assembly' AND page_file<>'page_budget_report' ORDER BY page_file");	
	
	
	if (!$result){die("Query to show fields failed");}
	
	echo '	<table class="ui small  collapsing celled table segment"><thead><tr>
				<th>sadaļa</th>
				<th>lietotāji, piekļuves tiesības</th>
	<style>
		.link-container a:hover {
			background: #f8f8f8;
		}	
	</style>
			</tr></thead><tbody>';
	while($row = mysqli_fetch_array($result)){
		echo '	<tr>
					<td style="text-transform: uppercase;">'.$row['page_header'].'
					<td>';
					$getNames = mysqli_query($conn, "
					
						SELECT u.id, u.full_name, u.expire, r.p_view, r.p_edit
						FROM {$page_table} AS u
						
						
						LEFT JOIN user_rights AS r
						ON u.id=r.user_id
						
						
						WHERE u.user_group='w_user' 
						AND u.deletedDate IS NULL 
						AND u.id!='1' AND r.page_name='".$row['page_file']."'
						AND (r.p_view='on' OR r.p_edit='on')
						AND u.expire='n'
						ORDER BY u.full_name					
					
					");
					while($gNrow = mysqli_fetch_array($getNames)){
						
						echo '<div style="cursor: pointer; margin: 5px;" onMouseOver="this.style.color=\'#00F\'" onMouseOut="this.style.color=\'#000\'" onclick="window.location=\'?view=edit&id='.$gNrow['id'].'\'">
						
						'.$gNrow['full_name'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					
						if(($gNrow['full_name']) && ($gNrow['p_view']=='on' || $gNrow['p_edit']=='on')){
							
							echo ' <div style="float: right;">';
							if($gNrow['p_view']=='on'){
								echo ' lietot <i style="color: green;" class="glyphicon glyphicon-ok"></i> ';
							}else{
								echo ' lietot <i style="color: red;" class="glyphicon glyphicon-ban-circle"></i> ';
							}
							
							if($gNrow['p_edit']=='on'){
								echo ' administrēt <i style="color: green;" class="glyphicon glyphicon-ok"></i> ';
							}else{
								echo ' administrēt <i style="color: red;" class="glyphicon glyphicon-ban-circle"></i> ';
							}
							echo '</div>';							
						}

						echo '
						</div>';
					
					}
		echo '	</tr>';
	}
	
	echo '</tbody></table>';
	
}





				

//skats uz ierakstiem
if (!$view){
	
	require('inc/s.php');

	echo '
		<div class="page-header" style="margin-top: -5px;">
		  <h4>aktīvie portāla lietotāji</h4>
		</div>	
	';
	
	if($query){echo '<div class="alert alert-info">Meklētā frāze: <b>'.$query.'</b> <a href="'.$page_file.'" style="float: right;"><i class="glyphicon glyphicon-remove"></i></a></div>';}

	$result = mysqli_query($conn,"SELECT id, email, full_name, phone, createdDate, expire, img, user_group, user_objects FROM {$page_table} WHERE (full_name LIKE '%".$query."%' OR email LIKE '%".$query."%' OR user_group LIKE '%".$query."%') AND deletedDate IS NULL ORDER BY full_name");
	if (!$result){die("Query to show fields failed");}
	
	echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
				<th>vārds, uzvārds</th>
				<th>lietotāja id</th>				
				<th>tālrunis</th>
				<th>izveidots</th>
				<th>pēdējoreiz manīts</th>
			</tr></thead><tbody>';
	while($row = mysqli_fetch_array($result)){
		if ($row['expire']=="y"){$rowtype='class="warning"';}else{$rowtype=null;}
		echo '	<tr '.$rowtype.' onclick="window.location=\'?view=edit&id='.$row['id'].'\'">
					<td>'.$row['full_name'].'
					<td>'.$row['email'].'					
					<td>'.$row['phone'].'
					<td>'.date('d.m.Y H:i:s', strtotime($row['createdDate'])).'
					<td>';
					$result2 = mysqli_query($conn,"SELECT datums FROM user_stat WHERE user_name = '".$row['email']."' AND ok = 'y' ORDER BY datums DESC LIMIT 1");
					if (!$result){die("Query to show fields failed");}
					$row2 = mysqli_fetch_array($result2);
		if($row2['datums']){echo date('d.m.Y H:i:s', strtotime($row2['datums']));}
		echo ' 
				</tr>';
	}
	
	echo '</tbody></table></div>';
	mysqli_close($conn);
}
//dzēstie
if ($view=="recycle"){
	
	echo '
		<div class="page-header" style="margin-top: -5px;">
		  <h4>izdzēstie portāla lietotāji</h4>
		</div>		
	';

	if($query){echo '<div class="alert alert-info">Meklētā frāze: <b>'.$query.'</b> <a href="'.$page_file.'?view='.$view.'" style="float: right;"><i class="glyphicon glyphicon-remove"></i></a></div>';}
	
	require('inc/s.php');
	
	$result = mysqli_query($conn,"SELECT id, email, full_name, phone, createdDate, expire, img, user_group FROM {$page_table} WHERE (full_name LIKE '%".$query."%' OR email LIKE '%".$query."%' OR user_group LIKE '%".$query."%') AND deletedDate > '0000.00.00' ORDER BY full_name");
	if (!$result){die("Query to show fields failed");}
	
	echo '	<table class="table table-hover table-responsive"><thead><tr>
				<th>vārds, uzvārds</th>
				<th>lietotāja id</th>
				<th>tālrunis</th>
				<th>izveidots</th>
				<th>pēdējoreiz manīts</th>
			</tr></thead><tbody>';
	while($row = mysqli_fetch_array($result)){
		if ($row['expire']=="y"){$rowtype='class="warning"';}else{$rowtype=null;}
		echo '	<tr '.$rowtype.' onclick="window.location=\'?view=edit&id='.$row['id'].'\'">
					<td>'.$row['full_name'].'
					<td>'.$row['email'].'
					<td>'.$row['phone'].'
					<td>'.date('d.m.Y H:i:s', strtotime($row['createdDate'])).'
					<td>';
					$result2 = mysqli_query($conn,"SELECT datums FROM user_stat WHERE user_name = '".$row['email']."' AND ok = 'y' ORDER BY datums DESC LIMIT 1");
					if (!$result){die("Query to show fields failed");}
					$row2 = mysqli_fetch_array($result2);
		if($row2['datums']){ echo date('d.m.Y H:i:s', strtotime($row2['datums']));}
		echo'
				</tr>';
	}
	
	echo '</tbody></table>';
	mysqli_close($conn);
}
//datu labošana
if ($view=='edit'){
	if (!$id){header("Location: {$page_file}");die(0);}

	//pamatdatu labošana
	if (!$section){
		require('inc/s.php');
		$result = mysqli_query($conn,"SELECT email, full_name, phone, img, deletedDate, user_group, user_objects FROM {$page_table} WHERE id = {$id} LIMIT 1");
		if (!$result){die("Attention! Query to show fields failed.");}
		$row = mysqli_fetch_assoc($result);
		mysqli_close($conn);
	?>
		<form role="form" class="form-horizontal" name="edit" enctype='multipart/form-data' method="POST" action="">
		<input type='hidden' name='source' value='edit'/>
		<input type='hidden' name='id' value='<?=$id?>'/>
					
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-xs" role="group" aria-label="Small button group"> 
								<a class="btn btn-default active" href="?view=edit&id=<?=$id?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD"></i></a> 
								<a class="btn btn-default" href="?view=edit&section=2&id=<?=$id?>"><i class="glyphicon glyphicon-wrench" style="color: #00B5AD"></i></a> 
							</div>

							<?php
								if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success btn-xs">saglabāts!</div></div>';}
								if ($res=="invited"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success btn-xs">uzaicināts!</div></div>';}
							?>
						
						</div>	
						
						<h4>LIETOTĀJA DATI</h4>
												
												
						<div class="form-group">
							<div class="col-md-3">
								<small>lietotāja id</small>
								<input type="text" class="form-control" name="email" disabled="disabled" value="<?=$row['email']?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>vārds, uzvārds</small>
								<input type="text" class="form-control" name="full_name" value="<?=$row['full_name']?>">
							</div>
							<div class="col-md-3">
								<small>tālrunis</small>
								<input type="text" class="form-control" name="phone" value="<?=$row['phone']?>">
							</div>
						</div>

								
						<br>							
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="<?=$page_file?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>
						<?php if ($row['deletedDate'] > '0000.00.00'){ ?>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="?action=restore&id=<?=$id?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-refresh" aria-hidden="true" style="color: green;"></span> atjaunot
						</a>
						<?php }else{ ?>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.edit.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
						</a>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="?action=invite&id=<?=$id?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-envelope" aria-hidden="true"  style="color: teal;"></span> uzaicināt
						</a>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="?action=delete&id=<?=$id?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: red;"></span> dzēst
						</a>
						<?php } ?>

		</form>
	<?php
	}
	
	//lietotāju tiesības
	if ($section=='2'){
	?>
	
		<form name="editUserRights" enctype='multipart/form-data' method="POST" action="">
		<input type='hidden' name='source' value='editUserRights'/>
		<input type='hidden' name='id' value='<?=$id?>'/>
					
					
						<div class="page-header" style="margin-top: -5px;">

							<div class="btn-group btn-group-xs" role="group" aria-label="Small button group"> 
								<a class="btn btn-default" href="?view=edit&id=<?=$id?>"><i class="glyphicon glyphicon-user" style="color: #00B5AD"></i></a> 
								<a class="btn btn-default active" href="?view=edit&section=2&id=<?=$id?>"><i class="glyphicon glyphicon-wrench" style="color: #00B5AD"></i></a> 
							</div>

							<?php if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-success btn-xs">saglabāts!</div></div>';}?>
							<?php if ($res=="deleted"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-danger btn-xs">dzēsts!</div></div>';}?>
							<?php if ($res=="added"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-info btn-xs">pievienots!</div></div>';}?>							
						
						</div>						
						
						<h4>LIETOTĀJA TIESĪBAS</h4>
						<?
							require('inc/s.php');
							$result = mysqli_query($conn,"SELECT user_group FROM {$page_table} WHERE id = {$id} LIMIT 1");
							if (!$result){die("Attention! Query to show fields failed.");}
							$row = mysqli_fetch_assoc($result);
							$row_user_group=$row['user_group'];
							if ($row_user_group=="w_user"){	
		
								$result = mysqli_query($conn,"SELECT id, page_file, page_header, page_icon FROM setup_pages WHERE page_file<>'page_admin' AND page_file<>'movement' AND page_file<>'assembly' ORDER BY page_file");
								if (!$result){die("Query to show fields failed");}
								
								echo '	<table class="table table-bordered">';
								
								$i=null;
								while($row = mysqli_fetch_array($result)){
									++$i;
									echo '	<input type="hidden" name="page['.$i.']" value="'.$row['page_file'].'"/>';
									
									$result2 = mysqli_query($conn,"SELECT p_view, p_edit FROM user_rights WHERE user_id = '".$id."' AND page_name = '".$row['page_file']."'");
									if (!$result2){die("Attention! Query to show fields failed.");}
									$row2 = mysqli_fetch_assoc($result2);
									$page_view=$page_edit=$page_delete=$page_create=null;
									if ($row2['p_view']){$page_view='checked="checked"';}
									if ($row2['p_edit']){$page_edit='checked="checked"';}
									
									echo '	<tr>
												<td style="text-transform: uppercase;" valign="middle">
														'.$row['page_header'].'
												<td>
												
												<span class="button-checkbox">
													<button type="button" class="btn btn-xs" data-color="success">lietot</button>
													<input type="checkbox" class="hidden" id="view['.$i.']" name="view['.$i.']" '.$page_view.'>
												</span>	
												
												<span class="button-checkbox">
													<button type="button" class="btn btn-xs" data-color="success">administrēt</button>
													<input type="checkbox" class="hidden" id="edit['.$i.']" name="edit['.$i.']" '.$page_edit.'>
												</span>';													

										echo '
											</tr>';
								}
								
								echo '</tbody></table>';
								echo '	<input type="hidden" name="totalrows" value="'.$i.'"/>';
								mysqli_close($conn);
							}
							


						?>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="<?=$page_file?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>
							
							<?php 	if ($p_edit=='on'){
								if ($row_user_group=="w_user"){	
									echo '	
										<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.editUserRights.submit()" 	style="margin-bottom: 1px;">
											<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: green;"></span> saglabāt
										</a>';
								}
								if ($row_user_group=="customer"){	
									echo '	
										<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.editUserRights.submit()" style="margin-bottom: 1px;">
										  <span class="glyphicon glyphicon-plus" aria-hidden="true" style="color: green;"></span> pievienot
										</a>';
								}
							}?>	
							

		</form>
<?php
	}
}

//administrācijas pievienošana 
if ($view=='addUser'){?>
		<form name="addUser" class="form-horizontal" enctype='multipart/form-data' method="POST" action="">
		<input type='hidden' name='source' value='addUser'/>

						
							<a class="active item" href="?view=edit&id=<?=$id?>">
								<i class="large user icon"></i>
							</a>
							<? if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;"><div class="btn btn-danger">kļūda lietotāja ID!</div></div>';}?>
						
						
						<div class="page-header" style="margin-top: -5px;">
						  <h4>JAUNA LIETOTĀJA PIEVIENOŠANA</h4>
						</div>	

						<div class="form-group">
							<div class="col-md-3">
								<small>lietotāja id (epasts)</small>
								<input type="text" class="form-control" name="email">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<small>vārds, uzvārds</small>
								<input type="text" class="form-control" name="full_name">
							</div>
							<div class="col-md-3">
								<small>tālrunis</small>
								<input type="text" class="form-control" name="phone">
							</div>
						</div>
						<br>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="<?=$page_file?>" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-remove" aria-hidden="true" style="color: black;"></span> aizvērt
						</a>
						<a class="btn btn-default btn-xs" aria-label="Left Align" href="javascript:document.addUser.submit()" style="margin-bottom: 1px;">
						  <span class="glyphicon glyphicon-plus" aria-hidden="true" style="color: green;"></span> pievienot
						</a>

		</form>
	<?php
	}

?>
</div>
</div>
</div>
</div>
</div>


<script type='text/javascript'>
var config = {'.chosen-select'  : {allow_single_deselect:true, disable_search_threshold: 10, width:"100%"}}
for (var selector in config) {$(selector).chosen(config[selector]);}
</script>
<script>
var fileExtentionRange = '.jpg .jpeg .png .gif';

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