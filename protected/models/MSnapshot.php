<?php 

/**
* 异常时的页面快照
*/
class MSnapshot extends EMongoDocument
{

	public $dt;

	//reference to asin
	public $a;

	//listing content
	public $l_c;

	//buybox_content
	public $b_c;
	
	public static function model($className=__class__) 
	{
    	return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'snapshot';
	}
}