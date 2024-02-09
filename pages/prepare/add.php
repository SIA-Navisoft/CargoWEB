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

$view = null;
if (isset($_GET['view'])){$view = htmlentities($_GET['view'], ENT_QUOTES, "UTF-8");}


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

<script>	
$(document).ready(function () {
    $('#generate_form').on('submit', function(e) {
        e.preventDefault();

        var dFrom = $('#dFrom').val();
        var dTo = $('#dTo').val();
        var clientCode = $('#clientCode').val();
        var agreements = $('#agreements').val();

        var data = 'from=' + dFrom + '&to=' + dTo + '&clientCode=' + clientCode + '&agreements=' + agreements;

        var update_div = $('#dataForm');

        $.ajax({
            type: 'GET',
            url: '/pages/prepare/return_form.php',
            data: data,   
            success:function(html){
            update_div.html(html);
            }
        });
       
    });
});

</script>
	
<?php



    echo '<div class="page-header" style="margin-top: -5px;">
                                
    <div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
        <a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a>
        <a class="btn btn-default archive"><i class="glyphicon glyphicon-time" style="color: #00B5AD" title="arhīvs"></i></a>
        <a class="btn btn-default active classlistadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD" title="veidot"></i></a>
    </div>';

    echo '</div>';

    echo '<p style="display:inline-block;">jauna noliktavas aktivitāšu atskaite</p>';



    echo '
        <div class="form-row">';

?>

<script>
$('#clientCode').on('change', function() {
		var typez = $('#clientCode option:selected').attr('data-cnid');

		$( "#secondOption" ).load( "/pages/prepare/change.php?select="+typez+"" );
		
});
</script>

<script>
    $(document).ready(function () {

        $('#generate').on('click', function() {

            var dFrom = $('#dFrom').val();
            var dTo = $('#dTo').val();
            var clientCode = $('#clientCode').val();
            var agreements = $('#agreements').val();
            var referenceType = $('#referenceType').val();
            $("#pleaseWait").toggle();
            $('#dataForm').load('/pages/prepare/return_form.php?from=' + dFrom + '&to=' + dTo + '&clientCode=' + clientCode + '&agreements=' + agreements + '&referenceType=' + referenceType);

        });

    });    
</script>

<?

            echo '
            <div class="form-group col-md-2">
                <label for="clientCode">klienta kods - nosaukums</label>
                <select class="form-control selectpicker btn-group-xs"  name="clientCode" id="clientCode"  data-live-search="true" title="klienta kods - nosaukums"  '.$disabled.'>
                    <option></option>';
                    $selectClient = mysqli_query($conn, "
                        SELECT DISTINCT(c.Code) AS Code, c.Name 
                        FROM n_customers AS c
                        LEFT JOIN agreements AS a
                        ON c.Code=a.customerNr 
                        WHERE (a.dateTo='0000-00-00 00:00:00' OR a.dateTo IS NULL OR a.dateTo>CURDATE()) AND a.status<20 AND a.deleted='0'	          
                    ") or die(mysqli_error($conn));
                    while($rowc = mysqli_fetch_array($selectClient)){
                        echo '<option data-cnid="'.$rowc['Code'].'" value="'.$rowc['Code'].'"';
                        if($row['clientCode']==$rowc['Code']){echo ' selected';}
                        echo '>'.$rowc['Code'].' - '.$rowc['Name'].'</option>';
                    }
                
                echo '
                </select>	
            </div>

        <div id="secondOption">    
            <div class="form-group col-md-2">
            <label for="agreements">līgums</label>
                <select class="form-control selectpicker btn-group-xs"  name="agreements" id="agreements"  data-live-search="true" title="līgums"  '.$disabled.'>
                    <option></option>';
                    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr FROM agreements WHERE (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0'") or die(mysqli_error($conn));
                    while($rowa = mysqli_fetch_array($selectAgreements)){
                        echo '<option data-cna="'.$rowc['contractNr'].'" value="'.$rowa['contractNr'].'"';
                        if($row['agreements']==$rowa['contractNr']){echo ' selected';}
                        echo '>'.$rowa['contractNr'].' - '.$rowa['customerNr'];
                        if($rowa['customerName']){echo '('.$rowa['customerName'].')';}
                    echo '</option>';
                    }
                    echo '
                </select>		
            </div>
        </div>

            <div class="form-group col-md-2">
                <label for="deliveryDate">datums no</label>
                <input type="text" class="form-control dFrom datepicker" id="dFrom" name="from" value="'.$periodFrom.'">
            </div>
 
            <div class="form-group col-md-2">
                <label for="deliveryDate">datums līdz</label>
                <input type="text" class="form-control dTo datepicker" id="dTo" name="to" value="'.$periodTo.'">
            </div>';            
 
			echo '   
				<div class="form-group col-md-2">
				<label for="referenceType">veids</label>
					<select class="form-control selectpicker btn-group-xs"  name="referenceType" id="referenceType"  data-live-search="true" title="veids"  '.$disabled.'>
						<option value="1" selected>viss</option>
						<option value="2">glabāšana</option>
						<option value="3">pakalpojumi</option>
					</select>		
				</div>			
			'; 
            
            echo '
            <div class="form-group col-md-2">
                <br>           
                <button class="btn btn-default btn-xs" id="generate">
                    <i class="glyphicon glyphicon-refresh" style="color: #00B5AD"  title="ģenerēt"></i> Ģenerēt 
                </button>
            </div>';
			
			

            

        echo '
        </div> 
        <div class="clearfix"></div>'; 
                    
echo '<br>
        <div id="dataForm">
            <div id="pleaseWait" style="display: none;">
                <h1 align="center" style="padding-top: 300px;"><i class="glyphicon glyphicon-refresh spin" style="color: #00B5AD"></i></h1>
            </div>        
        </div>';                
?>


<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>


<?php include_once("../../datepicker.php"); ?>