<?php
/*
SELECT DISTINCTROW oitems.itemid AS sku, Sum(oitems.numitems) AS qty
FROM orders INNER JOIN oitems ON orders.orderid = oitems.orderid
GROUP BY oitems.itemid, orders.odate, orders.order_status
HAVING (((orders.odate) Between #3/1/2014# And #3/31/2014#) AND ((orders.order_status)=7))


Hanes no add-on criteria:
SELECT DISTINCTROW oitems.itemid AS sku, Sum(oitems.numitems) AS qty
FROM orders INNER JOIN oitems ON orders.orderid = oitems.orderid
GROUP BY oitems.itemid, orders.odate, orders.order_status


*/


function setDateSql($from_date, $to_date, $model){
	if(empty($from_date) || empty($to_date)){
		$date_sql = "";
		return $date_sql;
	}
	
	switch ($model) {
	    case 'default':
			$date_sql = " HAVING (((orders.odate) Between #".$from_date."# And #".$to_date."#)";
			break;

	    case 'loom':
			$date_sql = " HAVING (((orders.odate) Between #".$from_date."# And #".$to_date."#)";		
			break;
		
	    case 'xpert':
			$date_sql = " HAVING (((orders.odate) Between #".$from_date."# And #".$to_date."#)";
			break;
		
	    case 'hanes':
		   $date_sql = " HAVING (((orders.odate) Between #".$from_date."# And #".$to_date."#)";	
		   break;
	}
	return $date_sql;
	
}

function setStatusSql($status, $model){
	
	if(empty($status) || empty($model)){
		$status = "";
		return $status;
	}
	
	switch ($model) {
	    case 'default':
			$status = " AND ((orders.order_status)=".$status.") ";
			break;

	    case 'loom':
			$status = " AND ((orders.order_status)=".$status.") ";	
			break;
		
	    case 'xpert':
			$status = " AND ((orders.order_status)=".$status.") ";
			break;
		
	    case 'hanes':
		
		   $status = " AND ((orders.order_status)=".$status.") ";
		   break;
	}	
	return $status;
	

}


//query the api 
function soap_call($sql) {
	// build parameters for call
	$param = array(
	'storeUrl'=>"www.jocksntees.com",
	'userKey'=>"56200466507949522562004665079495",
	'sqlStatement'=>$sql
	);
	// check if soap class is active.  if not, load soap class.
	if (!isset($client)) {
		$client = new soapclient('http://api.3dcart.com/cart_advanced.asmx?WSDL', array('trace' => 1,'soap_version' => SOAP_1_1));
	}
	$result = $client->runQuery($param);
	// parse results.  Return error code or array of results.
	if (is_soap_fault($result)) {
		$array['0'] = "Soap Fault";
		$array = array_merge($array, htmlspecialchars($result, ENT_QUOTES));
	} else {
		$pos = strpos($client->__getLastResponse(), "Error");
		if ($pos == true) {
			$array['0'] =  "SQL Error: " . $client->__getLastResponse();
		} else {
			// create an array from soap call.
			$netresult = $result->runQueryResult->any;
			$netresult = str_replace("<![CDATA[", '', $netresult);
			$netresult = str_replace("]]>", '', $netresult);			
			$array=json_decode(str_replace(':{}',':null', json_encode(simplexml_load_string($netresult,null,LIBXML_NOCDATA))),true);
		}
	}
	if (array_key_exists('0',$array)) {
		return $array;
	} elseif (array_key_exists('0',$array['runQueryRecord'])) {
		return $array['runQueryRecord'];
	} else {
		$result_array['0'] = $array['runQueryRecord'];
		return $result_array;
	}
}




$file_name = $_REQUEST['file_name'];  #  '\t'  tab  '|' pipe ',' comma
if(empty($file_name)){
    $file_name = " ";
}
$uid = $file_name;


$delimiter = $_REQUEST['delimiter'];  #  '\t'  tab  '|' pipe ',' comma
if(empty($delimiter)){
    $delimiter = ",";
}

$sql_query = trim($_REQUEST['sql_query']);  
if(empty($sql_query)){
    $sql_query = "";
}

$csv_output = $_REQUEST['csv_output'];  
if(empty($csv_output)){
    $csv_output = "default";
}

$ad_hoc = $_REQUEST['ad_hoc'];  
if(!empty($ad_hoc)){
    $csv_output = "default";
}

//need to handle date ranges for each csv_output case
$from_date = $_REQUEST['from_date'];  
if(empty($from_date)){
    $from_date = "";
} 

$to_date = $_REQUEST['to_date'];  
if(empty($to_date)){
    $to_date = "";
}

$status = $_REQUEST['status'];  
if(empty($status)){
    $status = "";
}

