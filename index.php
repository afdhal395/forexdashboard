<?php require 'class/class.charts.php' ?>
<!doctype html>
<html lang="en">
  <head>
    <title>Forex Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>

    <?php
        if ($_POST['forexpair']) {
          $currencyselect = $_POST['forexpair'];
        } else {
          $currencyselect = "EURUSD";
        }
    ?>

    <div class="container">
    <div>
      <form name="currencypair" action="" method="post">
        <div class="form-group-inline">
          <label for="forexpair">Forex Pair</label>
          <select class="form-control my-3" name="forexpair" id="forexpair" onchange="this.form.submit()">
            <?php
              $currencyList = array("EURUSD", "GBPUSD", "EURCHF", "EURGBP", "AUDUSD", "NZDUSD", "CHFJPY", "GBPJPY", "CADCHF", );
              asort($currencyList);

              foreach ($currencyList as $pair) {
                if ($pair == $currencyselect) {
                  echo "<option value='$pair' selected>$pair</option>";
                } else {
                  echo "<option value='$pair'>$pair</option>";
                }
              }
            ?>
            
          </select>
        </div>
      </form>

      
    </div>
      <div class="row">
          <div class="col-md">
            <?php 
              $chart = new Chart;
              $chart->symbol="$currencyselect";
              $chart->interval="1h";
              $chart->showIntervalTabs="true";
              $chart->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $chart = new Chart;
              $chart->symbol="$currencyselect";
              $chart->interval="4h";
              $chart->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $chart = new Chart;
              $chart->symbol="$currencyselect";
              $chart->interval="1D";
              $chart->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $chart = new Chart;
              $chart->symbol="$currencyselect";
              $chart->interval="1W";
              $chart->generate();
            ?>
          </div>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5mdXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjmdVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>