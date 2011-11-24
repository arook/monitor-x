<?php

/**
 * This is the model class for table "fetching_detail".
 *
 * The followings are the available columns in table 'fetching_detail':
 * @property string $id
 * @property string $fetching_id
 * @property integer $rank
 * @property string $seller
 * @property double $shipping_price
 * @property double $sell_price
 * @property integer $if_buybox
 * @property integer $if_fba
 */
class FetchingDetail extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return FetchingDetail the static model class
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
		return 'fetching_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fetching_id, rank, seller, shipping_price, sell_price, if_fba', 'required'),
			array('rank, if_buybox, if_fba', 'numerical', 'integerOnly'=>true),
			array('shipping_price, sell_price', 'numerical'),
			array('fetching_id', 'length', 'max'=>11),
			array('seller', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, fetching_id, rank, seller, shipping_price, sell_price, if_buybox, if_fba', 'safe', 'on'=>'search'),
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
			'fetching_id' => 'Fetching',
			'rank' => 'Rank',
			'seller' => 'Seller',
			'shipping_price' => 'Shipping Price',
			'sell_price' => 'Sell Price',
			'if_buybox' => 'If Buybox',
			'if_fba' => 'If Fba',
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
		$criteria->compare('fetching_id',$this->fetching_id,true);
		$criteria->compare('rank',$this->rank);
		$criteria->compare('seller',$this->seller,true);
		$criteria->compare('shipping_price',$this->shipping_price);
		$criteria->compare('sell_price',$this->sell_price);
		$criteria->compare('if_buybox',$this->if_buybox);
		$criteria->compare('if_fba',$this->if_fba);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}