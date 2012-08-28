<?php

class SiteController extends Controller
{
  public function filters()
  {
    return array(
      'accessControl',
    );
  }

  public function accessRules()
  {
    return array(
      array(
        'allow',
        'actions'=>array('login'),
      ),
      array(
        'allow',
        'users'=>array('@'),
      ),
      array(
        'deny',
        'users'=>array('*'),
      ),
    );
  }
  
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
     $sql = "SELECT c.`asin` as `asin`, `dt` 
      FROM  `fetching_issue` a
      LEFT JOIN  `fetching` b ON a.fetching_id = b.id
      LEFT JOIN  `asin` c ON b.`asin` = c.`id` 
      ORDER BY a.`fetching_id` DESC
      LIMIT 10";
    $issues = Yii::app()->db->createCommand($sql)->queryAll();

   $model = new AsinForm;
    if (isset($_GET['AsinForm'])) {
      $model->attributes = $_GET['AsinForm'];
    } else {
      $this->render('index', array('model'=>$model, 'issues'=>$issues));
      Yii::app()->end();
    }
    
    /*
    $data_provider = new CActiveDataProvider('Fetching', array(
      'criteria'=>array(
        'condition'=>
          "asin0.asin='".$model->asin."' and date(dt) between '" . $model->date_from . "' and '" . $model->date_to . "'",
        'order'=>'dt desc',
        'with'=>array('asin0', 'fetchingDetails'),
      ),
    ));
    */

    $sql = sprintf("select `f`.`id` as `id`, `dt`,  `shipping_price` + `sell_price` as `price`, 
      concat(`seller`, if(`if_fba`, '[F]', '[N]')) as `seller`,
      `if_buybox`
      from `fetching_detail` `d`
      left join `fetching` `f`
      on `d`.`fetching_id` = `f`.`id`
      left join `asin` `a`
      on `f`.`asin` = `a`.`id` 
      where `a`.`asin` = '%s' 
      and date(`dt`) between '%s' and '%s'", 
      $model->asin,
      $model->date_from,
      $model->date_to
    );
    $data_provider = Yii::app()->db->createCommand($sql)->queryAll();

    $keys = $data = array();
    foreach($data_provider as $item) {
      if (!array_key_exists($item['seller'], $keys)) {
        $keys[$item['seller']] = count($keys);
      }
      if ($item['if_buybox']) {
        $data[$item['dt']][-1] = $item['price'];
        $data[$item['dt']][-2] = $keys[$item['seller']];
      }
      $data[$item['dt']][$keys[$item['seller']]] = $item['price'];
    }
    arsort($keys);

    foreach($data as $k=>$item) {
      foreach ($keys as $key) {
        if (!array_key_exists($key, $item)) {
          $data[$k][$key] = 'undefined';
        }
      }
      if (!isset($data[$k][-1])) {
        $data[$k][-1] = 'undefined';
        $data[$k][-2] = '-2';
      }
      krsort($data[$k]);
    }

    $asin = Asin::model()->findByAttributes(array('asin'=>$model->asin));

    $sql = sprintf("SELECT t1.seller AS seller, t1.if_fba AS if_fba, t1.rate AS rate1, t2.rate AS rate2, t3.rate AS rate3
      FROM (
        
        SELECT * 
        FROM  `buybox_rate` 
        WHERE TYPE =  '1'
        AND asin = %s
        ORDER BY  `dt` DESC 
        LIMIT 6
      ) AS t1
      LEFT JOIN (
        
        SELECT * 
        FROM  `buybox_rate` 
        WHERE TYPE =  '3'
        AND asin = %s
        ORDER BY  `dt` DESC 
        LIMIT 6
      ) AS t2 ON t1.seller = t2.seller
      AND t1.if_fba = t2.if_fba
      LEFT JOIN (
        
        SELECT * 
        FROM  `buybox_rate` 
        WHERE TYPE =  '7'
        AND asin = %s
        ORDER BY  `dt` DESC 
        LIMIT 6
      ) AS t3 ON t2.seller = t3.seller
      AND t2.if_fba = t3.if_fba", $asin->id, $asin->id, $asin->id);
    $buybox=Yii::app()->db->createCommand($sql)->queryAll();

    $this->render('index', array(
      'keys'=>$keys,
      'data'=>$data,
      'model'=>$model,
      'buybox'=>$buybox,
      'issues'=>$issues,
    ));
	}

  public function actionDefault()
  {
    $form = new AsinForm;
    $model = new MFetching('search');
    $asin = new MAsin;
    $bbr = $sales = $columns = array();

    if (isset($_REQUEST['AsinForm']))
      $form->attributes = $_REQUEST['AsinForm'];

    if ($form->asin)
      $asin = MAsin::model()->findByAttributes(array('asin' => $form->asin));
    if (isset($asin)) {
      $model->getDbCriteria()->addCond('a.$id', '==', $asin->_id)->sort('t', -1);
      foreach(range(0, $asin->fs - 1) as $k) {
        $columns[] = array('header' =>$k + 1, 'name'=>"l.$k", 'type'=>'rank');
      }
    }


    $this->render('default', array(
      'form'=>$form,
      'model'=>$model,
      'columns'=>$columns,
      'bbr'=>$bbr,
      'sales'=>$sales,
      'asin'=>$asin,
    ));
  }

  public function actionContent()
  {
    $asin = MAsin::model()->findByPK(new MongoId($_GET['id']));
    $attr = ($_GET['type'] == 'listing') ? '_sl' : '_sb';
    if($asin->$attr)
      echo $asin->$attr;
    else
      echo 'nothing';
  }

  public function actionMain()
  {
    $form = new AsinForm;
    if(isset($_REQUEST['AsinForm'])) {
      $form->attributes = $_REQUEST['AsinForm'];
    }

    $dataProvider = new FetchingListDataProvider($form->asin);
    $aid = Redis::client()->hget('asins', $form->asin);
    $columns = array();
    for($i=0;$i<Redis::client()->get('asin:'.$aid.':fs'); $i++)
      $columns[] = array('name'=>'r'.$i, 'header'=>$i+1, 'type'=>'rank');

    $bbr = AsinService::getInstance()->bbrSummary($aid);
    $sales = AsinService::getInstance()->salesSummary($aid);
    $this->render('main', array(
      'form'=>$form,
      'dataProvider'=>$dataProvider,
      'columns'=>$columns,
      'bbr'=>$bbr,
      'sales'=>$sales,
    ));
  }

  public function actionAsinList()
  {
    $list = array();
    foreach (MAsin::model()->findAllByAttributes(array('asin' => new MongoRegex('/.*' . $_GET['term'] . '.*/i'))) as $asin) {
      $list[] = $asin->asin;
    }
    echo CJSON::encode($list);
    Yii::app()->end();

    foreach(Redis::client()->hkeys('asins') as $asin) {
      if(stristr($asin, $_GET['term']))
        $list[] = $asin;
    }
    echo CJSON::encode($list);
    Yii::app()->end();
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

  public function actionIssue($dt, $asin)
  {
    $sql = sprintf("select b.`id` as `id`
      from `asin` a
      left join `fetching` b
      on `a`.`id` = `b`.`asin`
      where a.`asin` = '%s' and b.`dt` = '%s'", $asin, $dt);

    $result = Yii::app()->db->createCommand($sql)->queryRow();
    if ($result) {
      $issue = FetchingIssue::model()->findByPk($result['id']);
      $this->render('issue', array('issue'=>$issue));
    }
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
