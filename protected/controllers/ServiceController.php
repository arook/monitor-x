<?php

class ServiceController extends CController {

  public function actions() {
    return array(
      'index'=>array(
        'class'=>'CWebServiceAction',
        'classMap'=>array(
        ),
      ),
    );
  }

  /**
   * @return string
   * @soap
   */
  public function test() {
    return 'test';
  }

  /**
   * @param string seller
   * @return array array('asin-1'=>0, 'asin-2'=>1, ...)
   * @soap
   */
  public function listBuyBoxStatus($seller) {
    $return = array();
    $x1 = $x7 = array();
    $sql = sprintf("
      SELECT `asin`.`asin` as `asin`, `if_fba`, `rate` 
      FROM  `asin` 
      LEFT JOIN  `buybox_rate` ON  `buybox_rate`.asin =  `asin`.`id` 
      WHERE TYPE = '1'
      AND seller =  '%s'
      ", $seller);
    $results = BuyboxRate::model()->findAllBySql($sql);
    foreach ($results as $result) {
      $x1[$result['asin'] . '-' . $result['if_fba']] = $result['rate'];
    }

    $sql = sprintf("
      SELECT `asin`.`asin` as `asin`, `if_fba`, `rate` 
      FROM  `asin` 
      LEFT JOIN  `buybox_rate` ON  `buybox_rate`.asin =  `asin`.`id` 
      WHERE TYPE = '7'
      AND seller =  '%s'
      ", $seller);

    $results = BuyboxRate::model()->findAllBySql($sql);
    foreach ($results as $result) {
      $x7[$result['asin'] . '-' . $result['if_fba']] = $result['rate'];
    }

    foreach ($x1 as $k=>$v) {
      if (array_key_exists($k, $x7)) {
        if ($v > 2 * $x7[$k]) {
          $return[$k] = 1;
        } elseif ($v < 0.5 * $x7[$k]) {
          $return[$k] = -1;
        } else {
          $return[$k] = 0;
        }
      }
    }

    return $return;
  }

  /**
   * @param string seller
   * @return array array('asin-1'=>0, 'asin-2'=>1, ...)
   * @soap
   */
  public function listSalePrice($seller) {
    $return = array();
    $x1 = $x7 = array();
    $sql = sprintf("
      SELECT c.`asin` , AVG(  `shipping_price` +  `sell_price` ) AS price, '1' as `if_fba`
      FROM  `fetching` a
      LEFT JOIN  `fetching_detail` b ON a.`id` = b.`fetching_id`
      LEFT JOIN  `asin` c ON c.`id` = a.`asin`
      WHERE a.`dt` > DATE_SUB( NOW( ) , INTERVAL 1 DAY)
      AND b.`if_buybox` =1
      AND b.`seller` =  '%s'
      AND b.`if_fba` = 1
      GROUP BY c.`asin`
      UNION
      SELECT c.`asin` , AVG(  `shipping_price` +  `sell_price` ) AS price, '0' as `if_fba`
      FROM  `fetching` a
      LEFT JOIN  `fetching_detail` b ON a.`id` = b.`fetching_id`
      LEFT JOIN  `asin` c ON c.`id` = a.`asin`
      WHERE a.`dt` > DATE_SUB( NOW( ) , INTERVAL 1 DAY)
      AND b.`if_buybox` =1
      AND b.`seller` =  '%s'
      AND b.`if_fba` = 0
      GROUP BY c.`asin`
      ", $seller, $seller);
    $results = Yii::app()->db->createCommand($sql)->queryAll();
    foreach ($results as $result) {
      $x1[$result['asin'] . '-' . $result['if_fba']] = $result['price'];
    }

    $sql = sprintf("
      SELECT c.`asin` , AVG(  `shipping_price` +  `sell_price` ) AS price, '1' as `if_fba`
      FROM  `fetching` a
      LEFT JOIN  `fetching_detail` b ON a.`id` = b.`fetching_id`
      LEFT JOIN  `asin` c ON c.`id` = a.`asin`
      WHERE a.`dt` > DATE_SUB( NOW( ) , INTERVAL 1 WEEK)
      AND b.`if_buybox` =1
      AND b.`seller` =  '%s'
      AND b.`if_fba` = 1
      GROUP BY c.`asin`
      UNION
      SELECT c.`asin` , AVG(  `shipping_price` +  `sell_price` ) AS price, '0' as `if_fba`
      FROM  `fetching` a
      LEFT JOIN  `fetching_detail` b ON a.`id` = b.`fetching_id`
      LEFT JOIN  `asin` c ON c.`id` = a.`asin`
      WHERE a.`dt` > DATE_SUB( NOW( ) , INTERVAL 1 WEEK)
      AND b.`if_buybox` =1
      AND b.`seller` =  '%s'
      AND b.`if_fba` = 0
      GROUP BY c.`asin`
      ", $seller, $seller);
    $results = Yii::app()->db->createCommand($sql)->queryAll();
    foreach ($results as $result) {
      $x7[$result['asin'] . '-' . $result['if_fba']] = $result['price'];
    }
    
    foreach ($x1 as $k=>$v) {
      if (array_key_exists($k, $x7)) {
        if ($v > 2 * $x7[$k]) {
          $return[$k] = 1;
        } elseif ($v < 0.5 * $x7[$k]) {
          $return[$k] = -1;
        } else {
          $return[$k] = 0;
        }
      }
    }
    return $return;
  }

}
