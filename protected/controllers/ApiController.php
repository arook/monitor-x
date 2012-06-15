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
    $aid = Redis::client()->hget('asins', $asin);
    if(!$aid)
      return false;
    $fid = Redis::client()->lrange("asin:{$aid}:fetch", -1, -1);
    if(!$fid)
      return false;
    $list = Redis::client()->lrange("fetch:{$fid[0]}:list", 0, -1);
    foreach($list as &$item) {
      $item = CJSON::decode($item);
      $item['seller'] = $this->getSellerName($item['sid']);
    }

    $fs = Redis::client()->get("asin:{$aid}:fs");
    return array($list, $fs);
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
