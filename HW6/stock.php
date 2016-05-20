<!DOCTYPE html>
<html>
    <head>     
        <style type="text/css">
            body {
                font-size:15px;
            }   
            #form {
                margin: 0 auto;
                width: 400px;
                border: 1px solid #DDDDDD;
                background-color: #F5F5F5;
            }
            #stock_search {
                font-size:30px;
                width: 380px;
                margin-left:10px;
                font-style: italic;
                text-align: center;
                border-bottom: 1px solid #DDDDDD;
            }
            #button {
                margin-top:5px;
            }
            
            #xml_table {
                margin: 0 auto;
                background-color: #FCFCFB;
                border: 1px solid #DFDFDF;
                border-spacing: 0;
                width:600px;
            }
            #xml_th th {
                background-color: #F5F5F5;
                border: 0.5px solid #DFDFDF;
                text-align: left;
                padding-top:5px;
                padding-bottom:5px;
            }
            #xml_tr td {
                border: 0.5px solid #DFDFDF;
                padding-top:5px;
                padding-bottom:5px;
            }
            
            #json_table {
                margin: 0 auto;
                border: 1px solid #DDDDDD;
                border-spacing: 0;
                width:600px;
            }
            #json_table td {
                border: 0.5px solid #DDDDDD;
                padding-top:5px;
                padding-bottom:5px;
            }
            #json_table .left {
                background-color: #F5F5F5;
                font-weight:bold;
            }
            #json_table .right {
                background-color: #FCFCFB;
                text-align:center;
            }
            #fail1 {
                margin: 0 auto;
                text-align: center;
                width:600px;
                padding-top:5px;
                padding-bottom:5px;
                background-color: #FCFCFB;
                border: 1px solid #DFDFDF;
            }
            #fail2 {
                margin: 0 auto;
                text-align: center;
                width:600px;
                padding-top:5px;
                padding-bottom:5px;
                background-color: #FCFCFB;
                border: 1px solid #DFDFDF;
            }
        </style>
    </head>
        <title>Homework6</title>
    <body> 
        
        <script language="javascript">
                    
            function empty() // empty search box
            {
                var input_text = document.getElementById("input_text").value;
                if(input_text == null || input_text == "")
                {
                    // document.getElementById("input_text").setCustomValidity('Please enter Name or Symbol!!!');
                    
                    alert("Please enter Name or Symbol");
                    return false;
                }   
            }
            
            
            function clean() // reset button
            {
                // not allowed to reload page
                document.getElementById("input_text").value = "";
                
                if(document.getElementById("xml_table"))
                {
                    var x = document.getElementById("xml_table");
                    x.parentNode.removeChild(x);
                }
                else if(document.getElementById("fail1"))
                {
                    var f1 = document.getElementById("fail1");    
                    f1.parentNode.removeChild(f1);
                }
                else if(document.getElementById("json_table"))
                {
                    var j = document.getElementById("json_table");     
                    j.parentNode.removeChild(j);
                }
                 else if(document.getElementById("fail2"))
                {
                    var f2 = document.getElementById("fail2");    
                    f2.parentNode.removeChild(f2);
                }
                    
                // location.href ="stock.php";
                
                return 0;
            }
            
            // x-moz-errormessage="Please enter Name or Symbol"
        </script>

        <div id ="form">
            <form action="stock.php" onsubmit="return empty()" method=POST action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div id = "stock_search">Stock Search</div><br>
                &nbsp;Company Name or Symbol: <input type="text" id="input_text" name="name" 
                    required oninvalid="this.setCustomValidity('Please enter Name or Symbol')"
    oninput="setCustomValidity('')" value="<?php
                    if(isset($_GET['name'])){
                        echo $_GET['name']; 
                    }
                    if(isset($_POST['name'])){
                        echo $_POST['name'];
                    }
                 ?>"><br>
                
                
                <div id = "button">
                    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp;
                    <input type="submit" id="submit" value="search" >
                    <input type="button" id="reset" value="clear" onClick="clean()"><br>
                </div>
                &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                <a href="http://www.markit.com/product/markit-on-demand">Powered by Markit on Demand</a>
                <br><br>
                
                
            </form>
        </div>
        <br><br>
        <?php
        
            if(isset($_POST['name']))
            {
                $name = $_POST['name'];
                $xml_url = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/xml?input=$name";
                $xml = simplexml_load_file($xml_url);
                
                if($xml->children()->getName() == "LookupResult") {
                    echo "<table id='xml_table'>";
                    echo "<tr id='xml_th'>";       
                    echo "<th>Name</th>";
                    echo "<th>Symbol</th>";
                    echo "<th>Exchange</th>";
                    echo "<th>Details</th>";
                    echo "</tr>";

                    foreach($xml->children() as $LookupResult)
                    {
                        echo "<tr id = 'xml_tr'>";       
                        echo "<td>".$LookupResult->Name."</td>";
                        echo "<td>".$LookupResult->Symbol."</td>";
                        echo "<td>".$LookupResult->Exchange."</td>";

                        echo "<td><a href='stock.php?symbol=$LookupResult->Symbol&name=$name'>More Info</a></td>";

                        echo "</tr>";
                    }
                    echo "</table>";
                }
                else if($_POST['name'] == '')
                {
                    
                }
                else
                    echo "<div id='fail1'>No Records has been found</div>";
            }
        ?>
         
        <?php
            
            if(isset($_GET['symbol'])) 
            {      
                $symbol = $_GET['symbol'];
                $name = $_GET['name'];
                $json_url = "http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=$symbol";
                $contents = file_get_contents($json_url); 
                $contents = utf8_encode($contents); 
                $json_result = json_decode($contents); 
            
                if($json_result->Status == "SUCCESS") {
                    
                    $change = number_format($json_result->Change,2);
                    $changepercent = number_format($json_result->ChangePercent,2);
                    
                    
                    if($json_result->MarketCap >= 5000000) {
                        $marketcap = number_format($json_result->MarketCap/1000000000,2);
                        $marketcap = $marketcap." B";
                    }
                    else {
                        $marketcap = number_format($json_result->MarketCap/1000000,2);
                        $marketcap = $marketcap." M";
                    }
                    
                    
                    
                    $volume = number_format($json_result->Volume);
                    $changeYTD = number_format($json_result->LastPrice-$json_result->ChangeYTD,2);
                    $changepercentYTD = number_format($json_result->ChangePercentYTD,2);
                    
                    date_default_timezone_set('America/Los_Angeles');
                    $time = strtotime($json_result->Timestamp);
                    $pacific_time = date("Y-m-d h:i A T", $time);
                    
                    echo "<table id='json_table'>";

                    echo "<tr>";       
                    echo "<td class='left'>Name</td>";
                    echo "<td class='right'>".$json_result->Name."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Symbol</td>";
                    echo "<td class='right'>".$json_result->Symbol."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Last Price</td>";
                    echo "<td class='right'>".$json_result->LastPrice."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Change</td>";
                    if ($change < 0)
                        echo "<td class='right'>".$change."<img src='Red_Arrow_Down.png' width=12px height=12px></td>";
                    else if ($change > 0)
                        echo "<td class='right'>".$change."<img src='Green_Arrow_Up.png' width=12px height=12px></td>";
                    else
                        echo "<td class='right'>".$change."</td>"; 
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Change Percent</td>";          
                    if ($changepercent < 0)
                        echo "<td class='right'>".$changepercent."%<img src='Red_Arrow_Down.png' width=12px height=12px></td>";
                    else if ($changepercent > 0)
                        echo "<td class='right'>".$changepercent."%<img src='Green_Arrow_Up.png' width=12px height=12px></td>";  
                    else
                        echo "<td class='right'>".$changepercent."%</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Timestamp</td>";                    
                    echo "<td class='right'>".$pacific_time."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Market Cap</td>";
                    echo "<td class='right'>".$marketcap."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Volume</td>";
                    echo "<td class='right'>".$volume."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Change YTD</td>"; 
                    if ($changeYTD < 0)
                        echo "<td class='right'>(".$changeYTD.")<img src='Red_Arrow_Down.png' width=12px height=12px></td>";
                    else if ($changeYTD > 0)    
                        echo "<td class='right'>".$changeYTD."<img src='Green_Arrow_Up.png' width=12px height=12px></td>";
                    else
                        echo "<td class='right'>".$changeYTD."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Change Percent YTD</td>";       
                    if ($changepercentYTD < 0)
                        echo "<td class='right'>".$changepercentYTD."%<img src='Red_Arrow_Down.png' width=12px height=12px></td>";
                    else if ($changepercentYTD > 0)    
                        echo "<td class='right'>".$changepercentYTD."%<img src='Green_Arrow_Up.png' width=12px height=12px></td>";
                    else
                        echo "<td class='right'>".$changepercentYTD."%</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>High</td>";
                    echo "<td class='right'>".$json_result->High."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Low</td>";
                    echo "<td class='right'>".$json_result->Low."</td>";
                    echo "</tr>";

                    echo "<tr>";       
                    echo "<td class='left'>Open</td>";
                    echo "<td class='right'>".$json_result->Open."</td>";
                    echo "</tr>";

                    echo "</table>";


                    
          /*
                    echo "<pre>";
                    print_r($json_result);
                    echo "</pre>";
            */
          
                    
                    
                }
                else {
                    echo "<div id='fail2'>There is no stock information available</div>";
                    
                    
             /*       
                    echo "<pre>";
                    print_r($json_result);
                    echo "</pre>";
             */
                    
                    

                }
            }             
        ?>
        
    <noscript>   
    </body>
</html>

