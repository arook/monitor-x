<?php $this->pageTitle=Yii::app()->name; ?>


<div style="float:right;display:block;border:0px solid black;width:750px;height:160px;margin-top:5px;">
Issues:
<?php
foreach ($issues as $issue) {
  echo CHtml::link($issue['asin'] . ':' . $issue['dt'], array('site/issue', 'asin'=>$issue['asin'], 'dt'=>$issue['dt']), array('target'=>'_blank')), " ";
}
?>
</div>

<?php $this->renderPartial('_form', array('model'=>$model));?>

<?php
if (isset($keys)) {
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
      'buybox'=>$buybox,
    )
  )
);
}
?>
