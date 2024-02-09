<?php
error_reporting(E_ALL ^ E_NOTICE);
require('lock.php');

$page_file="prepare";


require('inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}
$page_file="prepare";
if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}
$row = mysqli_fetch_assoc($result);
$p_view=$row['p_view'];
$p_edit=$row['p_edit'];

if($p_view!='on'){
		header("Location: welcome"); 
		die(0);		
}


$page_header=$row['page_header'];
$page_icon=$row['page_icon'];
$page_table=$row['page_table'];
mysqli_close($conn);

include('functions/base.php');
include('header.php');

if(!empty($_GET['page'])) {$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);if(false === $page) {$page = 1;}}else{$page = 1;}  //IEGŪSTAM LAPAS NUMURU

if($page){$glpage = '?page='.$page;}else{$glpage = null;}

if (isset($_GET['date'])){$date = htmlentities($_GET['date'], ENT_QUOTES, "UTF-8");}

if (isset($_GET['from'])){$from = htmlentities($_GET['from'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['to'])){$to = htmlentities($_GET['to'], ENT_QUOTES, "UTF-8");}
?>

<style>
.col-centered{
    float: none;
    margin: 0 auto;
}
</style> 

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/prepare/prepare.php<?=$glpage;?>');
    });
})

$(document).ready(function(){
    $('.classlistadd').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/prepare/add.php<?=$glpage;?>');
    });
})

$(document).ready(function(){
    $('.archive').click(function(){
		$("#pleaseWait").toggle();
        $('#contenthere').load('/pages/prepare/prepare.php?view=archive');
    });
})
</script>

<script>
function openDoc(val) {

    var val = '&report_id='+val;
	
	$('#contenthere').load('/pages/prepare/prepare.php?view=approve'+val);

}

function editDoc(val) {

    var val = '&report_id='+val;
	
	$('#contenthere').load('/pages/prepare/prepare.php?view=edit'+val);

}

function openArc(val) {

    var val = '&arcId='+val;
	
	$('#contenthere').load('/pages/prepare/prepare.php?view=archive'+val);

}
</script>


<script>
function dateFilter() {
    
}
</script>

<script type="text/javascript">

function getStates(value) {
    var search = $.post("/pages/prepare/search.php?view=prepare", {name:value},function(data){
        $("#results").html(data);		
    });
	if(search){
		$('#showWait').html('<i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i>&nbsp;&nbsp;');
	}
    search.done(function( data ) {
		$('#showWait').html('');
    });	
}
</script>

<div class="container-fluid">
	<div class="row">

		<div class="col-lg-10 col-centered">
			<div class="panel panel-default">
				<div class="panel-body"> 
                   <div id="contenthere">	
					<?php

                    echo '<div class="page-header" style="margin-top: -5px;">
                                                
                    <div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
                        <a class="btn btn-default active classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
                        <a class="btn btn-default archive" ><i class="glyphicon glyphicon-time" style="color: #00B5AD" title="arhīvs"></i></a>
                        <a class="btn btn-default classlistadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="veidot"></i></a>
                    </div>';

                    echo '</div>';

                    echo '<p style="display:inline-block;">noliktavas aktivitāšu atskaite</p>';
					
					
					echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div>';  					

			echo '<div id="results">';
					
                    require('inc/s.php');
                    $query = mysqli_query($conn, "
                   
                            (
                                SELECT 'a' as Source, woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to, nc.Name,
								u.full_name, woi.createdDate, SUM(amount) AS total
                                
                                FROM web_objects_invoices AS woi
                                
                                LEFT JOIN n_customers AS nc
                                ON woi.customer_id=nc.Code

								LEFT JOIN user AS u
								ON woi.createdBy=u.id								
								
                                WHERE woi.service!='KOMENTĀRS' 
                                GROUP BY woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to
                            )
                            UNION ALL
                            (
                                SELECT 'b' as Source, woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to, nc.Name,
								u.full_name, woi.createdDate, SUM(amount) AS total
                                
                                FROM web_objects_invoices_accepted AS woi
                                
                                LEFT JOIN n_customers AS nc
                                ON woi.customer_id=nc.Code
                                
								LEFT JOIN user AS u
								ON woi.createdBy=u.id																
								
                                WHERE woi.service!='KOMENTĀRS' 
                                GROUP BY woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to
                            ) 
                            
                            ") or die(mysqli_error($conn));
                    if(mysqli_num_rows($query)>0){

                    echo '
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>līgums</th>
                                <th>klienta kods - nosaukums</th>';

                                echo '
                                <th>datums no</th>
                                <th>datums līdz</th>

								<th>izveidoja</th>
								<th>izveidošanas datums</th>

								<th>kopā, EUR</th>								
                                ';

                            echo '    
                            </tr>
                        <thead>
                        <tbody>
                        ';

                    }    
                    $a = $b = $c = $d = $onclick = null;       
                    while($row = mysqli_fetch_array($query)){

                        $rep = '"'.trim($row['report_id']).'"';
                        if($row['Source']=='a'){
                            $onclick = 'onclick=\'editDoc('.$rep.')\'';
                        }
                        if($row['Source']=='b'){
                            $onclick = ' style="background-color: lightgreen;" onclick=\'openDoc('.$rep.')\'';
                        }                        
                        echo '<tr '.$onclick.'>';

                        echo '  <td>'.$row['report_id'].'</td>
                                <td>'.$row['agreement_id'].' </td>
                                <td>'.$row['customer_id'].' - '.$row['Name'].'</td>';

                                echo '
                                <td>'.date('d.m.Y', strtotime($row['period_from'])).'</td>
                                <td>'.date('d.m.Y', strtotime($row['period_to'])).'</td>
								
                                <td>'.$row['full_name'].'</td>
                                <td>'.date('d.m.Y H:i:s', strtotime($row['createdDate'])).'</td>
								<td>'.number_format((float)$row['total'], 2, '.', '').'</td>								
                            </tr>
                        ';
                    }
                        echo '
                        </tbody>
                    </table>                   
                    ';
					?>

					<div id="pleaseWait" style="display: none;">
						<h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
					</div>

					</div>
					
					
					</div>




					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<?php include_once("datepicker.php"); ?>
<?php include("footer.php"); ?>