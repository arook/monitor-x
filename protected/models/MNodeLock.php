<?php

class MNodeLock extends EMongoDocument {
	public $node;
	public $lock_start;
	public $lock_end;

	public static function model($className=__class__) {
		return parent::model($className);
	}
	
	public function getCollectionName()
	{
		return 'node_lock';
	}
}
