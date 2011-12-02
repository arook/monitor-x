<?php

/**
 * This is the model class for table "buybox_rate".
 *
 * The followings are the available columns in table 'buybox_rate':
 * @property string $rate_id
 * @property string $type
 * @property string $dt
 * @property string $asin
 * @property string $seller
 * @property integer $if_fba
 * @property string $rate
 *
 * The followings are the available model relations:
 * @property Asin $asin0
 */
class BuyboxRate extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return BuyboxRate the static model class
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
		return 'buybox_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dt, asin, seller, if_fba, rate', 'required'),
			array('if_fba', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>1),
			array('asin', 'length', 'max'=>11),
			array('seller', 'length', 'max'=>100),
			array('rate', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('rate_id, type, dt, asin, seller, if_fba, rate', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'rate_id' => 'Rate',
			'type' => 'Type',
			'dt' => 'Dt',
			'asin' => 'Asin',
			'seller' => 'Seller',
			'if_fba' => 'If Fba',
			'rate' => 'Rate',
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

		$criteria->compare('rate_id',$this->rate_id,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('dt',$this->dt,true);
		$criteria->compare('asin',$this->asin,true);
		$criteria->compare('seller',$this->seller,true);
		$criteria->compare('if_fba',$this->if_fba);
		$criteria->compare('rate',$this->rate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}