<?php

class TCommand extends CConsoleCommand {
  public function run() {
    $asins = Redis::client()->hkeys('asins');
    $m = new Mongo();
    $db = $m->monitor;
    $c = $db->asins;
    foreach($asins as $asin) {
      $obj = array('v'=>$asin);
      $c->insert($obj);
    }
  }
}
