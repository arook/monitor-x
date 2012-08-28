<?php

class MFetching extends EMongoDocument {
  //time
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

}