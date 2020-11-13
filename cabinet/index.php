<?php
include('class/class.api.php');
if (!file_exists('accounts.json')) {
  die('File accounts.json not found. Please rename accounts-sample.json to accounts.json and insert required data inside it.');
}
$file = file_get_contents('accounts.json');
$accounts = json_decode($file, true);

//function to set price float precision
function formatfloat($input, $precision){
  $result = number_format($input,$precision,".","");
  return $result;
}
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Account Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <?php include('../includes/navbar.php') ?>
    <div class="mx-3 my-3"> <!-- padding horizontal and vertical rather than using container class -->
      <div class="alert alert-success" role="alert">
        <strong>InstaForex Account Status</strong>
      </div>
    
        <?php
          // Begin iterate each content in accounts.json
          for ($index=0; $index < count($accounts); $index++) { 
            
            $account = new Api;
            $account->setLogin($accounts[$index]['login']);
            $account->setPassword($accounts[$index]['password']);
            $account->requestToken();

            // Request Account Balance
            $accountStatus = $account->getBalanceStatus();
            $accountStatus = json_decode($accountStatus, true); 
            
            // Store Account data into variables
            $accountOwner = $accounts[$index]['name'];
            $accountNumber = $accountStatus['Login'];
            $accountBalance = formatfloat($accountStatus['Balance'],2);
            $accountEquity = formatfloat($accountStatus['Equity'],2);
            
            //Account Statistics Calculation
            $accountFloat = formatfloat($accountEquity - $accountBalance,2);
            $accountRisk = formatfloat($accountFloat / $accountBalance * 100,2);

            echo "<div class=\"card\">";
            echo "<div class=\"card-header\">";
              echo "Owner: ".$accountOwner." Account Number: ".$accountNumber."<br>"; 
              echo "Balance: $".$accountBalance.". Equity: $".$accountEquity."<br>";
              echo "Float: $".$accountFloat.". Risk: ".$accountRisk."%.";
            echo "</div>";
        
            echo "<div class=\"card-body\">";
            echo "<h5 class=\"card-title\">Open Trades</h5>";
            echo "<table class=\"table table-responsive table-sm table-hover\">";
            echo "  <thead>";
            echo "    <tr>";
            echo "      <th scope=\"col\">Order Date</th>";
            echo "      <th scope=\"col\">Pair</th>";
            echo "      <th scope=\"col\">Order Type</th>";
            echo "      <th scope=\"col\">Lot</th>";
            echo "      <th scope=\"col\">Open Price</th>";
            echo "      <th scope=\"col\">Current Price</th>";
            echo "      <th scope=\"col\">S/L</th>";
            echo "      <th scope=\"col\">T/P</th>";
            echo "      <th scope=\"col\">Current Profit</th>";
            echo "      <th scope=\"col\">Expected Profit</th>";
            echo "    </tr>";
            echo "  </thead>";
            echo "  <tbody>";
                
            // Request Open Trades through API
            $tradeStatus = $account->getOpenTrades();
            $tradeStatus = json_decode($tradeStatus, true);
            echo "<pre>";
            //var_dump($tradeStatus);
            echo "</pre>";
            $sum = 0; //initialize variable to store net expected profit            

            // Begin iterate each trade data
            for ($tradeIndex=0; $tradeIndex < count($tradeStatus); $tradeIndex++) {   
              

              //Store Trades data into variables
              $tradeOpenTime = $tradeStatus[$tradeIndex]['OpenTime'];
              $tradeSymbol = $tradeStatus[$tradeIndex]['Symbol'];
              $tradeTypeInteger = $tradeStatus[$tradeIndex]['Type']; // return integer 1 for BUY trade, integer 2 for SELL trade
              
              //change $tradeTypeInteger to $tradeType
              if ($tradeTypeInteger == "1") {
                $tradeType = "BUY";
              } elseif ($tradeTypeInteger == "2") {
                $tradeType = "SELL";
              }
                  

              $tradeVolume = $tradeStatus[$tradeIndex]['Volume'];
              $tradeOpenPrice = formatfloat($tradeStatus[$tradeIndex]['OpenPrice'],4);
              $tradeCurrentPrice = formatfloat($tradeStatus[$tradeIndex]['CurrentPrice'],4);
              $tradeSL = formatfloat($tradeStatus[$tradeIndex]['SL'],4);
              $tradeTP = formatfloat($tradeStatus[$tradeIndex]['TP'],4);
              $tradeCurrentProfit = formatfloat($tradeStatus[$tradeIndex]['Profit'],2);
                  
                  
              //Trade Statistics Calculation
              // identify how the expected profit will be calculated
              if ($tradeType == "BUY") {
                // calculate T/P - OpenPrice
                $pip = round($tradeTP - $tradeOpenPrice,4)*10000;
                $calc = $pip * $tradeVolume;
                $expectedProfit = formatfloat($calc,2);
              } elseif ($tradeType == "SELL") {
                //calculate OpenPrice - T/P
                $pip = round($tradeOpenPrice - $tradeTP,4)*10000;
                $calc = $pip * $tradeVolume;
                $expectedProfit = formatfloat($calc,2);
              }
                  
              $sum += $expectedProfit; //store each trade's expected profit for net expected profit calculation
              // echo all trade data
              echo "<tr>";
              echo "<td scope='row'>".$tradeOpenTime."</td>";
              echo "<td>".$tradeSymbol."</td>";
              echo "<td>".$tradeType."</td>";
              echo "<td>".$tradeVolume."</td>";
              echo "<td>".$tradeOpenPrice."</td>"; 
              echo "<td>".$tradeCurrentPrice."</td>";
              echo "<td>".$tradeSL."</td>";
              echo "<td>".$tradeTP."</td>";
              echo "<td>".$tradeCurrentProfit."</td>";
              echo "<td>".$expectedProfit."</td>";
              echo "</tr>";
            } // End iterate each trade data  
          
          echo "  </tbody>";
          echo "</table>";
          echo "</div>";
          echo "<div class=\"card-footer\">";
          echo "Expected Profit on Trade Closing: $".$sum;
          echo "  </div>";
          echo "</div>";

          echo "&nbsp";
          echo "&nbsp";
          } // End iterate each content in accounts.json
          ?>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>