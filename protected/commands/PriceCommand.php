<?php

class PriceCommand extends CConsoleCommand {

  private $_seller = array(
    'BetterStuff LowerPrice', 
    'E-Bundle',
    'Shopping Cloud',
  );

  public function run($args) {
    $asins = Asin::model()->findAll();
    foreach ($asins as $asin) {
      $sql = "SELECT AVG(`shipping_price` + `sell_price`) as `price`, 1 as `if_fba`
        FROM `fetching` a
        LEFT JOIN `fetching_detail` b
        ON a.`id` = b.`fetching_id`
        WHERE a.`dt` > DATE_SUB(NOW(), INTERVAL 1 DAY)
        AND b.`if_buybox` = 1
        AND b.`if_fba` = 1
        AND a.`ASIN` = $asin->id";
      $result = Yii::app()->db->createCommand($sql)->queryRow();
      $entity = new SalesPrice;
      $entity->asin = $asin->id;
      $entity->type = 's1';
      $entity->price = $result['price'];
      $entity->seller = $result['seller'];
      $entity->if_fba = 1;
      $entity->save();
    }
  }
}
