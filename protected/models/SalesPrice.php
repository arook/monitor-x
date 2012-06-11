<?php

/**
 * This is the model class for table "sales_price".
 *
 * The followings are the available columns in table 'sales_price':
 * @property string $price_id
 * @property string $asin
 * @property string $type
 * @property string $price
 * @property integer $if_fba
 * @property string $seller
 */
class SalesPrice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SalesPrice the static model class
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
		return 'sales_price';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('asin, type, price, if_fba, seller', 'required'),
			array('if_fba', 'numerical', 'integerOnly'=>true),
			array('asin', 'length', 'max'=>11),
			array('type', 'length', 'max'=>2),
			array('price', 'length', 'max'=>6),
			array('seller', 'length', 'max'=>22),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('price_id, asin, type, price, if_fba, seller', 'safe', 'on'=>'search'),
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
			'price_id' => 'Price',
			'asin' => 'Asin',
			'type' => 'Type',
			'price' => 'Price',
			'if_fba' => 'If Fba',
			'seller' => 'Seller',
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

		$criteria->compare('price_id',$this->price_id,true);
		$criteria->compare('asin',$this->asin,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('if_fba',$this->if_fba);
		$criteria->compare('seller',$this->seller,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}