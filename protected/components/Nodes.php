<?php 

/**
* 负责执行任务的节点
* Weighted Round-Robin Scheduling
* 
*/
class Nodes extends CComponent
{

	const TIME_AREA = '- 1 hour';

	private static $_instance;

	/**
	 * 当前调度的权值
	 * @var int
	 */
	private static $cw = 0;

	/**
	 * 上一次选择的node的编号
	 * @var [type]
	 */
	private static $i = -1;

	/**
	 * 所有node的权重列表
	 * @var array
	 */
	private static $nodes = array();

	private function __construct() {}

	private function __clone() {}

	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * [select_node_to_run description]
	 * @return Mnode $node 分配执行的node
	 * 
	 */
	public function select_node_to_run(/*array $queues*/)
	{
		$nodes = $this->get_nodes_list();
		// 起始cw
		if (self::$cw == 0) {
			self::$cw = $this->greatest_common_divisor(self::$nodes);
		}

		// 遍历边界处理
		if (self::$i == count($nodes) -1) {
			self::$i = -1;
			self::$cw += $this->greatest_common_divisor(self::$nodes);
			// cw边界处理
			if (self::$cw > max(self::$nodes)) {
				self::$cw = 0;
			}
		}
		self::$i ++;
		if (self::$nodes[self::$i] >= self::$cw) {
			return $nodes[self::$i];
		} else {
			return $this->select_node_to_run();
		}
	}

	private function gcd($a, $b)
	{
		if ($a == 0 || $b == 0)
        	return abs( max(abs($a), abs($b)) );
 		$r = $a % $b;
    	return ($r != 0) ? $this->gcd($b, $r) : abs($b);
	}

	private function greatest_common_divisor(array $value, $a = 0)
	{
		$b = array_pop($value);
		return ($b === null) ? (int) $a : $this->greatest_common_divisor($value, $this->gcd($a, $b));
	}

	/**
	 * 检查目前所有节点的运行状况
	 * 通过统计过去一个小时各node的以下参数来衡量
	 * 
	 * 执行的任务总数
	 * 当前的任务数
	 * 成功率
	 * 平均执行时间(成功的)
	 * 
	 * @return array $nodes 按照权重的高低顺序
	 */
	public function get_nodes_list()
	{
		if ($nodes = Yii::app()->cache->get('sys.nodes')) {
			if (count(self::$nodes) == 0) {
				foreach ($nodes as $node) {
					$nws[] = $node['weight'];
				}
				self::$nodes = $nws;
			}
			return $nodes;
		}

		Yii::trace('cache expired...', get_class($this).'.get_nodes_list');
		$n_t = $n_c = $a_c = 0;
		foreach (MNode::model()->findAll() as $node) {
			$node['n_total'] = $this->count_total_tasks($node);
			$node['n_current'] = $this->count_current_tasks($node);
			$node['n_rate'] = $node['n_total'] ? round($this->count_success_tasks($node) / $node['n_total'], 2) : 0;
			$node['n_time'] = $this->count_avg_time_cost($node);

			$n_t += $node['n_total'];
			$n_c += $node['n_current'];
			$a_c += $node['n_time'];

			$node->save(false);
		}

		$n_nodes = MNode::model()->count();
		$n_t = $node['n_total'] / $n_nodes;
		$n_c = $node['n_current'] / $n_nodes;
		$a_c = $a_c / $n_nodes;

		/**
		 * 权重(正整数)算法
		 * round(radio+priority+s_rate-total-avg_cost-current)
		 * 项目			初始值	评分算法
		 * radio 		1 		const
		 * priority 	1		const
		 * total 		3		sum(total)/x
		 * current 		5		sum(current)/x
		 * s_rate 		10		const
		 * avg_cost 	10		avg()/x
		 */
		foreach (MNode::model()->findAll() as $node) {
			$node['weight'] = round(
				1 * $node['radio'] 
				+ 1 * $node['priority'] 
				+ 3 * $node['n_rate']
				+ 5 * ($node['n_total'] > 0 ? $n_t / $node['n_total'] : 0)
				+ 10 * ($node['n_current'] > 0 ? $n_c / $node['n_current'] : 0)
				+ 10 * ($node['n_time'] > 0 ? $a_c/$node['n_time'] : 0)
			);
			$node->save();
		}

		$criteria = new EMongoCriteria();
		// $criteria->addCond();
		$criteria->sort('priority', -1);
		$nodes = MNode::model()->findAll($criteria);
		foreach ($nodes as $node) {
			$nws[] = $node['weight'];
		}
		self::$nodes = $nws;

		Yii::app()->cache->set('sys.nodes', $nodes, 60 * 10, new CGlobalStateCacheDependency('MNode'));
		return $nodes;
	}

	/**
	 * 获取全部的任务总数
	 * @param  MNode  $node
	 * @return int       
	 */
	private function count_total_tasks(MNode $node)
	{
		$criteria = new EMongoCriteria();
		$criteria->addCond('sc', '==', (int) $node['id']);
		$criteria->addCond('t', '>=', new MongoDate(strtotime(self::TIME_AREA)));
		return MFetching::model()->count($criteria);
	}

	/**
	 * 获取当前的任务总数
	 * @param  MNode  $node 
	 * @return int       
	 */
	private function count_current_tasks(MNode $node)
	{
		$criteria = new EMongoCriteria();
		$criteria->addCond('sc', '==', (int) $node['id']);
		$criteria->addCond('t', '>=', new MongoDate(strtotime(self::TIME_AREA)));
		$criteria->addCond('rt', '==', null);
		return MFetching::model()->count($criteria);
	}

	/**
	 * 获取成功的任务总数
	 * @param  MNode  $node 
	 * @return int       
	 */
	private function count_success_tasks(MNode $node)
	{
		$criteria = new EMongoCriteria();
		$criteria->addCond('sc', '==', (int) $node['id']);
		$criteria->addCond('t', '>=', new MongoDate(strtotime(self::TIME_AREA)));
		$criteria->addCond('rt', '!=', null);
		$criteria->addCond('status', '==', 200);
		return MFetching::model()->count($criteria);
	}

	/**
	 * 计算平均执行时间(成功的任务)
	 * 如果没有结果 则返回-1
	 * @param  MNode  $node 
	 * @return int|fasle       执行的秒数
	 */
	private function count_avg_time_cost(MNode $node)
	{
		$costs = array();
		$criteria = new EMongoCriteria();
		$criteria->addCond('sc', '==', (int) $node['id']);
		$criteria->addCond('t', '>=', new MongoDate(strtotime(self::TIME_AREA)));
		$criteria->addCond('rt', '!=', null);
		$criteria->addCond('status', '==', 200);
		foreach (MFetching::model()->findAll($criteria) as $item) {
			$costs[] = $item['rt']->sec - $item['t']->sec;
		}
		return count($costs) > 0 ? round(array_sum($costs) / count($costs), 2) : -1;
	}

}