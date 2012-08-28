<?php

class MListing extends EMongoDocument {
  //rank
  public $r;

  //price
  public $p;

  //seller
  public $s;

  //if_fba
  public $f;

  //if_buybox
  public $b;

  public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'listing';
  }

  public function rules() {
    return array(
      array('r, p, s, f, b', 'required'),
      array('s', 'safe', 'on'=>'search'),
    );
  }

  public function attributeLabels() {
    return array(
    );
  }

}
