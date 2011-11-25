<?php

class MainCommand extends CConsoleCommand {

  public function run($args) {
    echo date('Y-m-d H:i:s');
    $yiic = dirname(__FILE__) . '/../yiic';
    $asins = $this->get_asin_list();
    foreach ($asins as $asin) {
      echo sprintf("%s core %s %s", $yiic, $asin->id, $asin->asin);
      exec(sprintf("%s core %s %s", $yiic, $asin->id, $asin->asin));
    }
  }

  private function get_asin_list() {
    return Asin::model()->findAll();
  }

}
?>
