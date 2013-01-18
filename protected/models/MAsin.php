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

  //最后一次抓取
  public $last;

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
  
  /************ Fetching Stastics ****************/
  
  public function fetchingSummary()
  {
    # code...
  }
  
  public function buyboxLine()
  {
    MFetching::model()->findAllByAttributes(array('a.$id'=>$this->_id));
  }

}

/****

key {
  asin,
  sid,
  
}

var mapFunction = function() {
	var key = {
		asin: this.a,
		dt: this.dt,
		sku: this.sku
	};
	var value = {
		quantity: this.quantity,
		disposition: this.disposition
	};

	emit( key, value );
};

var reduceFunction = function(key, values) {
	var reducedObject = {
		quantity:0
	};

	values.forEach(function(value) {
		if (value.disposition == 'SELLABLE') {
			reducedObject.quantity += value.quantity;
		};		
	});
	
	return reducedObject;
};

db.iws_inventory_snapshots.mapReduce( mapFunction,
	reduceFunction,
	{out: { reduce: "iws_inventory_stat" }}
);

 */

