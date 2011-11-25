<?php

class MainCommand extends CConsoleCommand {

  public function run($args) {
    echo date('Y-m-d H:i:s');
    Asin::model()->updateAll(array('retry'=>0));
    $yiic = dirname(__FILE__) . '/../yiic';

    $process = new Process();


    $asins = $this->get_asin_list();
    $count = count($asins);
    $current = 0;
    
    while ($current < $count) {
      
      $asin = $asins[$current];

      if($process->get_alive_count() < 20) {
        $process->add(sprintf("%s core %s %s", $yiic, $asin->id, $asin->asin));
        echo sprintf("[%s]:%s core %s %s(%s)\n", date("H:i:s"), $yiic, $asin->id, $asin->asin, $process->get_alive_count());
        $current++;
      } else {
        echo 'waiting...', "\n";
        sleep(1);
      }
    }
 }

  private function get_asin_list() {
    /*
    $criteria = new CDbCriteria();
    $criteria->compare('retry', 0, true);
    $criteria->limit = 50;
    return Asin::model()->findAll($criteria);
     */
    $criteria = new CDbCriteria();
    $criteria->limit = 50;
    return Asin::model()->findAll($criteria);
  }

}
?>
