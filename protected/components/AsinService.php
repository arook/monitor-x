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

  /**
   * 获取指定ASIN的bbr摘要
   *
   * @param int $aid
   * @return
   */
  public function bbrSummary($aid) {
    $sids = array('A2KV19AYUKS3X0', 'A2GV4BOQ11MZEE', 'A26E6OJSZ6UKD8');
    $summary = array();
    foreach($sids as $sid) {
      $summary[$sid]['1'] = $this->bbrGet("bbr:{$aid}:{$sid}:1");
      $summary[$sid]['2'] = $this->bbrGet("bbr:{$aid}:{$sid}:2");
      $summary[$sid]['3'] = $this->bbrGet("bbr:{$aid}:{$sid}:3");
    }
    return $summary;
  }

  /**
   * 从原始数据计算给定ASIN对没一个卖家的BBR，并保存
   * 长key = bbr:{$aid}:{$sid}:{$level}
   */
  public function bbrComputeFromSource($aid) {
    list($b1, $b2, $b3) = array(
      Yii::app()->params['fetch_frequency'] * 12,
      Yii::app()->params['fetch_frequency'] * 36,
      Yii::app()->params['fetch_frequency'] * 84
    );
    $bbrs = array('1'=>array(), '2'=>array(), '3'=>array());
    $fetches = self::$_client->lrange("asin:{$aid}:fetch", 0, $b3-1);
    $key = 0;
    foreach($fetches as $key=>$fid) {
      $list = self::$_client->lrange("fetch:{$fid}:list", 0, -1);
      foreach($list as $entity) {
        $tmp = CJSON::decode($entity);
        if($tmp['if_buybox']) {
          if(array_key_exists($tmp['sid'], $bbrs[3])) {
            $bbrs[3][$tmp['sid']] += 1;
          } else {
            $bbrs[3][$tmp['sid']] = 1;
          }
        }

      }

      if($key+1 == $b1) {
        $bbrs[1] = $bbrs[3];
      }
      if($key+1 == $b2) {
        $bbrs[2] = $bbrs[3];
      }
    }

    if($key < $b1) {
      $bbrs[1] = $bbrs[3];$bbrs[3]=array();
    } elseif ($key < $b2) {
      $bbrs[2] = $bbrs[3];$bbrs[3]=array();
    }

    foreach($bbrs as $level=>$bbr) {
      foreach($bbr as $seller=>$num) {
        $sum = 'b'.$level;
        $this->bbrSet("bbr:{$aid}:{$seller}:{$level}", round($num/$$sum, 4));
      }
    }
  }

  /**
   * 储存指定ASIN ID，指定SELLER ID，指定LEVEL的BBR
   *
   * @param string $key bbr:{$aid}:{$sid}:{$level}
   * @param int $aid
   * @param int $sid
   * @param int $level in 1, 2, 3
   */
  public function bbrSet($key, $val) {
    list($k, $f) = $this->bbrKeyField($key);
    self::$_client->hset($k, $f, $val);
  }

  public function bbrGet($key) {
    list($k, $f) = $this->bbrKeyField($key);
    return self::$_client->hget($k, $f);
  }

  /**
   * 内存优化，将bbr平均分布
   *
   * @param string $key
   * @return array array(key, field)
   */
  private function bbrKeyField($key) {
    list(,$aid,$sid,$level) = explode(':', $key);
    $k = 'bbr:' . substr($aid, 0, -2);
    $f = substr($aid, -2) . ':' .$sid . ':' . $level;
    return array($k, $f);
  }

  public function bbrComputeFromDiff($aid) {
  }

}
