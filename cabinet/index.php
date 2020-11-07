<?php
include('class/class.api.php');
if (!file_exists('accounts.json')) {
  die('File accounts.json not found. Please rename accounts-sample.json to accounts.json and insert required data inside it.');
}
$file = file_get_contents('accounts.json');
$accounts = json_decode($file, true);
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
    <div class="mx-3 my-3"> <!-- padding horizontal and vertical rather than using container class -->
      <div class="alert alert-success" role="alert">
        <strong>InstaForex Account Status</strong>
      </div>
    
        <?php
          $account = new Api;
          $account->setLogin($accounts['0']['login']);
          $account->setPassword($accounts['0']['password']);
          $account->requestToken();
          $accountStatus = $account->getBalanceStatus();
          $accountStatus = json_decode($accountStatus, true); 

          // store data into new variable
          $accountOwner = $accounts['name'];
          $accountNumber = $accountStatus['Login'];
          $accountBalance = $accountStatus['Balance'];
          $accountEquity = $accountStatus['Equity'];

          //Calculation
          $accountFloat = $accountEquity - $accountBalance;
          $accountRisk = round($accountFloat / $accountBalance * 100,2);
          echo "<pre>";
          //print_r($trade_arr);
          echo "</pre>";
        ?>

      <div class="card">
        <div class="card-header">
          <?php
            echo $accountOwner." Account Number: ".$accountNumber.". Balance: $".$accountBalance.". Equity: $".$accountEquity.". Float: $".$accountFloat.". Risk: ".$accountRisk."%.";
          ?>
        </div>
        <div class="card-body">
          <h4 class="card-title">Open Trades</h4>
          <table class="table">
            <thead>
              <tr>
                <th>Order Date</th>
                <th>Pair</th>
                <th>Order Type</th>
                <th>Lot</th>
                <th>Open Price</th>
                <th>Current Price</th>
                <th>S/L</th>
                <th>T/P</th>
                <th>Current Profit</th>
                <th>Expected Profit</th>
              </tr>
            </thead>
            <tbody>
              <?php
                // get Open Trades through API
                $tradeStatus=$account->getOpenTrades();
                $tradeStatus = json_decode($tradeStatus, true);
                
                $sum = 0; //variable init for total expected profit on trades closing

                for ($index=0; $index < count($tradeStatus); $index++) { 
                  echo "<tr>";
                  echo "<td scope='row'>".$tradeStatus[$index]['OpenTime']."</td>";
                  echo "<td>".$tradeStatus[$index]['Symbol']."</td>";

                  if ($tradeStatus[$index]['Type'] == "1") {
                    $tradeType = "BUY";
                  } elseif ($tradeStatus[$index]['Type'] == "2") {
                    $tradeType = "SELL";
                  }

                  echo "<td>".$tradeType."</td>";
                  echo "<td>".$tradeStatus[$index]['Volume']."</td>";
                  echo "<td>".number_format($tradeStatus[$index]['OpenPrice'],4,".","")."</td>"; //convert to 4 float precision
                  echo "<td>".number_format($tradeStatus[$index]['CurrentPrice'],4,".","")."</td>"; //convert to 4 float precision
                  echo "<td>".number_format($tradeStatus[$index]['SL'],4,".","")."</td>"; //convert to 4 float precision
                  echo "<td>".number_format($tradeStatus[$index]['TP'],4,".","")."</td>"; //convert to 4 float precision
                  echo "<td>".number_format($tradeStatus[$index]['Profit'],2,".","")."</td>"; //convert to 2 float precision

                  //check for how the expected profit will be calculated
                  if ($tradeStatus[$index]['Type']=="1") { //1 = Buy, 2 = Sell
                    // Calculate T/P - OpenPrice
                    $pip = round($tradeStatus[$index]['TP']-$tradeStatus[$index]['OpenPrice'],4)*10000;
                    $expectedProfit = number_format($pip * $tradeStatus[$index]['Volume'],2,".",""); //convert to 2 float precision
                    
                  } else {
                    // Calculate OpenPrice - TP
                    ($pip = round($tradeStatus[$index]['OpenPrice']-$tradeStatus[$index]['TP'],4)*10000)." ";
                    $expectedProfit = str_pad($pip * $tradeStatus[$index]['Volume'],5,"0",STR_PAD_RIGHT);
                    $expectedProfit = number_format($pip * $tradeStatus[$index]['Volume'],2,".",""); //convert to 2 float precision
                  }

                  echo "<td>".$expectedProfit."</td>";
                  echo "</tr>";
          
                  $sum += $expectedProfit; //store result into total expected result

                }
              ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          Expected Profit on Trade Closing: <?php echo "$".$sum;?>
        </div>
      </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>