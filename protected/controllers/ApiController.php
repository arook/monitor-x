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
    $masin = MAsin::model()->findByAttributes(array('asin'=>$asin));
    if(!$masin)
      return false;
    $fetching = MFetching::model()->getCollection()->getDbRef($masin->last);
    if(!$fetching)
      return false;
    $list = array();
    foreach($fetching->l as $row) {
      $seller = MFetching::model()->getCollection()->getDbRef($row->s);
      $list[] = array(
        'rank' => $row->r,
        'seller' => $seller['name'],
        'if_fba' => $row->f,
        'sell_price' => $row->p,
      );
    }

    return array(
      'list' => $list,
      'fs' => $masin->fs,
      'br' => $fetching->br,
    );
  }

  /**
   * @param string the asin
   * @return array list
   * @soap
   */
  public function getBuybox($asin) {
    $masin = MAsin::model()->findByAttributes(array('asin'=>$asin));
    if(!$masin)
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
