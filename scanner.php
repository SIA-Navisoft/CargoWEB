<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="scanner";

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

include('functions/base.php');
include('header.php');
$view = $action = $section = $id = $query = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['action'])){$action = htmlentities($_GET['action'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['query'])){$query = htmlentities($_GET['query'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['mode'])){$mode = htmlentities($_GET['mode'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['exitingSerial'])){$exitingSerial = htmlentities($_GET['exitingSerial'], ENT_QUOTES, "UTF-8");}

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU

?>
<style>					
input[type], .bootstrap-select {
	height: 34px;
	line-height: 1.5;
	font-size: 12px;
	padding: 1px 5px;
	border-radius: 3px;
}		
</style>
<?						

// ADD, EDIT or DELETE
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["source"])){$source = null;}else{$source = htmlentities($_POST['source'], ENT_QUOTES, "UTF-8");}
	
	if ($source=='scan') {
		
		if (isset($_POST['serial'])){$serial = htmlentities($_POST['serial'], ENT_QUOTES, "UTF-8");}
		if($serial && $id){
			
			
			if(!$mode){
				
				$queryin = mysqli_query($conn, "SELECT id, productNr FROM cargo_line WHERE docNr='".$id."' AND serialNoPlan='".$serial."'");
				$countin = mysqli_num_rows($queryin);				
				
				if($countin==1){
					
					$row = mysqli_fetch_array($queryin);
					
					if(!$row['productNr']){
						$_SESSION['serialInDoc']='y';
					}
				
					$query = mysqli_query($conn, "SELECT id FROM scanner_lines WHERE docNr='".$id."' AND serialNo='".$serial."'");
					$count = mysqli_num_rows($query);
					
					if($count==0){
						
						$form_data = array(
							'docNr' => $id,
							'serialNo' => $serial,
							'createdBy' => $myid,
							'createdDate' => date('Y-m-d H:i:s')
						);
						insertNewRows("scanner_lines", $form_data);
						
						$_SESSION['serial']=$serial;
						$_SESSION['serialAction']='add';
						
					}
					
					if($count==1){
						$_SESSION['serial']=$serial;
						$_SESSION['serialAction']='add';
					}
					
					if(checkIfScanned($conn, $id)==1){

						$query = "UPDATE cargo_header SET scanStatus='100' WHERE docNr = '".$id."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
						
					}					
				
				}else{
					?>
						<style>
							.modal-dialog {
							  width: 100%;
							  height: 100%;
							  margin: 0;
							  padding: 0;
							}

							.modal-content {
							  height: auto;
							  min-height: 100%;
							  border-radius: 0;
							}	
							
							.modal-footer {
							  border-radius: 0;
							  bottom:0px;
							  position:absolute;
							  width:100%;
							}							
						</style>
						
						<style>
						.btn-file {
							position: relative;
							overflow: hidden;
						}
						.btn-file input[type=file] {
							position: absolute;
							top: 0;
							right: 0;
							min-width: 100%;
							min-height: 100%;
							font-size: 100px;
							text-align: right;
							filter: alpha(opacity=0);
							opacity: 0;
							outline: none;
							background: white;
							cursor: inherit;
							display: block;
						}

						#img-upload{
							width: 100%;
						}

						.modal-body{
						  height: calc(100vh - 180px);;
						  overflow-y: auto;
						}
						</style>						
					
						<script>
						$(document).ready( function() {
							
								$(document).on('change', '.btn-file :file', function() {
								var input = $(this),
									label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
								input.trigger('fileselect', [label]);
								});

								$('.btn-file :file').on('fileselect', function(event, label) {
									
									var input = $(this).parents('.input-group').find(':text'),
										log = label;
									
									if( input.length ) {
										input.val(log);
									} else {
										if( log ) alert(log);
									}
								
								});
								
								function readURL(input) {
									if (input.files && input.files[0]) {
										var reader = new FileReader();
										
										reader.onload = function (e) {
											$('#img-upload').attr('src', e.target.result);
										};
										
										reader.readAsDataURL(input.files[0]);
									}
								}

								$("#image").change(function(){
									readURL(this);
								});
								
								$("#clear").click(function(){
									$('#img-upload').attr('src','');
									$('#urlname').val('');
								});
								
								$(".doUpload").on('click', function() {
									$("#pleaseWait").toggle();
									$('#addPicture').submit();
								});	

								$('.cancel').click(function(){
									$("#pleaseWait").toggle();
								});									
								
							});
						</script>					
					
						<script type="text/javascript">
							$(window).on('load',function(){
								$('#uploadPic').modal('show');
							});						
						</script>					
					
						<div class="modal fade" id="uploadPic" role="dialog">
							<div class="modal-dialog">
								
								<div class="modal-content">
									<div class="modal-header">
										<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="close cancel">&times;</a>
										<h4 class="modal-title">Foto režīms</h4>
									</div>
									<div class="modal-body">

										<form id="addPicture" name="addPicture" enctype='multipart/form-data' method="POST" action="">
											<input type='hidden' name='source' value='addPicture'/>
											<input type='hidden' name='serial' value='<?=$serial;?>'>
											<input type='hidden' name='id' value='<?=$id;?>'>
										
											<div class="col-md-6">
												<div class="form-group">
													<label>Fotogrāfēt etiķeti </label>
													<div class="input-group">
														<span class="input-group-btn">
															<span class="btn btn-default btn-file">
																Fotogrāfēt… <input type="file" name="capture" accept="image/*" id="image" capture="camera">
															</span>
														</span>
														<input id='urlname' type="text" class="form-control" readonly>
													</div>
													
													<div style="display: inline-block; width: 100%;">
														<img id='img-upload'/>
													</div>
													
												</div>
											</div>
											
										</form>
										
										<div id="pleaseWait" style="display: none;">
											<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
										</div>
										
									</div>
									<div class="modal-footer">
									
										<div style="display: inline-block; float: left;">						
											<button id="clear" class="btn btn-default" style="display: inline-block;">Notīrīt</button>
											<button class="btn btn-primary doUpload" style="display: inline-block;" id="doUpload">Augšuplādēt</button>
										</div>			
										
										<div style="display: inline-block; float: right;">
											<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="btn btn-default cancel">Atcelt</a>
										</div>
										
									</div>
								</div>
							</div>
						</div>
	
					<?
					
					if(checkIfScanned($conn, $id)==1){

						$query = "UPDATE cargo_header SET scanStatus='100' WHERE docNr = '".$id."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
						
					}					
					
					die();
				}
				
			
			}
			
			if($mode=='delete'){
				
				
				$query = mysqli_query($conn, "SELECT img FROM scanner_lines WHERE docNr='".$id."' AND serialNo='".$serial."' AND img!='' AND img IS NOT NULL");
				$count = mysqli_num_rows($query);
				
				if($count==1){
					$row = mysqli_fetch_array($query);					
					mysqli_query($conn,"DELETE FROM scanner_lines_images WHERE filekey='".$row['img']."'") or die(mysqli_error($conn));				
				}
				
				mysqli_query($conn,"DELETE FROM scanner_lines WHERE docNr='".$id."' AND serialNo='".$serial."'") or die(mysqli_error($conn));
				$deleted = mysqli_affected_rows($conn);
				
				if($deleted==1){
					$_SESSION['serial']=$serial;
					$_SESSION['serialAction']='delete';
				}	


				
			}
						
		}

		header("Location: ".$page_file."?view=scan&id=".$id."");
		die(0);		
		
		
	}
	
	
	if ($source=='addPicture') {
		
		
		if (isset($_POST['id'])){$id = htmlentities($_POST['id'], ENT_QUOTES, "UTF-8");}
		if (isset($_POST['serial'])){$serial = htmlentities($_POST['serial'], ENT_QUOTES, "UTF-8");}
		if($serial && $id){		
		
			if($_FILES['capture']['size'] > 0)	{
				
				$fileName = $_FILES['capture']['name'];
				$tmpName  = $_FILES['capture']['tmp_name'];
				
				$fileSize = $_FILES['capture']['size'];
				$fileType = $_FILES['capture']['type'];
				
				
				$fp      = fopen($tmpName, 'r');
				$content = fread($fp, filesize($tmpName));
				$content = addslashes($content);
				fclose($fp);

				if(!get_magic_quotes_gpc())	{
					$fileName = addslashes($fileName);
				}

				$extension = getExtension($fileName);
				$extension = strtolower($extension);
				
				if($extension=="jpg" || $extension=="jpeg" ){
					$src = imagecreatefromjpeg($tmpName);
				}else if($extension=="png"){
					$src = imagecreatefrompng($tmpName);
				}else{
					$src = imagecreatefromgif($tmpName);
				}

				list($width,$height)=getimagesize($tmpName);

				if ($height<$width){
					$newwidth=500; $newheight=($height/$width)*$newwidth;
					$newwidth1=300; $newheight1=($height/$width)*$newwidth1;
					$newwidth2=150; $newheight2=($height/$width)*$newwidth2;
				}else{
					$newheight=400; $newwidth=($width/$height)*$newheight;
					$newheight1=200; $newwidth1=($width/$height)*$newheight1;
					$newheight2=150; $newwidth2=($width/$height)*$newheight2;
				}

				$thumb_width = 100;
				$thumb_height = 100;

				$original_aspect = $width / $height;
				$thumb_aspect = $thumb_width / $thumb_height;

				if ( $original_aspect >= $thumb_aspect ){
				   $new_height = $thumb_height; $new_width = $width / ($height / $thumb_height);
				}else{
				   $new_width = $thumb_width; $new_height = $height / ($width / $thumb_width);
				}

				$tmp=imagecreatetruecolor($newwidth,$newheight);
				$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
				$tmp2=imagecreatetruecolor($newwidth2,$newheight2);
				$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
				imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
				imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
				imagecopyresampled($thumb, $src, 0 - ($new_width - $thumb_width) / 2, 0 - ($new_height - $thumb_height) / 2, 0, 0, $new_width, $new_height, $width, $height);
				
				$big = "images/big_". $_FILES['capture']['name'];
				$middle = "images/middle_". $_FILES['capture']['name'];
				$small = "images/small_". $_FILES['capture']['name'];
				$ico = "images/ico_". $_FILES['capture']['name'];
				
				imagejpeg($tmp,$big,100);
				imagejpeg($tmp1,$middle,100);
				imagejpeg($tmp2,$small,100);
				imagejpeg($thumb, $ico, 80);
				
				$fp = fopen($big, 'r');
				$big = addslashes(fread($fp, filesize($big)));
				fclose($fp);
				
				$fp = fopen($middle, 'r');
				$middle = addslashes(fread($fp, filesize($middle)));
				fclose($fp);
				
				$fp = fopen($small, 'r');
				$small = addslashes(fread($fp, filesize($small)));
				fclose($fp);
				
				$fp = fopen($ico, 'r');
				$ico = addslashes(fread($fp, filesize($ico)));
				fclose($fp);

				require('inc/s.php');
				
				
				
				if($exitingSerial=='y'){
					
					
					$queryi = mysqli_query($conn, "SELECT id FROM scanner_lines WHERE docNr='".$id."' AND serialNo='".$serial."'") or die(mysqli_error($conn));
					$count = mysqli_num_rows($queryi);
					
					$queryPart = " AND serialNo = '".$serial."' ";
					$_SESSION['serial']=$serial;											
					$_SESSION['serialAction']='add';
					$_SESSION['serialInDoc']='y';
											
					$result = mysqli_query($conn,"SELECT img FROM scanner_lines WHERE docNr = '".$id."' ".$queryPart." LIMIT 1") or die(mysqli_error($conn));
					
					$row = mysqli_fetch_assoc($result);
					if (!$row['img']){
						
						$filekey=md5(uniqid(rand()));
						$query = "UPDATE scanner_lines SET img='".$filekey."' WHERE docNr = '".$id."' ".$queryPart."";
						mysqli_query($conn, $query) or die(mysqli_error($conn));
					}else{
						
						$filekey=$row['img'];
						$result3 = mysqli_query($conn,"DELETE FROM scanner_lines_images WHERE filekey = '".$filekey."'") or die(mysqli_error($conn));
					}
					
					mysqli_query($conn, "INSERT INTO scanner_lines_images (docNr, name, size, type, thumb, small, medium, big, content, createdBy, filekey) ".
					"VALUES ('$id', '$fileName', '$fileSize', '$fileType', '$ico', '$small', '$middle', '$big', '$content', '$myid', '$filekey')") or die(mysqli_error($conn));
					
					if(checkIfScanned($conn, $id)==1){

						$query = "UPDATE cargo_header SET scanStatus='100' WHERE docNr = '".$id."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
						
					}					
						
					
				}else{
							
					$queryi = mysqli_query($conn, "SELECT id FROM scanner_lines WHERE docNr='".$id."' AND serialNo='".$serial."'") or die(mysqli_error($conn));
					$count = mysqli_num_rows($queryi);
					
					$queryPart = " AND serialNo = '".$serial."' ";
					$_SESSION['serial']=$serial;
					
					if($count==0 || $serial=='UNKNOWN'){
						
						$now = date('Y-m-d H:i:s');
						if($serial!='UNKNOWN'){
													
							mysqli_query($conn, "INSERT INTO scanner_lines (docNr, serialNo, createdBy, createdDate) "." VALUES ('$id', '$serial', '$myid', '$now')") or die(mysqli_error($conn));						
																			
						}else{
							
							$_SESSION['serial']='NEZINĀMS';
							
							mysqli_query($conn, "INSERT INTO scanner_lines (docNr, serialNo, createdBy, createdDate) "." VALUES ('$id', '', '$myid', '$now')") or die(mysqli_error($conn));
							$last_id = mysqli_insert_id($conn);
							
							$queryPart = " AND id = '".$last_id."' ";
							
						}
						
					}
											
					$_SESSION['serialAction']='add';
						
					$result = mysqli_query($conn,"SELECT img FROM scanner_lines WHERE docNr = '".$id."' ".$queryPart." LIMIT 1") or die(mysqli_error($conn));
					
					$row = mysqli_fetch_assoc($result);
					if (!$row['img']){
						
						$filekey=md5(uniqid(rand()));
						$query = "UPDATE scanner_lines SET img='".$filekey."' WHERE docNr = '".$id."' ".$queryPart."";
						mysqli_query($conn, $query) or die(mysqli_error($conn));
					}else{
						
						$filekey=$row['img'];
						$result3 = mysqli_query($conn,"DELETE FROM scanner_lines_images WHERE filekey = '".$filekey."'") or die(mysqli_error($conn));
					}
					
					mysqli_query($conn, "INSERT INTO scanner_lines_images (docNr, name, size, type, thumb, small, medium, big, content, createdBy, filekey) ".
					"VALUES ('$id', '$fileName', '$fileSize', '$fileType', '$ico', '$small', '$middle', '$big', '$content', '$myid', '$filekey')") or die(mysqli_error($conn));
					
					if(checkIfScanned($conn, $id)==1){

						$query = "UPDATE cargo_header SET scanStatus='100' WHERE docNr = '".$id."'";
						mysqli_query($conn, $query) or die(mysqli_error($conn));					
						
					}	

				}					
				
				mysqli_close($conn);
				
				
				
				unlink("images/big_". $_FILES['capture']['name']);
				unlink("images/middle_". $_FILES['capture']['name']);
				unlink("images/small_". $_FILES['capture']['name']);
				unlink("images/ico_". $_FILES['capture']['name']);

			}	

			header("Location: ".$page_file."?view=scan&id=".$id."");
			die(0);
		
		
		}
		
	}	
	
	
	if($source=='finish' && $id){
		
		
		echo '<br><br><br>do you really want to finish? '.$id.'<br><br><br>';
		
		require('inc/s.php');
		$query = mysqli_query($conn, "
		
			SELECT cl.serialNo, cl.serialNoPlan
		
			FROM cargo_header AS ch 
			
			LEFT JOIN cargo_line AS cl
			ON ch.docNr=cl.docNr
			
			WHERE ch.docNr='".$id."' AND ch.status='0' AND ch.rowBeforeSent='0' AND ch.rowSent='0' AND (cl.serialNoPlan!='' AND cl.serialNoPlan IS NOT NULL)
													
		") or die(mysqli_error($conn));	

		if(mysqli_num_rows($query)>=0){
			
			$serialNo=$serialNoPlan=null;
			$allSerials=array();
			$allSerialsCopy=array();
			while($row = mysqli_fetch_array($query)){
				array_push($allSerials, $row['serialNoPlan']);
				array_push($allSerialsCopy, $row['serialNoPlan']);
			}

			$fromScanner = mysqli_query($conn, "SELECT id, serialNo, createdBy, createdDate FROM scanner_lines WHERE docNr='".$id."'") or die(mysqli_error($conn));
			while($fs = mysqli_fetch_array($fromScanner)){
				
				$serialNoFact = $fs['serialNo'];
				$scanId = $fs['id'];
				$createdBy = $fs['createdBy'];
				$createdDate = $fs['createdDate'];
				
				if(in_array($serialNoFact, $allSerials)){
					
					echo 'vecs - Sakrīt: '.$serialNoFact.'<br>';
					
					$query = "UPDATE cargo_line SET scannedBy='".$createdBy."', scannedDate='".$createdDate."', scanId='".$scanId."' WHERE docNr = '".$id."' AND serialNoPlan='".$serialNoFact."'";
					mysqli_query($conn, $query) or die(mysqli_error($conn));										
					
					if (($key = array_search($serialNoFact, $allSerials)) !== false) {
						unset($allSerialsCopy[$key ]);
					}
					
				}else{
					
					if($serialNoFact==''){
						
						echo 'jauns - UNKNOWN<br>';
						
						$now = date('Y-m-d H:i:s');
						mysqli_query($conn, "INSERT INTO cargo_line (docNr, serialNo, serialNoPlan, enteredBy, enteredDate, scannedBy, scannedDate, scanId) "." VALUES ('$id', '', '', '$myid', '$now', '$createdBy', '$createdDate', '$scanId')") or die(mysqli_error($conn));
						
					}else{
						
						echo 'jauns - Nesakrīt '.$serialNoFact.'<br>';
						
						$now = date('Y-m-d H:i:s');
						mysqli_query($conn, "INSERT INTO cargo_line (docNr, serialNo, serialNoPlan, enteredBy, enteredDate, scannedBy, scannedDate, scanId) "." VALUES ('$id', '$serialNoFact', '', '$myid', '$now', '$createdBy', '$createdDate', '$scanId')") or die(mysqli_error($conn));						
						
					}
					
				}
				
			}
			
			foreach($allSerialsCopy AS $key => $val){
				
				echo 'vecs - nesakrīt '.$val.'<br>';
			
				$query = "UPDATE cargo_line SET serialNo='' WHERE docNr = '".$id."' AND serialNoPlan='".$val."'";
				mysqli_query($conn, $query) or die(mysqli_error($conn));			
				
			}
			
			$query = "UPDATE cargo_header SET scanned='1', scanFinishedBy='".$myid."', scanFinishedDate='".date('Y-m-d H:i:s')."', scanStatus='200' WHERE docNr = '".$id."'";
			mysqli_query($conn, $query) or die(mysqli_error($conn));			
		
		}
		echo '<br><br>';
		
			header("Location: ".$page_file."");
			die(0);		
		
	}
	
	
	
}

