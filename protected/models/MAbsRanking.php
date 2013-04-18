<?php

class MAbsRanking extends EMongoDocument
{
	// 日期
	public $dt;
	
	// 分类ID
	public $cat;
	
	// 排名
	public $rank;
	
	// ASIN
	public $asin;
	
	public $name;
	
	public static function model($className=__class__) {
    return parent::model($className);
  }

  public function getCollectionName() {
    return 'abs_ranking';
  }

	public function rules() {
		return array(
			array('dt, cat, rank, asin', 'safe', 'on'=>'search'),
		);
	}
}
