<?php

/**
 * This is the model class for table "asin".
 *
 * The followings are the available columns in table 'asin':
 * @property string $id
 * @property string $asin
 * @property integer $retry
 */
class Asin extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Asin the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'asin';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('asin', 'required'),
			array('retry', 'numerical', 'integerOnly'=>true),
			array('asin', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, asin, retry', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'asin' => 'Asin',
			'retry' => 'Retry',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('asin',$this->asin,true);
		$criteria->compare('retry',$this->retry);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}