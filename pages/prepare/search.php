<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="prepare";


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
if (isset($_GET['do'])){$do = htmlentities($_GET['do'], ENT_QUOTES, "UTF-8");}



if($page){$glpage = '?page='.$page;}else{$glpage = null;}
if($page){$elpage = '&page='.$page;}else{$elpage = null;}


if($view=='prepare'){
	require('../../inc/s.php');

	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " AND (woi.report_id LIKE '%$s%' || woi.agreement_id LIKE '%$s%' || woi.customer_id LIKE '%$s%' || nc.Name LIKE '%$s%' || u.full_name LIKE '%$s%' || date_format(woi.period_from, '%d.%m.%Y') LIKE '%$s%' || date_format(woi.period_to, '%d.%m.%Y') LIKE '%$s%' || date_format(woi.createdDate, '%d.%m.%Y') LIKE '%$s%')";
	}else{
		$search = "";	
	}
	

                    $query = mysqli_query($conn, "
                   
                            (
                                SELECT 'a' as Source, woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to, nc.Name,
								u.full_name, woi.createdDate, SUM(amount) AS total
                                
                                FROM web_objects_invoices AS woi
                                
                                LEFT JOIN n_customers AS nc
                                ON woi.customer_id=nc.Code

								LEFT JOIN user AS u
								ON woi.createdBy=u.id								
								
                                WHERE woi.service!='KOMENTĀRS' ".$search."
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
								
                                WHERE woi.service!='KOMENTĀRS' ".$search."
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
                                <th>klienta kods - nosaukums</th>
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
						
	

}

if($view=='archive'){
	require('../../inc/s.php');

	if($_POST['name']){
		$s = mysqli_real_escape_string($conn, $_POST['name']);	
		$search = " WHERE (woi.report_id LIKE '%$s%' || woi.agreement_id LIKE '%$s%' || woi.customer_id LIKE '%$s%' || nc.Name LIKE '%$s%' || u.full_name LIKE '%$s%' || date_format(woi.period_from, '%d.%m.%Y') LIKE '%$s%' || date_format(woi.period_to, '%d.%m.%Y') LIKE '%$s%' || date_format(woi.createdDate, '%d.%m.%Y') LIKE '%$s%')";
	}else{
		$search = "";	
	}
	

                     $query = mysqli_query($conn, "
                            SELECT woi.report_id, woi.agreement_id, woi.customer_id, woi.period_from, woi.period_to, nc.Name,
							u.full_name, woi.createdDate, SUM(amount) AS total
                            FROM web_objects_invoices_archived AS woi
                            
                            LEFT JOIN n_customers AS nc
                            ON woi.customer_id=nc.Code 

							LEFT JOIN user AS u
							ON woi.createdBy=u.id
                            ".$search."
                            GROUP BY woi.report_id
                    ") or die(mysqli_error($conn));
                    if(mysqli_num_rows($query)>0){

                    echo '
                    <table class="table table-hover table-responsive">
                        <thead>
                            <tr>
								<th>id</th>
                                <th>līgums</th>
                                <th>klienta kods - nosaukums</th>
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
                                <td>'.$row['customer_id'].' - '.$row['Name'].'</td>
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
	
?>
