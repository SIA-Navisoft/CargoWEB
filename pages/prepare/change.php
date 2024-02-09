<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');

$page_file="prepare";


require('../../inc/s.php');

$selected = $_GET['select'];
			
			echo '

            <div class="form-group col-md-2">
            <label for="agreements">līgums</label>
                <select class="form-control selectpicker btn-group-xs"  name="agreements" id="agreements"  data-live-search="true" title="līgums"  '.$disabled.'>
                    <option></option>';
                    $selectAgreements = mysqli_query($conn, "SELECT id, contractNr, customerNr, customerName FROM agreements WHERE customerNr='".mysqli_real_escape_string($conn, $selected)."' AND  (dateTo='0000-00-00 00:00:00' OR dateTo IS NULL OR dateTo>CURDATE()) AND status<20 AND deleted='0'") or die(mysqli_error($conn));
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
			';

?>

<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>