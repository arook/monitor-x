<?php

class MAsin extends EMongoDocument {
  public $asin;
  public $fs;
  public $fetching;
  public $level;

  public $dt;
  public $next;

  //正在抓取
  public $_x;

  //重试的次数
  public $_r;

  //最后一条错误信息
  public $_e;

  //原始html
  public $_sl;
  public $_sb;

  public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'asin';
  }

  public function rules() {
    return array(
      array('asin', 'required'),
      array('fs, level', 'numerical', 'integerOnly'=>true),
      array('asin, next, level, _r', 'safe', 'on'=>'search'),
    );
  }

  public function attributeLabels() {
    return array(
      'asin' => 'ASIN',
    );
  }

}
