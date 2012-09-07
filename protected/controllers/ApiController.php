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
}
