<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="agreements";


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

$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('../../functions/base.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;} 

if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['id'])){$id = htmlentities($_GET['id'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['se'])){$se = htmlentities($_GET['se'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['sid'])){$sid = htmlentities($_GET['sid'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}

$user_group_array = array("w_user" => "NNVT", "w_partner" => "");

if($se){
	$_POST['name'] = $se;
}

if($sid){
	$_POST['id'] = $sid;
}

if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}






if($search=='agr'){

    require('../../inc/s.php');



	if($_POST['name']){

        $s = mysqli_real_escape_string($conn, $_POST['name']);	
        $searchit = " AND (contractNr LIKE '%$s%' || customerNr LIKE '%$s%' || customerName LIKE '%$s%')  ORDER BY customerNr DESC";
        $searchUrl = 'search='.$s.'&';	
        }else{
        $searchUrl = null;	
        $searchit = "  ORDER BY customerNr DESC";	
        }
        
        if($_POST['aStatus']=='on'){$eQue = " WHERE (dateTo!='0000-00-00 00:00:00' OR dateTo IS NOT NULL) AND dateTo<CURDATE() AND deleted='0'";}else{$eQue = " WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND deleted='0'";}

        $rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
        $link_to = $page_file.'?'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
        $query = "SELECT * FROM agreements ".$eQue." ".$searchit."";  //NEPIECIEŠAMAIS VAICĀJUMS
        list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 
    
        echo '<div style="display: inline-block;">'.$page_menu.'</div>';   				
    

        
    $count_GL7 = mysqli_num_rows($resultGL7) or die (mysqli_error($conn));  //IEGŪST SKAITU 	
       
		if ($count_GL7!=0){

			echo '	<div class="table-responsive"><table class="table table-hover table-responsive"><tbody>';   

			while($row = mysqli_fetch_array($resultGL7)){
				
				if($row['contractNr']==$id){$bg = ' style="background-color: #337ab7; color: white;"';}else{$bg = null;}
				?>
				<tr onclick="newDoc('<?=$row['contractNr'];?>')" <?=$bg;?>>
				<?php
				echo '	

							<td>'.$row['contractNr'].'
							<td>'.$row['customerNr'].' ('.$row['customerName'].')

						</tr>';
			}
			
			echo '</tbody></table></div></div>';
			mysqli_close($conn);
		}


}


if($p_edit=='on'){

if($line!=''){
	$formPost = '#agreement_line_edit';
	$formUrl = '/pages/agreements/post.php?r=edit&id='.$_POST['id'].'&line='.$_POST['line'];
}else{
	$formPost = '#agreement_line';
	$formUrl = '/pages/agreements/post.php?r=add&id='.$_POST['id'];
}

?>

<script>
$(document).ready(function () {
    $('<?=$formPost;?>').on('submit', function(e) {
        e.preventDefault();
		
        $.ajax({
            url : '<?=$formUrl;?>',
            type: "POST",
            data: $(this).serializeArray(),
			beforeSend: function(){
				$('#savebtn').html("gaidiet...");
				$("#savebtn").prop("disabled",true);
			},			
            success: function (data) {
				console.log(data);
				var productNr = $("#productNr").val();
				var amount = $("#amount").val();
				$('#agreements').load('/pages/agreements/agreements.php?view=edit&s=<?=$_POST['name'];?>&id=<?=$_POST['id'];?>&res=done');
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
</script>

<?php } 

if($search=='lines'){

	require('../../inc/s.php');

	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);	
	$searchit = " AND (service LIKE '%$s%' || service_name LIKE '%$s%' || item LIKE '%$s%' || dateFrom LIKE '%$s%' || dateTo LIKE '%$s%') ORDER BY id DESC";
	$searchUrl = 'search='.$s.'&';	
	}else{
	$searchUrl = null;	
	$searchit = " ORDER BY id DESC";	
	}

    
    $getInfoAgr = mysqli_query($conn, "SELECT customerNr, outerNr, freeDays, dateFrom, dateTo, status FROM agreements WHERE contractNr='".mysqli_real_escape_string($conn, $_POST['id'])."'");
    $giaRow = mysqli_fetch_array($getInfoAgr) or die(mysqli_error($conn));

	$rec_limit = $a_p_l; $offset=0; $max_pages = 10;  //DEFINĒJAM LIMITUS
	$link_to = $page_file.'?'.$searchUrl.'page='; //LINKS KAS TIKS ATVĒRTS KAD NOSPIEDĪS UZ LAPAS, RAKSTĪT LĪDZ page 
	$query = "SELECT * FROM agreements_lines WHERE contractNr='".mysqli_real_escape_string($conn, $_POST['id'])."' AND deleted='0' ".$searchit."";  //NEPIECIEŠAMAIS VAICĀJUMS
	list($page_menu, $result_all, $resultGL7) = pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query);  //TIEK IEGŪTS ARRAY KO UZŅEM AR LIST 

	echo $page_menu;   		
	
	$count_GL7 = mysqli_num_rows($resultGL7);  //IEGŪST SKAITU
		
				if ($count_GL7>0){
		
					echo '
					<div class="table-responsive">';

					if($p_edit=='on' && $giaRow['status']<20 && (date('d.m.Y', strtotime($giaRow['dateTo']))>=date('d.m.Y') || $giaRow['dateTo']=='0000-00-00 00:00:00')){
						if($line!=''){
							echo '<form id="agreement_line_edit">';
						}else{
							echo '<form id="agreement_line">';
						}
					}
						

						echo '
							<table class="table table-hover table-responsive">
								
								<thead>
									<th>palīg pak.
									<th>pakalpojums
									<th>glab.
                                    <th nowrap>pakalpojuma nos.
                                    <th style="min-width: 150px;">prece
                                    <th>mērvienība
									<th>tarifs
									<th>datums no
									<th>datums līdz';
									if($p_edit=='on' && $giaRow['status']<20 && date('d.m.Y', strtotime($giaRow['dateTo']))>=date('d.m.Y')){echo '<th>dzēst';}
                                echo '
                                </thead>
								<tbody>';   
			
								while($row = mysqli_fetch_array($resultGL7)){

									echo '
									<tr '; if($p_edit=='on' && $giaRow['status']<20 && date('d.m.Y', strtotime($giaRow['dateTo']))>=date('d.m.Y')){ echo ' onclick="editDoc('.$row['id'].')"';} echo ' >
										<td>';
										if($row['extra_resource']=='on'){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
										echo '									
                                        <td>'.$row['service'].' ('.returnResourceName($conn, $row['service']).')
										<td>';
										if($row['keeping']=='on'){echo '<i class="glyphicon glyphicon-ok" style="color: green;"></i>';}
										echo '
										<td>'.$row['service_name'].'
                                        <td>';
										if($row['item']){echo $row['item'].' ('.returnProductName($conn, $row['item']).')';}
										echo '
                                        <td>'.$row['uom'].' ('.returnUomName($conn, $row['uom']).')
										<td>'.floatval($row['tariffs']).'
										<td>';
										if($row['dateFrom']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['dateFrom']));}
										echo '<td>';
										if($row['dateTo']!='0000-00-00 00:00:00'){echo date('d.m.Y', strtotime($row['dateTo']));}
                                        
										if($p_edit=='on' && $giaRow['status']<20 && date('d.m.Y', strtotime($giaRow['dateTo']))>=date('d.m.Y')){
											echo '<td><button class="btn btn-default btn-xs" data-dol="'.$row['id'].'" onclick="delLine(event)" id="del'.$row['id'].'" style="display: inline-block;"><i class="glyphicon glyphicon-erase" style="color: red;"></i> dzēst</button>';
										}
                                        
                                        echo '
										<td>				
									</tr>';

								}

								if($p_edit=='on' && $giaRow['status']<20 && (date('d.m.Y', strtotime($giaRow['dateTo']))>=date('d.m.Y') || $giaRow['dateTo']=='0000-00-00 00:00:00')){

									if($line){

										$line = mysqli_real_escape_string($conn, $line);
										$selectLineInfo = mysqli_query($conn, "SELECT * FROM agreements_lines WHERE id='".$line."' AND deleted='0'");
										$l_row = mysqli_fetch_array($selectLineInfo);
										
										$resource = $l_row['service'];
                                        $product = $l_row['item'];
                                        $uom = $l_row['uom'];
										$tariffs = ' value="'.floatval($l_row['tariffs']).'"';
                                        if($l_row['dateFrom']){ $dateFrom = ' value="'.date('d.m.Y', strtotime($l_row['dateFrom'])).'"';}else{$dateFrom=null;}
										if($l_row['dateTo']){$dateTo = ' value="'.date('d.m.Y', strtotime($l_row['dateTo'])).'"';}else{$dateTo = null;}
										$serviceName = ' value="'.$l_row['service_name'].'"';
										
										$keeping = $l_row['keeping'];
										$extraResource = $l_row['extra_resource'];


									}else{
										$resource = $product = $tariffs = $keeping = $extraResource = null;
									}

									echo '							
									<tr>
										<td><input type="checkbox" id="extraResource" name="extraResource"'; if($extraResource=='on'){echo ' checked';} echo ' >
										<td>
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="pakalpojums" name="resource">';
											require('../../inc/s.php');	
											$selectResource = mysqli_query($conn, "SELECT * FROM n_resource");
											while($rowi = mysqli_fetch_array($selectResource)){
												echo '<option value="'.$rowi['id'].'"';
													if($resource!='' && $resource==$rowi['id']){ echo ' selected';}
												echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
                                            </select>
                                            
										<td><input type="checkbox" name="keeping"'; if($keeping=='on'){echo ' checked';} echo '>
                                        <td><input type="text" class="form-control" name="serviceName" placeholder="pakalpojuma nosaukums" '.$serviceName.'>


										<td>';

										if($extraResource!='on'){
											echo'
											<select data-width="150px" class="form-control selectpicker btn-group-xs product"  data-live-search="true" title="prece" name="product">';
											
											$selectProducts = mysqli_query($conn, "SELECT * FROM n_items");
											while($rowi = mysqli_fetch_array($selectProducts)){
												echo '<option value="'.$rowi['code'].'"';
													if($product!='' && $product==$rowi['code']){ echo ' selected';}
												echo '>'.$rowi['code'].' ('.$rowi['name1'].' '.$rowi['name2'].')</option>';
											}
											
											echo '
                                            </select>';
										}
										
										echo '	
                                        <td>							
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="mērvienība" name="uom">';
											
											$selectUOM = mysqli_query($conn, "SELECT code, name FROM unit_of_measurement");
											while($rowi = mysqli_fetch_array($selectUOM)){
												echo '<option value="'.$rowi['code'].'"';
													if($uom!='' && $uom==$rowi['code']){ echo ' selected';}
												echo '>'.$rowi['code'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>                                            

										<td><input type="text" class="form-control numbersOnly" name="tariffs" placeholder="tarifs" '.$tariffs.'>

										<td><input type="text" class="form-control datepicker" name="dateFrom" '.$dateFrom.'>	

										<td><input type="text" class="form-control datepicker" name="dateTo" '.$dateFrom.'>

										<td>';

											if($line!=''){
												echo '<button type="submit" class="btn btn-default btn-sm" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';
											}else{
												echo '<button type="submit" class="btn btn-default btn-sm" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>';
											}

									echo '	
									</tr>';
								}

								echo '	
								</tbody>
							</table>';


						
							if($p_edit=='on'){
								echo '</form>';
							}


					echo '	
					</div>';
					mysqli_close($conn);

				}else{

					echo '<div><i class="glyphicon glyphicon-ban-circle glyphicon-lg" style="color: red;"></i> nav neviena ieraksta!</div>';


					if($p_edit=='on'){
						if($line!=''){
							echo '<form id="agreement_line_edit">';
						}else{
							echo '<form id="agreement_line">';
						}
					

					echo '
					<div class="table-responsive">
						<form id="agreement_line">
							<table class="table table-hover table-responsive">
								
								<thead>
                                    <th>pakalpojums
                                    <th nowrap>pakalpojuma nos.
                                    <th>prece
                                    <th>mērvienība
									<th>tarifs
									<th>datums no
									<th>datums līdz
									<th>
								</thead>
								<tbody>
									<tr>
										<td>

											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="pakalpojums" name="resource">';
											require('../../inc/s.php');	
											$selectResource = mysqli_query($conn, "SELECT * FROM n_resource");
											while($rowi = mysqli_fetch_array($selectResource)){
												echo '<option value="'.$rowi['id'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
											</select>

                                        <td><input type="text" class="form-control" name="serviceName" placeholder="pakalpojuma nosaukums">

                                        <td>
										
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="prece" name="product">';
											
											$selectProducts = mysqli_query($conn, "SELECT * FROM n_items");
											while($rowi = mysqli_fetch_array($selectProducts)){
												echo '<option value="'.$rowi['code'].'">'.$rowi['code'].' ('.$rowi['name1'].' '.$rowi['name2'].')</option>';
											}
											
											echo '
                                            </select>
                                            
 										<td>
										
											<select data-width="150px" class="form-control selectpicker btn-group-xs"  data-live-search="true" title="mērvienība" name="uom">';
											
											$selectUOM = mysqli_query($conn, "SELECT code, name FROM unit_of_measurement");
											while($rowi = mysqli_fetch_array($selectUOM)){
												echo '<option value="'.$rowi['code'].'">'.$rowi['code'].' ('.$rowi['name'].')</option>';
											}
											
											echo '
                                            </select>
                                            
                                            

										<td><input type="text" class="form-control numbersOnly" name="tariffs" placeholder="tarifs">

										<td><input type="text" class="form-control datepicker" name="dateFrom">	

										<td><input type="text" class="form-control datepicker" name="dateTo">

										<td><button type="submit" class="btn btn-default btn-sm" id="savebtn"><i class="glyphicon glyphicon-plus" style="color: green;"></i> pievienot</button>

									</tr>								
								</tbody>
							</table>
						</form>';
					}	
					echo '	
					</div>'; 							
                }
}
?>
<script>

$("#extraResource").click(function() {
    if($(this).is(":checked")) {
        $(".product").hide();
    } else {
        $(".product").show();
    }
});
</script>

<script>
    $(document).ready(function() {
        $('.numbersOnly').keypress(function (event) {
            return isNumber(event, this)
			
        });
    });
    // THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
    function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    } 
</script>
<script>
$(function() {
    $(".paging").delegate("a", "click", function(event) {	
		var url = $(this).attr('href');
		 
		var page = url.match(/\d+$/);
		
		<?php if($search=='agr'){ ?>
			$('#sAgr').load('/pages/agreements/search.php?page='+page+'&search=<?=$search;?>&se=<?=$_POST['name'];?>&sid=<?=$_POST['id'];?>');
		<? } ?>

		<?php if($search=='lines'){ ?>
			$('#sLines').load('/pages/agreements/search.php?page='+page+'&search=<?=$search;?>&se=<?=$_POST['name'];?>&sid=<?=$_POST['id'];?>');
		<? } ?>

		event.preventDefault();
    });
});
</script>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>
<?php include_once("../../datepicker.php"); ?>