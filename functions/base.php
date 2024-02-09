<?

$numbermappings = array("zero", "one","two","three", "four", "five", "six", "seven", "eight", "nine", "ten");
$currenttime=date('Y-m-d H:i:s');

function webStatisticsPages($myemail, $myid, $source, $view, $action){		
		$fileName=basename($_SERVER['PHP_SELF']);
		$session_id=session_id();
		require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');		
		$result = mysqli_query($conn,"SELECT createdDate FROM web_statistics_pages WHERE session_id='".$session_id."' AND username='".$myemail."'  AND createdBy='".$myid."' AND fileName='".$fileName."' AND source='".$source."' AND view='".$view."' AND action='".$action."' ORDER BY id DESC LIMIT 1");
		if (!$result){die("Query to show fields failed");}
		$row = mysqli_fetch_array($result);		
		if ((mysqli_num_rows($result)==0)||(time()-strtotime($row['createdDate'])>120)){
			mysqli_query($conn, "INSERT INTO web_statistics_pages (session_id, username, createdBy, fileName, source, view, action) "." VALUES ('$session_id', '$myemail', '$myid', '$fileName', '$source', '$view', '$action')");
		}
}


function isValidPK($pk) {
    $pk = str_replace('-', '', $pk);
    if (strlen($pk) != 11) return false;
    $calc = 1*$pk[0] + 6*$pk[1] + 3*$pk[2] + 7*$pk[3] + 9*$pk[4] + 10*$pk[5] + 5*$pk[6] + 8*$pk[7] + 4*$pk[8] + 2*$pk[9];
    $checksum = (1101 - $calc)%11;
   if ($checksum == $pk[10]){return 'true';}
}


function insertNewRows($table_name, $form_data){ 
	$fields = array_keys($form_data); 
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn, "INSERT INTO ".$table_name." (`".implode('`,`', $fields)."`) VALUES('".implode("','", $form_data)."')");
	if (!$result){die(mysqli_error($conn));}
}

function findMyLastRow($table_name, $createdBy){ 
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT id FROM {$table_name} WHERE createdBy = '".$createdBy."' ORDER BY id DESC LIMIT 1");
	if (!$result){die("Query to show fields failed");}
	$row = mysqli_fetch_array($result);
	return $row['id'];
	mysqli_close($conn);
}

function findLastRow($table_name){ 
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT id FROM {$table_name}  ORDER BY id DESC LIMIT 1") or die(mysqli_error($conn));
	
	$row = mysqli_fetch_array($result);
	
	return $row['id'];
	
	mysqli_close($conn);
}
 


function deleteSomeRow($table_name, $where_clause=''){
	$whereSQL = '';
	if(!empty($where_clause)){
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE'){ $whereSQL = " WHERE ".$where_clause;}else{$whereSQL = " ".trim($where_clause);}
	}
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	mysqli_query($conn, "DELETE FROM ".$table_name.$whereSQL." LIMIT 1");
}

function deleteAllRows($table_name, $where_clause=''){
	$whereSQL = '';
	if(!empty($where_clause)){
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE'){ $whereSQL = " WHERE ".$where_clause;}else{$whereSQL = " ".trim($where_clause);}
	}
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	mysqli_query($conn, "DELETE FROM ".$table_name.$whereSQL."");
}

function updateSomeRow($table_name, $form_data, $where_clause=''){
	$whereSQL = '';
	if(!empty($where_clause)){
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE'){$whereSQL = " WHERE ".$where_clause;}else{$whereSQL = " ".trim($where_clause);}
	}
	
    $sql = "UPDATE ".$table_name." SET ";
	
	$sets = array();
	foreach($form_data as $column => $value){
		$sets[] = "`".$column."` = '".$value."'";
	}
	$sql .= implode(', ', $sets);	
	$sql .= $whereSQL;	
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	mysqli_query($conn, $sql);
}

function updateSomeRowBank($table_name, $form_data_bank, $where_clause=''){
	$whereSQL = '';
	if(!empty($where_clause)){
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE'){$whereSQL = " WHERE ".$where_clause;}else{$whereSQL = " ".trim($where_clause);}
	}
	
    $sql = "UPDATE ".$table_name." SET ";
	
	$sets = array();
	foreach($form_data_bank as $column => $value){
		$sets[] = "`".$column."` = '".$value."'";
	}
	$sql .= implode(', ', $sets);	
	$sql .= $whereSQL;	
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	mysqli_query($conn, $sql);
}
 
function webmessage($from, $to, $subject, $body, $source){ 
   require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
   mysqli_query($conn, "INSERT INTO web_messages (sender, receiver, topic, message, createdSource) "." VALUES ('$from', '$to', '$subject', '$body', '$source')");
}

function showData($table, $offset=0, $rec_limit=100){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT * FROM {$table} LIMIT {$offset}, {$rec_limit}");
	if (!$result){die("Query to show fields failed");}
	$fields_num = mysqli_num_fields($result);
	echo '<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>';
	for($i=0; $i<$fields_num; $i++){
		$field = mysqli_fetch_field($result);
		echo '<th>'.$field->name.'</th>';
	}
	echo '</tr></thead><tbody>';
	while($row = mysqli_fetch_row($result)){
		echo '<tr>';
		foreach($row as $cell)
			echo '<td>'.truncate($cell).'</td>';
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	mysqli_close($conn);
}

function safe($value){ 
	return mysqli_real_escape_string($value); 
 }
 
 function safeHTML($value){ 
	return htmlentities($value, ENT_QUOTES, "UTF-8");
 } 

function showDataCustom($table, $form_data){
	$fields = array_keys($form_data);
	$select_fiels = implode(',', $fields);
	$fields_num = count($fields);
	
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT ".implode(',', $fields)." FROM {$table} WHERE deletedDate IS NULL");
	if (!$result){die("Query to show fields failed");}
	echo '<div class="table-responsive"><table class="table table-hover table-responsive"><thead><tr>';
	for($i=0; $i<$fields_num; $i++)	{
		echo '<th>'.$form_data[$fields[$i]].'</th>';
	}
	echo '</tr></thead><tbody>';
	while($row = mysqli_fetch_row($result))	{
		echo '<tr onclick="window.location=\'?action=edit&id='.$row[0].'\'" class="datarow">';
		foreach($row as $cell)
			echo '<td>'.$cell.'</td>';
		echo '</tr>';
	}
	
	echo '</tbody></table></div>';
	mysqli_close($conn);
}

function countData($table){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT id FROM {$table} WHERE deletedDate IS NULL");
	if (!$result){die("Query to show fields failed");}
	echo mysqli_num_rows($result);
	mysqli_close($conn);
}

function lastData($table){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT createdDate FROM {$table} ORDER BY createdDate DESC LIMIT 1");
	if (!$result){die("Query to show fields failed");}
	while ($row = mysqli_fetch_array($result)){
		echo $row['createdDate'];
	}
	mysqli_close($conn);
}

function lastUpdated($table){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT Updated FROM {$table} ORDER BY Updated DESC LIMIT 1");
	if (!$result){die("Query to show fields failed");}
	while ($row = mysqli_fetch_array($result)){
		echo $row['Updated'];
	}
	mysqli_close($conn);
}

function tellMeWho($id){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT full_name FROM user WHERE id = {$id} LIMIT 1");
	if (!$result){die("Query to show fields failed");}
	$row = mysqli_fetch_array($result);
	echo $row['full_name'];
	mysqli_close($conn);
}

function returnMeWho($id){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT full_name FROM user WHERE id = {$id} LIMIT 1");
	if (!$result){die("Query to show fields failed");}
	$row = mysqli_fetch_array($result);
	return $row['full_name'];
	mysqli_close($conn);
}

function countDataAll($table){
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	$result = mysqli_query($conn,"SELECT id FROM {$table}");
	if (!$result){die("Query to show fields failed");}
	echo mysqli_num_rows($result);
	mysqli_close($conn);
}

function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
 }
 
