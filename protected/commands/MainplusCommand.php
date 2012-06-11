<?php

class MainplusCommand extends CConsoleCommand {

  public function run($args) {
    echo date('Y-m-d H:i:s');
    $yiic = dirname(__FILE__) . '/../yiic';

    $process = new Process();


    $asins = $this->get_asin_list();
    $count = count($asins);
    $current = 0;
    
    while ($current < $count) {
      
      $aid = $asins[$current];

      if($process->get_alive_count() < 50) {
        $process->add(sprintf("%s coreplus %s", $yiic, $aid));
        echo sprintf("[%s]:%s coreplus %s \n", date("H:i:s"), $yiic, $aid);
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
    return Asin::model()->findAll();
     */
    return Redis::client()->hkeys('asins');
  }

}
?>
