<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	$model->asin,
);

$this->menu=array(
	array('label'=>'List Asin','url'=>array('index')),
	array('label'=>'Create Asin','url'=>array('create')),
	array('label'=>'Update Asin','url'=>array('update','id'=>$model->_id)),
	array('label'=>'Delete Asin','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Asin','url'=>array('admin')),
);
?>

<h1>View Asin #<?php echo $model->_id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'_id',
		'asin',
		'level',
	),
)); ?>