function truncate($string,$length=50,$appendStr="..."){
    $truncated_str = "";
	$string=html_entity_decode($string, ENT_QUOTES, "UTF-8");
    $useAppendStr = (strlen($string) > intval($length))? true:false;
    $truncated_str = mb_substr($string,0,$length, "utf-8");
    $truncated_str .= ($useAppendStr)? $appendStr:"";
    return $truncated_str;
}
function spendtime($spend){
	$minutes = sprintf('%02d', ($spend / 60) % 60);
    $hours = floor($spend / (60 * 60));
	$seconds = sprintf('%02d', $spend - $hours*60*60 - $minutes*60);
	return $hours."h ".$minutes."m";
}


function randomStr($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function paswStr($length = 10) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
    if(!$contents) return array(); 

    if(!function_exists('xml_parser_create')) { 
        return array(); 
    } 

    $parser = xml_parser_create(''); 
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, trim($contents), $xml_values); 
    xml_parser_free($parser); 

    if(!$xml_values) return; 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array; //Refference 

    //Go through the tags. 
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
    foreach($xml_values as $data) { 
        unset($attributes,$value);//Remove existing values, or there will be trouble 

        //This command will extract these variables into the foreach scope 
        // tag(string), type(string), level(int), attributes(array). 
        extract($data);//We could use the array by itself, but this cooler. 

        $result = array(); 
        $attributes_data = array(); 
         
        if(isset($value)) { 
            if($priority == 'tag') $result = $value; 
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
        } 

        //Set the attributes too. 
        if(isset($attributes) and $get_attributes) { 
            foreach($attributes as $attr => $val) { 
                if($priority == 'tag') $attributes_data[$attr] = $val; 
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
            } 
        } 

        //See tag status and do the needed. 
        if($type == "open") {//The starting of the tag '<tag>' 
            $parent[$level-1] = &$current; 
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                $current[$tag] = $result; 
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 

                $current = &$current[$tag]; 

            } else { //There was another element with the same tag name 

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                    $repeated_tag_index[$tag.'_'.$level]++; 
                } else {//This section will make the value an array if multiple tags with the same name appear together 
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array 
                    $repeated_tag_index[$tag.'_'.$level] = 2; 
                     
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                        unset($current[$tag.'_attr']); 
                    } 

                } 
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                $current = &$current[$tag][$last_item_index]; 
            } 

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
            //See if the key is already taken. 
            if(!isset($current[$tag])) { //New Key 
                $current[$tag] = $result; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

            } else { //If taken, put all things inside a list(array) 
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

                    // ...push the new element into that array. 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                     
                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; 

                } else { //If it is not an array... 
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $get_attributes) { 
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                             
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
                         
                        if($attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                } 
            } 

        } elseif($type == 'close') { //End of tag '</tag>' 
            $current = &$parent[$level-1]; 
        } 
    } 
     
    return($xml_array); 
} 

function pageing_menu($offset, $rec_limit, $max_pages, $link_to, $page, $conn, $query){

    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $count_result = mysqli_num_rows($result);


    if ($count_result>$rec_limit){
        //$page = 1;
		$total_pages=ceil($count_result/$rec_limit);

        if ($page>$total_pages){$page=$total_pages;}
        $offset = ($page - 1) * $rec_limit;

        $page_menu = '<nav aria-label="Page navigation" class="paging"><ul class="pagination pagination-sm">';
        if ($page == 1){$status='disabled';$page_back=$page;}else{$status=null;$page_back=$page-1;}

        $page_menu.= '<li class="'.$status.'"><a class="menuClick" href="'.$link_to.'1" aria-label="Previous"><span aria-hidden="true"><i class="glyphicon glyphicon-chevron-left"></i></span></a></li>';
        $page_menu.= '<li class="'.$status.'"><a class="menuClick" href="'.$link_to.$page_back.'" aria-label="Previous"><span aria-hidden="true"><i class="glyphicon glyphicon-menu-left"></i></span></a></li>';
        $u=null;
        for($i=1; $i<=$total_pages; $i++){
            if ($i==$page){$status='active';}else{$status=null;}
            if (($page<($i+5))&&($u<$max_pages)){
                    $page_menu.= '	<li class="'.$status.'"><a  class="menuClick" href="'.$link_to.$i.'">'.$i.'</a></li>';
                    ++$u;
            }
        }
        if ($page == $total_pages){$status='disabled';$page_front=$page;}else{$status=null;$page_front=$page+1;}
        $page_menu.= '<li class="'.$status.'"><a class="menuClick" href="'.$link_to.$page_front.'"><span aria-hidden="true"><i class="glyphicon glyphicon-menu-right"></i></span></a></li>';
        $page_menu.= '<li class="'.$status.'"><a class="menuClick" href="'.$link_to.$total_pages.'"><span aria-hidden="true"><i class="glyphicon glyphicon-chevron-right"></i></span></i></a></li>';
        $page_menu.= '</ul></nav>';
	
    }

    $result_base = mysqli_query($conn, $query);
	$result_page = mysqli_query($conn, $query ." LIMIT ".$offset.", ".$rec_limit);


    return array($page_menu, $result_base, $result_page);
} 

		function reArrayFiles(&$file_post) {

			$file_ary = array();
			$file_count = count($file_post['name']);
			$file_keys = array_keys($file_post);

			for ($i=0; $i<$file_count; $i++) {
				foreach ($file_keys as $key) {
					
					if($key != 'error' && $file_post['size'][$i] != 0){

						$file_ary[$i][$key] = $file_post[$key][$i];
					}
					
				}
			}

			return $file_ary;
		}

function global_settings($conn, $sel){
	$result = mysqli_query($conn, "SELECT ".$sel." FROM  global_settings") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row[$sel];
}	

function returnClientName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT Name FROM  n_customers WHERE Code='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['Name'];
}

function returnOwnerName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT Name FROM  n_customers WHERE Code='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['Name'];
}

function returnReceiverName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT Name FROM  n_customers WHERE Code='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['Name'];
}

function returnProductName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT CONCAT(COALESCE(name1,''), ' ', COALESCE(name2,'')) AS name FROM  n_items WHERE code='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['name'];
}

function returnUomName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT name FROM  unit_of_measurement WHERE code='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['name'];
}

function returnResourceName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT name FROM  n_resource WHERE id='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['name'];
}

function returnLocationName($conn, $code){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT name FROM  n_location WHERE id='".$code."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return $row['name'];
}

function lastActionBy($conn, $id){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT lastBy, lastDate FROM  cargo_header WHERE id='".$id."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return returnMeWho($row['lastBy']).' '.date('d.m.Y H:i:s', strtotime($row['lastDate']));
}

