<?php 

class Indicator
{
  var $symbol; //pair symbol. E.g: EURUSD, GBPJPY
  var $interval; //1m, 5m, 15m, 1h, 4h, 1D, 1W
  var $width; //pixel
  var $isTransparent; //boolean
  var $height; //pixel
  var $showIntervalTabs; //boolean
  var $locale; //en
  var $colorTheme; //dark, light

  public function __construct()
  {
    $this->interval="4h";
    $this->width="100%";
    $this->height="450";
    $this->isTransparent="false";
    $this->showIntervalTabs="false";
    $this->locale="en";
    $this->colorTheme="dark";
  }

  public function generate()
  {
    $html_content = <<<EOF
    <!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div class="tradingview-widget-container__widget"></div>
  <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/symbols/$this->symbol/technicals/" rel="noopener" target="_blank"><span class="blue-text">Technical Analysis for $this->symbol</span></a> by TradingView</div>
  <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js" async>
  {
  "interval": "$this->interval",
  "width": "$this->width",
  "isTransparent": $this->isTransparent,
  "height": "$this->height",
  "symbol": "$this->symbol",
  "showIntervalTabs": $this->showIntervalTabs,
  "locale": "$this->locale",
  "colorTheme": "$this->colorTheme"
}
  </script>
</div>
<!-- TradingView Widget END -->
EOF;
  echo $html_content;
  echo $this->text();
  }

  public function text()
  {
    $text = <<<EOF
    <div class="py-4"><center>$this->symbol for $this->interval</center></div>
EOF;
    return $text;
  }


}

?>