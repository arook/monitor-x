<?php 

/**
* 	主控
* 	@todo 处理超时
*/
class MonitorCommand extends CConsoleCommand
{
	/*
		TODO Handler 404
	*/

	// 请求发送的时间间隔(ms)
	const T = 500000;

	// 任务超时时间(s)
	const TIMEOUT = 30;
	
  // 公共的locking时间数列(s)
	private static $_lock_time = 300;

	public function run($args)
	{
		file_put_contents(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'monitor.send.pid', getmypid());
		
		$formatter = "DT[%s],ASIN[%s],LEVEL[%s],NODE[%s],RTN[%s],RTRY[%s]\n";
		while (true) {
			$this->check_item_timeout();
			$this->check_node_unlock();
			$asins = $this->get_asin_list(10);
			if (count($asins) == 0) {
				echo 'empty queue,sleeping 10s...', "\n";
				sleep(10);
			} else {
				foreach ($asins as $asin) {
				  $queue_try = 0;
					do {
						// @TODO:提高效率
						$node = Nodes::getInstance()->select_node_to_run();
						if ($node->if_locked) {
						  $rtn = 0;
						  continue;
						}
						$rtn = $this->push_to_queue($asin, $node);
						// if ($rtn === 0) echo 'node no response', "\t";
						printf($formatter, date('H:i:s'), $asin['asin'], $asin['level'], $node['id'], $rtn, $asin['_r']);
					} while ($rtn === 0 and $queue_try++ < 10);	
					// echo 'sleep', "\n";			
					usleep(self::T);
				}
			}
		}
 	}

	private function check_item_timeout()
	{
		$criteria = new EMongoCriteria;
		$criteria->addCond('status', '==', null);
		$criteria->addCond('rt', '==', null);
		$criteria->addCond('t', '<=', new MongoDate(strtotime("- " . self::TIMEOUT . ' second')));
		foreach (MFetching::model()->findAll($criteria) as $fetching) {
			$fetching['status'] = 504;
			$fetching['rt'] = new MongoDate();
			$fetching['rc'] = null;
			$fetching->save(false);
			
		} ;
	}
	
	private function check_node_unlock($value='')
	{
	  /*
	   TODO 想一个好点的算法
	  */
		echo 'check_node_unlock', "\n";
		$locking_nodes = MNode::model()->findAllByAttributes(array('if_locked'=>true));
		foreach ($locking_nodes as $node) {
      $last_lock = MNodeLock::model()->findByPK($node->last_lock['$id']);
		  if(time() - $last_lock->lock_start->sec > self::$_lock_time)
		    $node->unlock();
		}
	}

	/**
	 * 搜索条件及优先级
	 * 1.已经到了执行时间
	 * 2.相同时间，优先级高的排前面
	 */
	private function get_asin_list($count) 
	{
		$criteria = new EMongoCriteria();
		$criteria->addCond('next', '<=', new MongoDate());
		$criteria->addCond('_r', '<=', 10);
		$criteria->limit($count);
		//频率高的总是放到前面跑
		$criteria->sort('level', 1);
		$criteria->sort('next', 1);
		return MAsin::model()->findAll($criteria);
	}

	/**
	 * 选择合适的NODE来运行指定的queue
	 * 
	 * @param  MAsin $asin 目标队列
	 * @return boolean	$success	是否成功
	 */
	private function push_to_queue(MAsin $asin, MNode $node)
	{
		$f = new MFetching;
		$f->t = new MongoDate();
    	$f->a = MAsin::model()->getCollection()->createDbRef($asin);
    	$f->sc = (int) $node->id;
    	$f->l = array();
		$f->save(false);

		$asin->dt = new MongoDate();
        $asin->next = new MongoDate($asin->dt->sec + $asin->level);
        $asin->_x = true;
        $asin->save(false);

		return Redis::client()->publish('monitor_' . $node->id, $asin->asin . ',' . $f->_id);
	}
}