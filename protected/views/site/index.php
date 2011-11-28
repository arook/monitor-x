<?php $this->pageTitle=Yii::app()->name; ?>

<?php
$this->renderPartial('_form', array('model'=>$model));

$this->widget('zii.widgets.CListView', array(
  'dataProvider'=>$dataProvider,
  'itemView'=>'_view',
));
?>
