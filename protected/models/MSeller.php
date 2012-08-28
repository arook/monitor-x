<?php

class MSeller extends EMongoDocument {
  public $sid;
  public $name;
  public $avatar;

  public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'seller';
  }

  public function rules() {
    return array(
      array('sid, name', 'required'),
      array('sid, name', 'safe', 'on'=>'search'),
    );
  }

  public function attributeLabels() {
    return array(
      'asin' => 'ASIN',
    );
  }

}
