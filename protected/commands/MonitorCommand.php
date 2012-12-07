<?php 

/**
* 	主控
*/
class MonitorCommand extends CConsoleCommand
{

	const T = 800000;

	public function run($args)
	{
		$formatter = "ASIN[%s],LEVEL[%s],NODE[%s],RTN[%s]\n";
		while (true) {
			foreach ($this->get_asin_list(10) as $asin) {
				do {
					$node = Nodes::getInstance()->select_node_to_run();
					$rtn = $this->push_to_queue($asin, $node);
					// if ($rtn === 0) echo 'node no response', "\t";
					printf($formatter, $asin['asin'], $asin['level'], $node['id'], $rtn);
					sleep(1);
				} while ($rtn === 0);	
				// echo 'sleep', "\n";			
				usleep(self::T);
			}
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