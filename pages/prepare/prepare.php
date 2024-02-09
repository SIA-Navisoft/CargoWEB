<?php

error_reporting(E_ALL & ~E_NOTICE);

require('../../lock.php');

$page_file="prepare";



require('../../inc/s.php');
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

$view = $val = $arcId = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['val'])){$val = htmlentities($_GET['val'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['arcId'])){$arcId = htmlentities($_GET['arcId'], ENT_QUOTES, "UTF-8");}


include('../../functions/base.php');
require('../../inc/s.php');


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
function dateFilter() {
    
}
</script>


<script type="text/javascript">

function getStates(value) {
	
	var view = 'prepare';
	<?php if($view=='archive'){ ?>
		var view = 'archive';
	<?php } ?>

    var search = $.post("/pages/prepare/search.php?view="+view, {name:value},function(data){
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
	
					<?php

                    if($view=='archive'){$act_ar = 'active'; $act_lis = ''; $titleText = 'atskaites arhīvs';}else{$act_ar = ''; $act_lis = 'active'; $titleText = 'atskaite';}

                    echo '<div class="page-header" style="margin-top: -5px;">
                                                
                    <div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
                        <a class="btn btn-default classlist '.$act_lis.'"><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
                        <a class="btn btn-default archive '.$act_ar.'"><i class="glyphicon glyphicon-time" style="color: #00B5AD" title="arhīvs"></i></a>
                        <a class="btn btn-default classlistadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="veidot"></i></a>
                    </div>';

                    echo '</div>';

                    echo '<p style="display:inline-block;">noliktavas aktivitāšu '.$titleText.'</p>';
                   
				   if(($view=='archive' && !$arcId) || ($view=='')){
					   echo ' <div style="float: right;"><div id="showWait" style="display: inline-block;"></div><div style="display: inline-block;"><input type="text" id="searchWait" class="form-control input-xs" onkeyup="getStates(this.value)" placeholder="meklēt"></div></div>';
				   }					

                    echo '<br>';

if(!$view){

                    require('../../inc/s.php');
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
                    
                    ");
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
                    $a = $b = $c = $d = null;       
                    while($row = mysqli_fetch_array($query)){
                        
                        $rep = '"'.trim($row['report_id']).'"';
                        if($row['Source']=='a'){
                            $onclick = 'onclick=\'editDoc('.$rep.')\'';
                        }
                        if($row['Source']=='b'){
                            $onclick = ' style="background-color: lightgreen;" onclick=\'openDoc('.$rep.')\'';
                        }                        
                        echo '<tr '.$onclick.'>
                                <td>'.$row['report_id'].'</td>
                                <td>'.$row['agreement_id'].'</td>
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
} 



if($view=='period'){
    echo 'PERIOD '.$val;
}



if($view=='edit'){

    if (isset($_GET['from'])){$from = htmlentities($_GET['from'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['to'])){$to = htmlentities($_GET['to'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}

    if (isset($_GET['report_id'])){$report_id = htmlentities($_GET['report_id'], ENT_QUOTES, "UTF-8");}

    $get_info = mysqli_query($conn, "SELECT agreement_id, customer_id, period_from, period_to FROM web_objects_invoices WHERE report_id='".$report_id."' AND service!='KOMENTĀRS'") or die(mysqli_error($conn));
    $giRow = mysqli_fetch_array($get_info);
    $agreements = $giRow['agreement_id'];
    $clientCode = $giRow['customer_id'];
    
    $from = date('Y-m-d', strtotime($giRow['period_from']));
    $to = date('Y-m-d', strtotime($giRow['period_to']));


?>

<script>
function submitForm(url){
    var data = $("#send_action").serialize();
   
    $.ajax({
        type : 'POST',
        url  : '/pages/prepare/post.php?r='+url,
        data : data,
        beforeSend: function(){
				$('.savebtn').html("gaidiet...");
				$(".savebtn").prop("disabled",true);
			},        
        success :  function(data){
            console.log(data);
            if(url=='delete'){
                $('#contenthere').load('/pages/prepare/prepare.php');
            }else if(url=='comment'){
                $('#contenthere').load('/pages/prepare/prepare.php?view=edit&report_id=<?=$report_id;?>&res=done');
            }else{
                $('#contenthere').load('/pages/prepare/prepare.php?view='+url+'&report_id=<?=$report_id;?>&res=done');
            }
        },
        error: function (jXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
</script>

<?php    

    $selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers WHERE Code='".$clientCode."'") or die(mysqli_error($conn));
    $rowc = mysqli_fetch_array($selectClient);
        
    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">klienta kods - nosaukums</label>
        <input type="text" class="form-control" value="'.$rowc['Code'].' - '.$rowc['Name'].'" disabled>
    </div>';
    
    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE contractNr='".$agreements."'") or die(mysqli_error($conn));
    $rowa = mysqli_fetch_array($selectAgreements);

    $selectSum = mysqli_query($conn, "SELECT SUM(amount) AS total FROM web_objects_invoices WHERE report_id='".$report_id."'") or die(mysqli_error($conn));
    $rows = mysqli_fetch_array($selectSum);		
	
    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">līgums</label>
        <input type="text" class="form-control" value="'.$rowa['contractNr'].' - '.$rowa['customerNr'];
        if($rowa['customerName']){echo ' ('.$rowa['customerName'].')';}
    echo '" disabled>
    </div>    
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums no</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($from)).'" disabled>
    </div>
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums līdz</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($to)).'" disabled>
    </div>
	
    <div class="form-group col-md-2">
        <label for="deliveryDate">kopā, EUR</label>
        <br><b>'.number_format((float)$rows['total'], 2, '.', '').'</b>
    </div>'; 
    echo '<div class="clerarfix"></div><br><br><br><br>';

echo '
<div class="panel panel-default">
    <div class="panel-body">';
    
    echo '<p style="display:inline-block;">dati apstiprināšanai</p><a href="pages/prepare/print?agr='.$agreements.'&cus='.$clientCode.'&from='.$from.'&to='.$to.'&report='.$report_id.'" target="_blank" class="btn btn-default btn-xs" style="float: right;"><i class="glyphicon glyphicon-print"></i> pielikums</a>';
    echo '<div class="clearfix"></div>';

    if($from){
        $dateFrom = date('Y-m-d 00:00:00', strtotime($from));
        $periodFrom = date('d.m.Y', strtotime($from));
    }else{
        $dateFrom = date('Y-m-01 00:00:00');
        $periodFrom = date('d.m.Y');
    }

    if($to){
        $dateTo = date('Y-m-t 23:59:59', strtotime($to));
        $periodTo = date('d.m.Y', strtotime($to));
    }else{
        $dateTo = date('Y-m-t 23:59:59');
        $periodTo = date('d.m.Y');
    }   

    if(!$from){
        $from=date('Y-m-d');
    }
    if(!$to){
        $to=date('Y-m-d');
    }


    $query = mysqli_query($conn, "
                SELECT woi.service, woi.service_code, woi.description, woi.unit, SUM(woi.amount) as amount, SUM(woi.qty) as qty, ile.productNr, ni.name1, ni.name2
                
                FROM web_objects_invoices AS woi
				
				LEFT JOIN item_ledger_entry AS ile
				ON ile.id=woi.ledger_entry
				
				LEFT JOIN n_items AS ni
				ON ni.code=ile.productNr
				
                WHERE woi.report_id='".$report_id."' AND woi.service!='KOMENTĀRS'
				
				GROUP BY  ile.productNr, woi.service_code, woi.unit
                
				
            ") or die(mysqli_error($conn)); 

    if(mysqli_num_rows($query)>0){

        echo '
        <table class="table table-hover table-responsive report" border="1">
            <thead>
                <tr>
                    <th>produkts - nosaukums</th>
                    <th>pakalpojuma nosaukums</th>
                    <th>pakalpojums</th>
 
                    <th>daudzums</th>  
                    <th>mērvienība</th>                                     
                    <th>kopā EUR</th>';


                echo '    
                </tr>
            <thead>
            <tbody>';

			$tQty=$tAmount=null;
			while($roi = mysqli_fetch_array($query)){
				
				echo '<tr>
						<td>'.$roi['productNr'].' '.$roi['name1'].' '.$roi['name2'].'</td>
						<td>'.$roi['service'].'</td>
						<td>'.$roi['service_code'].'</td>
						<td>'.$roi['qty'].'</td>    
						<td>'.$roi['unit'].'</td>                              
						<td>'.number_format((float)$roi['amount'], 2, '.', '').'</td>				
					</tr>';
					$tQty+=$roi['qty'];
					$tAmount+=$roi['amount'];
				
			}
			if($tQty || $tAmount){
				echo '<tr><td colspan="3"></td><td><b>'.$tQty.'</b></td><td></td><td><b>'.number_format((float)$tAmount, 2, '.', '').'</b></td></tr>';
			}

            echo '
            </tbody>
        </table>';

	}	
	
	
    $query = mysqli_query($conn, "
                SELECT id, service, service_code, description, unit, sales_price, amount, qty, createdBy, createdDate,
                
                (SELECT productNr FROM item_ledger_entry WHERE id=ledger_entry) AS productNr
                
                FROM web_objects_invoices 
                WHERE report_id='".$report_id."' 
               
                ORDER BY FIELD(service, 'KOMENTĀRS') ASC
            "); 

    if(mysqli_num_rows($query)>0){
        echo '
        <div class="table-responsive">
<form id="send_action"> ';



        echo '
        <table class="table table-hover table-responsive">
            <thead>
                <tr>
                    <th>produkts (nosaukums)</th>
                    <th>pakalpojuma nosaukums</th>
                    <th>pakalpojums</th>
                   
                    <th>tarifs EUR</th>  
                    <th>daudzums</th>
					<th>mērvienība</th>					
                    <th>kopā EUR</th>
                    
                    <th>izveidoja</th>
                    <th>izveidošanas datums</th>    
                </tr>
            <thead>
            <tbody>';
    }
    $i=0;
	$vmoney = $money = $gvmoney = $gmoney = null;
	$total_money = null;		
	$table1=$table2=$table3=null;	
    while($row = mysqli_fetch_array($query)){

        if($row['service']!='KOMENTĀRS'){

            $getNames = mysqli_query($conn, "SELECT name1, name2 FROM n_items WHERE code='".$row['productNr']."'");
            $gNrow = mysqli_fetch_array($getNames);

            $table1 .= '
                <tr> 
                    <td>'.$row['productNr'].'<br>'.$gNrow['name1'].' '.$gNrow['name2'].'</td>
                    <td>'.$row['service'].'</td>
                    <td>'.$row['service_code'].'</td>
                    <td>'.$row['sales_price'].'</td> 
                    <td>'.$row['qty'].'</td>    
                    <td>'.$row['unit'].'</td>                              
                    <td>'.$row['amount'].'</td>
                    
                    <td>'.returnMeWho($row['createdBy']).'</td>
                    <td>'.$row['createdDate'].'</td>    
                </tr>';
				
			if($row['service_code']=='LDST0'){
				$money += $row['amount'];
				$vmoney += $row['qty'];	
			}else{
				$gmoney += $row['amount'];
				$gvmoney += $row['qty'];	
			}				
				
			$total_money += $row['amount'];	
        }
        if($row['service']=='KOMENTĀRS'){

              $table2  .= '<tr>
                        <td colspan="2">
                            <input type="hidden" name="line_id[]" value="'.$row['id'].'">
                            <input type="checkbox" name="record['.$i.']"> (ja atzīmēts, tad pie labošanas dzēš)
                        </td>
                        <td>KOMENTĀRS</td>
                        <td colspan="6">
                            <div class="form-group">
                                <input type="text" maxlength="80" class="form-control" name="comment[]" value="'.$row['description'].'">
                            </div>
                        </td>
                    </tr>';
                    $i++;
        }
                
    }
	
	if($total_money>0){

		$table3 .= '<tr>
				  <td colspan="5">
				  </td>
				  <td colspan="1">kopā, EUR</td>
				  <td colspan="3">
					  <b>'.$total_money.'</b>
				  </td>
			  </tr>';
   }		
	
	echo $table1.$table3.$table2;	
   
    echo '
            <input type="hidden" name="result" value="'.$i.'">
            <input type="hidden" name="report_id" value="'.$report_id.'">
            <input type="hidden" name="pFrom" value="'.$from.'">
            <input type="hidden" name="pTo" value="'.$to.'">
            <input type="hidden" name="agreements" value="'.$agreements.'">
            <input type="hidden" name="clientCode" value="'.$clientCode.'">
            </tbody>
        </table>';
		
		echo '
        <input type="button" class="btn btn-default btn-xs add-row" value="pievienot komentāru" onclick="submitForm(\'comment\')">
        <br><br>

        
        <button type="submit" class="btn btn-default btn-xs savebtn" id="savebtn" onclick="submitForm(\'edit\')">
            labot komentāru
        </button>'; 

        if($p_edit=='on'){
        echo '
        <button type="submit" class="btn btn-default btn-xs savebtn" id="savebtn" onclick="submitForm(\'approve\')">
            apstiprināt
        </button>';
        }
        echo '
        <button type="submit" class="btn btn-default btn-xs savebtn" id="savebtn" onclick="submitForm(\'delete\')">
            dzēst
        </button>  

</form>        
        

        </div>
    </div>     
</div>';  
    
}


if($view=='approve'){

    if (isset($_GET['from'])){$from = htmlentities($_GET['from'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['to'])){$to = htmlentities($_GET['to'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['clientCode'])){$clientCode = htmlentities($_GET['clientCode'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['agreements'])){$agreements = htmlentities($_GET['agreements'], ENT_QUOTES, "UTF-8");}

    if (isset($_GET['report_id'])){$report_id = htmlentities($_GET['report_id'], ENT_QUOTES, "UTF-8");}

    $get_info = mysqli_query($conn, "SELECT agreement_id, customer_id, period_from, period_to FROM web_objects_invoices_accepted WHERE report_id='".$report_id."' AND service!='KOMENTĀRS'");
    $giRow = mysqli_fetch_array($get_info);
    $agreements = $giRow['agreement_id'];
    $clientCode = $giRow['customer_id'];

    $from = date('Y-m-d', strtotime($giRow['period_from']));
    $to = date('Y-m-d', strtotime($giRow['period_to']));    
?>

<script>
function submitForm(url){
    var data = $("#recall_action").serialize();
    
    $.ajax({
        type : 'POST',
        url  : '/pages/prepare/post.php?r='+url,
        data : data,
        beforeSend: function(){
				$('.savebtn').html("gaidiet...");
				$(".savebtn").prop("disabled",true);
			},        
        success :  function(data){
            console.log(data);
            $('#contenthere').load('/pages/prepare/prepare.php?view=edit&report_id=<?=$report_id;?>&res=done');
        },
        error: function (jXHR, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
</script>

<?php    

    $selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers WHERE Code='".$clientCode."'") or die(mysqli_error($conn));
    $rowc = mysqli_fetch_array($selectClient);
    
    $selectSum = mysqli_query($conn, "SELECT SUM(amount) AS total FROM web_objects_invoices_accepted WHERE report_id='".$report_id."'") or die(mysqli_error($conn));
    $rows = mysqli_fetch_array($selectSum);	
    
    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">klienta kods - nosaukums</label>
        <input type="text" class="form-control" value="'.$rowc['Code'].' - '.$rowc['Name'].'" disabled>
    </div>';
    
    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE contractNr='".$agreements."'") or die(mysqli_error($conn));
    $rowa = mysqli_fetch_array($selectAgreements);

    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">līgums</label>
        <input type="text" class="form-control" value="'.$rowa['contractNr'].' - '.$rowa['customerNr'];
        if($rowa['customerName']){echo ' ('.$rowa['customerName'].')';}
    echo '" disabled>
    </div>    
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums no</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($from)).'" disabled>
    </div>
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums līdz</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($to)).'" disabled>
    </div>

    <div class="form-group col-md-2">
        <label for="deliveryDate">kopā, EUR</label>
        <br><b>'.number_format((float)$rows['total'], 2, '.', '').'</b>
    </div>';
	
    echo '<div class="clerarfix"></div><br><br><br><br>';

echo '
<div class="panel panel-default">
    <div class="panel-body">';
    
    echo '<p style="display:inline-block;">apstiprinātie dati</p><a href="pages/prepare/print?agr='.$agreements.'&cus='.$clientCode.'&from='.$from.'&to='.$to.'&report='.$report_id.'" target="_blank" class="btn btn-default btn-xs" style="float: right;"><i class="glyphicon glyphicon-print"></i> pielikums</a>';
    echo '<div class="clearfix"></div>';

    if($from){
        $dateFrom = date('Y-m-d 00:00:00', strtotime($from));
        $periodFrom = date('d.m.Y', strtotime($from));
    }else{
        $dateFrom = date('Y-m-01 00:00:00');
        $periodFrom = date('d.m.Y');
    }

    if($to){
        $dateTo = date('Y-m-t 23:59:59', strtotime($to));
        $periodTo = date('d.m.Y', strtotime($to));
    }else{
        $dateTo = date('Y-m-t 23:59:59');
        $periodTo = date('d.m.Y');
    }   

    if(!$from){
        $from=date('Y-m-d');
    }
    if(!$to){
        $to=date('Y-m-d');
    }


	
    $query = mysqli_query($conn, "
                SELECT woi.service, woi.service_code, woi.description, woi.unit, SUM(woi.amount) as amount, SUM(woi.qty) as qty, ile.productNr, ni.name1, ni.name2
                
                FROM web_objects_invoices_accepted AS woi
				
				LEFT JOIN item_ledger_entry AS ile
				ON ile.id=woi.ledger_entry
				
				LEFT JOIN n_items AS ni
				ON ni.code=ile.productNr
				
                WHERE woi.report_id='".$report_id."' AND woi.service!='KOMENTĀRS'
				
				GROUP BY  ile.productNr, woi.service_code, woi.unit
                
				
            ") or die(mysqli_error($conn)); 

    if(mysqli_num_rows($query)>0){

        echo '
        <table class="table table-hover table-responsive report" border="1">
            <thead>
                <tr>
                    <th>produkts (nosaukums)</th>
                    <th>pakalpojuma nosaukums</th>
                    <th>pakalpojums</th>
 
                    <th>daudzums</th>  
                    <th>mērvienība</th>                                     
                    <th>kopā EUR</th>';


                echo '    
                </tr>
            <thead>
            <tbody>';

			$tQty=$tAmount=null;
			while($roi = mysqli_fetch_array($query)){
				
				echo '<tr>
						<td>'.$roi['productNr'].' '.$roi['name1'].' '.$roi['name2'].'</td>
						<td>'.$roi['service'].'</td>
						<td>'.$roi['service_code'].'</td>
						<td>'.$roi['qty'].'</td>    
						<td>'.$roi['unit'].'</td>                              
						<td>'.number_format((float)$roi['amount'], 2, '.', '').'</td>				
					</tr>';
					$tQty+=$roi['qty'];
					$tAmount+=$roi['amount'];
				
			}
			if($tQty || $tAmount){
				echo '<tr><td colspan="3"></td><td><b>'.$tQty.'</b></td><td></td><td><b>'.number_format((float)$tAmount, 2, '.', '').'</b></td></tr>';
			}

            echo '
            </tbody>
        </table>';

	}		
	
	
	
    $query = mysqli_query($conn, "
                SELECT id, service, service_code, description, unit, sales_price, amount, qty, createdBy, createdDate,
                
                (SELECT productNr FROM item_ledger_entry WHERE id=ledger_entry) AS productNr
                
                FROM web_objects_invoices_accepted 
                WHERE report_id='".$report_id."' 
            
                ORDER BY FIELD(service, 'KOMENTĀRS') ASC
            "); 

    if(mysqli_num_rows($query)>0){
        echo '
        <div class="table-responsive">
<form id="recall_action"> ';

        echo '
        <table class="table table-hover table-responsive">
            <thead>
                <tr>
                    <th>produkts (nosaukums)</th>
                    <th>pakalpojuma nosaukums</th>
                    <th>pakalpojums</th>
                    
                    <th>tarifs EUR</th>  
                    <th>daudzums</th>  
                    <th>mērvienība</th>                                    
                    <th>kopā EUR</th>
                    
                    <th>izveidoja</th>
                    <th>izveidošanas datums</th>    
                </tr>
            <thead>
            <tbody>';
    }
    $i=0;
	$total_money = null;		
	$table1=$table2=$table3=null;	
    while($row = mysqli_fetch_array($query)){


                


                if($row['service']!='KOMENTĀRS'){
                    $getNames = mysqli_query($conn, "SELECT name1, name2 FROM n_items WHERE code='".$row['productNr']."'");
                    $gNrow = mysqli_fetch_array($getNames);
                $table1 .= '
                    <tr><input type="hidden" name="line_id[]" value="'.$row['id'].'"> 
                        <td>'.$row['productNr'].'<br>'.$gNrow['name1'].' '.$gNrow['name2'].'</td>
                        <td>'.$row['service'].'</td>
                        <td>'.$row['service_code'].'</td>
                        
                        
                        <td>'.$row['sales_price'].'</td> 
                        <td>'.$row['qty'].'</td>   
                        <td>'.$row['unit'].'</td>                               
                        <td>'.$row['amount'].'</td>
                        <td>'.returnMeWho($row['createdBy']).'</td>
                        <td>'.$row['createdDate'].'</td>    
                    </tr>';
					$total_money += $row['amount'];
                }
                if($row['service']=='KOMENTĀRS'){

                    $table2 .= '<tr><input type="hidden" name="line_id[]" value="'.$row['id'].'">
                              <td>
                              </td>
                              <td></td>
                              <td>KOMENTĀRS</td>
                              <td colspan="6">
                                  <div class="form-group">
                                      <input type="text" class="form-control" value="'.$row['description'].'" disabled>
                                  </div>
                              </td>
                          </tr>';
              }               
        $i++;        
    }
	
	if($total_money>0){

		$table3 .= '<tr>
				  <td colspan="5">
				  </td>
				  <td colspan="1">kopā, EUR</td>
				  <td colspan="3">
					  <b>'.$total_money.'</b>
				  </td>
			  </tr>';
   }		
	
	echo $table1.$table3.$table2;	
	
    echo '  <input type="hidden" name="report_id" value="'.$report_id.'">
            <input type="hidden" name="result" value="'.$i.'">
            </tbody>
        </table>

        <div class="clearfix"></div>';

        if($p_edit=='on'){
        echo '
        <button type="submit" class="btn btn-default btn-xs savebtn" id="savebtn" onclick="submitForm(\'recall\')">
            atsaukt
        </button>';
        }
        
    echo '    
</form>        
        

        </div>
    </div>     
</div>';

}


if($view=='archive' && !$arcId){


echo '<div id="results">';

                    require('../../inc/s.php');
                    $query = mysqli_query($conn, "
                            SELECT woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to, nc.Name,
							u.full_name, woi.createdDate, SUM(amount) AS total
                            FROM web_objects_invoices_archived AS woi
                            
                            LEFT JOIN n_customers AS nc
                            ON woi.customer_id=nc.Code 

							LEFT JOIN user AS u
							ON woi.createdBy=u.id
                            
                            GROUP BY woi.report_id
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
     
                    while($row = mysqli_fetch_array($query)){
                        $rep = '"'.trim($row['report_id']).'"';
                        echo '<tr onclick=\'openArc('.$rep.')\'>

                                <td>'.$row['report_id'].'</td>
								<td>'.$row['agreement_id'].'</td>
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

echo '</div>';					

}


if($view=='archive' && $arcId){

    $selectArch = mysqli_query($conn, "SELECT agreement_id, customer_id, period_from, period_to FROM web_objects_invoices_archived WHERE report_id='".$arcId."' LIMIT 1") or die(mysqli_error($conn));
    $rowar = mysqli_fetch_array($selectArch);    

    $selectClient = mysqli_query($conn, "SELECT DISTINCT(Code) AS Code, Name FROM n_customers WHERE Code='".$rowar['customer_id']."'") or die(mysqli_error($conn));
    $rowc = mysqli_fetch_array($selectClient);
        
    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">klienta kods - nosaukums</label>
        <input type="text" class="form-control" value="'.$rowc['Code'].' - '.$rowc['Name'].'" disabled>
    </div>';
    
    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE contractNr='".$rowar['agreement_id']."'") or die(mysqli_error($conn));
    $rowa = mysqli_fetch_array($selectAgreements);
	
    $selectSum = mysqli_query($conn, "SELECT SUM(amount) AS total FROM web_objects_invoices_archived WHERE report_id='".$arcId."'") or die(mysqli_error($conn));
    $rows = mysqli_fetch_array($selectSum);	

    $from = date('Y-m-d', strtotime($rowar['period_from']));
    $to = date('Y-m-d', strtotime($rowar['period_to']));

    echo '
    <div class="form-group col-md-3">
        <label for="deliveryDate">līgums</label>
        <input type="text" class="form-control" value="'.$rowa['contractNr'].' - '.$rowa['customerNr'];
        if($rowa['customerName']){echo ' ('.$rowa['customerName'].')';}
    echo '" disabled>
    </div>    
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums no</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($from)).'" disabled>
    </div>
    
    <div class="form-group col-md-2">
        <label for="deliveryDate">datums līdz</label>
        <input type="text" class="form-control" value="'.date('d.m.Y', strtotime($to)).'" disabled>
    </div>
	
    <div class="form-group col-md-2">
        <label for="deliveryDate">kopā, EUR</label>
        <br><b>'.number_format((float)$rows['total'], 2, '.', '').'</b>
    </div>';  

    echo '<div class="clerarfix"></div><br><br><br><br>';

    echo '
    <div class="panel panel-default">
        <div class="panel-body">';
        
        echo '<p style="display:inline-block;">arhīva dati</p><a href="pages/prepare/print?agr='.$rowar['agreement_id'].'&cus='.$rowar['customer_id'].'&from='.$from.'&to='.$to.'&report='.$arcId.'" target="_blank" class="btn btn-default btn-xs" style="float: right;"><i class="glyphicon glyphicon-print"></i> pielikums</a>';
        echo '<div class="clearfix"></div>';
 
 
 
    $query = mysqli_query($conn, "
                SELECT woi.service, woi.service_code, woi.description, woi.unit, SUM(woi.amount) as amount, SUM(woi.qty) as qty, ile.productNr, ni.name1, ni.name2
                
                FROM web_objects_invoices_archived AS woi
				
				LEFT JOIN item_ledger_entry AS ile
				ON ile.id=woi.ledger_entry
				
				LEFT JOIN n_items AS ni
				ON ni.code=ile.productNr
				
                WHERE woi.report_id='".$arcId."' AND woi.service!='KOMENTĀRS'
				
				GROUP BY  ile.productNr, woi.service_code, woi.unit
                
				
            ") or die(mysqli_error($conn)); 

    if(mysqli_num_rows($query)>0){

        echo '
        <table class="table table-hover table-responsive report" border="1">
            <thead>
                <tr>
                    <th>produkts (nosaukums)</th>
                    <th>pakalpojuma nosaukums</th>
                    <th>pakalpojums</th>
 
                    <th>daudzums</th>  
                    <th>mērvienība</th>                                     
                    <th>kopā EUR</th>';


                echo '    
                </tr>
            <thead>
            <tbody>';

			$tQty=$tAmount=null;
			while($roi = mysqli_fetch_array($query)){
				
				echo '<tr>
						<td>'.$roi['productNr'].' '.$roi['name1'].' '.$roi['name2'].'</td>
						<td>'.$roi['service'].'</td>
						<td>'.$roi['service_code'].'</td>
						<td>'.$roi['qty'].'</td>    
						<td>'.$roi['unit'].'</td>                              
						<td>'.number_format((float)$roi['amount'], 2, '.', '').'</td>				
					</tr>';
					$tQty+=$roi['qty'];
					$tAmount+=$roi['amount'];
				
			}
			if($tQty || $tAmount){
				echo '<tr><td colspan="3"></td><td><b>'.$tQty.'</b></td><td></td><td><b>'.number_format((float)$tAmount, 2, '.', '').'</b></td></tr>';
			}

            echo '
            </tbody>
        </table>';

	}		 
 
 
 
 
        $query = mysqli_query($conn, "
                    SELECT id, service, service_code, description, unit, sales_price, amount, qty, createdBy, createdDate,
                    
                    (SELECT productNr FROM item_ledger_entry WHERE id=ledger_entry) AS productNr
                    
                    FROM web_objects_invoices_archived 
                    WHERE report_id='".$arcId."' 
                
                    ORDER BY FIELD(service, 'KOMENTĀRS') ASC
                "); 
    
        if(mysqli_num_rows($query)>0){
            echo '
            <div class="table-responsive">

            <table class="table table-hover table-responsive">
                <thead>
                    <tr>
                        <th>produkts (nosaukums)</th>
                        <th>pakalpojuma nosaukums</th>
                        <th>pakalpojums</th>

                        
                        <th>tarifs EUR</th>  
                        <th>daudzums</th>  
                        <th>mērvienība</th>                                    
                        <th>kopā EUR</th>

                       
                        
                        <th>izveidoja</th>
                        <th>izveidošanas datums</th>    
                    </tr>
                <thead>
                <tbody>';
        }
		
        $total_money = null;		
		$table1=$table2=$table3=null;
        while($row = mysqli_fetch_array($query)){
    
    
                    if($row['service']!='KOMENTĀRS'){
                        $getNames = mysqli_query($conn, "SELECT name1, name2 FROM n_items WHERE code='".$row['productNr']."'");
                        $gNrow = mysqli_fetch_array($getNames);
						
                    $table1 .= '
                        <tr> 
                            <td>'.$row['productNr'].'<br>'.$gNrow['name1'].' '.$gNrow['name2'].'</td>
                            <td>'.$row['service'].'</td>
                            <td>'.$row['service_code'].'</td>
                            
                            
                            <td>'.$row['sales_price'].'</td> 
                            <td>'.$row['qty'].'</td>   
                            <td>'.$row['unit'].'</td>                               
                            <td>'.$row['amount'].'</td>
                            <td>'.returnMeWho($row['createdBy']).'</td>
                            <td>'.$row['createdDate'].'</td>    
                        </tr>';
						
						$total_money += $row['amount'];
					
					
						
						
                    }
					
 					
					
					
                    if($row['service']=='KOMENTĀRS'){
    
                        $table2 .= '<tr>
                                  <td>
                                  </td>
                                  <td></td>
                                  <td>KOMENTĀRS</td>
                                  <td colspan="6">
                                      <div class="form-group">
                                          <input type="text" class="form-control" value="'.$row['description'].'" disabled>
                                      </div>
                                  </td>
                              </tr>';
                  }               
			 
        }

		if($total_money>0){

			$table3 .= '<tr>
					  <td colspan="5">
					  </td>
					  <td colspan="1">kopā, EUR</td>
					  <td colspan="3">
						  <b>'.$total_money.'</b>
					  </td>
				  </tr>';
	   }		
		
		echo $table1.$table3.$table2;
		
		
		
		
        echo ' 
                </tbody>
            </table>
      
            
    
            </div>
        </div>     
    </div>';    

}
?>


<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<div id="pleaseWait" style="display: none;">
    <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
</div>
<?php include_once("../../datepicker.php"); ?>