<?php
$this->breadcrumbs=array(
	'Asins',
);

$this->menu=array(
	array('label'=>'Create Asin', 'url'=>array('create')),
	array('label'=>'Manage Asin', 'url'=>array('admin')),
);
?>

<h1>Asins</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