if($view=='add'){
	
	
	
	if(COUNT($allowedClients)==1){
			
			
		if($allowedClients[0]){	
		
			$id=findLastRow("cargo_header")+1;
			  
			$invoice_number = 'PN'.sprintf("%'05d\n", $id);
			$invoice_number = trim($invoice_number);

			$form_data = array(
				'docNr' => $invoice_number,

				'clientCode' => $allowedClients[0],
				'clientName' => returnClientName($conn, $allowedClients[0]),
				'ownerCode' => $allowedClients[0],
				'ownerName' => returnClientName($conn, $allowedClients[0]),
				
				'deliveryDate' => date('Y-m-d H:i:s'),
					
				'lastBy' => $myid,
				'lastDate' => date('Y-m-d H:i:s')		

			);
			insertNewRows("cargo_header", $form_data);
			
			header("Location: ".$page_file."?view=scan&id=".$invoice_number."");
			die(0);
			
		}else{
			header("Location: ".$page_file."");
			die(0);			
		}
		
	}	
	
	if(COUNT($allowedClients)>1){
			
			
			
		if(isset($_GET['allowedClients'])){	
		
			if (isset($_GET['allowedClients'])){$allowedClientsGet = htmlentities($_GET['allowedClients'], ENT_QUOTES, "UTF-8");}
		
			$id=findLastRow("cargo_header")+1;
			  
			$invoice_number = 'PN'.sprintf("%'05d\n", $id);
			$invoice_number = trim($invoice_number);

			$form_data = array(
				'docNr' => $invoice_number,

				'clientCode' => $allowedClientsGet,
				'clientName' => returnClientName($conn, $allowedClientsGet),
				'ownerCode' => $allowedClientsGet,
				'ownerName' => returnClientName($conn, $allowedClientsGet),				
				
				'deliveryDate' => date('Y-m-d H:i:s'),
					
				'lastBy' => $myid,
				'lastDate' => date('Y-m-d H:i:s')		

			);
			insertNewRows("cargo_header", $form_data);
			
			header("Location: ".$page_file."?view=scan&id=".$invoice_number."");
			die(0);	
			
		}
		
	}

	
	
}



