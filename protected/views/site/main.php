<?php $this->pageTitle=Yii::app()->name; ?>


<?php $this->renderPartial('_form', array('model'=>$form));?>

<?php $this->widget('bootstrap.widgets.BootGridView', array(
  'dataProvider'=>$dataProvider,
  'columns'=>array_merge(array(
//    'id',
    array(
      'name'=>'time',
      'value'=>'date("F j, g:i a", $data["time"])',
    ),
//    'bp',
//    'bs',
  ), $columns),
));
?>
