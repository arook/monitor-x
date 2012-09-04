<?php

class ApiController extends Controller
{

	public function actions()
	{
		return array(
			'index'=>array(
				'class'=>'CWebServiceAction',
			),
		);
	}

  /**
   * @param string the asin to look up
   * @return array list
   * @soap
   */
  public function getListing($asin) {
    //$aid = Redis::client()->hget('asins', $asin);
    $masin = MAsin::model()->findByAttributes(array('asin'=>$asin));
    if(!$masin)
      return false;
    //$fid = Redis::client()->lrange("asin:{$aid}:fetch", 0, 1);
    $fetching = MFetching::model()->findByAttributes(array('a.$id'=>$masin->_id));
    if(!$fetching)
      return false;
    $list = array();
    foreach($fetching->l as $row) {
      $seller = MFetching::model()->getCollection()->getDbRef($row->s);
      $list[] = array(
        'seller' => $seller['name'],
        'if_fba' => $row->f,
        'sell_price' => $row->p,
      );
    }
    /*
    $list = Redis::client()->lrange("fetch:{$fid[0]}:list", 0, -1);
    foreach($list as &$item) {
      $item = CJSON::decode($item);
      $item['seller'] = $this->getSellerName($item['sid']);
      $item['if_fba'] = $item['if_fba'] ? true : false;
      //输出的price 是总价格
      $item['sell_price'] = (float) $item['sell_price'] + (float) $item['shipping_price'];
    }
     */

    //$fs = Redis::client()->get("asin:{$aid}:fs");
    $fs = $masin->fs;
    return array($list, $fs);
  }

  /**
   * @param string the asin
   * @return array list
   * @soap
   */
  public function getBuybox($asin) {
    $aid = Redis::client()->hget('asins', $asin);
    if(!$aid)
      return false;
    $fid = Redis::client()->lrange("asin:{$aid}:fetch", 0, 1);
    if(!$fid)
      return false;
    $seller = Redis::client()->get("fetch:{$fid}:seller");
    $price = Redis::client()->get("fetch:{$fid}:bp");
    $if_fba = Redis::client()->get("fetch:{$fid}:iffba");
    return array('seller'=>$seller, 'price'=>$price, 'if_fba'=>$if_fba);
  }

  /**
   * @param array asins to look up
   * @return array list list
   * @soap
   */
  public function getListingList($asins) {
    $list = array();
    foreach($asins as $asin) {
      $list[$asin] = $this->getListing($asin);
    }
    return $list;
  }

  /**
   * @param string the asin to add
   * @return bool if added successed
   * @soap
   */
  public function addAsin($asin) {
    if(!Redis::client()->hexists('asins', $asin)) {
      $aid = Redis::client()->incr('ids:nextAsin');
      Redis::client()->hset('asins', $asin, $aid);
      return true;
    }
    return false;
  }

  private function getSellerName($sid) {
    $skey = Redis::client()->hget('sellers', $sid);
    return Redis::client()->get("seller:{$skey}:name");
  }
}
