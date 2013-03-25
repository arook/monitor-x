<?php

class TCommand extends CConsoleCommand {
  public function run() {
    // $f =  MFetching::model()->find();
    // foreach ($f['l'] as $key => $value) {
    //   var_dump($value['s']['$id']->{'$id'});
    // }
    // return;
    
    $client = new SoapClient('http://monitor/?r=api');
    try {
      // $resp = $client->getPricing('B002DPO222', 24);
      $resp = $client->getBbxPricing('B002DPO222', 24);
      var_dump($resp);
    } catch (Exception $e) {
      var_dump($e);
    }
  }
}
