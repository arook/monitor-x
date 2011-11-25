<?php

class MainCommand extends CConsoleCommand {

  public function run($args) {
    echo date('Y-m-d H:i:s');
    Asin::model()->updateAll(array('retry'=>0));
    $yiic = dirname(__FILE__) . '/../yiic';
    $running = true;
    while ($running) {
      $processes = array();
      $asins = $this->get_asin_list();

      if (count($asins) == 0) {
        $running = false;
      }

      foreach ($asins as $key=>$asin) {
        echo sprintf("%s core %s %s\n", $yiic, $asin->id, $asin->asin);
        $processes[$key] = new Process();
        $processes[$key]->open(sprintf("%s core %s %s", $yiic, $asin->id, $asin->asin));
      }

    }
 }

  private function get_asin_list() {
    $criteria = new CDbCriteria();
    $criteria->compare('retry', 0, true);
    $criteria->limit = 50;
    return Asin::model()->findAll($criteria);
  }

}
?>
