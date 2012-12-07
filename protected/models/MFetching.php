<?php

class MFetching extends EMongoDocument {
  //任务发起的时间
  public $t;

  //asin
  public $a;

  //list
  public $l;

  //list html
  public $lh;

  //buybox html
  public $bh;

  //buybox price
  public $bp;

  //buybox rank
  public $br;

  //参与当前任务发起的节点ID
  public $sc;

  //参与当前任务接收的节电ID
  public $rc;

  //任务返回的时间
  public $rt;



  public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'fetching';
  }

  public function rules() {
    return array(
      array('t, a, l, bp, br', 'required'),
      //array('', 'safe', 'on'=>'search'),
    );
  }

  public function attributeLabels() {
    return array(
    );
  }

  public function behaviors() {
    return array(
      'embeddedArrays' => array(
        'class' => 'ext.YiiMongoDbSuite.extra.EEmbeddedArraysBehavior',
        'arrayPropertyName' => 'l',
        'arrayDocClassName' => 'MListing',
      ),
    );
  }

  public function scopes() {
    return array(
      'latest'=>array(
        'order' => 't -1',
        'limit' => 1,
      ),
    );
  }

}
