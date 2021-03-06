<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
		   
        <h3>Orders CSV Report Generator</h3>
       <form name="orders" action="api.php" method="post" id="orders">
            From: <input type="text" name="from_date" id="from_date" title="from date" />		
			To:    <input type="text" name="to_date" id="to_date" title="to date" />	
			<br /><br />
			
			Status <!-- ><div id="showresults" style="display:inline;"></div> -->
			
			<?php include_once("status.php"); ?>
			
				   
				   <br/><br/>
				   
			CSV Output <select name="csv_output" id="csv_output" title="vendor" >
				     <option value="" selected>SELECT</option>
                     <option value="hanes">Hanes</option>
					 <option value="loom" >Fruit of the Loom</option>
					 <option value="xpert" >Xpert</option>
 
                   </select>	   
				   
				   <br/><br/>
				   
			CSV Delimiter  <select name="delimiter" id="delimiter" title="delimiter">
                     <option value="," selected>Comma</option>
					  <option value="\t" >Tabs</option>
					   <option value="|" >Pipe (|)</option>
 
                   </select>	   
		   
		   <br /><br />
		   
		    File Name <input type="text" name="file_name" id="file_name" title="file_name"  style="width: 240px;"/>
			<br /><br />
			
			
		<input type="checkbox" name="ad_hoc" id="ad_hoc" value="ad_hoc">Ad Hoc Query?<br>  

			<textarea name="sql_query" id="sql_query" style="width: 340px; height: 140px;">
				
			</textarea>

        <p><input type="submit" id="btn btn-primary btn-lg" name="btn btn-primary btn-lg" role="button" value="Run Query"></submit></p>
      
	  <br /><br />
	  CSV Header:
	  			<br />
	  			<textarea name="csv_header" id="csv_header" style="width: 640px; height: 140px;">
	  			</textarea>
	
	  </form>
	  <hr>
	  
	
	  
	  <div id="viewer" style="display: none;" class="loader" ><h3>CSV Viewer:</h3></div>
	  <div id="csv_viewer" style="width:640px; height:400px; overflow:scroll; display: none;"></div>
	  
	  <br />
	  
	  
	  
	  <p><a class="btn btn-primary btn-lg" role="button"  id="download" name="download" href="" style="display:none;"  download>Download &raquo;</a></p>
	  
	  
	  <hr>



      
	  <form id="ftp" name="ftp" action="ftp.php" method="post" style="display: none;">
	  
	  Username: <input type="text"  name="username" id="username" text="username" style="margin-left: 10px;" /><br />
	  Password: <input type="password"  name="password" id="password" text="password" style="margin-left: 12px;" /><br />
	  Hostname: <input type="text" name="hostname" id="hostname" text="hostname" style="margin-left: 10px;" /><br /><br />
	  <input type="hidden"  name="file_to_send" id="file_to_send">
	         <p><input type="submit" id="btn btn-primary btn-lg" name="btn btn-primary btn-lg" role="button" value="Send via FTP"></submit></p>
	  
	  </form>
	  <div id="ftp_status"></div>
	  
	  
	  
	   </div>
	   
	
	  
    </div>

    <div class="container">
      <!-- Example row of columns -->

      </div>

      <hr>

      <footer>
        <p>&copy; Company 2014</p>
      </footer>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>
		
		
		<script>
		
		
		
		
		//compiles the status list for the status dropdown ie. "processing", created by status.php
		$( document ).ready(function() {
		
		  $.ajax({
		        url: "status.php",
		        data: {
		            
		        },
		        type: "GET",
		        dataType: "html",
		        success: function (data) {
		            //alert(data);
		            $('#showresults').html(data);
		        },
		        error: function (xhr, status) {
		            alert("Sorry, there was a problem!");
		        },
		        complete: function (xhr, status) {
		            //$('#showresults').slideDown('slow')
		        }
		    });
		});
		
		
