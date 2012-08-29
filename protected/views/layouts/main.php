<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
  <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>

<?php
/*
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
 */?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
  'type'=>null,
  'brand'=>'Monitor',
  'collapse'=>true,
  'items'=>array(
    array(
      'class'=>'bootstrap.widgets.TbMenu',
      'items'=>array(
        array('label'=>'Home', 'url'=>array('/site/default')),
        array('label'=>'ASINS', 'url'=>array('/asins/admin'), 'active'=>$this->id == 'asins'),
        array('label'=>'Status', 'url'=>array('/status'), 'active'=>$this->id == 'status'),
        array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
        array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
      ),
    ),
  ),
))?>
<div class="container">
<?php if(isset($this->breadcrumbs)):?>
<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
  'links'=>$this->breadcrumbs,
));?>
<?php endif?>

<?php $this->widget('bootstrap.widgets.TbAlert', array(
  'block'=>true, // display a larger alert block?
  'fade'=>true, // use transitions?
  'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
  'alerts'=>array( // configurations per alert type
    'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
  ),
)); ?>
<?php echo $content;?>
</div>
</body>
</html>
