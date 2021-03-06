<?php

class AbsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	// public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','asin'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin', '@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new MAbsRanking;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MAbsRanking']))
		{
			$model->attributes=$_POST['MAbsRanking'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MAbsRanking']))
		{
			$model->attributes=$_POST['MAbsRanking'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new EMongoDocumentDataProvider('MAbsRanking');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new MAbsRanking('search');
		$model->unsetAttributes();
		
		$cats = array();
		foreach (MAbs::model()->findAll() as $cat) {
			$cats[(string) $cat->_id] = $cat->c1 . ($cat->c2 ? ('->' . $cat->c2) : '');
		}

		if(isset($_POST['MAbsRanking'])) {
			$model->setAttributes($_POST['MAbsRanking']);
		} else {
			$this->render('admin', array(
				'model'=>$model,
				'cats'=>$cats,
				'ranking'=>new CArrayDataProvider(array()),
			));
			return;
		}

		$c = new EMongoCriteria;
		$model->cat ? $c->{'cat.$id'} = new MongoId($model->cat) : '';
		$model->dt ? $c->dt = new MongoDate(strtotime($model->dt)) : '';
		$c->sort('rank', 1);

		$ranking = new CArrayDataProvider(
			MAbsRanking::model()->findAll($c),
			array(
				'keyField' => false,
				'pagination' => array(
					'pageSize' => 100,
				),
			)
		);
		
		// var_dump($ranking);

		$this->render('admin', array(
			'model'=>$model,
			'cats'=>$cats,
			'ranking'=>$ranking,
		));
	}
	
	public function actionAsin($asin)
	{
		$this->layout=false;
		$c = new EMongoCriteria;
		$c->asin = $asin;
		$c->sort('dt', -1);
		$history = MAbsRanking::model()->findAll($c);
		
		$this->render('asin', array(
			'history' => $history,
			'asin' => $asin,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=MAbsRanking::model()->findByPk(new MongoId($id));
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='mabs-ranking-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
