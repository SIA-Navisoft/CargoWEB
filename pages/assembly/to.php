<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="assembly";

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

require('../../inc/s.php');




if (isset($_GET['select'])){$select = htmlentities($_GET['select'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['mainId'])){$mainId = htmlentities($_GET['mainId'], ENT_QUOTES, "UTF-8");}
if (isset($_GET['res'])){$res = htmlentities($_GET['res'], ENT_QUOTES, "UTF-8");}

$toCargo = mysqli_query($conn, "SELECT * FROM cargo_header WHERE docNr='".$select."'");
$tCrow = mysqli_fetch_array($toCargo);
if($tCrow['cargoCode']){$cCode=$tCrow['cargoCode'];}else{$tCrow=null;}
if($tCrow['docNr']){$isDocNr = $tCrow['docNr'];}else{$tCrow = null;}

$status = $tCrow['status'];
$rowSent = $tCrow['rowSent']; 
if($status>0 || $rowSent==1){$disabled='disabled';}else{$disabled=null;}

?>
<script>
    setTimeout(function() {
        $('#hideMessage').fadeOut('fast');
    }, 3000);
	  
    $(document).ready(function () {
        $('#send_profile_two').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url : '/pages/assembly/post.php?r=editTwo&id=<?=$tCrow['id'];?>',
                type: "POST",
                data: $(this).serializeArray(),
                beforeSend: function(){
                    
                    $("#changebtn").on('click',function(e) {
                    $('#changebtn').html("gaidiet...");
                    $("#changebtn").prop("disabled",true);
                    });
                },			
                success: function (data) {
                    console.log(data);

                    $( "#secondOptionCargo" ).load( "pages/assembly/move.php?select=<?=$select;?>" );
                    $( "#toCargo" ).load( "pages/assembly/to.php?select=<?=$select;?>&mainId=<?=$mainId;?>&res=done" );
                },
                error: function (jXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        });
    });  
</script>
<?
echo '
<form id="send_profile_two">
<div class="form-row">
    <input type="hidden" id="docNr" value="'.$tCrow['docNr'].'">
    
      <div class="table-responsive">
          <table class="table table-hover table-responsive">
              <thead><tr><th colspan="2"><p style="display:inline-block;">Uz</p> '.$cCode;
              
              if ($res=="done"){echo '<div class="pull-right" style="margin-top: -2px;" id="hideMessage"><div class="btn btn-success">saglabāts!</div></div>';}             
              
              echo '</th></tr></thead>
              <tbody>
                  <tr>
                      <td>piegādes datums:</td>
                      <td>'.date('d.m.Y', strtotime($tCrow['deliveryDate'])).'</td>
                  </tr>
                  <tr>
                      <td>pavadzīmes nr:</td>
                      <td>'.$tCrow['ladingNr'].'</td>
                  </tr>
                  <tr>
                      <td>kravas tips:</td>
                      <td>'.$tCrow['deliveryType'].'</td>
                  </tr>
                  <tr>
                      <td>kravas kods:</td>
                      <td>'.$tCrow['deliveryCode'].'</td>
                  </tr>
                  <tr>
                      <td>klienta kods - nosaukums:</td>
                      <td>'.$tCrow['clientCode'].'</td>
                  </tr>
                  <tr>
                      <td>īpašnieka kods - nosaukums:</td>
                      <td>'.$tCrow['ownerCode'].'</td>
                  </tr>
                  <tr>
                      <td>saņēmēja kods - nosaukums:</td>
                      <td>'.$tCrow['receiverCode'].'</td>
                  </tr>
                  <tr>
                      <td>līgums:</td>
                      <td>'.$tCrow['agreements'].'</td>
                  </tr>
                  <tr>
                      <td>transporta nr.:</td>
                      <td>'.$tCrow['transportNo'].'</td>
                  </tr>
                  <tr>
                      <td>noliktava</td>
                      <td>
                          
                              <select class="form-control selectpicker btn-group-xs"  name="locationTwo"  data-live-search="true" title="noliktava"';
                              if($p_edit!='on'){echo ' disabled';}
                              echo '>
                              ';
                              $selectLocation = mysqli_query($conn, "SELECT id, name FROM n_location") or die(mysqli_error($conn));
                              while($rowl = mysqli_fetch_array($selectLocation)){
                                  echo '<option  value="'.$rowl['id'].'"';
                                  if($tCrow['location']==$rowl['id']){echo ' selected';}
                                  echo '>'.$rowl['id'].' - '.$rowl['name'].'</option>';
                              }
                                  echo '
                              </select>		
                                                          
                      </td>
                  </tr>																														
              </tbody>
          </table>
      </div>';
      
  echo '

</div>

<div class="clearfix"></div>';
          		
		if($isDocNr){

			
			$inCargo = mysqli_query($conn, "
							SELECT SUM(amount) as amount, SUM(volume) as volume, 
							SUM(tare) as t, SUM(gross) as b, SUM(net) as n, SUM(cubicMeters) as a,
							productUmo, productNr FROM cargo_line WHERE docNr='".$isDocNr."' GROUP BY productUmo") or die (mysqli_error($conn));

			if(mysqli_num_rows($inCargo)>0){
			
			echo '<p style="display:inline-block;">daudzumi</p>';
			
				echo '
				<div class="table-responsive">
					<table class="table table-hover table-responsive">
						<thead>
							<th>mērvienība</th>
							<th>daudzums</th>
							<th>tara (kg)</th>
							<th>bruto (kg)</th>
							<th>neto (kg)</th>
							<th>apjoms (m3)</th>
						</thead>
						<tbody>
						';			
			while($icRow = mysqli_fetch_array($inCargo)){				
				echo '

						
							<tr>
								<td>'.$icRow['productUmo'].'</td>
								<td>'.floatval($icRow['amount']).'</td>
								<td>'.floatval($icRow['t']).'</td>
								<td>'.floatval($icRow['b']).'</td>
								<td>'.floatval($icRow['n']).'</td>
								<td>'.floatval($icRow['a']).'</td>
								
							</tr>

				';
			
			}
				echo '
						</tbody>
					</table>
				</div>				
				';
				
			}
		}

  echo '<div class="clearfix"></div>';
  
echo '<button type="submit" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-floppy-save" style="color: blue;"></i> saglabāt</button>';

echo '
<a href="/pages/receipt/status.php?id='.$tCrow['id'].'" data-toggle="modal" data-target="#showStatus" data-remote="false" class="btn btn-default btn-xs">
  <i class="glyphicon glyphicon-option-vertical"></i> statuss
</a>	  

<div class="clearfix"></div>';


echo '</form>
';
?>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>