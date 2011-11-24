<?php $this->pageTitle=Yii::app()->name; ?>

<?php
$this->widget('zii.widgets.CListView', array(
  'dataProvider'=>$dataProvider,
  'itemView'=>'_view',
));
?>
