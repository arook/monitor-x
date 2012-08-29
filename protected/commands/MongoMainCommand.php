<?php

/**
 *
 * 按照next的先后顺序
 * 每次拿100个符合条件的ASIN发送抓取请求
 * 发送抓取请求的时间间隔为1S,并不管返回结果,只负责发送请求
 *
 * 在没有队列的情况下，ASIN一分钟拿一次
 */
class MongoMainCommand extends CConsoleCommand {

  //允许最大的线程数
  const MAX_THREAD = 5;

  //抓取周期,单位是micro_seconds
  const T = 1000000;

  public function run($args) {
    $yiic = dirname(__FILE__) . '/../yiic';

    $handles = $pipes = array();
    $desc = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));


    while (true) {
      $nactive = 0;
      //关闭已经完成的handles
      foreach($handles as $key=>$handle) {
        $status = proc_get_status($handle);
        if($status['running'] === true) {
          $nactive++;
        } else {
          proc_close($handle);
          unset($handles[$key]);
        }
      }

      if($nactive >= self::MAX_THREAD) {
        echo 'to much Threads, sleep 3 sec...', "\n";
        sleep(3);
        continue;
      }

      $asins = $this->get_asin_list(self::MAX_THREAD - $nactive);
      if(!$asins) {
        echo 'nothing to do, sleep 10sec...', "\n";
        sleep(10);
        continue;
      }

      foreach ($asins as $asin) {
        echo sprintf("[%s][%s/%s][%s][%s] %s %s\n", 
          date("H:i:s"),
          count($handles),
          self::MAX_THREAD,
          date('H:i:s', @$asin->next->sec),
          $asin->level,
          $asin->_id, $asin->asin
        );
        $asin->dt = new MongoDate();
        $asin->next = new MongoDate($asin->dt->sec + $asin->level);
        $asin->_x = true;
        $asin->save();

        $handles[] = proc_open(sprintf('%s mongocore %s', $yiic, $asin->asin), $desc, $pipes[], null, $_ENV);
        usleep(self::T);
      }
    }
  }

  /**
   * 搜索条件及优先级
   * 1.已经到了执行时间
   * 2.相同时间，优先级高的排前面
   */
  private function get_asin_list($count) {
    $criteria = new EMongoCriteria();
    $criteria->addCond('next', '<=', new MongoDate());
    $criteria->limit($count);
    //频率高的总是放到前面跑
    $criteria->sort('level', 1);
    $criteria->sort('next', 1);
    return MAsin::model()->findAll($criteria);
  }

  /**
   * 获取当前正在抓取的总线程数
   */
  private function get_active_asin() {
    $criteria = new EMongoCriteria();
    $criteria->addCond('_x', '==', true);
    return MAsin::model()->count($criteria);
  }

}
?>
