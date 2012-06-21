<?php

/**
 * 核心服务类
 * 负责比较常用的操作
 */
class AsinService extends CComponent {

  private static $_instance;

  private static $_client;

  /**
   * 获取该类实例
   *
   */
  public static function getInstance() {
    if(!self::$_instance instanceof self)
      self::$_instance = new self;

    return self::$_instance;
  }

  private function __construct() {
    self::$_client = Redis::client();
  }

  private function __clone() {
  }

  public function bbrComputeFromSource($aid) {
    list($b1, $b2, $b3) = array(
      Yii::app()->params['fetch_frequency'] * 12,
      Yii::app()->params['fetch_frequency'] * 36,
      Yii::app()->params['fetch_frequency'] * 84
    );
    $fetches = self::$_client->lrange("asin:{$aid}:fetch", 0, $b3);
    foreach($fetches as $key=>$fid) {
      $list = self::$_client->lrange("fetch:{$fid}:list", 0, 100);
      foreach($list as $entity) {
        echo $entity, "\n";
      }
    }
      //
  }

  public function bbrComputeFromDiff($aid) {
  }

}
