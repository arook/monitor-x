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

  public function sellers() {
    return array('A2KV19AYUKS3X0', 'A2GV4BOQ11MZEE', 'A26E6OJSZ6UKD8');
  }

  /**
   * 时间节点
   */
  public function points() {
    return array(
      Yii::app()->params['fetch_frequency'] * 12,
      Yii::app()->params['fetch_frequency'] * 36,
      Yii::app()->params['fetch_frequency'] * 84
    );
  }

  /**
   * 获取指定ASIN的bbr摘要
   *
   * @param int $aid
   * @return
   */
  public function bbrSummary($aid) {
    $summary = array();
    foreach($this->sellers() as $sid) {
      $summary[$sid]['1'] = $this->keyGet("bbr:{$aid}:{$sid}:1");
      $summary[$sid]['2'] = $this->keyGet("bbr:{$aid}:{$sid}:2");
      $summary[$sid]['3'] = $this->keyGet("bbr:{$aid}:{$sid}:3");
    }
    return $summary;
  }

  /**
   * 获取指定ASIN的Sales摘要
   *
   * @param int $aid
   * @return array
   */
  public function salesSummary($aid) {
    $summary = array();
    foreach($this->sellers() as $sid) {
      $summary[$sid]['1'] = $this->keyGet("sales:{$aid}:{$sid}:1");
      $summary[$sid]['2'] = $this->keyGet("sales:{$aid}:{$sid}:2");
      $summary[$sid]['3'] = $this->keyGet("sales:{$aid}:{$sid}:3");
    }
    return $summary;
  }

  /**
   * 从原始数据计算给定ASIN对没一个卖家的BBR，并保存
   * 长key = bbr:{$aid}:{$sid}:{$level}
   */
  public function bbrAndSalesComputeFromSource($aid) {
    list($b1, $b2, $b3) = $this->points();
    $sellers = $this->sellers();
    $bbrs = array('1'=>array(), '2'=>array(), '3'=>array());
    $sales = array('1'=>array(), '2'=>array(), '3'=>array());
    $fetches = self::$_client->lrange("asin:{$aid}:fetch", 0, $b3-1);
    $key = 0;
    foreach($fetches as $key=>$fid) {
      $list = self::$_client->lrange("fetch:{$fid}:list", 0, -1);
      foreach($list as $entity) {
        $tmp = CJSON::decode($entity);
        if(!in_array($tmp['sid'], $sellers) || !$tmp['if_fba'])
          continue;

        //sales
        if(array_key_exists($tmp['sid'], $sales[3])) {
          $sales[3][$tmp['sid']]['sum'] += $tmp['sell_price'] + $tmp['shipping_price'];
          $sales[3][$tmp['sid']]['count'] += 1;
        } else {
          $sales[3][$tmp['sid']]['sum'] = $tmp['sell_price'] + $tmp['shipping_price'];
          $sales[3][$tmp['sid']]['count'] = 1;
        }

        //bbr
        if(array_key_exists($tmp['sid'], $bbrs[3])) {
          if($tmp['if_buybox'])
            $bbrs[3][$tmp['sid']]['sum'] += 1;
          $bbrs[3][$tmp['sid']]['count'] += 1;
        } else {
          if($tmp['if_buybox']) {
            $bbrs[3][$tmp['sid']]['sum'] = 1;
          } else {
            $bbrs[3][$tmp['sid']]['sum'] = 0;
          }
          $bbrs[3][$tmp['sid']]['count'] = 1;
        }

      }

      if($key+1 == $b1) {
        $bbrs[1] = $bbrs[3];
        $sales[1] = $sales[3];
      }
      if($key+1 == $b2) {
        $bbrs[2] = $bbrs[3];
        $sales[2] = $sales[3];
      }
    }

    if($key < $b1) {
      $bbrs[1] = $bbrs[3];$bbrs[3]=array();
      $sales[1] = $sales[3];$sales[3]=array();
    } elseif ($key < $b2) {
      $bbrs[2] = $bbrs[3];$bbrs[3]=array();
      $sales[2] = $sales[3];$sales[3]=array();
    }

    foreach($bbrs as $level=>$bbr) {
      foreach($bbr as $seller=>$num) {
        $this->keySet("bbr:{$aid}:{$seller}:{$level}", round($num['sum']/$num['count'], 4));
      }
    }

    foreach($sales as $level=>$sale) {
      foreach($sale as $seller=>$num) {
        $this->keySet("sales:{$aid}:{$seller}:{$level}", round($num['sum']/$num['count'], 4));
      }
    }

  }

  /**
   * 储存指定ASIN ID，指定SELLER ID，指定LEVEL的BBR
   *
   * @param string $key [bbr|sales]:{$aid}:{$sid}:{$level}
   * @param int $aid
   * @param int $sid
   * @param int $level in 1, 2, 3
   */
  public function keySet($key, $val) {
    list($k, $f) = $this->keyField($key);
    self::$_client->hset($k, $f, $val);
  }

  public function keyGet($key) {
    list($k, $f) = $this->keyField($key);
    return self::$_client->hget($k, $f);
  }

  /**
   * 内存优化，将bbr平均分布
   *
   * @param string $key
   * @return array array(key, field)
   */
  private function keyField($key) {
    list($type,$aid,$sid,$level) = explode(':', $key);
    $k = $type . ':' . substr($aid, 0, 2);
    $f = substr($aid, 2) . ':' .$sid . ':' . $level;
    return array($k, $f);
  }

  public function bbrComputeFromDiff($aid) {
    list($b1, $b2, $b3) = $this->points();
    $bnew = self::$_client->lindex("asin:{$aid}:fetch", 0);
    $b1old = self::$_client->lindex("asin:{$aid}:fetch", $b1+1);
    $b2old = self::$_client->lindex("asin:{$aid}:fetch", $b2+1);
    $b3old = self::$_client->lindex("asin:{$aid}:fetch", $b3+1);

  }

}
