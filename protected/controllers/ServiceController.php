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
      SELECT `asin`.`asin` as `asin`, `if_fba`, `rate`·
      FROM  `asin` 
      LEFT JOIN  `buybox_rate` ON  `buybox_rate`.asin =  `asin`.`id` 
      WHERE AND TYPE =1
      AND seller =  '%s'
      ", $seller);
    $results = BuyboxRate::model()->findAllBySql($sql);
    foreach ($results as $result) {
      $x1[$result['asin'] . '-' . $result['if_fba']] = $result['rate'];
    }

    $sql = sprintf("
      SELECT `asin`.`asin` as `asin`, `if_fba`, `rate`·
      FROM  `asin` 
      LEFT JOIN  `buybox_rate` ON  `buybox_rate`.asin =  `asin`.`id` 
      WHERE AND TYPE = 7
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

}
