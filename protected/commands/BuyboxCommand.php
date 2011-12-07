<?php

class BuyboxCommand extends CConsoleCommand {

  private $_seller = array(
    'BetterStuff LowerPrice', 
    'E-Bundle',
    'Shopping Cloud',
  );

  public function run($args) {
    $type = $args[0];
    $dt = date("Y-m-d H:i:s");

    if (!in_array($type, array(1, 3, 7))) {
      throw new CException('args must in (1, 3, 7)');
    }

    $asins = Asin::model()->findAll();

    foreach ($asins as $asin) {
      foreach ($this->_seller as $seller) {
        //$rate = $this->bbr($asin, $seller, 0, $type);
        $rate = $this->realRate($asin, $seller, $type, 0);
        $bbr = new BuyboxRate;
        $bbr->type = $type;
        $bbr->dt = $dt;
        $bbr->asin = $asin->id;
        $bbr->seller = $seller;
        $bbr->if_fba = 0;
        $bbr->rate = $rate;
        $bbr->save();

        //$rate = $this->bbr($asin, $seller, 1, $type);
        $rate = $this->realRate($asin, $seller, $type, 1);
        $bbr = new BuyboxRate;
        $bbr->type = $type;
        $bbr->dt = $dt;
        $bbr->asin = $asin->id;
        $bbr->seller = $seller;
        $bbr->if_fba = 1;
        $bbr->rate = $rate;
        $bbr->save();
      }
    }
  }

  private function bbr($asin, $seller, $if_fba, $type) {
    $A = $this->currentRate($asin, $seller, $type, $if_fba);
    $diff = $this->computeRateDiff($asin, $seller, $if_fba, $type);
    return sprintf("%.4f", $A + $diff);
  }

  /**
   *
   * @var asin Asin
   * @var seller string
   */
  private function currentRate($asin, $seller, $type, $if_fba) {
    $bbr = BuyboxRate::model()->findByAttributes(
      array('seller'=>$seller, 'asin'=>$asin->id, 'type'=>$type, 'if_fba'=>$if_fba),
      array('order'=>'dt DESC')
    );
    if ($bbr !== null) {
      return $bbr->rate;
    } else {
      return $this->realRate($asin, $seller, $type, $if_fba);
    }
  }

  private function realRate($asin, $seller, $type, $if_fba) {
    $sql = sprintf("SELECT sum(if(if_buybox, 1, 0))/count(*) as `rate` FROM `fetching` a
        left join `fetching_detail` b
        on a.id = b.fetching_id
        where b.`seller` = '%s' and a.`asin` = %s
        and b.`if_fba` = %s and a.`dt` > DATE_SUB(curdate(), INTERVAL %s day)", $seller, $asin->id, $if_fba, $type);
    $result = Yii::app()->db->createCommand($sql)->queryRow();
    return sprintf("%.4f", $result['rate']);
  }

  /**
   * @var $time_step int in (1, 3, 7)
   */
  private function computeRateDiff($asin, $seller, $if_fba, $time_step) {
    $limit = array(1=>1, 3=>6, 7=>24*6);
    $latest = $oldest = 0;
    $sql = sprintf("select if(`if_buybox`, 1, 0) as buybox from `fetching` 
      left join `fetching_detail`
      on `fetching`.`id` = `fetching_detail`.`fetching_id`
      where `fetching`.`asin` = %s and `fetching_detail`.`seller` = '%s' and `fetching_detail`.`if_fba` = %s
      order by `fetching`.`dt` desc limit %s", $asin->id, $seller, $if_fba, $limit[$time_step]);
    $result = Yii::app()->db->createCommand($sql)->queryAll();
    if ($result) {
      foreach($result as $item) {
        $latest += $item['buybox'];
      }
    }

    $sql = sprintf("select if(`if_buybox`, 1, 0) as buybox from `fetching` 
      left join `fetching_detail`
      on `fetching`.`id` = `fetching_detail`.`fetching_id`
      where `fetching`.`asin` = %s and `fetching_detail`.`seller` = '%s' and `fetching_detail`.`if_fba` = %s
      and `fetching`.`dt` < DATE_SUB(curdate(), INTERVAL %s day)
      order by `fetching`.`dt` desc limit %s", $asin->id, $seller, $if_fba, $time_step, $limit[$time_step]);
    $result = Yii::app()->db->createCommand($sql)->queryRow();
    if ($result) {
      foreach ($result as $item) {
        $oldest += $item['buybox'];
      }
    }

    $diff = $latest/(24*6*$time_step) - $oldest/(24*6*$time_step);
    return $diff;
  }

  private function bbrbak($asin, $type, $dt) {
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
