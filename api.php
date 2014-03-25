<?php


//api.php

/*
<userKey>56200466507949522562004665079495</userKey>
<storeUrl>www.jocksntees.com</storeUrl>

*/

//Send data to the 3dcart api to query the database. 


$delimiter = $_REQUEST['delimiter'];  #  '\t'  tab  '|' pipe ',' comma
if(empty($delimiter)){
    $delimiter = ",";
}
$sql_query = $_REQUEST['sql_query'];  
if(empty($sql_query)){
    $sql_query = "";
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
    $fp = fopen('./csv/test.csv', 'w');
        foreach ($json_obj as $row) {
            //fputcsv($fp, $row);
			fputcsv($fp, $row, $delimiter);
		    echo $row;
        }
    fclose($fp);
    echo "</pre>";
} else {
    //
    // prints out error message
	
    echo "ERROR \n";
	echo $result[0];
}





?>