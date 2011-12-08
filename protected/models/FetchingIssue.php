<?php

/**
 * This is the model class for table "fetching_issue".
 *
 * The followings are the available columns in table 'fetching_issue':
 * @property string $fetching_id
 * @property string $content_listing
 * @property string $content_buybox
 *
 * The followings are the available model relations:
 * @property Fetching $fetching
 */
class FetchingIssue extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return FetchingIssue the static model class
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
		return 'fetching_issue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fetching_id, content_listing, content_buybox', 'required'),
			array('fetching_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('fetching_id, content_listing, content_buybox', 'safe', 'on'=>'search'),
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
			'fetching' => array(self::BELONGS_TO, 'Fetching', 'fetching_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'fetching_id' => 'Fetching',
			'content_listing' => 'Content Listing',
			'content_buybox' => 'Content Buybox',
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

		$criteria->compare('fetching_id',$this->fetching_id,true);
		$criteria->compare('content_listing',$this->content_listing,true);
		$criteria->compare('content_buybox',$this->content_buybox,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}