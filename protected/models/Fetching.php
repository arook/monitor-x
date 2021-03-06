<?php

/**
 * This is the model class for table "fetching".
 *
 * The followings are the available columns in table 'fetching':
 * @property string $id
 * @property string $asin
 * @property string $dt
 *
 * The followings are the available model relations:
 * @property Asin $asin0
 * @property FetchingDetail[] $fetchingDetails
 */
class Fetching extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Fetching the static model class
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
		return 'fetching';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('asin, dt', 'required'),
			array('asin', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, asin, dt', 'safe', 'on'=>'search'),
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
			'asin0' => array(self::BELONGS_TO, 'Asin', 'asin'),
			'fetchingDetails' => array(self::HAS_MANY, 'FetchingDetail', 'fetching_id'),
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
			'dt' => 'Dt',
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
		$criteria->compare('dt',$this->dt,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}