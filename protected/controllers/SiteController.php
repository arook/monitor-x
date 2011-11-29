<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
    $model = new AsinForm;
    if (isset($_POST['AsinForm'])) {
      $model->attributes = $_POST['AsinForm'];
    }
    
    $data_provider = new CActiveDataProvider('Fetching', array(
      'criteria'=>array(
        'condition'=>'t.asin=\''.$model->asin.'\'',
        'with'=>array('asin0', 'fetchingDetails'),
      ),
    ));
   
    $this->render('index', array(
      'dataProvider'=>$data_provider,
      'model'=>$model,
    ));
	}

  public function actionAsinList()
  {
    $res = array();

    if (isset($_GET['term'])) {
      $sql = "select `asin` from `asin` where asin like :asin";
      $command = Yii::app()->db->createCommand($sql);
      $command->bindValue(":asin", '%'.$_GET['term'].'%', PDO::PARAM_STR);
      $res = $command->queryColumn();
    }

    echo CJSON::encode($res);
    Yii::app()->end();
  }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

  /**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

}