$csv_header = trim($_REQUEST['csv_header']);  
if(empty($csv_header)){
    $csv_header = "";
}



//need a switch to deal with swapping out delimiter of pre-defined csv headers
//str_replace($orig_delimiter,$delimiter,$csv_header);
if (strpos($csv_header,'\t') !== false) {
	$orig_delimiter = '\t';
	$delimiter = "\t";
	$csv_header = str_replace($orig_delimiter,$delimiter,$csv_header);
    
} elseif (strpos($csv_header,'|') !== false){
	$orig_delimiter = '|';
	$csv_header = str_replace($orig_delimiter,$delimiter,$csv_header);
} else {
	$orig_delimiter = ',';
	$csv_header = str_replace($orig_delimiter,$delimiter,$csv_header);
	
}




//csv_output is whether vendor is hanes, fruit of the loom ("loom"), xpert or an ad_hoc (default) query
switch ($csv_output) {
    case 'default':
	    $date_sql = setDateSql($from_date, $to_date, 'default');
	    $status = setStatusSql($status, 'default');
		$sql_query = $sql_query . $date_sql . $status;
		echo $sql_query;
		break;

		
    case 'loom':
		//$sql_query = "select p.mfgid, ao.AO_Name, oi.itemname from oitems as oi, products as p, options_Advanced as ao WHERE oi.catalogid = p.catalogid AND ao.ProductID = oi.catalogid ".$date_sql . $status;
	    $date_sql = setDateSql($from_date, $to_date, 'loom');
	    $status = setStatusSql($status, 'loom');
		$sql_query = $sql_query . $date_sql . $status;
		echo $sql_query;
		
		break;
		
    case 'xpert':
		//$sql_query = "select o.orderid, o.invoicenum_prefix, o.invoicenum, oi.itemid, oi.numitems, o.odate, o.oshipfirstname, o.oshiplastname, o.oshipaddress, o.oshipaddress2, o.oshipcity, o.oshipstate, o.oshipzip, o.oshipphone, o.oshipemail, o.ocomment   from orders as o, oitems as oi where o.orderid = 27 AND oi.orderid = o.orderid ";
		//$csv_header_temp = "order_number".$delimiter." sku".$delimiter." qty".$delimiter." order_date".$delimiter." first_name".$delimiter." last_name".$delimiter." address_1".$delimiter." address_2".$delimiter." city".$delimiter." state ".$delimiter."zip ".$delimiter."telephone ".$delimiter."email ".$delimiter." notes";
	    $date_sql = setDateSql($from_date, $to_date, 'expert');
	    $status = setStatusSql($status, 'expert');
		$sql_query = $sql_query . $date_sql . $status;
		echo $sql_query;
		break;
		
    case 'hanes':
	    $date_sql = setDateSql($from_date, $to_date, 'hanes');
	    $status = setStatusSql($status, 'hanes');
		$sql_query = $sql_query . $date_sql . $status;
		echo $sql_query;
	   break;
}





// call soap function.
$result = soap_call($sql_query);

//
// checks for result set or error return
if (is_array($result['0'])) {	
    $delimiter = $delimiter;
    $result = json_encode($result);

    $json_obj = json_decode($result, true);
	$file = 'csv/'.$uid;
	$text = trim($csv_header) . "\n";
	//echo "TEXT ".$text;

	$cnt = 0;
	$lines = array();
	$read_lines = array();
	$line = "";
    
	foreach($json_obj as $jo){
     
		$jo_keys = array_keys($jo);
		if($csv_output == 'default'){
			//$text = $jo_keys;
			//$text = implode($delimiter,$text);
			//$text = $text . " \n";
		}
		
		$keys_len = count($jo);
		$c = 0;
		foreach($jo_keys as $jk){

			if($c < $keys_len-1){
				
				$line .=  $jo[$jk] . $delimiter;
			} else {
				$line .= $jo[$jk] . " \n";
			}
			//echo $line;
			$c++;	
		}
	}
	array_push($lines, $line);
	array_unshift($lines, $text);
	$result = array_merge((array)$lines, (array)$read_lines);
	
	$fp = fopen('csv/'.$uid, 'w');
	echo "<pre>";
	foreach($lines as $l){
		$l_arr = array();
		array_push($l_arr, $l);
		echo $l;
		$l_arr = implode($delimiter, $l_arr);
		fwrite($fp, $l);
		
	}

	echo "</pre>";
	//echo $delimiter;
	//$lines = implode($delimiter, $l_arr);
	//print_r($lines);
	//file_put_contents($fp, $lines);
	//fwrite($fp, $lines);

	
} else {
    //
    // prints out error message
	
    echo "ERROR \n";
	echo $result[0];
}





?>