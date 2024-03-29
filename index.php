<?php
require_once('env.php');
require_once($class."/Indicator.php");
require_once($class."/Chart.php");
?>
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

        if ($_POST['showChart']) {
          $showChart = true;
        }
    ?>
    <?php include('includes/navbar.php') ?>
    <div class="container my-3">
      <form name="currencypair" action="" method="post">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" for="forexpair">Forex Pair</label>
          <div class="col-sm-3">
          <input class="form-control" type="text" name="forexpair" id="forexpair" value="<?php echo $currencyselect ?>">
          </div>
          <div class="col-sm-2">
            <div class="form-check-inline">
              <label class="form-check-label">
                <?php
                  if ($showChart) {
                    echo "<input type='checkbox' class='form-check-input' name='showChart' id='showChart' value='showChart' checked onchange='this.form.submit()'>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input' name='showChart' id='showChart' value='showChart' onchange='this.form.submit()'>";
                  }
                ?>
                Show Chart?
              </label>
            </div>
          </div>
        </div>
      </form>

      <div>
        <?php
          $chart = new Chart;
          $chart->symbol="$currencyselect";
          $chart->showChart($showChart);
          
        ?>
      </div>

      <div class="row">
          <div class="col-md">
            <?php 
              $indicator = new Indicator;
              $indicator->symbol="$currencyselect";
              $indicator->interval="1h";
              $indicator->showIntervalTabs="true";
              $indicator->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $indicator = new Indicator;
              $indicator->symbol="$currencyselect";
              $indicator->interval="4h";
              $indicator->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $indicator = new Indicator;
              $indicator->symbol="$currencyselect";
              $indicator->interval="1D";
              $indicator->generate();
            ?>
          </div>
          <div class="col-md">
            <?php 
              $indicator = new Indicator;
              $indicator->symbol="$currencyselect";
              $indicator->interval="1W";
              $indicator->generate();
            ?>
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