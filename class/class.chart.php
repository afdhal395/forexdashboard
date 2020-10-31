<?php

class Chart  
{
  var $symbol;

  public function generate()
  {
    $htmlOutput = <<<EOF
    <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div id="tradingview_5baed"></div>
  <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/EURUSD/" rel="noopener" target="_blank"><span class="blue-text">EURUSD Chart</span></a> by TradingView</div>
  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
  <script type="text/javascript">
  new TradingView.widget(
  {  
  "width": "100%",
  "height": 610,
  "symbol": "$this->symbol",
  "interval": "240",
  "timezone": "Asia/Singapore",
  "theme": "dark",
  "style": "1",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "hide_top_toolbar": true,
  "save_image": false,
  "container_id": "tradingview_5baed"
}
  );
  </script>
</div>
<!-- TradingView Widget END -->
EOF;
  return $htmlOutput;
  }

  public function showChart($isTrue)
  {
    if ($isTrue) {
      echo $this->generate();
    }
  }
}


?>