<?php $this->pageTitle=Yii::app()->name; ?>

<?php $this->renderPartial('_form', array('model'=>$model));?>

<?php
$this->widget('CTabView',
  array(
    'tabs'=>array(
      'tab1'=>array(
        'title'=>'Chart',
        'view'=>'chart',
      ),
      'tab0'=>array(
        'title'=>'BBR',
        'view'=>'bbr',
      ),
      'tab2'=>array(
        'title'=>'Grid',
        'view'=>'grid',
      ),
    ),
    'viewData'=>array(
      'model'=>$model,
      'keys'=>$keys,
      'data'=>$data,
      'buybox_provider'=>$buybox_provider,
    )
  )
);
?>
