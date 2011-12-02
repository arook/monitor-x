<?php

class BuyboxCommand extends CConsoleCommand {

  private $_seller = array(
    'BetterStuff LowerPrice', 
    'E-Bundle',
    'Shopping Cloud',
  );

  public function run($args) {
    $dt = date('Y-m-d H:i:s');
    $type = $args[0];

    $asins = Asin::model()->findAll();

    foreach ($asins as $asin) {
      $this->bbr($asin, $type, $dt);
    }
  }

  private function bbr($asin, $type, $dt) {
    foreach ($this->_seller as $seller) {
      $sql = sprintf("SELECT sum(if(if_buybox, 1, 0))/count(*) as `rate` FROM `fetching` a
        left join `fetching_detail` b
        on a.id = b.fetching_id
        where b.`seller` = '%s' and a.`asin` = %s
        and b.`if_fba` =1 and a.`dt` > DATE_SUB(curdate(), INTERVAL %s day)", $seller, $asin->id, $type);
      $result = Yii::app()->db->createCommand($sql)->queryAll();
      $bbr = new BuyboxRate;
      $bbr->type = $type;
      $bbr->dt = $dt;
      $bbr->asin = $asin->id;
      $bbr->seller = $seller;
      $bbr->rate = sprintf("%.4f", $result[0]['rate']);
      $bbr->if_fba = 1;
      if ($bbr->save()) {
      } else {
        print_r($bbr->getErrors());
      }
      
    }
  }

}
?>
