<?php
require_once('../env.php');
require_once($class.'/CabinetApi.php');
if (!file_exists('accounts.json')) {
  die('File accounts.json not found. Please rename accounts-sample.json to accounts.json and insert required data inside it.');
}
$file = file_get_contents('accounts.json');
$accounts = json_decode($file, true);

//function to check whether the pair has JPY or not
function checkYen($pair) {
  if (substr($pair,0,3) == "JPY" || substr($pair,3,5) == "JPY") {
    return true;
  }
}

//function to set price float precision
function formatfloat($pair, $price){
  if (checkYen($pair) == true) {
    $result = number_format($price,2,".", "");
    return $result;
  } else
  $result = number_format($price,4,".","");
  return $result;
}

//function to calculate profit
function calculateProfit($pair, $tradeType, $openPrice, $profitPrice, $lotSize) {
  if ($tradeType == "BUY") {
    //TP - OPEN = pips
    if (checkYen($pair) == true && $profitPrice > 0) {  //check JPY pair and has profit price set
      $pips = ($profitPrice - $openPrice) * 100;
      $result = $pips * $lotSize;
      return $result;
    } elseif (checkYen($pair) == true && $profitPrice == 0) { //check JPY pair but no profit price is set
      $result = number_format(0, 2, ".", "");
      return $result;
    } elseif ($profitPrice > 0) { //check if profit price is set
      $pips = ($profitPrice - $openPrice) * 10000;
      $result = $pips * $lotSize;
      return $result;
    } else {  //no profit price was set
      $result = 0;
      return $result;
    }

  } elseif ($tradeType == "SELL") {
    if (checkYen($pair) == true && $profitPrice > 0) {
      $pips = ($openPrice - $profitPrice) * 100;
      $result = $pips * $lotSize;
      return $result;
    } elseif (checkYen($pair) == true && $profitPrice == 0) {
      $pips = ($openPrice - $profitPrice) * 100;
      $result = $pips * $lotSize;
      return $result;
    } elseif ($profitPrice > 0) {
      $pips = ($openPrice - $profitPrice) * 10000;
      $result = $pips * $lotSize;
      return $result;
    } else {
      $result = 0;
      return $result;
    }
  }
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
            $accountBalance = number_format($accountStatus['Balance'], 2);
            $accountEquity = number_format($accountStatus['Equity'], 2);
            
            //Account Statistics Calculation
            $accountFloat = number_format($accountEquity - $accountBalance,2);
            $accountRisk = number_format($accountFloat / $accountBalance * 100,2);

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
              $tradeOpenPrice = formatfloat($tradeStatus[$tradeIndex]['Symbol'], $tradeStatus[$tradeIndex]['OpenPrice']);
              $tradeCurrentPrice = formatfloat($tradeStatus[$tradeIndex]['Symbol'], $tradeStatus[$tradeIndex]['CurrentPrice']);
              $tradeSL = formatfloat($tradeStatus[$tradeIndex]['Symbol'], $tradeStatus[$tradeIndex]['SL']);
              $tradeTP = formatfloat($tradeStatus[$tradeIndex]['Symbol'], $tradeStatus[$tradeIndex]['TP']);
              $tradeCurrentProfit = number_format($tradeStatus[$tradeIndex]['Profit'], 2);
              $expectedProfit = number_format(calculateProfit($tradeSymbol, $tradeType, $tradeOpenPrice, $tradeTP, $tradeVolume), 2, ".", "");
                  
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