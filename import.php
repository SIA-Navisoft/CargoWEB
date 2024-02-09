<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php'); 

$page_file = 'import';

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

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}

include('functions/base.php');
require('inc/s.php');



$import = $id = $res = $file = $view = null;
if (isset($_GET['import'])){$import = htmlentities($_GET['import'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['file'])){$file = htmlentities($_GET['file'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}


if ($import=='all' && !$file) {


	function scanDirectories($rootDir, $allData=array()) {
		// set filenames invisible if you want
		$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
		// run through content of root directory
		$dirContent = scandir($rootDir);
		foreach($dirContent as $key => $content) {
			// filter all files not accessible
			$path = $rootDir.'/'.$content;
			if(!in_array($content, $invisibleFileNames)) {
				// if content is file & readable, add to array
				if(is_file($path) && is_readable($path)) {
					// save file name with path
					$allData[] = $content;
				// if content is a directory and readable, add path and name
				}
			}
		}
		return $allData;
	}


	$files = scanDirectories("kurekss_xml");

	if(count($files)>0){	
		$noFiles='n';	
		foreach($files AS $val){
			
			$checkLogs = mysqli_query($conn, "SELECT file_name FROM xml_imports WHERE file_name='".$val."' AND status=0");
			$cl = mysqli_num_rows($checkLogs);
			
			if (file_exists('kurekss_xml/'.$val) && $cl==0) {				

				$xml = simplexml_load_file('kurekss_xml/'.$val);
				
					$id=findLastRow("cargo_header")+1;

					$invoice_number = 'PN'.sprintf("%'05d\n", $id);
					$invoice_number = trim($invoice_number);

					$deliveryDate = date('Y-m-d', strtotime($xml->date));

					$landingDate = date('Y-m-d', strtotime($xml->date));	

					$applicationDate = date('Y-m-d', strtotime($xml->date));
													
					$resource = 'LDST0';
					$clientCode = '40003250931';
					$agreements = 'LIG00067';
					$transport = 'autotransports';
			
					$cargoCode = safeHTML($xml->id).$clientCode;
			
			
					$selectClient = mysqli_query($conn, "
						SELECT Name 
						FROM n_customers
						WHERE code='".$clientCode."'
					") or die(mysqli_error($conn));
					$row = mysqli_fetch_array($selectClient);
					
					$clientName = $row['Name'];

					//hedera inserts
					$form_data = array(
					'docNr' => safeHTML($invoice_number),
					'ladingNr' => safeHTML($xml->id),

					'application_no' => safeHTML($xml->callOffId),
					'application_date' => $applicationDate,
					'landingDate' => $landingDate,
					
					'deliveryDate' => $deliveryDate,

					'agreements' => $agreements,
					'clientCode' => $clientCode,
					'clientName' => $clientName,	
					'ownerCode' => $clientCode,
					'ownerName' => $clientName,
					
					'lastBy' => $myid,
					'lastDate' => date('Y-m-d H:i:s'),		
					'cargoCode' => $cargoCode,

					'transport' => $transport,
					
					'resource' => $resource,
					'copyRest' => 0,
					'thisTransport' => safeHTML($xml->car),
					'importDate' => date('Y-m-d H:i:s') 
					
					);
					insertNewRows("cargo_header", $form_data);			
					

					foreach($xml AS $data){
						
						if(isset($data->id) || isset($data->number) || isset($data->date) || isset($data->inventory) || isset($data->product) || isset($data->length) || isset($data->moistureContent) || isset($data->exLog) || isset($data->typeOfBoard) || isset($data->packageType) || isset($data->pieces) || isset($data->m3net) || isset($data->m3nom) || isset($data->lm) || isset($data->destination) || isset($data->marking) || isset($data->certification)){	
							
							$productz = safeHTML($data->destination).' '.safeHTML($data->marking);
							
							$getProduct = mysqli_query($conn, "
								SELECT i.code, i.unitOfMeasurement, i.assistantUmo
								FROM agreements_lines AS a
								LEFT JOIN n_items i
								ON a.item=i.code
								WHERE a.productMatch='".$productz."'
							") or die(mysqli_error($conn));
							$row = mysqli_fetch_array($getProduct);
							
							$product = $row['code'];							
							$productUmo = $row['unitOfMeasurement'];							
							$assistantUmo = $row['assistantUmo'];							
							
							//līniju inserts
							$form_data_l = array(
							'docNr' => $invoice_number,
							
							'serialNo' => safeHTML($data->number),
							'serialNoPlan' => safeHTML($data->number),
							
							'place_count' => 1,
							
							'activityDate' => $deliveryDate,
							'productNr' => $product,
							'productUmo' => $productUmo,
							'assistantUmo' => $assistantUmo,
							'amount' => str_replace(',', '.', $data->m3net),
							'assistant_amount' => 1,
							
							'location' => 'L',
							'thisDate' => $deliveryDate,
							'thisTransport' => safeHTML($xml->car),	

							
							'enteredDate' => date('Y-m-d H:i:s'),
							'enteredBy' => $myid,
					
							'cubicMeters' => str_replace(',', '.', $data->m3net),
	
							'resource' => $resource,
							'import_product_name' => safeHTML($data->product)	
							);
							insertNewRows("cargo_line", $form_data_l);							
							
						}
						
					}
					
					$file_logs = array(
						'docNr' => $invoice_number,
						'file_name' => $val,
						'createdBy' => $myid,
						'createdDate' => date('Y-m-d H:i:s')
					);
					insertNewRows("xml_imports", $file_logs);					

				$noFiles='y';
			} 
			
			if(!file_exists('kurekss_xml/'.$val)){
				header("Location: ".$page_file."?res=2");
				die(0);
			}	
			
			
		}
		
		if($noFiles=='n'){
			header("Location: ".$page_file."?res=3");
			die(0);
		}		
		
	}else{
		header("Location: ".$page_file."?res=3");
		die(0);
	}

	header("Location: ".$page_file."?res=1");
	die(0); 
}	



if ($import=='this' && $file) {
			
	$file = $_GET['file'];
	
	$checkLogs = mysqli_query($conn, "SELECT file_name FROM xml_imports WHERE file_name='".$file."' AND status=0");
	$cl = mysqli_num_rows($checkLogs);
	
	if (file_exists('kurekss_xml/'.$file) && $cl==0) {	

		$xml = simplexml_load_file('kurekss_xml/'.$file);
		
			$id=findLastRow("cargo_header")+1;

			$invoice_number = 'PN'.sprintf("%'05d\n", $id);
			$invoice_number = trim($invoice_number);

			$deliveryDate = date('Y-m-d', strtotime($xml->date));

			$landingDate = date('Y-m-d', strtotime($xml->date));	

			$applicationDate = date('Y-m-d', strtotime($xml->date));
											
			$resource = 'LDST0';
			$clientCode = '40003250931';
			$agreements = 'LIG00067';
			$transport = 'autotransports';
	
			$cargoCode = safeHTML($xml->id).$clientCode;
	
	
			$selectClient = mysqli_query($conn, "
				SELECT Name 
				FROM n_customers
				WHERE code='".$clientCode."'
			") or die(mysqli_error($conn));
			$row = mysqli_fetch_array($selectClient);
			
			$clientName = $row['Name'];

			//hedera inserts
			$form_data = array(
			'docNr' => safeHTML($invoice_number),
			'ladingNr' => safeHTML($xml->id),

			'application_no' => safeHTML($xml->callOffId),
			'application_date' => $applicationDate,
			'landingDate' => $landingDate,
			
			'deliveryDate' => $deliveryDate,
			
			'agreements' => $agreements,
			'clientCode' => $clientCode,
			'clientName' => $clientName,	
			'ownerCode' => $clientCode,
			'ownerName' => $clientName,
		
			'lastBy' => $myid,
			'lastDate' => date('Y-m-d H:i:s'),		
			'cargoCode' => $cargoCode,

			'transport' => $transport,
			
			'resource' => $resource,
			'copyRest' => 0,
			'thisTransport' => safeHTML($xml->car),
			'importDate' => date('Y-m-d H:i:s') 
			);
			insertNewRows("cargo_header", $form_data);			
			

			foreach($xml AS $data){
				
				if(isset($data->id) || isset($data->number) || isset($data->date) || isset($data->inventory) || isset($data->product) || isset($data->length) || isset($data->moistureContent) || isset($data->exLog) || isset($data->typeOfBoard) || isset($data->packageType) || isset($data->pieces) || isset($data->m3net) || isset($data->m3nom) || isset($data->lm) || isset($data->destination) || isset($data->marking) || isset($data->certification)){	
					
					$productz = safeHTML($data->destination).' '.safeHTML($data->marking);
					
					$getProduct = mysqli_query($conn, "
						SELECT i.code, i.unitOfMeasurement, i.assistantUmo
						FROM agreements_lines AS a
						LEFT JOIN n_items i
						ON a.item=i.code
						WHERE a.productMatch='".$productz."' 
					") or die(mysqli_error($conn));
					$row = mysqli_fetch_array($getProduct);
					
					$product = $row['code'];							
					$productUmo = $row['unitOfMeasurement'];							
					$assistantUmo = $row['assistantUmo'];							
					
					//līniju inserts
					$form_data_l = array(
					'docNr' => $invoice_number,
					
					'serialNo' => safeHTML($data->number),
					'serialNoPlan' => safeHTML($data->number),
					
					'place_count' => 1,
					
					'activityDate' => $deliveryDate,
					'productNr' => $product,
					'productUmo' => $productUmo,
					'assistantUmo' => $assistantUmo,
					'amount' => str_replace(',', '.', $data->m3net),
					'assistant_amount' => 1,
					
					'location' => 'L',
					'thisDate' => $deliveryDate,
					'thisTransport' => safeHTML($xml->car),	

					
					'enteredDate' => date('Y-m-d H:i:s'),
					'enteredBy' => $myid,
			
					'cubicMeters' => str_replace(',', '.', $data->m3net),
					
					'resource' => $resource,
					'import_product_name' => safeHTML($data->product)
						
					);
					insertNewRows("cargo_line", $form_data_l);							
					
				}
				
			}
			
			$file_logs = array(
				'docNr' => $invoice_number,
				'file_name' => $file,
				'createdBy' => $myid,
				'createdDate' => date('Y-m-d H:i:s')
			);
			insertNewRows("xml_imports", $file_logs);
		
	} else {
		header("Location: ".$page_file."?res=2");
		die(0);
	}	

	header("Location: ".$page_file."?res=1");
	die(0); 
}

include('header.php');
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

							<div>KUREKSS SIA Imports</div>

							<?php
								if ($res=="1"){echo '<div class="pull-right" style="margin-top: -25px;"><div class="btn btn-success btn-sm">Importēts!</div></div>';}
								if ($res=="2"){echo '<div class="pull-right" style="margin-top: -25px;"><div class="btn btn-danger btn-sm">Kļūda atverot failu!</div></div>';}
								if ($res=="3"){echo '<div class="pull-right" style="margin-top: -25px;"><div class="btn btn-danger btn-sm">Faili nav atrasti!</div></div>';}
							?>
						
						</div>					
				
				
				
				
				
					<?php
					
					
					
						if(!$view && !$file){
							
							echo '
							<a href="?import=all" class="btn btn-default btn-xs">
								<i class="glyphicon glyphicon-import"></i> importēt visus dokumentus
							</a>';
								
							echo '<br><br>';
							
							function scanDirectories($rootDir, $allData=array()) {
								// set filenames invisible if you want
								$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
								// run through content of root directory
								$dirContent = scandir($rootDir);
								foreach($dirContent as $key => $content) {
									// filter all files not accessible
									$path = $rootDir.'/'.$content;
									if(!in_array($content, $invisibleFileNames)) {
										// if content is file & readable, add to array
										if(is_file($path) && is_readable($path)) {
											// save file name with path
											$allData[] = $content;
										// if content is a directory and readable, add path and name
										}
									}
								}
								return $allData;
							}


							$files = scanDirectories("kurekss_xml");

							echo '<div class="table-responsive table-sm"><table border="1" class="table table-hover table-responsive table-sm" style="border-top: solid #ddd 2px !important;">
									<thead>
										<tr>
											<th>faila nosaukums</th>
											<th>importēt</th>
										</tr>
									</thead>
									<tbody>';
									
									if(count($files)>0){
											
										$noFiles='n';
										foreach($files AS $val){
										
											$checkLogs = mysqli_query($conn, "SELECT file_name FROM xml_imports WHERE file_name='".$val."' AND status=0");
											$cl = mysqli_num_rows($checkLogs);
											
											if (file_exists('kurekss_xml/'.$val) && $cl==0) {

												echo '<tr>
														<td>'.$val.'</td>
														<td>
															<a href="?import=this&file='.urlencode($val).'" class="btn btn-default btn-xs" style="margin: 2px;">
																<i class="glyphicon glyphicon-import"></i> importēt
															</a>														
														</td>
													  </tr>';
												$noFiles='y';	  
											}											
											
										}
										
										if($noFiles=='n'){
											echo '<tr><td colspan="2"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> Faili nav atrasti!</td></tr>';
										}										

									}else{
										echo '<tr><td colspan="2"><i class="glyphicon glyphicon-ban-circle" style="color: red;"></i> Faili nav atrasti!</td></tr>';
									}
							
							echo '</tbody>
							</table></div>';															
							
						}
					

						if($view=='all' && !$file){
					
							function scanDirectories($rootDir, $allData=array()) {
								// set filenames invisible if you want
								$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
								// run through content of root directory
								$dirContent = scandir($rootDir);
								foreach($dirContent as $key => $content) {
									// filter all files not accessible
									$path = $rootDir.'/'.$content;
									if(!in_array($content, $invisibleFileNames)) {
										// if content is file & readable, add to array
										if(is_file($path) && is_readable($path)) {
											// save file name with path
											$allData[] = $content;
										// if content is a directory and readable, add path and name
										}
									}
								}
								return $allData;
							}

							$files = scanDirectories("kurekss_xml");

							if(count($files)>0){		
								foreach($files AS $val){
									
									if (file_exists('kurekss_xml/'.$val)) {
										$xml = simplexml_load_file('kurekss_xml/'.$val);

										$id=findLastRow("cargo_header")+1;

										$invoice_number = 'PN'.sprintf("%'05d\n", $id);
										$invoice_number = trim($invoice_number);

										$deliveryDate = date('Y-m-d', strtotime($xml->date));

										$landingDate = date('Y-m-d', strtotime($xml->date));	

										$applicationDate = date('Y-m-d', strtotime($xml->date));
																		
										$resource = 'LDST0';
										$clientCode = '40003250931';
										$agreements = 'LIG00067';
										$transport = 'autotransports';
								
										$cargoCode = safeHTML($xml->callOffId).$clientCode;
								
								
										$selectClient = mysqli_query($conn, "
											SELECT Name 
											FROM n_customers
											WHERE code='".$clientCode."'
										") or die(mysqli_error($conn));
										$row = mysqli_fetch_array($selectClient);
										
										$clientName = $row['Name'];

										//hedera inserts
										$form_data = array(
										'docNr' => safeHTML($invoice_number),
										'ladingNr' => safeHTML($xml->callOffId),

										'application_no' => safeHTML($xml->id),
										'application_date' => $applicationDate,
										'landingDate' => $landingDate,
										
										'deliveryDate' => $deliveryDate,
										
										'agreements' => $agreements,
										'clientCode' => $clientCode,
										'clientName' => $clientName,	
										'ownerCode' => $clientCode,
										'ownerName' => $clientName,
										
										'lastBy' => $myid,
										'lastDate' => date('Y-m-d H:i:s'),		
										'cargoCode' => $cargoCode,

										'transport' => $transport,
										
										'resource' => $resource,
										'copyRest' => 0,
										'thisTransport' => safeHTML($xml->car),
										'importDate' => date('Y-m-d H:i:s') 
										
										);

										//header
										echo '<br><br>hederis??? '.$val.'<br>';
										echo '<table border="1">
												<thead>
													<tr>
														<td>id</td>
														<td>callOffId</td>
														<td>date</td>
														<td>client</td>
														<td>car</td>
														<td>driver</td>
														<td>shipper</td>
														<td>source</td>
														<td>destination</td>
													</tr>
												</thead>
												<tbody>';
												
													
													echo '<tr>';
														echo '<td>'.$xml->id.'</td>';
														echo '<td>'.$xml->callOffId.'</td>';
														echo '<td>'.$xml->date.'</td>';
														echo '<td>'.$xml->client.'</td>';
														echo '<td>'.$xml->car.'</td>';
														echo '<td>'.$xml->driver.'</td>';
														echo '<td>'.$xml->shipper.'</td>';
														echo '<td>'.$xml->source.'</td>';
														echo '<td>'.$xml->destination.'</td>';
													echo '</tr>';

												
											echo '</tbody>
											 </table>';

										//lines
										echo '<br><br>līnijas???<br>';	 		 
										echo '<table border="1">
												<thead>
													<tr>
														<td>id</td>
														<td>number</td>
														<td>date</td>
														<td>inventory</td>
														<td>product</td>
														<td>length</td>
														<td>moistureContent</td>
														<td>exLog</td>
														<td>typeOfBoard</td>
														<td>packageType</td>
														<td>pieces</td>
														<td>m3net</td>
														<td>m3nom</td>
														<td>lm</td>
														<td>destination</td>
														<td>marking</td>
														<td>certification</td>
													</tr>
												</thead>
												<tbody>';
										
												foreach($xml AS $data){
													
													if(isset($data->id) || isset($data->number) || isset($data->date) || isset($data->inventory) || isset($data->product) || isset($data->length) || isset($data->moistureContent) || isset($data->exLog) || isset($data->typeOfBoard) || isset($data->packageType) || isset($data->pieces) || isset($data->m3net) || isset($data->m3nom) || isset($data->lm) || isset($data->destination) || isset($data->marking) || isset($data->certification)){	
														
														
														
														$getProduct = mysqli_query($conn, "
															SELECT i.code, i.unitOfMeasurement, i.assistantUmo
															FROM agreements_lines AS a
															LEFT JOIN n_items i
															ON a.item=i.code
															WHERE a.productMatch='".safeHTML($data->product)."' AND a.productLength='".safeHTML($data->length)."'
														") or die(mysqli_error($conn));
														$row = mysqli_fetch_array($getProduct);
														
														$product = $row['code'];							
														$productUmo = $row['unitOfMeasurement'];							
														$assistantUmo = $row['assistantUmo'];							
														
														//līniju inserts
														$form_data_l = array(
														'docNr' => $invoice_number,
														
														'serialNo' => safeHTML($data->number),
														'serialNoPlan' => safeHTML($data->number),
														
														'activityDate' => $deliveryDate,
														'productNr' => $product,
														'productUmo' => $productUmo,
														'assistantUmo' => $assistantUmo,
														'amount' => intval($data->pieces),

														'thisDate' => $deliveryDate,
														'thisTransport' => safeHTML($xml->car),	

														
														'enteredDate' => date('Y-m-d H:i:s'),
														'enteredBy' => $myid,

														'cubicMeters' => str_replace(',', '.', $data->m3net),

														'resource' => $resource,
																
														);

														echo '<tr>';
															echo '<td>'.$data->id.'</td>';
															echo '<td>'.$data->number.'</td>';
															echo '<td>'.$data->date.'</td>';
															echo '<td>'.$data->inventory.'</td>';
															echo '<td style="background-color: silver;" nowrap>&nbsp;&nbsp;'.$data->product.'&nbsp;&nbsp;</td>';
															echo '<td style="background-color: silver;" nowrap>&nbsp;&nbsp;'.$data->length.'&nbsp;&nbsp;</td>';
															echo '<td>'.$data->moistureContent.'</td>';
															echo '<td>'.$data->exLog.'</td>';
															echo '<td>'.$data->typeOfBoard.'</td>';
															echo '<td>'.$data->packageType.'</td>';
															echo '<td>'.$data->pieces.'</td>';
															echo '<td>'.$data->m3net.'</td>';
															echo '<td>'.$data->m3nom.'</td>';
															echo '<td>'.$data->lm.'</td>';
															echo '<td>'.$data->destination.'</td>';
															echo '<td>'.$data->marking.'</td>';
															echo '<td>'.$data->certification.'</td>';
														echo '</tr>';
													}
													
												}
												
										echo '</tbody>
										 </table>';		
										
										

									} else {
										echo '<td colspan="9">Kļūda atverot '.$val.'.</td>';
									}	
									
									
								}
							}else{
								echo 'Faili nav atrasti.';
							}
							
						}
						
						if($view=='this' && $file){
							
							echo 'importē konkrēto failu';
							
						}
					?>


				</div>
			</div>
		</div>
	</div>
</div>





<?php include("footer.php"); ?>