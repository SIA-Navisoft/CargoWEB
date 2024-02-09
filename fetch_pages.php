<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="product_catalog";


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
if (isset($_GET['search'])){$search = htmlentities($_GET['search'], ENT_QUOTES, "UTF-8");}

require('inc/s.php');

	if($_POST['name']){
	$s = mysqli_real_escape_string($conn, $_POST['name']);	
	$search = " WHERE (code LIKE '%$s%' || barCode LIKE '%$s%' || name1 LIKE '%$s%' || unitOfMeasurement LIKE '%$s%') ORDER BY status DESC, code ASC";
	$searchUrl = '?search='.$s.'&';	
	}else{
	$searchUrl = null;	
	$search = " ORDER BY status DESC, code ASC";	
	}
 

//sanitize post value
if(isset($_POST["page"])){
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1;
}


//get current starting point of records
$position = (($page_number-1) * $item_per_page);



$results = mysqli_query($conn,"SELECT COUNT(*) FROM n_items ".$search."");
$get_total_rows = mysqli_fetch_array($results); //total records

//break total records into pages
$pages = ceil($get_total_rows[0]/$item_per_page);	


//Limit our results within a specified range. 
$results = mysqli_query($conn, "SELECT * FROM n_items ".$search." LIMIT $position, $item_per_page") or die(mysqli_error($conn));


			echo '<div class="pagination"></div>	<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>
						<th>kods</th>
						<th>mērvienība</th>
						<th>svītrkods</th>
						<th>nosaukums</th>
						<th>darbība</th>
					</tr></thead><tbody>';
			while($row = mysqli_fetch_array($results)){
				?>
				<tr class="classlistedit" onclick="newDoc('<?=$row['code'];?>')">
				<?php
				echo '	

							<td>'.$row['code'].'
							<td>'.$row['unitOfMeasurement'].'
							<td>'.$row['barCode'].'
							<td>'.$row['name1'].'
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
			
			echo '</tbody></table></div></div>';

?>
<script type="text/javascript">
function getSearch(value) {
    $.post("fetch_pages.php", {name:value},function(data){
        $("#results").html(data);
    }); 

}
    $(".pagination").bootpag({
       total: <?php echo $pages; ?>, // total number of pages
       page: 1, //initial page
       maxVisible: 5 //maximum visible links
    }).on("page", function(e, num){
        e.preventDefault();
        $("#results").prepend('<div class="loading-indication"><i class="glyphicon glyphicon-refresh"></i> gaidiet...</div>');
        $("#results").load("fetch_pages.php", {'page':num});
    });	
</script>