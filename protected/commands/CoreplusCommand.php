<?php

class CoreplusCommand extends CConsoleCommand {

  private $_content_listing;
  private $_content_buybox;
  private $_need_save_content = false;

  const SELLER_ID = 'ids:nextSeller';
  const SELLER_NAME = 'seller:%d:name';
  const SELLER_AVATAR = 'seller:%d:avatar';

  //featured seller
  const ASIN_FS = 'asin:%d:fs';

  const FETCHING_ID = 'ids:nextFetching';
  const FETCHING_LIST = 'fetch:%d:list';
  const FETCHING_SELLER = 'fetch:%d:seller';
  const FETCHING_BP = 'fetch:%d:bp';
  const FETCHING_TIME = 'fetch:%d:time';

  const ASIN_FETCH = 'asin:%d:fetch';

  public function run($args) {
    echo date('Y-m-d H:i:s');
    if (count($args) <> 1) {
      return;
    }
    list($asin) = $args;
    $asin_id = Redis::client()->hget('asins', $asin);

    if(!isset($asin))
      return;

    $listing = $this->listing($asin);
    $buybox = $this->buybox($asin);
    Redis::client()->set(sprintf(self::ASIN_FS, $asin_id), count($listing));

    $fid = Redis::client()->incr(self::FETCHING_ID);
    Redis::client()->lpush(sprintf(self::ASIN_FETCH, $asin_id), $fid);
    Redis::client()->set(sprintf(self::FETCHING_TIME, $fid), time());
    foreach($listing as $item) {
      if($buybox['seller'] == $item['seller'] && $buybox['price'] == ($item['sell_price'] + $item['shipping_price']) && $buybox['if_fba'] == $item['if_fba']) {
        Redis::client()->set(sprintf(self::FETCHING_SELLER, $fid), $buybox['seller']);
        Redis::client()->set(sprintf(self::FETCHING_BP, $fid), $buybox['price']);
        $item['if_buybox'] = 1;
      }

      //handle sellers && avatar
      if(!Redis::client()->hexists('sellers', $item['sid'])) {
        $sid = Redis::client()->incr(self::SELLER_ID);
        Redis::client()->hset('sellers', $item['sid'], $sid);
        Redis::client()->set(sprintf(self::SELLER_NAME, $sid), $item['seller']);
        Redis::client()->set(sprintf(self::SELLER_AVATAR, $sid), $item['avatar']);
      } else {
        //$sid = Redis::client()->hget('sellers', $item['sid']);
      }

      unset($item['avatar'], $item['rank'], $item['seller']);
      Redis::client()->rpush(sprintf(self::FETCHING_LIST, $fid), CJSON::encode($item));
    }

    return;
  }

  private function fetch($fetching_id, $asin) {
    $listing = $this->listing($asin);
    $buybox = $this->buybox($asin);
    $buybox_returned = false;
    foreach ($listing as $item) {
      $detail = new FetchingDetail;
      $detail->attributes = $item;
      $detail->fetching_id = $fetching_id;
      if ($buybox['seller'] == $item['seller'] && $buybox['price'] == ($item['sell_price'] + $item['shipping_price']) && $buybox['if_fba'] == $item['if_fba']) {
        $detail->if_buybox = 1;
        $buybox_returned = true;
      }
      if ($detail->save(true)) {

      } else {
        print_r($detail->getErrors());
      }
    }

    //如果buybox返回空
    if (!$buybox_returned) {
      $this->_need_save_content = true;
    }

    //如果buybox的降幅大于10%
    $sql = sprintf("SELECT sell_price + shipping_price AS price
      FROM  `asin` a
      LEFT JOIN  `fetching` b ON a.`id` = b.`asin` 
      LEFT JOIN  `fetching_detail` c ON b.`id` = c.`fetching_id` 
      WHERE a.`asin` =  '%s'
      AND b.`dt` > DATE_SUB( NOW( ) , INTERVAL 1 HOUR ) 
      AND if_buybox =1
      ORDER BY dt DESC 
      LIMIT 2", $asin);
    $rows = Yii::app()->db->createCommand($sql)->queryAll();
    if (count($rows) == 2 && ($rows[0]['price'] < 0.9 * $rows[1]['price'])) {
      $this->_need_save_content = true;
    }


    $this->issue($fetching_id);
  }

  /**
   *
   * @var $fetching_id Fetching
   */
  private function issue($fetching_id) {
    if (!$this->_need_save_content)
      return;
    $issue = new FetchingIssue;
    $issue->fetching_id = $fetching_id;
    $issue->content_listing = $this->_content_listing;
    $issue->content_buybox = $this->_content_buybox;
    if (!$issue->save()) {
      print_r($issue->getErrors());
    }
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Host: www.amazon.com',
      'Connection: keep-alive',
      'Cache-Control: max-age=0',
      'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.54 Safari/536.5',
      'Accept-Encoding: gzip,deflate,sdch',
      'Accept-Language: en-US,en;q=0.8',
    ));
//    curl_setopt ($ch, CURLOPT_PROXY, "127.0.0.1:8889");
//    curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    $html_contents = curl_exec($ch);
    curl_close($ch);
    return $html_contents=="" ? false : $html_contents;
  }
}
?>