?>
<style>
.aborder{
	border-right: solid #eee 1px !important;
}
.table td, .table th {
	padding: 2px !important;
	font-size: 15px;
}
</style>
<nav class="navbar navbar-default" style="margin-top: -20px; border-radius: 0px;">
  <div class="container-fluid">
    <div class="navbar-header">
	   
	  <a class="navbar-brand aborder menuClick" href="<?=$page_file;?>" title="Saņemšanas dokumenti"><i class="glyphicon glyphicon-list"></i></a>
	  <? if($view=='scan' && $id!=''){ ?>
		<a class="navbar-brand aborder menuClick" href="<?=$page_file;?>?view=scan&id=<?=$id;?>&mode=delete" title="Dzēšanas režīms"><i style="color: red;" class="glyphicon glyphicon-erase"></i></a>
		<a class="navbar-brand aborder menuClick" href="<?=$page_file;?>?view=recognize&id=<?=$id;?>" title="Atpazīšanas režīms"><i style="color: blue;" class="glyphicon glyphicon-camera"></i></a>
	  <? } ?> 
	  <? if(!$view){ ?><a class="navbar-brand aborder menuClick" href="<?=$page_file;?>?view=add" title="Dokumenta pievienošana"><i style="color: green;" class="glyphicon glyphicon-plus"></i></a><? } ?> 

    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
      <ul class="nav navbar-nav">
        <li><a href="<?=$page_file?>?view=scan&id=<?=$id;?>&mode=delete">DZĒŠANAS REŽĪMS</a></li>
		<li><a href="<?=$page_file?>?view=add">IZVEIDOT SAŅEMŠANAS DOKUMENTU</a></li>	
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
				
				
				
				
				
				
					<div class="page-header" style="margin-top: -20px; height: 20px; padding:0">
						<?
							$showId = null;
							if($id){ $showId .= '(<b>';
							
							$ladingNr = returnladingNrFromDoc($conn, $id);
							if($ladingNr){
								$showId .= $ladingNr;
							}else{
								$showId .= $id;
							}
							
							$showId .= '</b>)'; }
						
							$title = 'Saņemšanas dokumenti '.$showId;
							if($view=='scan' && $mode=='delete'){
								$title = 'Dzēšanas režīms '.$showId;
							}
							if($view=='scan' && $mode==''){
								$title = 'Skenēšanas režīms '.$showId;
							}
							if($view=='recognize' && $mode==''){
								$title = 'Atpazīšanas režīms '.$showId;
							}
							if($view=='add'){
								$title = 'Pievienot dokumentu';
							}	
						
						?>
						<h5><?=$title;?></h5>
					</div>				
				
				
				
				
				
				

				<?php	
				
				if($view=='add'){
					?>
					<div class="form-group col-md-3">
					<label for="clientCode">klienta kods - nosaukums</label>
						<select class="form-control selectpicker btn-group-xs"  id="clientCode" name="clientCode"  data-live-search="true" title="klienta kods - nosaukums" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
						
						<?
						$selectCustomers = mysqli_query($conn, "
						
							SELECT Code, Name 
							FROM n_customers AS c
							
							LEFT JOIN agreements AS a
							ON c.Code=a.customerNr
							
							WHERE a.useScan=1
							
						") or die(mysqli_error($conn));
						while($rowc = mysqli_fetch_array($selectCustomers)){
							echo '<option  value="'.$page_file.'?view=add&allowedClients='.$rowc['Code'].'">'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
						}
						?>
						
						</select>	
					</div>	
					<?					
				}

				if($view && $id){
				
					$query = mysqli_query($conn, "SELECT id
					FROM cargo_header 
					WHERE status='0' AND rowBeforeSent='0' AND rowSent='0' AND scanned='0'
					AND clientCode IN(".$allowedClientsList.")
					AND docNr='".$id."'
					ORDER BY deliveryDate ASC");
					
					if(mysqli_num_rows($query)==0){
						header("Location: ".$page_file."");
						die(0);	
					}	
					
				}
				
				
				if (!$view){
					
					require('inc/s.php');			
					$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
					$link_to = $page_file.'?page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
					$query = "SELECT *,
					(SELECT SUM(amount) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as vienibas,
					(SELECT SUM(gross) FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr) as bruto,
					(SELECT thisTransport FROM cargo_line WHERE cargo_line.docNr=cargo_header.docNr LIMIT 1) AS lineThisTransport
					FROM cargo_header WHERE status='0' AND rowBeforeSent='0' AND rowSent='0' AND scanned='0'
					AND clientCode IN(".$allowedClientsList.")
					ORDER BY deliveryDate ASC";  //NEPIECIEŠAMAIS VAICĀJUMS
					list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

					echo $page_menu;   //IZVADA TABULU AR LAPĀM			
					
					$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU 					
					if ($count_GL7!=0){

						echo '	<div class="table-responsive table"><table class="table table-hover table-responsive table"><thead><tr>
									<th>pavadzīmes nr.</th>
									<th>dokumenta nr. (iekš.)</th>
									<th>piegādes dat.</th>
									
									
									<th>transporta nr.</th>
									<th>pieņemšanas akta nr.</th>
									<th>vienības</th>
									<th>bruto (kg)</th>
									
									<th>nosaukums</th>				
									
									<th>statuss</th>
									<th>importa datums</th>
								</tr></thead><tbody>';
						$sta = null;		
						while($row = mysqli_fetch_array($resultGL7)){

							echo '	<tr class="menuClick" onclick="window.location=\''.$page_file.'?view=scan&id='.$row['docNr'].'\'">
										<td>'.$row['ladingNr'].'
										<td>'.$row['docNr'].'
										<td>';
										if($row['deliveryDate']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['deliveryDate']));}
										
										echo '<td>'; 							
										if($row['thisTransport']){
											echo $row['thisTransport'];
										}else{
											echo $row['lineThisTransport'];
										}

										echo '<td>'.$row['acceptance_act_no'].'
										<td>'.floatval($row['vienibas']).'
										<td>'.floatval($row['bruto']).'
										
										<td>'.$row['clientName'];						
										echo '<td>'.returnCargoStatus($conn, $row['id']).'
										<td>';

										if($row['importDate']!='0000-00-00 00:00:00'){
											echo $row['importDate'];
										}

										echo '
									</tr>';
						}
						
						echo '</tbody></table></div>';
						mysqli_close($conn);
					}else{
						echo '<i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!';
					}
					echo '</div>';				
							
				}

				if($view=='scan' && $id){
					
					?>			
											
						<div class="row">
						
							<div class="col-lg-6">
							  <div id="response">
								<?php
								
									require('inc/s.php');
									$query = mysqli_query($conn, "
									
										SELECT COUNT(DISTINCT(cl.serialNoPlan)) AS plan
									
										FROM cargo_header AS ch 
										
										LEFT JOIN cargo_line AS cl
										ON ch.docNr=cl.docNr
										
										WHERE ch.docNr='".$id."' AND ch.status='0' AND ch.rowBeforeSent='0' AND ch.rowSent='0' AND (cl.serialNoPlan!='' AND cl.serialNoPlan IS NOT NULL)
																				
									");	

									$row = mysqli_fetch_array($query);

									$plan = $row['plan'];

									$scanLines = mysqli_query($conn, "
									
										SELECT COUNT(serialNo) AS fact
									
										FROM scanner_lines 
										WHERE docNr='".$id."'
																				
									");	
									
									$rowSl = mysqli_fetch_array($scanLines);
									
									$fact = $rowSl['fact'];	


									$scanMatch = mysqli_query($conn, "
									
										SELECT COUNT(DISTINCT(cl.serialNoPlan)) AS document_match

										FROM cargo_header AS ch 

										LEFT JOIN cargo_line AS cl
										ON ch.docNr=cl.docNr

										LEFT JOIN scanner_lines AS sl
										ON cl.serialNoPlan=sl.serialNo

										WHERE ch.docNr='".$id."' AND ch.status='0' AND ch.rowBeforeSent='0' AND ch.rowSent='0' AND (cl.serialNoPlan!='' AND cl.serialNoPlan IS NOT NULL)
										AND cl.serialNoPlan=sl.serialNo
																				
									") or die(mysqli_error($conn));	

									$rowSm = mysqli_fetch_array($scanMatch);

									$match = $rowSm['document_match'];									
			
									if($_SESSION['serial']){
										
										$labelClass=null;
										if($_SESSION['serialAction']=='add'){
											$labelClass='success';
										}
										if($_SESSION['serialAction']=='delete'){
											$labelClass='danger';
										}	
										
										
										
										if($_SESSION['serialInDoc']=='y' && $_SESSION['serial']!='NEZINĀMS'){
										
					?>
						<style>
							.modal-dialog {
							  width: 100%;
							  height: 100%;
							  margin: 0;
							  padding: 0;
							}

							.modal-content {
							  height: auto;
							  min-height: 100%;
							  border-radius: 0;
							}	
							
							.modal-footer {
							  border-radius: 0;
							  bottom:0px;
							  position:absolute;
							  width:100%;
							}							
						</style>
						
						<style>
						.btn-file {
							position: relative;
							overflow: hidden;
						}
						.btn-file input[type=file] {
							position: absolute;
							top: 0;
							right: 0;
							min-width: 100%;
							min-height: 100%;
							font-size: 100px;
							text-align: right;
							filter: alpha(opacity=0);
							opacity: 0;
							outline: none;
							background: white;
							cursor: inherit;
							display: block;
						}

						#img-upload{
							width: 100%;
						}

						.modal-body{
						  height: calc(100vh - 180px);;
						  overflow-y: auto;
						}
						</style>						
					
						<script>
						$(document).ready( function() {
							
								$(document).on('change', '.btn-file :file', function() {
								var input = $(this),
									label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
								input.trigger('fileselect', [label]);
								});

								$('.btn-file :file').on('fileselect', function(event, label) {
									
									var input = $(this).parents('.input-group').find(':text'),
										log = label;
									
									if( input.length ) {
										input.val(log);
									} else {
										if( log ) alert(log);
									}
								
								});
								
								function readURL(input) {
									if (input.files && input.files[0]) {
										var reader = new FileReader();
										
										reader.onload = function (e) {
											$('#img-upload').attr('src', e.target.result);
										};
										
										reader.readAsDataURL(input.files[0]);
									}
								}

								$("#image").change(function(){
									readURL(this);
								});
								
								$("#clear").click(function(){
									$('#img-upload').attr('src','');
									$('#urlname').val('');
								});
								
								$(".doUpload").on('click', function() {
									$("#pleaseWait").toggle();
									$('#addPicture').submit();
								});	

								$('.cancel').click(function(){
									$("#pleaseWait").toggle();
								});								
								
							});
						</script>					
					
						<script type="text/javascript">
						$(document).ready(function(){
							$('#addImageToExiting').on('click',function(){
								$('#uploadPic').modal('show');
							});						
						});						
						</script>					
					
						<div class="modal fade" id="uploadPic" role="dialog">
							<div class="modal-dialog">
								
								<div class="modal-content">
									<div class="modal-header">
										<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="close cancel">&times;</a>
										<h4 class="modal-title">Foto režīms</h4>
									</div>
									<div class="modal-body">

										<form id="addPicture" name="addPicture" enctype='multipart/form-data' method="POST" action="">
											<input type='hidden' name='source' value='addPicture'/>
											<input type='hidden' name='serial' value='<?=$_SESSION['serial'];?>'>
											<input type='hidden' name='exitingSerial' value='y'>
											<input type='hidden' name='id' value='<?=$id;?>'>
										
											<div class="col-md-6">
												<div class="form-group">
													<label>Fotogrāfēt etiķeti </label>
													<div class="input-group">
														<span class="input-group-btn">
															<span class="btn btn-default btn-file">
																Fotogrāfēt… <input type="file" name="capture" accept="image/*" id="image" capture="camera">
															</span>
														</span>
														<input id='urlname' type="text" class="form-control" readonly>
													</div>
													
													<div style="display: inline-block; width: 100%;">
														<img id='img-upload'/>
													</div>
													
												</div>
											</div>
											
										</form>
										
										<div id="pleaseWait" style="display: none;">
											<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
										</div>										

									</div>
									<div class="modal-footer">
									
										<div style="display: inline-block; float: left;">						
											<button id="clear" class="btn btn-default" style="display: inline-block;">Notīrīt</button>
											<button class="btn btn-primary doUpload" style="display: inline-block;" id="doUpload">Augšuplādēt</button>
										</div>			
										
										<div style="display: inline-block; float: right;">
											<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="btn btn-default cancel">Atcelt</a>
										</div>
										
									</div>
								</div>
							</div>
						</div>						
					<?
										
										}
										
										$showInfo=null;
										if($_SESSION['serialInDoc']=='y' && $_SESSION['serial']!='NEZINĀMS'){
											$showInfo = '<i class="glyphicon glyphicon-barcode" id="addImageToExiting" style="color: yellow;"></i>';
										}
										
										echo '<span class="label label-'.$labelClass.'" style="height: 30px; width: 100% !important; margin: 2px; font-size: 17px; color: black; padding: 10px 2px 0px 2px; display: block;">
												'.$showInfo.' '.$_SESSION['serial'].'
											  </span>';
										
										unset($_SESSION['serial']);
										unset($_SESSION['serialAction']);
										unset($_SESSION['serialInDoc']);
									}
									
									echo '<span class="label label-info" style="height: 30px; width: 100% !important; margin: 2px; font-size: 17px; color: black; padding: 8px 2px 0px 2px; display: block;">
											nosk.: <b>'.$fact.'</b> 
											dok.: <b>'.$plan.'</b>
											sakrīt: <b>'.$match.'</b>
										  </span>';
										  
									$buttonIcon='<i class="glyphicon glyphicon-plus" style="color: green;"></i>';	  
									if($mode=='delete'){
										$buttonIcon='<i class="glyphicon glyphicon-erase" style="color: red;"></i>';
									}
								?>
							  </div>
						    </div><br>
							
						    <div class="col-lg-6">
							
							
								<form name="scan" enctype='multipart/form-data' method="POST" action="">
								
									<input type='hidden' name='source' value='scan'/>							
								
								
								
									  <div class="input-group">
										<span class="input-group-btn">
										  <a class="btn btn-default" id="serialClear"><i class="glyphicon glyphicon-remove" style="color: red;"></i></a>
										 
										</span>									  
										<input type="text" class="form-control" id="serial" name="serial" placeholder="sērijas nr." autocomplete="off" autofocus oninvalid="this.setCustomValidity('šim laukam jābūt aizpildītam')" onchange="this.setCustomValidity('')" required>
										<span class="input-group-btn">
										  
										  <a class="btn btn-default" id="serialButton" href="javascript:document.scan.submit()"><?=$buttonIcon;?></a>
										</span>
									  </div>
									  
								</form> 
								  
						    </div>
							<br>
							<div class="col-lg-6">
							
								<form name="finish" enctype='multipart/form-data' method="POST" action="">
								
									<input type='hidden' name='source' value='finish'/>
									<a class="btn btn-success col-xs-12" style="font-size: 17px;" id="finishButton" href="javascript:document.finish.submit()" onclick="return confirm('Vai tiešām vēlaties pabeigt dokumentu?')">    pabeigt
									</a>
									
								</form>	
								
							</div>
						  
						</div>					
					
					<?
				}	
				
				
				if($view=='recognize' && $id){
					?>
						<style>
							.modal-dialog {
							  width: 100%;
							  height: 100%;
							  margin: 0;
							  padding: 0;
							}

							.modal-content {
							  height: auto;
							  min-height: 100%;
							  border-radius: 0;
							}	
							
							.modal-footer {
							  border-radius: 0;
							  bottom:0px;
							  position:absolute;
							  width:100%;
							}							
						</style>
						
						<style>
						.btn-file {
							position: relative;
							overflow: hidden;
						}
						.btn-file input[type=file] {
							position: absolute;
							top: 0;
							right: 0;
							min-width: 100%;
							min-height: 100%;
							font-size: 100px;
							text-align: right;
							filter: alpha(opacity=0);
							opacity: 0;
							outline: none;
							background: white;
							cursor: inherit;
							display: block;
						}

						#img-upload{
							width: 100%;
						}

						.modal-body{
						  height: calc(100vh - 180px);;
						  overflow-y: auto;
						}
						</style>						
					
						<script>
						$(document).ready( function() {
							
								$(document).on('change', '.btn-file :file', function() {
								var input = $(this),
									label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
								input.trigger('fileselect', [label]);
								});

								$('.btn-file :file').on('fileselect', function(event, label) {
									
									var input = $(this).parents('.input-group').find(':text'),
										log = label;
									
									if( input.length ) {
										input.val(log);
									} else {
										if( log ) alert(log);
									}
								
								});
								
								function readURL(input) {
									if (input.files && input.files[0]) {
										var reader = new FileReader();
										
										reader.onload = function (e) {
											$('#img-upload').attr('src', e.target.result);
										};
										
										reader.readAsDataURL(input.files[0]);
									}
								}

								$("#image").change(function(){
									readURL(this);
								});
								
								$("#clear").click(function(){
									$('#img-upload').attr('src','');
									$('#urlname').val('');
								});
								
								$(".doUpload").on('click', function() {
									$("#pleaseWait").toggle();
									$('#addPicture').submit();
								});	

								$('.cancel').click(function(){
									$("#pleaseWait").toggle();
								});								
								
							});
						</script>					
					
						<script type="text/javascript">
							$(window).on('load',function(){
								$('#uploadPic').modal('show');
							});						
						</script>					
					
						<div class="modal fade" id="uploadPic" role="dialog">
							<div class="modal-dialog">
							
								<div class="modal-content">
									<div class="modal-header">
										<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="close cancel">&times;</a>
										<h4 class="modal-title">Foto režīms</h4>
									</div>
									<div class="modal-body">

										<form id="addPicture" name="addPicture" enctype='multipart/form-data' method="POST" action="">
											<input type='hidden' name='source' value='addPicture'/>
											<input type='hidden' name='serial' value='UNKNOWN'>
											<input type='hidden' name='id' value='<?=$id;?>'>
										
											<div class="col-md-6">
												<div class="form-group">
													<label>Fotogrāfēt etiķeti </label>
													<div class="input-group">
														<span class="input-group-btn">
															<span class="btn btn-default btn-file">
																Fotogrāfēt… <input type="file" name="capture" accept="image/*" id="image" capture="camera">
															</span>
														</span>
														<input id='urlname' type="text" class="form-control" readonly>
													</div>
													
													<div style="display: inline-block; width: 100%;">
														<img id='img-upload'/>
													</div>
													
												</div>
											</div>
											
										</form>
										
										<div id="pleaseWait" style="display: none;">
											<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
										</div>										

									</div>
									<div class="modal-footer">
									
										<div style="display: inline-block; float: left;">						
											<button id="clear" class="btn btn-default" style="display: inline-block;">Notīrīt</button>
											<button class="btn btn-primary doUpload" style="display: inline-block;" id="doUpload">Augšuplādēt</button>
										</div>			
										
										<div style="display: inline-block; float: right;">
											<a href="<?=$page_file;?>?view=scan&id=<?=$id;?>" class="btn btn-default cancel">Atcelt</a>
										</div>
										
									</div>
								</div>
							</div>
						</div>						
					<?
				}				
				

				?>
				
				</div>
			</div>
		</div>
	</div>
</div>


<script type='text/javascript'>

	$(document).ready(function(){
		$('#serialClear').on('click', function(){
			location.reload();
			$("#pleaseWait").toggle();
		});
		
		$('#serialButton').click(function(){
			$("#pleaseWait").toggle();
		});
		
		$('.menuClick').click(function(){
			$("#pleaseWait").toggle();
		});		
		
	});

</script>

<div id="pleaseWait" style="display: none;">
    <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
</div>

<?php include("footer.php"); ?>