<?php
$this->breadcrumbs=array(
	'Mnodes'=>array('index'),
	$model->_id,
);

$this->menu=array(
	array('label'=>'List MNode', 'url'=>array('index')),
	array('label'=>'Create MNode', 'url'=>array('create')),
	array('label'=>'Update MNode', 'url'=>array('update', 'id'=>$model->_id)),
	array('label'=>'Delete MNode', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MNode', 'url'=>array('admin')),
);
?>

<h1>View MNode #<?php echo $model->_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'des',
		'weight',
		'radio',
		'priority',
		'n_total',
		'n_current',
		'n_rate',
		'n_time',
		'_id',
	),
)); ?>