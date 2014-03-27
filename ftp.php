<?php
/*
Host: ftp.pointbweb.net
Login: test@pointbweb.net
PW: pbw#test1


*/
print_r($_POST);

 $ftp_server=$_POST['hostname']; 
 $ftp_username=$_POST['username']; 
 $ftp_password=$_POST['password']; 
 $file = $_POST['file_to_send'];//tobe uploaded 
 
 $remote_file = $file; 

 // set up basic connection 
 $conn_id = ftp_connect($ftp_server); 

 // login with username and password 
 $login_result = ftp_login($conn_id, $ftp_username, $ftp_password); 

 // upload a file 
 //if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) { 
 if (ftp_put($conn_id, $file, "csv/".$file, FTP_ASCII)) { 
    
	echo "successfully uploaded $file\n"; 
	//then put unlink to file here to delete the file
    exit; 
	
 } else { 
	 
    echo "There was a problem while uploading $file\n"; 
    exit; 
    
	} 
 // close the connection 
 ftp_close($conn_id); 
	
	
	
	
	
	
	
	
?>