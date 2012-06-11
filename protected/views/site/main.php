<?php $this->pageTitle=Yii::app()->name; ?>


<?php $this->renderPartial('_form', array('model'=>$form));?>

<?php $this->widget('bootstrap.widgets.BootGridView', array(
  'dataProvider'=>$dataProvider,
  'columns'=>array_merge(array(
    'id',
    'time:time',
//    'bp',
//    'bs',
  ), $columns),
));
?>
