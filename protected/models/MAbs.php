<?php

class MAbs extends EMongoDocument
{
	// 大项分类
	public $c1;
	
	// 小项分类
	public $c2;
	
	// 链接
	public $link;
	
	public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'abs';
  }

	public function rules() {
		
	}
}
