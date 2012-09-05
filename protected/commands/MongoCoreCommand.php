<?php

class MongoCoreCommand extends CConsoleCommand {
  protected $_content_listing;
  protected $_content_buybox;

  public function run($args) {
    echo date('Y-m-d H:i:s');
    if (count($args) <> 1) {
      return;
    }
    list($asin) = $args;
    $asin = MAsin::model()->findByAttributes(array('asin'=>$asin));


    //找不到? 不睬,直接跳过
    if(!isset($asin))
      return;

    try {
      $listing = $this->listing($asin->asin);
      //大于0才更新
      if (count($listing) == 0)
        return;
    } catch (Exception $e) {
      $asin->_x = false;
      $asin->_r = $asin->_r +1;
      $asin->_e = $e->getMessage();
      $asin->_sl = new MongoBinData($this->_content_listing);
      if ($asin->_r < 3)
        $asin->next = new MongoDate();
      $asin->save();
      return;
    }

    try {
      $buybox = $this->buybox($asin->asin);
    } catch (Exception $e) {
      $asin->_x = false;
      $asin->_r = $asin->_r +1;
      $asin->_e = $e->getMessage();
      $asin->_sb = new MongoBinData($this->_content_buybox);
      if ($asin->_r < 3)
        $asin->next = new MongoDate();
      $asin->save();
      return;
    }

    $asin->fs = count($listing);

    $f = new MFetching;
    $f->t = new MongoDate();
    $f->a = MAsin::model()->getCollection()->createDbRef($asin);
    $f->l = array();

    foreach($listing as $item) {
      if($buybox['seller'] == $item['seller'] && $buybox['price'] == ($item['sell_price'] + $item['shipping_price']) && $buybox['if_fba'] == $item['if_fba']) {
        $f->bp = intval($buybox['price'] * 100);
        $f->br = intval($item['rank']);
        $item['if_buybox'] = 1;
      }

      //handle sellers && avatar
      if(!($s = MSeller::model()->findByAttributes(array('sid'=>$item['sid'])))) {
        $s = new MSeller;
        $s->sid = $item['sid'];
        $s->name = $item['seller'];
        $s->avatar = $item['avatar'];
        $s->save();
      }
      $s_ref = MSeller::model()->getCollection()->createDbRef($s);

      $l = new MListing;
      $l->r = intval($item['rank']);
      $l->p = intval($item['sell_price']*100) + intval($item['shipping_price']*100);
      $l->s = $s_ref;
      $l->f = $item['if_fba'] ? true : false;
      $l->b = $item['if_buybox'] ? true : false;

      $f->l[] = $l;

    }

    $f->save();

    //修成正果 更新状态
    $asin->_e = null;
    $asin->_r = 0;
    $asin->_x = false;
    $asin->last = MAsin::model()->getCollection()->createDBRef($f);
    $asin->save();

    return;
  }

  private function listing($asin) {
    $url = sprintf("http://www.amazon.com/gp/offer-listing/%s/sr=/qid=/ref=olp_tab_new?ie=UTF8&coliid=&me=&qid=&sr=&seller=&colid=&condition=new", $asin);
		$html = $this->_fetch($url);
    $this->_content_listing = $html;
    $list = array();
		
    if ($point = strpos($html, '<h2>New</h2>')) {
      $html = substr($html, 0, $point);
    }
		
    $reg = '/<tbody class="result">.*?(?:<span class="price">\$([\d\.]+)<\/span>.*?)?(?:<span class="price_shipping">\+ \$([^<>]*)<\/span>.*?)?(?:<a href="http\:\/\/www\.amazon\.com\/shops\/([A-Z0-9]+)\/[^"]+">.*?)?(?:<a href="\/gp\/help\/seller\/at-a-glance\.html.*?seller=([A-Z0-9]+)">.*?)?(?:<img src="([^"]+)" width="120" alt="([\w\s\-\,\.]+)?".*?)?(?:<b>([\w\s\-\,\.]*)<\/b><\/a>.*?)?(?:(Fulfillment) by Amazon.*?)?<\/tbody>/is';
    preg_match_all($reg, $html, $matches);
    unset($matches[0]);

    foreach ($matches[1] as $key=>$match) {
      $list[$key] = array(
        'rank'		=>	$key + 1,
        'sell_price' => $matches[1][$key] ? $matches[1][$key] : 0,
        'shipping_price'		=>	$matches[2][$key] ? $matches[2][$key] : 0,
        'sid'     =>  $matches[3][$key] ? $matches[3][$key] : $matches[4][$key],
        'avatar'     =>  $matches[5][$key] ? $matches[5][$key] : 0,
        'seller'	=>	$matches[6][$key] ? $matches[6][$key] : $matches[7][$key],
        'if_fba'		=>	($matches[8][$key] == 'Fulfillment' ? 1 : 0),
        'if_buybox'	=> 0	
      );

    }
    return $list;
  }

  private function buybox($asin) {
    $url = sprintf("http://www.amazon.com/gp/product/%s", $asin);
    $html = $this->_fetch($url);
    $this->_content_buybox = $html;

    $reg = '/<b class="priceLarge">\$([^<>]+)<\/b>.*?<div class="buying" style="[^"]+">.*?<span class="avail[^"]+">In Stock.*?<\/span><br \/>.*?(?:Ships from and sold by <b><a href="[^"]+">(.*?)<\/a>.*?)?(?:Sold by <b><a href="[^"]+">(.*?)<\/a><\/b> and.*?)?(?:<a href="[^"]+" id="[^"]+"><strong>(.*?)<\/strong>.*?)?<\/[b|a]?>\./is';
    preg_match_all($reg, $html, $matches);

    if (0 == count($matches[1]))
      throw new Exception('Error analyze buybox page!', 50);

    $buybox_price = str_replace(',', '.', $matches[1][0]);
    $buybox_seller = ($matches[3][0] ? $matches[3][0] : $matches[2][0]);
    $buybox_if_fba = ($matches[4][0] == 'Fulfilled by Amazon' ? 1 : 0);

    $reg_shipping = '/<span id="pricePlusShippingQty"><b class="price">\$([^<>]+)<\/b><span class="plusShippingText">.*?(?:\$([^\&]+).*?)?(?:(Free Shipping).*?)?<\/span><\/span>/is';
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
    curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($ch, CURLOPT_ENCODING, 1);
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Host: www.amazon.com',
      'Connection: keep-alive',
      'Cache-Control: max-age=0',
      'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.54 Safari/536.5',
      'Accept-Encoding: gzip,deflate,sdch',
      'Accept-Language: en-US,en;q=0.8',
    ));

    if(false === ($html_contents = curl_exec($ch)))
      throw new Exception('Curl error: ' . curl_error($ch), 10);

    if (200 != ($code = curl_getinfo($ch, CURLINFO_HTTP_CODE)))
      throw new Exception('HTTP error: ' . $code, 20);

    return $html_contents;
  }
}
?>
