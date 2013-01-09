<?php

class MNode extends EMongoDocument {
  
  // 服务器编号
  public $id;

  // 描述信息
  public $des;

  // 权重 必须是正整数
  public $weight;

  // 比率
  public $radio;

  // 优先权
  public $priority;

  // 执行的总任务
  public $n_total;

  // 当前运行中
  public $n_current;

  // 当前成功率
  public $n_rate;
  
  // 平均执行时间
  public $n_time;

  // 是否lock
  public $if_locked;

  // locking history
  public $last_lock;
	

  public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'node';
  }

  public function rules() {
    return array(
      array('id, radio, priority, des', 'required'),
      array('id, radio, priority', 'numerical'),
      array('id, radio, priority', 'safe', 'on'=>'search'),
    );
  }

  public function attributeLabels() {
    return array(
    );
  }

  public function afterSave() {
    Yii::app()->setGlobalState('MNode', time());

  }

  public function lock()
  {
  	$this->if_locked = true;
    $lock = new MNodeLock;
    $lock->node = $this->getCollection()->createDbRef($this);
    $lock->lock_start = new MongoDate();
    $lock->save();
    $this->last_lock = MNodeLock::model()->getCollection()->createDbRef($lock);
    $this->save();
  }

  public function unlock()
  {
  	$this->if_locked = false;
  	$this->last_lock = null;
    $this->save();
  }

}