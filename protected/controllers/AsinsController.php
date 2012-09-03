<?php

class AsinsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
//	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
//			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'samplefile'),
				'users'=>array('@'),
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
		$model=new MAsin;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MAsin']))
		{
			$model->attributes=$_POST['MAsin'];
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

		if(isset($_POST['MAsin']))
		{
			$model->attributes=$_POST['MAsin'];
      $model->level = intval($model->level);
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
    return $this->actionAdmin();
		$dataProvider=new EMongoDocumentDataProvider('MAsin');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
    //批量上传
    if(isset($_POST['upload']))
    {
      $content = file_get_contents($_FILES['MAsin']['tmp_name']['ASIN']);
      $rows = explode("\n", $content);
      $nupladed = count($rows);
      $start_time = time();
      $step = ceil((3600 * 24)) / $nupladed;//向后24小时进行平均分配
      $nadded = $nupdated = 0;
      foreach ($rows as $row) {
        list($asin, $intval) = array_map('trim', explode(',', $row));
        if(!$m = MAsin::model()->findByAttributes(array('asin'=>$asin))) {
          $m=new MAsin;
          $nadded ++;
          if($intval >= 3600)
            $m->next = new MongoDate($start_time = $start_time + $step);
        } else
          $nupdated++;
        $m->asin = $asin;
        $m->level = intval($intval);
        $m->save();
      }
      Yii::app()->user->setFlash('success', "上传成功!共上传{$nupladed},其中新增{$nadded},更新{$nupdated}.");
    }

		$model = new MAsin('search');
		$model->unsetAttributes();

		if(isset($_GET['MAsin'])) {
			$model->setAttributes($_GET['MAsin']);
      if($model->level)
        $model->level = new MongoInt32($model->level);
      if($model->next)
        if($model->next == 'now')
          $model->next = array('$lt' => new MongoDate());
        elseif ($model->next == 'next')
          $model->next = array('$lt' => new MongoDate(time() + 3600));
        elseif ($model->next == 'issues') {
          $model->next = null;
          $model->_r = array('$gte' => 3);
          $model->getDbCriteria()->sort('_r', -1);
        }
    }


		$this->render('admin', array(
			'model'=>$model
		));
	}

  public function actionRegen()
  {
    $this->uniform_distribute();
    Yii::app()->user->setFlash('success', "重新分配成功！");
    $this->redirect($this->createUrl('admin'));
  }

  public function actionSpark() {
    $range = isset($_GET['range']) ? intval($_GET['range']) : 6;
    //负载
    Yii::import('application.components.sparkline.Sparkline_Bar');
    $sparkline = new Sparkline_Bar();
    $sparkline->SetDebugLevel(DEBUG_NONE);
    $sparkline->SetBarWidth(1);
    $sparkline->SetBarSpacing(1);
//    $sparkline->SetBarColorDefault('blue');

    $db = MAsin::model()->getDb();
    $map = new MongoCode("function() {if(this.next){" .
      "var t = 0;" .
      "while(t < 3600 * {$range}) {" .
        "emit(Math.ceil((t + this.next/1000)/(20*{$range})), 1);" .
        "t = t + this.level;" .
      "}}}");
    $reduce = new MongoCode("function(k, vals) {" .
      "var sum = 0;" .
      "for (var i in vals) {" .
      "sum += vals[i];" .
      "}" .
      "return sum;" .
      "}");
    $res = $db->command(array('mapreduce'=>'asin', 'map'=>$map, 'reduce'=>$reduce, 'query'=>array('next'=>array('$gt'=>new MongoDate(), '$lt'=>new MongoDate(time() + 3600 * $range))), 'out'=>'example'));
    $args = $db->selectCollection($res['result'])->find();
//    var_dump($res);

    foreach($args as $k=>$v) {
      $sparkline->SetData($k, $v['value']);
//      echo $v['value'], "<br />";
    }

    $sparkline->Render(30);
    $sparkline->Output();
  }

  public function actionSamplefile()
  {
    Yii::app()->request->sendFile('monitor_asin_upload.csv',
      "B0026LQZP0,600\n",
      'text/csv'
    );
  }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=MAsin::model()->findByPk(new MongoId($id));
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='masin-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

  private function uniform_distribute() {
    $criteria = new EMongoCriteria();
    $criteria->addCond('level', '>=', 3600);
    $criteria->sort('next', 1);
    $criteria->sort('level', 1);

    $total = 3600 * 24;

    MAsin::model()->setUseCursor(true);
    $todo_asins = MAsin::model()->findAll($criteria);
    $nums = $todo_asins->count();
    $step = round($total/$nums);

    $t = time();
    foreach($todo_asins as $asin) {
      $asin->next = new MongoDate($t = $t + $step);
//      echo $asin->next->sec, "<br/>";
      $asin->save();
    }
  }

}
