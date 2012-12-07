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

}