function lastActionByAS($conn, $id){
	$code = mysqli_real_escape_string($conn, $code);
	$result = mysqli_query($conn, "SELECT lastBy, lastDate FROM  additional_services_header WHERE id='".$id."'") or die(mysqli_error($conn));
	$row =  mysqli_fetch_array($result);
	
	return returnMeWho($row['lastBy']).' '.date('d.m.Y H:i:s', strtotime($row['lastDate']));
}	
function returnCargoStatus($conn, $id){

	$query = mysqli_query($conn, "SELECT status FROM cargo_header WHERE id='".intval($id)."'") or die (mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	$status = $row['status'];


	if($status==0){
		$sta = 'ienākusi';
	}
	if($status==10){
		$sta = 'ienākusi (nodota)';
	}
	if($status==20){
		$sta = 'saņemta';
	}
	if($status==30){
		$sta = 'saņemta (nodota)';
	}
	if($status==40){
		$sta = 'izsniegta';
	}	
	return $sta;
}

function returnExtraStatus($conn, $id){

	$query = mysqli_query($conn, "SELECT status FROM additional_services_header WHERE id='".intval($id)."'") or die (mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	$status = $row['status'];


	if($status==0){
		$sta = 'izveidots';
	}
	if($status==10){
		$sta = 'izveidots (nodots)';
	}
	if($status==20){
		$sta = 'apstiprināts';
	}	
	return $sta;
}

function returnCargoLineStatus($conn, $id){

	$query = mysqli_query($conn, "SELECT status FROM cargo_line WHERE id='".intval($id)."'") or die (mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	$status = $row['status'];


	if($status==0){
		$sta = 'ienākusi';
	}
	if($status==10){
		$sta = 'ienākusi (nodota)';
	}
	if($status==20){
		$sta = 'saņemta';
	}
	if($status==30){
		$sta = 'saņemta (nodota)';
	}
	if($status==40){
		$sta = 'izsniegta';
	}
	return $sta;
}

function checkIfMainUOM($conn, $product, $uom){
	
	$result = mysqli_query($conn, "SELECT id FROM additional_uom WHERE productNr='".$product."' AND uom='".$uom."' AND base='1' AND status='1'");
	if(mysqli_num_rows($result)>0){
		return 1;
	}else{
		return 0;
	}
	
}

function selectBaseUom($conn, $product){
	
	$result = mysqli_query($conn, "SELECT uom FROM additional_uom WHERE productNr='".$product."' AND base='1' AND status='1'");
	if(mysqli_num_rows($result)>0){
		$row = mysqli_fetch_array($result);

		return $row['uom'];

	}
	
}

function getMainUOM($conn, $product){
	
	$result = mysqli_query($conn, "SELECT amount FROM additional_uom WHERE productNr='".$product."' AND base='1' AND status='1'");
	$row = mysqli_fetch_array($result);
	
	return $row['amount'];
}




	
function getTotValueUOM($conn, $product, $amount, $uom, $touom){	

	$imuom = checkIfMainUOM($conn, $product, $uom); // NO
	$imtouom = checkIfMainUOM($conn, $product, $touom); // UZ
	
	$result = mysqli_query($conn, "SELECT uom, amount FROM additional_uom WHERE productNr='".$product."' AND status='1'");
	
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_array($result)){
			
	
			
			if($imuom==1){
				if($uom==$touom){
					return $amount;
				}else{
					
					if($row['uom']==$touom){
						return $amount/$row['amount'];
					}
					
				}
			}


			if($imuom!=1 && $imtouom!=1){

				if($uom==$touom){
					return $amount;
				}else{
					
					
					
					if($row['uom']==$touom){
						return $amount/$row['amount']*1000;
					}
					if($row['uom']==$uom){
						return $amount*$row['amount']/1000;
					}
					
					
				}
				
			}

			if($imuom!=1 && $imtouom==1){
				
					if($row['uom']==$uom){
						return $amount*$row['amount'];
					}				
				
				
			}		
			
		}
		
	}	

}

function getSettingUOM($conn, $col){
	
	$col = mysqli_real_escape_string($conn, $col);
	
	$result = mysqli_query($conn, "SELECT ".$col." FROM settings");
	$row = mysqli_fetch_array($result);
	
	return $row[$col];
}	

function getCustInfo($conn, $col, $id){
	
	$col = mysqli_real_escape_string($conn, $col);
	$client = mysqli_real_escape_string($conn, $id);

	$result = mysqli_query($conn, "SELECT ".$col." FROM n_customers WHERE Code='".$client."'");
	$row = mysqli_fetch_array($result);
	
	return $row[$col];
}



function returnAction($id){

	if($id==0){
		$sta = 'ienākšana';
	}
	if($id==10){
		$sta = 'saņemšana';
	}
	if($id==20){
		$sta = 'pārvietošana';
	}
	if($id==30){
		$sta = 'komplektēšana';
	}
	if($id==40){
		$sta = 'izsniegšana';
	}	
	return $sta;
}

function returnStatus($status){

	if($status==0){
		$sta = 'ienākusi';
	}
	if($status==10){
		$sta = 'ienākusi (nodota)';
	}
	if($status==20){
		$sta = 'saņemta';
	}
	if($status==30){
		$sta = 'saņemta (nodota)';
	}
	if($status==40){
		$sta = 'izsniegta';
	}
	return $sta;

}



function getTypeUOM($conn, $product, $uom){
	
	$product = mysqli_real_escape_string($conn, $product);
	$uom = mysqli_real_escape_string($conn, $uom);

	$result = mysqli_query($conn, "SELECT weight FROM additional_uom WHERE productNr='".$product."' AND uom='".$uom."' AND status='1'");
	$row = mysqli_fetch_array($result);
	
	return $row['weight'];
}

function getAmountUOM($conn, $product, $uom){

	$product = mysqli_real_escape_string($conn, $product);
	$uom = mysqli_real_escape_string($conn, $uom);
	
	$result = mysqli_query($conn, "SELECT amount FROM additional_uom WHERE productNr='".$product."' AND uom='".$uom."' AND status='1'");
	$row = mysqli_fetch_array($result);
	
	return $row['amount'];
}


function retAm($conn, $w, $product){

	$selectType = mysqli_query($conn, "SELECT amount FROM additional_uom WHERE `weight`='".$w."' AND productNr='".$product."' AND status=1");
	$stRow = mysqli_fetch_array($selectType);

	return $stRow['amount'];
}

function retAmA($conn, $w, $product){

	$selectType = mysqli_query($conn, "SELECT amount FROM additional_uom WHERE `weight`='".$w."' AND productNr='".$product."' AND for_extra_uom=0 AND status=1");
	$stRow = mysqli_fetch_array($selectType);

	return $stRow['amount'];
}

function retAmB($conn, $w, $product){

	$selectType = mysqli_query($conn, "SELECT amount FROM additional_uom WHERE `weight`='".$w."' AND productNr='".$product."' AND for_extra_uom=1 AND status=1");
	$stRow = mysqli_fetch_array($selectType);

	return $stRow['amount'];
}

function retAmLeft($conn, $docNr, $product, $umo, $option){

	$result = mysqli_query($conn, "SELECT SUM(".$option.") AS total FROM cargo_line WHERE docNr='".$docNr."' AND productNr='".$product."' AND productUmo='".$umo."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($result);

	return $row['total'];
}

// UNIQUEID CLASS
class UUID {
	public static function v3($namespace, $name) {
	  if(!self::is_valid($namespace)) return false;
  
	  // Get hexadecimal components of namespace
	  $nhex = str_replace(array('-','{','}'), '', $namespace);
  
	  // Binary Value
	  $nstr = '';
  
	  // Convert Namespace UUID to bits
	  for($i = 0; $i < strlen($nhex); $i+=2) {
		$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
	  }
  
	  // Calculate hash value
	  $hash = md5($nstr . $name);
  
	  return sprintf('%08s-%04s-%04x-%04x-%12s',
  
		// 32 bits for "time_low"
		substr($hash, 0, 8),
  
		// 16 bits for "time_mid"
		substr($hash, 8, 4),
  
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 3
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
  
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
  
		// 48 bits for "node"
		substr($hash, 20, 12)
	  );
	}
  
	public static function v4() {
	  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
  
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
  
		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),
  
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
  
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
  
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	  );
	}
  
	public static function v5($namespace, $name) {
	  if(!self::is_valid($namespace)) return false;
  
	  // Get hexadecimal components of namespace
	  $nhex = str_replace(array('-','{','}'), '', $namespace);
  
	  // Binary Value
	  $nstr = '';
  
	  // Convert Namespace UUID to bits
	  for($i = 0; $i < strlen($nhex); $i+=2) {
		$nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
	  }
  
	  // Calculate hash value
	  $hash = sha1($nstr . $name);
  
	  return sprintf('%08s-%04s-%04x-%04x-%12s',
  
		// 32 bits for "time_low"
		substr($hash, 0, 8),
  
		// 16 bits for "time_mid"
		substr($hash, 8, 4),
  
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 5
		(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
  
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
  
		// 48 bits for "node"
		substr($hash, 20, 12)
	  );
	}
  
	public static function is_valid($uuid) {
	  return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
						'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
	}
  }


// logos pārbauda uz dokumenta numuru cik preces ir palicis uz doto datumu
function productLeftOnDay($conn, $docNr, $productNr, $day, $line){
 
    if (isset($docNr)){$docNr = htmlentities($docNr, ENT_QUOTES, "UTF-8");}
    if (isset($productNr)){$productNr = htmlentities($productNr, ENT_QUOTES, "UTF-8");}
    if (isset($day)){$day = htmlentities($day, ENT_QUOTES, "UTF-8");} 
	
    $selectData = mysqli_query($conn, "
        SELECT e.amount, e.gross, e.net, e.cubicMeters, e.type, e.action, au.uom AS auuom, au.weight AS weight
		FROM item_ledger_entry AS e
		
		LEFT JOIN additional_uom AS au
		ON e.productNr=au.productNr AND au.status=1 AND au.convert_from=1 AND au.weight!='50'
 
        WHERE e.docNr='".$docNr."' AND e.productNr='".$productNr."' AND DATE_FORMAT(e.activityDate,'%Y-%m-%d')<='".$day."' AND (e.cargoLine='".$line."' OR e.orgLine='".$line."')
		
		GROUP BY e.id
		ORDER BY e.id
    ") or die(mysqli_error($conn));
	
    $calcIt=null;	
    while($row = mysqli_fetch_array($selectData)){	
            
				// bruto (kg)

				if($row['auuom'] && $row['weight']=='10'){
					$amount = abs($row['gross']);
				}
				// neto (kg)
				if($row['auuom'] && $row['weight']=='20'){
					$amount = abs($row['net']);
				}
				// apjoms (m3)
				if($row['auuom'] && $row['weight']=='30'){
					$amount = abs($row['cubicMeters']);
				}
				if($row['auuom']==''){
					$amount = abs($row['amount']);
				} 
                
                if($row['action']==10){
                    $calcIt = $amount;
                }
                if($row['action']!=20 && $row['type']=='negative'){
                    $calcIt -= $amount;
                }
                if($row['action']!=20 && $row['action']==30 && $row['type']=='positive'){
                    $calcIt += $amount;
                }                        
    } 
	
    return round($calcIt, 5);

}

function productLeftOnPeriod($conn, $docNr, $productNr, $from, $to){

	$selectData = mysqli_query($conn, "

	SELECT amount, action, type FROM item_ledger_entry 
	
	WHERE docNr='".$docNr."' AND productNr='".$productNr."' AND DATE_FORMAT(activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."'
	") or die(mysqli_error($conn));
	
	$calcIt=null;	
	while($row = mysqli_fetch_array($selectData)){	
			
				$amount = abs($row['amount']);

                if($row['action']==10){
                    $calcIt = $amount;
                }
                if($row['action']!=20 && $row['type']=='negative'){
                    $calcIt -= $amount;
                }
                if($row['action']!=20 && $row['action']==30 && $row['type']=='positive'){
                    $calcIt += $amount;
				} 
				
				
				
	}

	return $calcIt;

}

function productRelesedOnPeriod($conn, $docNr, $productNr, $day, $line){
 
    if (isset($docNr)){$docNr = htmlentities($docNr, ENT_QUOTES, "UTF-8");}
    if (isset($productNr)){$productNr = htmlentities($productNr, ENT_QUOTES, "UTF-8");}
    if (isset($day)){$day = htmlentities($day, ENT_QUOTES, "UTF-8");} 
    
    $selectData = mysqli_query($conn, "
        SELECT e.gross, e.net, e.cubicMeters, e.amount, e.action, e.type, au.uom AS auuom, au.weight AS weight
		FROM item_ledger_entry AS e
		
		LEFT JOIN additional_uom AS au
		ON e.productNr=au.productNr AND au.status=1 AND au.convert_from=1
 
        WHERE e.docNr='".$docNr."' AND e.productNr='".$productNr."' AND DATE_FORMAT(e.activityDate,'%Y-%m-%d')<='".$day."' AND (e.cargoLine='".$line."' OR e.orgLine='".$line."') AND e.status='40'
    ") or die(mysqli_error($conn));
	
    $calcIt=null;	
    while($row = mysqli_fetch_array($selectData)){	
            
				// bruto (kg)

				if($row['auuom'] && $row['weight']=='10'){
					$amount = abs($row['gross']);
				}
				// neto (kg)
				if($row['auuom'] && $row['weight']=='20'){
					$amount = abs($row['net']);
				}
				// apjoms (m3)
				if($row['auuom'] && $row['weight']=='30'){
					$amount = abs($row['cubicMeters']);
				}
				if($row['auuom']==''){
					$amount = abs($row['amount']);
				}							                
                
                if($row['action']==10){
                    $calcIt = $amount;
                }
                if($row['action']!=20 && $row['type']=='negative'){
                    $calcIt += $amount;
                }
                if($row['action']!=20 && $row['action']==30 && $row['type']=='positive'){
                    $calcIt -= $amount;
                }                        
    } 
    
    return $calcIt;

}


function productReceivedOnPeriod($conn, $docNr, $productNr, $from, $to, $line){
 
    if (isset($docNr)){$docNr = htmlentities($docNr, ENT_QUOTES, "UTF-8");}
    if (isset($productNr)){$productNr = htmlentities($productNr, ENT_QUOTES, "UTF-8");}
    if (isset($from)){$from = htmlentities($from, ENT_QUOTES, "UTF-8");} 
    if (isset($to)){$to = htmlentities($to, ENT_QUOTES, "UTF-8");} 
    
    $selectData = mysqli_query($conn, "
        SELECT e.amount, e.gross, e.net, e.cubicMeters, au.uom AS auuom, au.weight AS weight
		FROM item_ledger_entry AS e
		
		LEFT JOIN additional_uom AS au
		ON e.productNr=au.productNr AND au.status=1 AND au.convert_from=1
 
        WHERE e.docNr='".$docNr."' AND e.productNr='".$productNr."' AND DATE_FORMAT(e.activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."' AND (e.cargoLine='".$line."' OR e.orgLine='".$line."') AND e.status='20'
    ") or die(mysqli_error($conn));
	
    $calcIt=null;	
    while($row = mysqli_fetch_array($selectData)){	
            
				// bruto (kg)

				if($row['auuom'] && $row['weight']=='10'){
					$amount = abs($row['gross']);
				}
				// neto (kg)
				if($row['auuom'] && $row['weight']=='20'){
					$amount = abs($row['net']);
				}
				// apjoms (m3)
				if($row['auuom'] && $row['weight']=='30'){
					$amount = abs($row['cubicMeters']);
				}
				if($row['auuom']==''){
					$amount = abs($row['amount']);
				}							                
                
                $calcIt += $amount;
                        
    } 
    
    return $calcIt;

}

// atgriež dienu skaitu periodā
function daysBetweenDates($from, $to){

	$startTimeStamp = strtotime(date('Y-m-d', strtotime($from)));
	$endTimeStamp = strtotime(date('Y-m-d', strtotime($to)));
	
	$timeDiff = abs($endTimeStamp - $startTimeStamp);
	
	$numberDays = $timeDiff/86400;  // 86400 seconds in one day
	
	$numberDays = intval($numberDays)+1;

	return $numberDays;

}

function nextAction($conn, $from, $to, $id, $docNr, $activityDate){

	if (isset($from)){$from = htmlentities($from, ENT_QUOTES, "UTF-8");}
	if (isset($to)){$to = htmlentities($to, ENT_QUOTES, "UTF-8");}
	if (isset($docNr)){$docNr = htmlentities($docNr, ENT_QUOTES, "UTF-8");}
	if (isset($activityDate)){$activityDate = htmlentities($activityDate, ENT_QUOTES, "UTF-8");}
	$id = intval($id);

	$query = mysqli_query($conn, "
		SELECT activityDate FROM item_ledger_entry 
		WHERE docNr='".$docNr."' AND id='".$id."' AND DATE_FORMAT(activityDate,'%Y-%m-%d')>='".$activityDate."' AND 

		(
			DATE_FORMAT(activityDate,'%Y-%m-%d') BETWEEN '".date('Y-m-d', strtotime($from))."' AND '".date('Y-m-d', strtotime($to))."'
		)

	");

	$row = mysqli_fetch_array($query);

	if($row['activityDate']!='' && $row['activityDate']!='0000-00-00 00:00:00'){
		return date('Y-m-d', strtotime($row['activityDate']));
	}
	

}


function returnCargoCode($conn, $id, $code){

	if($code==1){
		$action = "WHERE id='".intval($id)."'";
	}else{
		$action = "WHERE docNr='".mysqli_real_escape_string($conn, $id)."'";
	}

	
	$query = mysqli_query($conn, "SELECT cargoCode FROM cargo_header ".$action." ") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	return $row['cargoCode'];
	
}

function checkIfScanned($conn, $docNr){
	
	$query = mysqli_query($conn, "SELECT id FROM scanner_lines WHERE docNr='".$docNr."' ") or die(mysqli_error($conn));
	
	if(mysqli_num_rows($query)==1){
		return 1;
	}else{
		return 0;
	}
	
}

function checkIfScannedIssuance($conn, $issuance_id){
	
	$query = mysqli_query($conn, "SELECT id FROM issuance_doc WHERE issuance_id='".$issuance_id."' ") or die(mysqli_error($conn));
	
	if(mysqli_num_rows($query)==1){
		return 1;
	}else{
		return 0;
	}
	
}

function checkScannedHeaderStatus($conn, $docNr){
	
	$query = mysqli_query($conn, "SELECT scanStatus FROM cargo_header WHERE docNr='".$docNr."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	return $row['scanStatus'];
	
}

function checkScannedIssuanceStatus($conn, $docNr){
	
	$query = mysqli_query($conn, "SELECT scanStatus FROM issuance_doc WHERE issuance_id='".$docNr."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	return $row['scanStatus'];
	
}

function allowedScannerClients(){
	
	require($_SERVER['DOCUMENT_ROOT'].'/inc/s.php');
	
	$query = mysqli_query($conn, "SELECT customerNr FROM agreements WHERE useScan=1") or die(mysqli_error($conn));
	
	$allowedClients=array();
	while($row = mysqli_fetch_array($query)){
		array_push($allowedClients, $row['customerNr']);
	}

	return $allowedClients;
	
}

function checkIfAllowedScannerClient($conn, $customer){
	
	$query = mysqli_query($conn, "SELECT customerNr FROM agreements WHERE customerNr='".$customer."' AND useScan=1 AND deleted=0") or die(mysqli_error($conn));
	
	if(mysqli_num_rows($query)==1){
		return 1;
	}

	return 0;
	
}

function returnladingNrFromDoc($conn, $docNr){
	
	$query = mysqli_query($conn, "SELECT ladingNr FROM cargo_header WHERE docNr='".$docNr."' ") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	return $row['ladingNr'];
	
}

$allowedClients = allowedScannerClients();
$allowedClientsList = implode(',', $allowedClients);


function checkIfSerialNoInIssuanceDoc($conn, $issuance_id, $serialNo){
	
	$knowIt = mysqli_query($conn, "
				SELECT *
					
				FROM issuance_doc 
				 
				WHERE issuance_id='".$issuance_id."'
			") or die(mysqli_error($conn));
	$kIrow = mysqli_fetch_array($knowIt);

		$showHi = "(cargo_line.issuance_id='' OR cargo_line.issuance_id='".$kIrow['issuance_id']."')"; 		

		$numz = null;
		if($kIrow['acceptance_act_no']){ $numz = " AND cargo_header.acceptance_act_no='".$kIrow['acceptance_act_no']."'"; }

		$lines = mysqli_query($conn, "
				SELECT cargo_line.serialNo
				FROM cargo_line 

				LEFT JOIN cargo_header 
				ON cargo_line.docNr=cargo_header.docNr 
				 
				WHERE  
				".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." AND cargo_line.action!='23' AND cargo_line.action!='27'

					AND (
							(
							
								cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
								FROM cargo_line 
								LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
								AND cargo_line.status = '30') 
								
							) OR (
							
								cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
                                AND cargo_line.status = '30') 
									   
                             ) OR (
							
								cargo_line.status = '40' AND EXISTS(SELECT cargo_line.* 
                                FROM cargo_line 
                                LEFT JOIN cargo_header 
                                ON cargo_line.docnr = cargo_header.docnr 
								WHERE  ".$showHi." ".$search." AND cargo_header.clientCode='".$kIrow['clientCode']."' AND cargo_header.agreements='".$kIrow['agreements']."' ".$numz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
                                AND cargo_line.status = '40') 
							)
									   
                        )	

					AND cargo_line.serialNo='".$serialNo."'
				
				ORDER BY cargo_header.deliveryDate
			") or die (mysqli_error($conn));

	if(mysqli_num_rows($lines)>0){	
		return 1;
	}else{
		return 0;
	}
	
}

//pārbauda vai produkta kods līdz pirmajai atstarpei sakrīt ar galamērķi
function checkIfSerialNumberHasSameDestination($conn, $serial, $destination){
	
	echo $serial.'<br><br>';
	
	$query = mysqli_query($conn, "SELECT substring_index(productNr,' ',1) AS productNr FROM cargo_line WHERE serialNo='".$serial."'");
	if(mysqli_num_rows($query)>0){
		
		$row = mysqli_fetch_array($query);
		$productNr = trim($row['productNr']);
		
		if($productNr==$destination){
			echo 'drīkts.';
			return 1;
		}else{
			$words = preg_replace('/[^a-z]/i','',$productNr);
			if($words==$destination){
				echo 'drīkst bet jautāt.';
				return 2;
			}
		}
		
	}
	
	echo 'nedrīkst';
	return 0;
	
}

function returnIssueDestination($conn, $issuance_id){
	
	$query = mysqli_query($conn, "SELECT destination FROM issuance_doc WHERE issuance_id='".$issuance_id."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	return $row['destination'];	
	
}

function checkIfPossibleBadRelease($conn, $issuance_id){

	$query = mysqli_query($conn, "SELECT linesForUpdate, linesForUpdateActual FROM release_row_count_logs WHERE issuance_id='".$issuance_id."'") or die(mysqli_error($conn));
	$row = mysqli_fetch_array($query);

	if(mysqli_num_rows($query)){
		if($row['linesForUpdate']!=$row['linesForUpdateActual']){
			return 'PLĀNOTĀS RINDAS: '.$row['linesForUpdate'].' IZDOTĀS RINDAS: '.$row['linesForUpdateActual'].' SAZINIETIES AR IZSTRĀDĀTĀJIEM';	
		}
	}

	return '';
		
}

function checkPossibleLinesInRelease($conn, $clientCode, $agreements, $issuance_id, $destination){
	
	$destz = null;
	if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }	

	$query = mysqli_query($conn, "
		SELECT cargo_line.id
		FROM cargo_line 

		LEFT JOIN cargo_header 
		ON cargo_line.docNr=cargo_header.docNr 
		 
		WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27'

			AND (
					(
					
						cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
						FROM cargo_line 
						LEFT JOIN cargo_header 
						ON cargo_line.docnr = cargo_header.docnr 
						WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
						AND cargo_line.status = '30' AND (issuance_id='".$issuance_id."' OR issuance_id='')) 
						
					) OR (
					
						cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
						FROM cargo_line 
						LEFT JOIN cargo_header 
						ON cargo_line.docnr = cargo_header.docnr 
						WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
						AND cargo_line.status = '30' AND (issuance_id='".$issuance_id."' OR issuance_id='')) 
							   
					 )
							   
				)

			AND (cargo_line.issuance_id='".$issuance_id."' OR cargo_line.issuance_id='')

		ORDER BY cargo_header.deliveryDate
	") or die (mysqli_error($conn));	
	
	$tot = mysqli_num_rows($query);
	
	return $tot;
}

function checkPossibleM3InRelease($conn, $clientCode, $agreements, $issuance_id, $destination){
	
	$destz = null;
	if($destination && $destination!='VISI'){ $destz = " AND substring_index(cargo_line.productNr,' ',1) LIKE '".$destination."%'"; }	

	$query = mysqli_query($conn, "
		SELECT SUM(cargo_line.cubicMeters) AS m3Tot
		FROM cargo_line 

		LEFT JOIN cargo_header 
		ON cargo_line.docNr=cargo_header.docNr 
		 
		WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27'

			AND (
					(
					
						cargo_line.status = '30' AND EXISTS(SELECT cargo_line.* 
						FROM cargo_line 
						LEFT JOIN cargo_header 
						ON cargo_line.docnr = cargo_header.docnr 
						WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
						AND cargo_line.status = '30' AND (issuance_id='".$issuance_id."' OR issuance_id='')) 
						
					) OR (
					
						cargo_line.status = '20' AND NOT EXISTS(SELECT cargo_line.* 
						FROM cargo_line 
						LEFT JOIN cargo_header 
						ON cargo_line.docnr = cargo_header.docnr 
						WHERE cargo_header.clientCode='".$clientCode."' AND cargo_header.agreements='".$agreements."' ".$destz." AND cargo_line.action!='23' AND cargo_line.action!='27' 
						AND cargo_line.status = '30' AND (issuance_id='".$issuance_id."' OR issuance_id='')) 
							   
					 )
							   
				)

			AND (cargo_line.issuance_id='".$issuance_id."' OR cargo_line.issuance_id='')

		ORDER BY cargo_header.deliveryDate
	") or die (mysqli_error($conn));	
	
	$tot = mysqli_fetch_array($query);
	
	return floatval($tot['m3Tot']);
}

function checkLinesScanned($conn, $issuance_id){
	
	$query = mysqli_query($conn, "SELECT id FROM scanner_lines_issuance WHERE issuance_id='".$issuance_id."' ") or die(mysqli_error($conn));
	
	$tot = mysqli_num_rows($query);
	
	return $tot;
	
}

function checkM3Scanned($conn, $issuance_id){
	
	$scanLinesM3 = mysqli_query($conn, "
	
		SELECT SUM(l.cubicMeters) as m3
		FROM cargo_line AS l

		JOIN scanner_lines_issuance AS s
		ON l.serialNo=s.serialNo
		
		WHERE s.issuance_id='".$issuance_id."'
												
	");	
	
	$rowSlM3 = mysqli_fetch_array($scanLinesM3);
	
	$m3 = $rowSlM3['m3'];	
	
	return floatval($m3);
	
}

function returnActiveShift($conn, $id){
	
	$query = mysqli_query($conn, "SELECT shift FROM scanner_lines_issuance WHERE issuance_id='".$id."' ORDER BY id DESC LIMIT 1");
	
	$tot = mysqli_fetch_array($query);
	
	return $tot['shift'];
	
}










//finish release on failed
function redoReleaseOnFail($conn, $issuance_id){
	
	$checkIssueance = mysqli_query($conn, "SELECT issueDate, actualDate, resource, thisTransport FROM issuance_doc WHERE issuance_id='".$issuance_id."'");
	
	$linesForUpdateActual=0;
	if(mysqli_num_rows($checkIssueance)==1){
		
		$iRow = mysqli_fetch_array($checkIssueance);
		
		$issueDateFinal = date('Y-m-d', strtotime($iRow['issueDate']));
		$actualDateFinal = date('Y-m-d', strtotime($iRow['actualDate']));

		$resource = mysqli_real_escape_string($conn, $iRow['resource']);
		
		$header_thisTransport = $iRow['thisTransport'];
		$header_declaration_type_no = $iRow['declaration_type_no'];

		$query = mysqli_query($conn, "SELECT * FROM cargo_line WHERE issuance_id='".$issuance_id."' and status=30");	
			
		while($row = mysqli_fetch_array($query)){

			$lineIdFinal = $row['id'];

			if($lineIdFinal){

				$issueAmountFinal = $row['issueAmount'];
				$issueAssistantAmountFinal = $row['issue_assistant_amount'];

				$e_place_count = $row['issue_place_count'];
				$eTare = $row['issueTare'];
				$eGross = $row['issueGross'];
				$eNet = $row['issueNet'];
				$eCubicMeters = $row['issueCubicMeters'];

				if(floatval($row['amount'])==$issueAmountFinal && date('Y-m-d', strtotime($row['issueDate']))==$issueDateFinal){

					$itt = null;
					if($row['issue_thisTransport']==''){
						$itt = $header_thisTransport;
					}else{
						$itt = $row['issue_thisTransport'];
					}
					
					$idtn = null;
					if($row['issue_declaration_type_no']==''){
						$idtn = $header_declaration_type_no;
					}else{
						$idtn = $row['issue_declaration_type_no'];
					}						
				
					$form_data = array(
					'issue_thisTransport' => $itt,
					'issue_declaration_type_no' => $idtn,
					'status' => 40,
					'action' => 40
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");	

										
					$getLines = mysqli_query($conn, "
					SELECT l.docNr AS docNr, l.productNr AS productNr, l.location AS location, h.clientCode AS clientCode, 
						   l.productUmo AS productUmo, l.assistantUmo AS assistantUmo, 
						   
						   l.tare AS tare, l.gross AS gross, l.net AS net, l.cubicMeters AS cubicMeters, l.serialNo AS serialNo, l.batchNo AS batchNo, 
						   l.lot_no AS lot_no, l.container_type_no AS container_type_no, l.thisTransport AS thisTransport, l.declaration_type_no AS declaration_type_no,
						   l.cargo_status AS cargo_status, l.seal_no AS seal_no, l.weighing_act_no AS weighing_act_no,
						 
						   l.issueTare AS issueTare, l.issue_place_count AS issue_place_count, l.issueGross AS issueGross, l.issueNet AS issueNet, l.issueCubicMeters AS issueCubicMeters,
						   l.issue_lot_no AS issue_lot_no, l.issue_container_type_no AS issue_container_type_no, l.issue_thisTransport AS issue_thisTransport,
						   l.issue_declaration_type_no AS issue_declaration_type_no, l.issue_cargo_status AS issue_cargo_status, l.issue_seal_no,
						   l.issue_weighing_act_no AS issue_weighing_act_no, l.fact_for_delta, l.real_delta,

						   h.clientName AS clientName, h.ownerCode AS ownerCode, 
						   h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName, h.cargoCode AS cargoCode, 
						   h.deliveryDate AS deliveryDate
					FROM cargo_line AS l
					JOIN cargo_header AS h
					ON l.docNr=h.docNr
					WHERE l.id='".intval($lineIdFinal)."'
					");
					while($gl = mysqli_fetch_array($getLines)){

						$form_data = array(
						'docNr' => $gl['docNr'],
						'cargoCode' => $gl['cargoCode'],	
						'productNr' => $gl['productNr'],
						'deliveryDate' => $gl['deliveryDate'],
						'activityDate' => $issueDateFinal,
						'type' => 'negative',
						'amount' =>  $issueAmountFinal,
						'assistant_amount' =>  $issueAssistantAmountFinal,
						'location' =>  $gl['location'],

						'clientCode' => $gl['clientCode'],
						'clientName' => $gl['clientName'],
						'ownerCode' => $gl['ownerCode'],
						'ownerName' => $gl['ownerName'],
						'receiverCode' => $gl['receiverCode'],
						'receiverName' => $gl['receiverName'],	

						'enteredDate' => date('Y-m-d H:i:s'),
						'orgLine' => intval($lineIdFinal),
						'enteredBy' => $myid,
						'action' => 40,
						'productUmo' => $gl['productUmo'],
						'assistantUmo' => $gl['assistantUmo'],
						'status' => 40,
						'resource' => $resource,
						'place_count' => $gl['issue_place_count'],
						'tare' => $gl['issueTare'],
						'gross' => $gl['issueGross'],
						'net' => $gl['issueNet'],
						'cubicMeters' => $gl['issueCubicMeters'],
						'serialNo' => $gl['serialNo'],
						
						'batchNo' => $gl['batchNo'],
						'lot_no' => $gl['issue_lot_no'],
						
						'container_type_no' => $gl['issue_container_type_no'],
						'thisTransport' => $gl['issue_thisTransport'],
						'declaration_type_no' => $gl['issue_declaration_type_no'],
						'cargo_status' => $gl['issue_cargo_status'],
						'seal_no' => $gl['issue_seal_no'],
						'weighing_act_no' => $gl['issue_weighing_act_no'],
						'issuance_id' => $issuance_id,
						'fact_for_delta' => $gl['fact_for_delta'],
						'real_delta' => $gl['real_delta']
						);
						insertNewRows("item_ledger_entry", $form_data);							
						
					}//while
					
					
					$linesForUpdateActual++;

				} // no change values

				if(floatval($row['amount'])!=$issueAmountFinal && $issueAmountFinal!=0 && date('Y-m-d', strtotime($row['issueDate']))==$issueDateFinal){

					$form_data = array(
					'issueAmount' => '',
					'issueBy' => $myid,
					'issueDate' => $issueDateFinal,
					'actualDate' => $actualDateFinal,
					'amount' => $row['amount']-$issueAmountFinal,
					'assistant_amount' => $row['assistant_amount']-$issueAssistantAmountFinal,

					'place_count' => $row['place_count']-$e_place_count,
					'tare' => $row['tare']-$eTare,
					'gross' => $row['gross']-$eGross,
					'net' => $row['net']-$eNet,
					'cubicMeters' => $row['cubicMeters']-$eCubicMeters, //uzliku eCubicMeters bija eNet??

					'issuance_id' => '',
					'status' => 20
					);
					updateSomeRow("cargo_line", $form_data, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");												

					$itt = null;
					if($row['issue_thisTransport']==''){
						$itt = $header_thisTransport;
					}else{
						$itt = $row['issue_thisTransport'];
					}
					
					$idtn = null;
					if($row['issue_declaration_type_no']==''){
						$idtn = $header_declaration_type_no;
					}else{
						$idtn = $row['issue_declaration_type_no'];
					}							
						
					$query = "
					INSERT INTO cargo_line
					  ( 
					  docNr,
					  batchNo,
					  serialNo,
					  productNr,
					  productUmo,
					  assistantUmo,
					  transportNo,
					  activityDate,
					  amount,
					  issueAmount,
					  assistant_amount,
					  issue_assistant_amount,
					  volume,
					  enteredDate,
					  location,
					  enteredBy,
					  thisDate,
					  issueBy,
					  issueDate,
					  actualDate,
					  resource,
					  thisTransport,
					  issue_thisTransport,
					  status,
					  editedBy,
					  editedDate,
					  orgLine,
					  issuance_id,
					  place_count,
					  tare,
					  gross,
					  net,
					  cubicMeters,
					  weighing_act_no,
					  container_type_no,
					  declaration_type_no,
					  issue_declaration_type_no,
					  cargo_status,
					  seal_no,
					  lot_no,
					  for_issue						  
					  )
					SELECT
					
					  docNr,
					  batchNo,
					  serialNo,
					  productNr,
					  productUmo,
					  assistantUmo,
					  transportNo,
					  activityDate,
					  '".$issueAmountFinal."',
					  '".$issueAmountFinal."',
					  '".$issueAssistantAmountFinal."',
					  '".$issueAssistantAmountFinal."',
					  volume,
					  enteredDate,
					  location,
					  enteredBy,
					  thisDate,
					  issueBy,
					  issueDate,
					  actualDate,
					  '".$resource."',
					  '".$itt."',
					  '".$itt."',
					  '40',
					  '".$myid."',
					  '".date('Y-m-d H:i:s')."',
					  '".intval($lineIdFinal)."',
					  '".$issuance_id."',
					  issue_place_count,
					  issueTare,
					  issueGross,
					  issueNet,
					  issueCubicMeters,
					  issue_weighing_act_no,
					  issue_container_type_no,
					  '".$idtn."',
					  '".$idtn."',
					  issue_cargo_status,
					  issue_seal_no,
					  issue_lot_no,
					  for_issue

					FROM cargo_line WHERE id='".intval($lineIdFinal)."'";
					mysqli_query($conn, $query) or die(mysqli_error($conn));						
		
					$getLines = mysqli_query($conn, "
						SELECT l.docNr AS docNr, l.productNr AS productNr, l.location AS location, h.clientCode AS clientCode, l.productUmo AS productUmo, l.assistantUmo AS assistantUmo, 
						
						l.tare AS tare, l.gross AS gross, l.net AS net, l.cubicMeters AS cubicMeters, l.serialNo AS serialNo, l.batchNo AS batchNo, 
						l.lot_no AS lot_no, l.container_type_no AS container_type_no, l.thisTransport AS thisTransport, l.declaration_type_no AS declaration_type_no,
						l.cargo_status AS cargo_status, l.seal_no AS seal_no, l.weighing_act_no AS weighing_act_no,	
						
						l.issueTare AS issueTare, l.issue_place_count AS issue_place_count, l.issueGross AS issueGross, l.issueNet AS issueNet, l.issueCubicMeters AS issueCubicMeters,
						l.issue_lot_no AS issue_lot_no, l.issue_container_type_no AS issue_container_type_no, l.issue_thisTransport AS issue_thisTransport,
						l.issue_declaration_type_no AS issue_declaration_type_no, l.issue_cargo_status AS issue_cargo_status, l.issue_seal_no,
						l.issue_weighing_act_no AS issue_weighing_act_no, l.fact_for_delta, l.real_delta,


						h.clientName AS clientName, h.ownerCode AS ownerCode, h.ownerName AS ownerName, h.receiverCode AS receiverCode, h.receiverName AS receiverName, h.cargoCode AS cargoCode, h.deliveryDate AS deliveryDate
						FROM cargo_line AS l
						JOIN cargo_header AS h
						ON l.docNr=h.docNr
						WHERE l.id='".intval($lineIdFinal)."'
					");
					while($gl = mysqli_fetch_array($getLines)){

						$form_data = array(
						'docNr' => $gl['docNr'],
						'cargoCode' => $gl['cargoCode'],	
						'productNr' => $gl['productNr'],
						'deliveryDate' => $gl['deliveryDate'],
						'activityDate' => $issueDateFinal,
						'type' => 'negative',
						'amount' =>  $issueAmountFinal,
						'assistant_amount' =>  $issueAssistantAmountFinal,
						'location' =>  $gl['location'],
						
						
						'clientCode' => $gl['clientCode'],
						'clientName' => $gl['clientName'],
						'ownerCode' => $gl['ownerCode'],
						'ownerName' => $gl['ownerName'],
						'receiverCode' => $gl['receiverCode'],
						'receiverName' => $gl['receiverName'],	

						
						'enteredDate' => date('Y-m-d H:i:s'),
						'orgLine' => intval($lineIdFinal),
						'enteredBy' => $myid,
						'action' => 40,
						'productUmo' => $gl['productUmo'],
						'assistantUmo' => $gl['assistantUmo'],
						'status' => 40,
						'resource' => $resource,
						'place_count' => $gl['issue_place_count'],
						'tare' => $gl['issueTare'],
						'gross' => $gl['issueGross'],
						'net' => $gl['issueNet'],
						'cubicMeters' => $gl['issueCubicMeters'],
						'serialNo' => $gl['serialNo'],
						'batchNo' => $gl['batchNo'],
						'lot_no' => $gl['issue_lot_no'],
						'container_type_no' => $gl['issue_container_type_no'],
						'thisTransport' => $gl['issue_thisTransport'],
						'declaration_type_no' => $gl['issue_declaration_type_no'],
						'cargo_status' => $gl['issue_cargo_status'],
						'seal_no' => $gl['issue_seal_no'],
						'weighing_act_no' => $gl['issue_weighing_act_no'],
						'issuance_id' => $issuance_id,
						'fact_for_delta' => $gl['fact_for_delta'],
						'real_delta' => $gl['real_delta']	

						);
						insertNewRows("item_ledger_entry", $form_data);	

						$form_data2 = array(
							'issue_place_count' => 0,
							'issueTare' => 0,
							'issueGross' => 0,
							'issueNet' => 0,
							'issueCubicMeters' => 0,
						
							
							'issue_weighing_act_no' => '',
							'issue_container_type_no' => '',
							
							'issue_cargo_status' => '',
							'issue_seal_no' => '',
							'issue_lot_no' => '',
							'for_issue' => 0,
							'fact_for_delta' => 0,
							'real_delta' => 0
						);
						updateSomeRow("cargo_line", $form_data2, "WHERE id='".intval($lineIdFinal)."' LIMIT 1");
						
					}		

					$linesForUpdateActual++;					
					
				}//values changed
				
			}//if($lineIdFinal)	

		}//for loop
		
	}

	return $linesForUpdateActual;
	
}

function actDocumentAmmounts($conn, $id, $productNr, $weightFormat){
	
	$query = mysqli_query($conn, "
		SELECT SUM(l.gross) AS sumGross, SUM(l.net) AS sumNet, 
		GREATEST(COUNT(l.thisTransport), COUNT(l.gross), COUNT(l.net)) AS maxLines
		FROM cargo_header AS h
		JOIN cargo_line AS l
		ON h.docNr=l.docNr
		JOIN n_items AS n
		ON l.productNr=n.code
		
			JOIN n_resource AS nr
			ON l.resource=nr.id				
		
		WHERE h.id='".intval($id)."' AND l.productNr='".$productNr."'
		order by l.productNr, l.thisTransport
	");

	$row = mysqli_fetch_array($query);

	$return = [];
	if($row['maxLines']>0){
		$return['max'] = $row['maxLines'];
	}	
	if($row['sumGross']>0){
		$return['gross'] = 'Brt. svars kopā, '.$weightFormat.' <b>'.floatval($row['sumGross']).'</b>';
	}
	if($row['sumNet']>0){
		$return['net'] = 'Net. svars kopā, '.$weightFormat.' <b>'.floatval($row['sumNet']).'</b>';
	}

	return $return;

}







ini_set('max_execution_time', 300);
?>
