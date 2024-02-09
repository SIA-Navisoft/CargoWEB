<?php
error_reporting(E_ALL ^ E_NOTICE);
require('../../lock.php');
include('../../functions/base.php');

$page_file="additional_services";


require('../../inc/s.php');

$selected=null;
$selected = $_GET['select'];
$selected = mysqli_real_escape_string($conn, $selected);

$id = $_GET['id'];
$id = mysqli_real_escape_string($conn, $id);

$row = $_GET['row'];
$row = mysqli_real_escape_string($conn, $row);

$view=$_GET['view'];

if($view=='single'){
    echo '
    <select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="pakalpojums" name="resourceNr">';	
    $selectResource = mysqli_query($conn, "
        SELECT r.id, r.name 

        FROM agreements_lines AS a 
        LEFT JOIN n_resource AS r
        ON a.service=r.id
        WHERE a.contractNr='".$id."' AND item='".$selected."' AND a.keeping!='on'
        GROUP BY a.service
    ");
    while($rowi = mysqli_fetch_array($selectResource)){
    echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
        if($rowL['resource']==$rowi['id']){ echo ' selected';}
    echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
    }

    echo '
    </select>
    ';
}

if($view=='multiple'){

echo '
<select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="pakalpojums" name="eResourceNr[]" '.$disabled.'>';	
$selectResource = mysqli_query($conn, "
    SELECT r.id, r.name 
    
    FROM agreements_lines AS a 
    LEFT JOIN n_resource AS r
    ON a.service=r.id
    WHERE a.contractNr='".$id."' AND item='".$selected."' AND a.keeping!='on'
    GROUP BY a.service
");
while($rowi = mysqli_fetch_array($selectResource)){
    echo '<option value="'.$rowi['id'].'" data-name="'.$rowi['name'].'"';
        if($rowL['resource']==$rowi['id']){ echo ' selected';}
    echo '>'.$rowi['id'].' ('.$rowi['name'].')</option>';
}

echo '
</select>
';    
}

if($view=='extrar'){
    if (isset($_GET['section'])){$section = htmlentities($_GET['section'], ENT_QUOTES, "UTF-8");}
    if (isset($_GET['agr'])){$agr = htmlentities($_GET['agr'], ENT_QUOTES, "UTF-8");}

    if(!$section){

        echo '
        <select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="pakalpojums" name="eresourceNr" id="extra_r">';	
        $selectResource = mysqli_query($conn, "
                    SELECT r.id, r.name, a.uom 
                    
                    FROM agreements_lines AS a 
                    LEFT JOIN n_resource AS r
                    ON a.service=r.id
                    WHERE a.contractNr='".$agr."' AND a.keeping!='on' AND a.extra_resource='on'
                    GROUP BY a.service
        ") or die(mysqli_error($conn));
        while($rowi = mysqli_fetch_array($selectResource)){
            echo '<option value="'.$rowi['id'].'" data-uom="'.$rowi['uom'].'">'.$rowi['id'].' ('.$rowi['name'].')</option>';
        }
        
        echo '
        </select>        
        ';    

    }

    if($section=='org'){
        echo '
        <select data-width="150px" class="form-control selectpicker btn-group-xs input-xs"  data-live-search="true" title="pakalpojums" disabled>';

        echo '
        </select>        
        '; 
    } 

}

?>
<script>
$(document).ready(function() {
   $('.selectpicker').selectpicker();
});
</script>