<?php

class ApiController extends Controller
{
  
  private $_seller_list = array();

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
    $fetching = MAsin::model()->getCollection()->getDbRef($masin->last);
    if(!$fetching)
      return false;
    $list = array();
    foreach($fetching['l'] as $row) {
      $seller = MFetching::model()->getCollection()->getDbRef($row['s']);
      $list[] = array(
        'rank' => $row['r'],
        'seller' => $seller['name'],
        'if_fba' => $row['f'],
        'sell_price' => $row['p'],
        'shipping_price' => $row['sp'],
      );
    }

    return array(
      'list' => $list,
      'fs' => $masin->fs,
      'br' => $fetching['br'],
    );
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
   * 获取指定asin的过去多少个小时的价格曲线
   * @param string $asin the asin to look up
   * @param int $from
   * @param int $to
   * @return array $list array(array('bp', 'list'), )
   * @soap
   */
  public function getPricingHistory($asin, $from, $to)
  {
    $list = array();
		$fetching = $this->getListByAsin($asin, $from, $to);
		
		foreach ($fetching as $key => $f) {
		  $list[$key]['bp'] = $f['l'][$f['br'] - 1]['p'] + $f['l'][$f['br'] - 1]['sp'];
		  foreach($f['l'] as $row) {
        $seller = $this->getSeller($row['s']);
        $list[$key]['list'][] = array(
          // 'bbx' => $f['br'],
          'bbx' => $f['br'] == ($row['r'] + 1),
          'rank' => $row['r'],
          'seller' => $seller['name'],
          'if_fba' => $row['f'],
          'sell_price' => $row['p'],
          'shipping_price' => $row['sp'],
        );
      }
		}
    return $list;
  }
  
  private function getListByAsin($asin, $from, $to)
  {
    $masin = MAsin::model()->findByAttributes(array('asin'=>$asin));
    if (!$masin)
      return;
    $c = MFetching::model()->getDbCriteria()
			->addCond('a.$id', '==', $masin->_id)
			->addCond('status', '==', 200)
			->addCond('t', '>=', new MongoDate($from))
			->addCond('t', '<=', new MongoDate($to))
			->sort('t', -1);
		return MFetching::model()->findAll($c);
  }
  
  private function getSeller($sid)
  {
    if (!array_key_exists($sid['$id']->{'$id'}, $this->_seller_list)) {
      $this->_seller_list[$sid['$id']->{'$id'}] = MFetching::model()->getCollection()->getDbRef($sid);
    }
    return $this->_seller_list[$sid['$id']->{'$id'}];
  }
}
