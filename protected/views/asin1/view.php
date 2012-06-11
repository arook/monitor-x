<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Asin','url'=>array('index')),
	array('label'=>'Create Asin','url'=>array('create')),
	array('label'=>'Update Asin','url'=>array('update','id'=>$model->id)),
	array('label'=>'Delete Asin','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Asin','url'=>array('admin')),
);
?>

<h1>View Asin #<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'asin',
		'retry',
	),
)); ?>
