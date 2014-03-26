<?php


//api.php

/*
<userKey>56200466507949522562004665079495</userKey>
<storeUrl>www.jocksntees.com</storeUrl>

*/

//Send data to the 3dcart api to query the database. 

$uid = uniqid();

$delimiter = $_REQUEST['delimiter'];  #  '\t'  tab  '|' pipe ',' comma
if(empty($delimiter)){
    $delimiter = ",";
}
$sql_query = $_REQUEST['sql_query'];  
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

if(!empty($from_date) && !empty($to_date) ) {

	$date_sql = " AND orders.odate BETWEEN \"".$from_date."\" AND \"".$to_date."\" ";
	
	
} else {
	$date_sql = "";
}

switch ($csv_output) {
    case 'default':
        echo "default  case (ad hoc query) \n";
		//$sql_query = $sql_query." ".$date_sql;
		$sql_query = $sql_query;
		$csv_header = "";
		/*
		get order status list
		select order_Status.StatusText from order_Status, orders where orders.status = order_Status.id
		select order_Status.StatusText from order_Status, orders where orders.status = order_Status.StatusId
		
		select distinct order_Status.StatusText from order_Status

		
		
select odate from orders where odate BETWEEN  CAST('2014-01-01' AS DATETIME)
                        AND CAST('2014-01-31' AS DATETIME);
		
		*/
        break;
    case 'loom':
        echo "fruit of the looms case \n";
		$sql_query = "select p.mfgid, ao.AO_Name, oi.itemname from oitems as oi, products as p, options_Advanced as ao WHERE oi.catalogid = p.catalogid AND ao.ProductID = oi.catalogid ";
		$csv_header = "";
	
		
        break;
    case 'xpert':
        echo "xpert case \n";
		$sql_query = "select o.orderid, o.invoicenum_prefix, o.invoicenum, oi.itemid, oi.numitems, o.odate, o.oshipfirstname, o.oshiplastname, o.oshipaddress, o.oshipaddress2, o.oshipcity, o.oshipstate, o.oshipzip, o.oshipphone, o.oshipemail, o.ocomment   from orders as o, oitems as oi where o.orderid = 27 AND oi.orderid = o.orderid ";
		$csv_header = "order_number".$delimiter." sku".$delimiter." qty".$delimiter." order_date".$delimiter." first_name".$delimiter." last_name".$delimiter." address_1".$delimiter." address_2".$delimiter." city".$delimiter." state ".$delimiter."zip ".$delimiter."telephone ".$delimiter."email ".$delimiter." notes";
		
        break;
    case 'hanes':
	   echo "hanes case \n";
	$sql_query = "select oi.itemid, oi.numitems from oitems as oi";
	$csv_header = "SKU, Quantity";
	
	   break;
}

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


// write your SQL statements
//$sql ="select * from orders where orders.orderid = 27;";
//
// call soap function.
$result = soap_call($sql_query);

//
// checks for result set or error return
if (is_array($result['0'])) {	
    $delimiter = $delimiter;
    $result = json_encode($result);
    //print_r($result);
    $json_obj = json_decode($result, true);
	
    //chmod("csv/'.$uid.'.csv, 0775);
	$fp = fopen('csv/'.$uid.'.csv', 'w');

	$file = 'csv/'.$uid.'.csv';
	
	$current = file_get_contents($file);
	
	$text = $csv_header . "\n";
	
	$current .= $text;
	$field_cnt = count(split($delimiter, $csv_header));
	echo "field count ".$field_cnt;
	//print_r($json_obj);
	
	
	echo $json_obj[0]['itemid'];
	echo $json_obj[0]['numitems'];
	//echo count($json_obj);
	
	//put in delimiter and write to file then display file for preview
	$cnt = 0;
	for($cnt = 0; $cnt<count($json_obj); $cnt++){
		echo $json_obj[$cnt]['itemid'];
		echo " ";
		echo $json_obj[$cnt]['numitems'];
		echo "<br />";
		
		
		
	}
	
	fputcsv($fp, $current, $delimiter);
	
	$file = file_get_contents('csv/'.$uid.'.csv', true);
	//echo $file;  //output to csv preview div
  
} else {
    //
    // prints out error message
	
    echo "ERROR \n";
	echo $result[0];
}





?>