$( document ).ready(function() {
	
    var today = new Date();
    var day = today.getDate();
    var month = today.getMonth();
    var minutes = today.getMinutes();
    var secs = today.getSeconds();
    var hour = today.getHours();
    var year = today.getFullYear();
    var outputter = $("#csv_output").val();


    //YYYY-MM-DD-hhmmss-name.csv (e.g. 2014-03-26-121507-Hanes.csv
    var file_name = year + "-" + month + "-" +day+ "-" +hour+minutes+secs+"-"+outputter+".csv";
   
    $("#file_name").val(file_name);
	$("#download").attr("href", "csv/"+file_name);
	$("#file_to_send").val(file_name);

		
		$("#ad_hoc").change(function(e)
		{
          if ($(this).is(':checked')) {
			  
            //$("#sql_query").val(" ");
			//$("#csv_header").val(" ");
		    file_name = year + "-" + month + "-" +day+ "-" +hour+minutes+secs+"-ad_hoc.csv";
			$("#file_name").val(file_name);
			$("#file_to_send").val(file_name);
			$("#download").attr("href", "csv/"+file_name);
          }
		   	
			
		});
		
	    $("#file_name").change(function(e)
		{
		    
		    var file_name = $("#file_name").val();
			$("#file_to_send").val(file_name);
			//alert(file_name);
			$("#download").attr("href", "csv/"+file_name);
			//need to update download href	
			
		});
		
		
		$("#status").change(function(e)
		{
		   $('#csv_output').trigger('change');
			
		});
		
		
		$("#csv_output").change(function(e)
		{
		    var csv_output = $(this).val();
			var csv_sql = "";
			var csv_header = "";
			var order_status = $("#status").val();
			//alert(order_status);
			
			//alert(csv_output);
			//alert($(this).val());	
			if(csv_output == 'xpert'){
				csv_sql = "select o.orderid, o.invoicenum_prefix, o.invoicenum, oi.itemid, oi.numitems, o.odate, o.oshipfirstname, o.oshiplastname, o.oshipaddress, o.oshipaddress2, o.oshipcity, o.oshipstate, o.oshipzip, o.oshipphone, o.oshipemail, o.ocomment   from orders as o, oitems as oi where o.orderid = 27 AND oi.orderid = o.orderid";
				csv_header = "Order Number,SKU,Quantity,Order Date,First Name,Last Name,Address Line 1,Address Line 2,City,State,Zip,Telephone,E-mail,Notes";
				$("#sql_query").val(csv_sql);
				$("#csv_header").val(csv_header);
				
			    file_name = year + "-" + month + "-" +day+ "-" +hour+minutes+secs+"-xpert.csv";
				$("#file_name").val(file_name);
				$("#file_to_send").val(file_name);
				$("#download").attr("href", "csv/"+file_name);
				
			} else if(csv_output == 'loom'){
				csv_sql = "select p.mfgid, ao.AO_Name, oi.itemname from oitems as oi, products as p, options_Advanced as ao WHERE oi.catalogid = p.catalogid AND ao.ProductID = oi.catalogid";
				csv_header = "Header Here";
				$("#sql_query").val(csv_sql);
				$("#csv_header").val(csv_header);
			    file_name = year + "-" + month + "-" +day+ "-" +hour+minutes+secs+"-loom.csv";
				$("#file_name").val(file_name);
				$("#file_to_send").val(file_name);
				$("#download").attr("href", "csv/"+file_name);
				
			} else {
				csv_sql = "SELECT DISTINCTROW 'O' as recordtype, oitems.itemid AS sku, Sum(oitems.numitems) AS qty FROM orders INNER JOIN oitems ON orders.orderid = oitems.orderid GROUP BY oitems.itemid, orders.odate, orders.order_status AND ((orders.order_status)="+order_status+")";
				csv_header = "Record Type|PO Number|Address Type|First Name|Last Name|Address 1|Address 2|City|State|Zip|Phone|Email\nRecord Type|UPC|Quantity|||||||||\nS|JNT1|R|Eric Dash|jocksntees Inc|c/o Xpert Fulfillment|8160 Cadillac Hwy|Benzonia|MI|49616|(206)588-2558|ericdash@comcast.net\n";
				$("#sql_query").val(csv_sql);
				$("#csv_header").val(csv_header);
			    file_name = year + "-" + month + "-" +day+ "-" +hour+minutes+secs+"-hanes.csv";
				$("#file_name").val(file_name);
				$("#file_to_send").val(file_name);
				$("#download").attr("href", "csv/"+file_name);
				
			}
			
			
		});
			
			$("#orders").submit(function(e)
			{
				$("#csv_viewer").show();
				$("#viewer").show();
				$("#download").show();
				$("#ftp").show();
				$("#csv_viewer").css("background-color","#000");
				$("#csv_viewer").css("color","#fff");
				$("#csv_viewer").css("font-family", "Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New");
				
				
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
				
				
			    $.ajax(
			    {
					url : "api.php",
			        //url : "http://admin.jocksntees.com/api.php",
					//crossDomain : true,
					//dataType: "jsonp",
			        type: "POST",
			        data : postData,
			        success:function(data, textStatus, jqXHR) 
			        {
			            //data: return data from server
						//alert(data);
						$('#csv_viewer').html(data);
						$(".loader").fadeOut("slow");
						//callFunction(data);
			        },
			        error: function(jqXHR, textStatus, errorThrown) 
			        {
			            
						alert(errorThrown);
						alert("orders query failed to be sent to server");    
			        }
			    });
			    e.preventDefault(); //STOP default action
			    //e.unbind(); //unbind. to stop multiple form submit.
			});
			
			
			
			
			$("#ftp").submit(function(e)
			{
				
			    var postData = $(this).serializeArray();
			    var formURL = $(this).attr("action");
				var file_to_send = $("#file_name").val();
				//alert(file_to_send);
				
				$("#file_to_send").val(file_to_send);
				//alert($("#file_to_send").val());
				
			    $.ajax(
			    {
			        url : formURL,
			        type: "POST",
			        data : postData,
			        success:function(data, textStatus, jqXHR) 
			        {
			            //data: return data from server
						$('#ftp_status').html(data);
			        },
			        error: function(jqXHR, textStatus, errorThrown) 
			        {
			            //if fails      
			        }
			    });
			    e.preventDefault(); //STOP default action
			    //e.unbind(); //unbind. to stop multiple form submit.
			});
			
			
			
			function callFunction(data){
			 $('#csv_viewer').innerHTML = data;
			}
			
			
			
			
		
	}); //ends document ready func
		</script>
		
		

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
    </body>
</html>
