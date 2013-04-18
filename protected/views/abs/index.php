<?php
$this->breadcrumbs=array(
	'Mabs Rankings',
);

$this->menu=array(
	array('label'=>'Create MAbsRanking', 'url'=>array('create')),
	array('label'=>'Manage MAbsRanking', 'url'=>array('admin')),
);
?>

<h1>Mabs Rankings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>