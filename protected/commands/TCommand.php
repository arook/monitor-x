<?php

class TCommand extends CConsoleCommand {
  public function run() {
    $asins = Asin::model()->findAll();
    foreach($asins as $asin) {
      Redis::client()->hset('asins', $asin->asin, $asin->id);
    }
  }
}
