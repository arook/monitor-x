<?php

class CoreCommand extends CConsoleCommand {

  public function run($args) {
    echo date('Y-m-d H:i:s');
    if (count($args) <> 2) {
      return;
    }
    list($asin_id, $asin) = $args;

    $event = new Fetching;
    $event->dt = date('Y-m-d H:i:s');
    $event->asin = $asin_id;
    if ($event->save()) {
      $this->fetch($event->id, $asin);
    }
  }

  private function fetch($fetching_id, $asin) {
    $listing = $this->listing($asin);
    $buybox = $this->buybox($asin);
    foreach ($listing as $item) {
      $detail = new FetchingDetail;
      $detail->attributes = $item;
      $detail->fetching_id = $fetching_id;
      if ($buybox['seller'] == $item['seller'] && $buybox['price'] == ($item['sell_price'] + $item['shipping_price']) && $buybox['if_fba'] == $item['if_fba']) {
        $detail->if_buybox = 1;
      }
      if ($detail->save(true)) {

      } else {
        print_r($detail->getErrors());
      }
    }
  }

  private function listing($asin) {
    $url = sprintf("http://www.amazon.com/gp/offer-listing/%s/sr=/qid=/ref=olp_tab_new?ie=UTF8&coliid=&me=&qid=&sr=&seller=&colid=&condition=new", $asin);
		$html = $this->_fetch($url);
		
    if ($point = strpos($html, '<h2>New</h2>')) {
      $html = substr($html, 0, $point);
    }
		
		$reg = '/<tbody class="result">.*?(?:<span class="price">\$([\d\.]+)<\/span>.*?)?(?:<span class="price_shipping">\+ \$([^<>]*)<\/span>.*?)?(?:width="120" alt="([\w\s\-\,\.]+)?".*?)?(?:<a href="[^"]+"><b>([\w\s\-\,\.]*)<\/b><\/a>.*?)?(?:(Fulfillment) by Amazon.*?)?<\/tbody>/is';
		preg_match_all($reg, $html, $matches);
		unset($matches[0]);
		
		foreach ($matches[1] as $key=>$match) {
			$list[$key] = array(
				'rank'		=>	$key + 1,
				'seller'	=>	$matches[3][$key] ? $matches[3][$key] : $matches[4][$key],
        'sell_price' => $matches[1][$key] ? $matches[1][$key] : 0,
				'shipping_price'		=>	$matches[2][$key] ? $matches[2][$key] : 0,
				'if_fba'		=>	($matches[5][$key] == 'Fulfillment' ? 1 : 0),
				'if_buybox'	=> 0	
			);
		}
    return $list;
	
  }

  private function buybox($asin) {
    $url = sprintf("http://www.amazon.com/gp/product/%s", $asin);
		$html = $this->_fetch($url);
		
    $reg = '/<b class="priceLarge">\$([^<>]+)<\/b>.*?<div class="buying" style="[^"]+">.*?<span class="avail[^"]+">In Stock.*?<\/span><br \/>.*?(?:Ships from and sold by <b><a href="[^"]+">(.*?)<\/a>.*?)?(?:Sold by <b><a href="[^"]+">(.*?)<\/a><\/b> and.*?)?(?:<a href="[^"]+" id="[^"]+"><strong>(.*?)<\/strong>.*?)?<\/[b|a]?>\./is';
		preg_match_all($reg, $html, $matches);
		
    $buybox_price = str_replace(',', '.', $matches[1][0]);
    $buybox_seller = ($matches[3][0] ? $matches[3][0] : $matches[2][0]);
    $buybox_if_fba = ($matches[4][0] == 'Fulfilled by Amazon' ? 1 : 0);
    
    $reg_shipping = '/<span id="pricePlusShippingQty"><b class="price">\$([^<>]+)<\/b><span class="plusShippingText">.*?\$([^\&]+).*?<\/span><\/span>/is';
    preg_match_all($reg_shipping, $html, $shipping_matches);

    if (count($shipping_matches[0]) > 0) {
      $buybox_price = str_replace(',', '.', $shipping_matches[1][0]) + str_replace(',', '.', $shipping_matches[2][0]);
    }

    return array('seller' => $buybox_seller, 'price' => $buybox_price, 'if_fba' => $buybox_if_fba);
	
  }

  private function _fetch($url) {
    $html_contents  = "";
    $ch = curl_init();
    $timeout = 10;
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $html_contents = curl_exec($ch);
    curl_close($ch);
    return $html_contents=="" ? false : $html_contents;
  }
}
?>
