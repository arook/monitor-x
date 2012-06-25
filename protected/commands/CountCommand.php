<?php

/**
 * 计算BBR的分布
 * 档位分为1, 2, 3
 * 取样区间分别为 12H, 36H, 84H
 * 对应的取值个数 FREQUENCY*12, FREQUENCY*36, FREQUENCY*84
 *
 * 从基础数据计算，不同于增值计算
 */
class CountCommand extends CConsoleCommand {

  private $_seller = array(
    'BetterStuff LowerPrice', 
    'E-Bundle',
    'Shopping Cloud',
  );

  public function run($args) {
    $dt = date("Y-m-d H:i:s");

    $asins = Redis::client()->hvals('asins');
    foreach($asins as $aid) {
      AsinService::getInstance()->bbrAndSalesComputeFromSource($aid);
    }

  }

}
