<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="release";


require('../../inc/s.php');
$result = mysqli_query($conn,"SELECT u_rights.p_view, u_rights.p_edit, s_pages.page_header, s_pages.page_icon, s_pages.page_table
								FROM setup_pages AS s_pages
								JOIN user_rights AS u_rights
								ON u_rights.page_name = s_pages.page_file
								WHERE u_rights.user_id = '".$myid."' AND u_rights.page_name='".$page_file."'");
if (!$result){die("Attention! Query to show fields failed.");}

if (mysqli_num_rows($result)<1){header("Location: welcome");die(0);}

echo '<script src="../../js/cbx.js"></script>';

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

if (isset($_GET['client'])){$client = htmlentities($_GET['client'], ENT_QUOTES, "UTF-8");}


require('../../inc/s.php');
?>
<script>
function selCli(val) {
	var val = val.value;
	$('#selected').load('/pages/release/selected.php?cli='+val+'');
}

function selAgr(val) {
	var val = val.value;
	$('#selected').load('/pages/release/selected.php?agr='+val+'');
}

function selClient(val) {

var val = val.value;
var val = encodeURI(val);

var agreements = selAgreement(val);
var agr = '';
if (agreements !== undefined){
	var agr = '&agreements='+agreements;
}



$('#selected').load('/pages/release/selected.php?clientCode='+val+''+agr);
$( "#resourceOption" ).load( "/pages/release/change.php?view=sort_resource&select="+val+"" );

$( "#numberOption" ).load( "/pages/release/change.php?view=sort_number&select="+val+"" );
}

function selAgreement(val) {

var val = val.value;
var val = encodeURI(val);

var clientCode = $('#clientCode').val();

var cli = '';
if (clientCode !== undefined){
	var cli = '&clientCode='+clientCode;
	var select = '&select='+clientCode;
}   

$('#selected').load('/pages/release/selected.php?agreements='+val+''+cli);
$( "#resourceOption" ).load( "/pages/release/change.php?view=sort_resource&resource="+val+"" );
$( "#numberOption" ).load( "/pages/release/change.php?view=sort_number"+select+"&resource="+val+"" );

}

function selNumber(val){
	var val = val.value;
var val = encodeURI(val);

var clientCode = $('#clientCode').val();

var cli = '';
if (clientCode !== undefined){
	var cli = '&clientCode='+clientCode;
	var select = '&select='+clientCode;
}  

var agreements = $('#agreements').val();

var agr = '';
if (agreements !== undefined){
	var agr = '&agreements='+agreements;
}  

$('#selected').load('/pages/release/selected.php?num='+val+''+cli+''+agr);

}
</script>

<script>
$(document).ready(function(){
    $('.classlist').click(function(){
        $('#contenthere').load('/pages/release/release.php<?=$glpage;?>');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classadd').click(function(){
        $('#contenthere').load('/pages/release/add.php');
    });
})
</script>

<script>
$(document).ready(function(){
    $('.classhistory').click(function(){
        $('#contenthere').load('/pages/release/release.php?view=history');
    });
})
</script>
<?php $idR=findLastRow("issuance_doc"); ?>
	<script>
	$(document).ready(function () {
		$('#issuanceDoc').on('submit', function(e) {			
			e.preventDefault();
		
			var searchWait = $('#searchWait').val();
			var searchWait = encodeURI(searchWait);
			
			$.ajax({
				url : '/pages/release/post.php?r=issuanceDoc',
				type: "POST",
				data: $(this).serializeArray(),
				beforeSend: function(){
					
					$('.actionLine').html("gaidiet...");
					$(".actionLine").prop("disabled",true);

				},			
				success: function (data) {
					var clientCode = $('#clientCode').val();
					var agreements = $('#agreements').val();
					
					$('#contenthere').load('/pages/release/release.php?view=edit&id=<?=$idR+1;?>');
				},
				error: function (jXHR, textStatus, errorThrown) {
					alert(errorThrown);
				}
			});
		});
	});
	</script>

<form id="issuanceDoc">
  <div class="form-row">
  
	<?php
	
	echo '<div class="page-header" style="margin-top: -5px;">
	  
		<div class="btn-group btn-group-xs" role="group" aria-label="Small button group" style="display:inline-block;"> 
			<a class="btn btn-default classlist" ><i class="glyphicon glyphicon-list" style="color: #00B5AD" title="pamatdati"></i></a> 
			<a class="btn btn-default classhistory" ><i class="glyphicon glyphicon-time" style="color: #00B5AD"  title="vēsture"></i></a>
			<a class="btn btn-default active classadd" ><i class="glyphicon glyphicon-plus" style="color: #00B5AD"  title="pievienot"></i></a>
		</div>';
		if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}
		if ($res=="del"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">izdzēsts!</div></div>';}
		if ($res=="error"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';}
		if ($res=="approved"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">nodots!</div></div>';}
		if ($res=="received"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saņemts!</div></div>';}
		if ($res=="changed"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">pārvietots!</div></div>';}
		echo '<div class="pull-right" style="margin-top: -2px; display: none;" id="serror" id="hideMessage"><div class="btn btn-danger">kļūda!</div></div>';
			  
	echo '</div>';	

            echo '
            <div class="form-group col-md-3">
                <label for="clientCode">klienta kods - nosaukums</label>
                <select class="form-control selectpicker btn-group-xs"  name="clientCode" id="clientCode"  data-live-search="true" title="klienta kods - nosaukums" onchange="selClient(this)" '.$disabled.'>
                    ';
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
            <div class="form-group col-md-3">
            <label for="agreements">līgums</label>
                <select class="form-control selectpicker btn-group-xs"  name="agreements" id="agreements"  data-live-search="true" title="līgums" onchange="selAgreement(this)" '.$disabled.'>
					';

                    echo '
                </select>		
            </div>
		</div>

	<div id="numberOption">
		<div class="form-group col-md-3">
			<label for="thatNumber">pieņemšanas akta nr.</label>
			<select class="form-control selectpicker btn-group-xs"  name="thatNumber" id="thatNumber"  data-live-search="true" title="pieņemšanas akta nr." onchange="selNumber(this)" '.$disabled.'>
				';

			
			echo '
			</select>	
		</div>
	</div>

		<div id="resourceOption">
			<div class="form-group col-md-3">	
			<label for="resource">pakalpojums</label>			
				<select class="form-control selectpicker btn-group-xs" id="resource"  data-live-search="true" title="pakalpojums" name="resource" '.$dis.' '.$hide.'>';	
				
				echo '
				</select>
			</div>			
        </div>';
			
	?>
  
  
<script>
	$('#clientCode').on('change', function() {
			var typez = $('#clientCode option:selected').attr('data-cnid');

			$( "#secondOption" ).load( "/pages/release/change.php?view=sort_agreements&select="+typez+"" );
			
	});
</script>  
  
  
  
    <div class="form-group col-md-2">
      <label for="deliveryDate">izdošanas datums</label>
      <input type="text" class="form-control datepicker" name="issueDate" id="deliveryDate" value="<?=date('d.m.Y');?>">
    </div>
    <div class="form-group col-md-2">
      <label for="deliveryDate">faktiskais datums</label>
      <input type="text" class="form-control datepicker" name="actualDate" id="deliveryDate" value="<?=date('d.m.Y');?>">
    </div>	
	
    <div class="form-group col-md-2">
      <label for="deliveryDate">brigāde</label>
      <input type="text" class="form-control" name="brigade" id="deliveryDate" placeholder="brigāde">
    </div>	

    <div class="form-group col-md-3">
      <label>pieteikuma nr.</label>
      <input type="text" class="form-control" name="applicationNo" placeholder="pieteikuma nr.">
    </div>	

    <div class="form-group col-md-3">
      <label>pieteikuma datums</label>
      <input type="text" class="form-control datepicker" name="applicationDate" value="<?=date('d.m.Y');?>">
    </div>		
	<div class="clearfix"></div>
	
	
	
	
    <div class="form-group col-md-2">
      <label for="deliveryDate">datums</label>
      <input type="text" class="form-control datepicker" name="date" id="deliveryDate" value="<?=date('d.m.Y');?>">
    </div>
    <div class="form-group col-md-1">
      <label for="deliveryDate">laiks no</label>
      <input type="text" class="form-control" name="time_from" placeholder="08:00" id="deliveryDate" onblur="validateTime(this)">
    </div>
    <div class="form-group col-md-1">
      <label for="deliveryDate">laiks līdz</label>
      <input type="text" class="form-control" name="time_to" placeholder="17:00" id="deliveryDate" onblur="validateTime(this)">
    </div>	


    <div class="form-group col-md-2">
      <label for="deliveryDate">rūme</label>
      <input type="text" class="form-control" name="place" id="deliveryDate" placeholder="rūme">
    </div>	

<?php
	echo '
			<div class="form-group col-md-3">
			<label for="deliveryType">transporta veids</label>
			  <select class="form-control selectpicker btn-group-xs" name="transport" data-live-search="true" title="transporta veids">';
			  $selectTypeT = mysqli_query($conn, "
			  SELECT transport FROM transport
			  WHERE status=1 ORDER BY transport") or die(mysqli_error($conn));
			  while($rowt = mysqli_fetch_array($selectTypeT)){
				  echo '<option value="'.$rowt['transport'].'">'.$rowt['transport'].'</option>';
			  }
			  
			  echo '
			  </select>	
			</div>';
			?>

    <div class="form-group col-md-3">
      <label for="thisTransport">izs. transporta nr.</label>
      <input type="text" class="form-control" name="thisTransport" id="thisTransport" placeholder="izs. transporta nr.">
    </div>

	<div class="clearfix"></div>
    <div class="form-group col-md-3">
      <label for="transportName">kuģis iekraušanai</label>
      <input type="text" class="form-control" name="transportName" id="transportName" placeholder="kuģis iekraušanai">
    </div>
	

			  
    <div class="form-group col-md-3">
      <label for="manifestNo">manifesta nr.</label>
      <input type="text" class="form-control" name="manifestNo" id="manifestNo" placeholder="manifesta nr.">
    </div>	

    <div class="form-group col-md-3">
      <label for="issuanceActNo">izdošanas akta nr.</label>
      <input type="text" class="form-control" name="issuanceActNo" id="issuanceActNo" placeholder="izdošanas akta nr.">
    </div>
	
    <div class="form-group col-md-3">
      <label for="declarationTypeNo">deklarācijas nr.</label>
      <input type="text" class="form-control" name="declarationTypeNo" id="declarationTypeNo" placeholder="deklarācijas nr.">
    </div>	

	<script>
		$('#receiverCode').on('change', function() {
				var typez = $('#receiverCode option:selected').attr('data-cnid');

				$( "#countryOption" ).load( "/pages/release/change.php?view=sort_receiver&select="+typez+"" );
				
				
		});
		
		$('#agreements').on('change', function() {
				var typez = $('#agreements option:selected').attr('data-cna');				
		});		
	</script>  	
	
	<?php

	echo '
	
	<div class="form-group col-md-3">
	<label for="receiverCode">saņēmēja kods - nosaukums</label>
	<select class="form-control selectpicker btn-group-xs"  name="receiverCode" id="receiverCode"  data-live-search="true" title="saņēmēja kods - nosaukums">';
	$selectReceiver = mysqli_query($conn, "SELECT Code, name, country FROM receivers") or die(mysqli_error($conn));
	while($rowr = mysqli_fetch_array($selectReceiver)){
		echo '<option value="'.$rowr['Code'].'" data-cnid="'.$rowr['country'].'">'.$rowr['Code'].' - '.$rowr['name'].'</option>';
	}
	
	echo '
	</select>	
	</div>';

?>	


		
		<div class="form-group col-md-3">
		  <label for="receiverName2">saņemēja nosaukums 2</label>
		  <input type="text" class="form-control" name="receiverName2" id="receiverName2" placeholder="saņemēja nosaukums 2">
		</div>

		<div id="countryOption">
			<div class="form-group col-md-2 ">
			<label class="lb-sm" for="receiverCountry">saņēmēja valsts</label>
			  <select class="form-control selectpicker btn-group-xs input-xs" name="receiverCountry" id="receiverCountry"  data-live-search="true" title="saņēmēja valsts">
			  <?php
			  $selectReceiver = mysqli_query($conn, "SELECT Code, country FROM countries") or die(mysqli_error($conn));
			  while($rowr = mysqli_fetch_array($selectReceiver)){
				  echo '<option value="'.$rowr['Code'].'">'.$rowr['Code'].' - '.$rowr['country'].'</option>';
			  }
				?>
			  </select>	
			</div>
			
			 <div class="form-group col-md-2">
			  <label class="lb-sm"  for="cargoStatus">kravas status</label>
				<select class="form-control selectpicker btn-group-xs input-xs" id="cargoStatus" name="cargoStatus"  data-live-search="true" title="kravas status">
						<option value="C">C - eksports</option>
						<option value="EU">EU</option>
				</select>	
			 </div>		
		
		</div>	
	
    <div class="clearfix"></div>



  
  
			

       <input type="hidden" class="form-control" id="clientName">
	   <input type="hidden" class="form-control" id="ownerName">
	   <input type="hidden" class="form-control" id="receiverName">
	   <input type="hidden" class="form-control" id="cargoCode">

  </div>
  
  <div class="clearfix"></div><br>
  <button type="submit" class="btn btn-default btn-xs" id="savebtn"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>
</form>  

    <div id="selected"></div>
  
  

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>

<script>
function validateTime(obj)
{
    var timeValue = obj.value;
    if(timeValue == "" || timeValue.indexOf(":")<0)
    {
        alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
        return false;
    }
    else
    {
        var sHours = timeValue.split(':')[0];
        var sMinutes = timeValue.split(':')[1];

        if(sHours == "" || isNaN(sHours) || parseInt(sHours)>23)
        {
            alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
            return false;
        }
        else if(parseInt(sHours) == 0)
            sHours = "00";
        else if (sHours <10)
            sHours = "0"+sHours;

        if(sMinutes == "" || isNaN(sMinutes) || parseInt(sMinutes)>59)
        {
            alert("Nepareizs laika formāts. Pareiza laika formāta piemērs: 12:03");
            return false;
        }
        else if(parseInt(sMinutes) == 0)
            sMinutes = "00";
        else if (sMinutes <10)
            sMinutes = "0"+sMinutes;    
		var sHours = sHours.slice(-2);
		var sMinutes = sMinutes.slice(-2);
        obj.value = sHours + ":" + sMinutes;        
    }

    return true;    
}


</script>
<script>
$(document).ready(function() {
	$('form').on('keyup change', 'input, select, textarea', function(){

		$("#savebtn").css("background","#F1F103");
		
	});
});

$(document).on('dp.change', 'input', function(e) {
	var shown = $(this).attr('dp.shown');
	if (typeof shown != 'undefined' && shown && e.date != e.oldDate)
	$("#savebtn").css("background","#F1F103");
});

$(document).on('dp.show', 'input', function(e) {
	$(this).attr('dp.shown', 1);
});

</script>
<?php include_once("../../datepicker.php"); ?>