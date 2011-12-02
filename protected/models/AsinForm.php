<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class AsinForm extends CFormModel
{
	public $asin;
	public $date_from;
	public $date_to;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('asin, date_from, date_to', 'required'),
			// rememberMe needs to be a boolean
			array('date_from date_to', 'date'),
		);
	}

  /**
   * set default value
   */
  public function init()
  {
    $this->date_from = date('Y-m-d');
    $this->date_to = date('Y-m-d');
  }

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
		);
	}

}
