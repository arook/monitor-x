<?php
$this->breadcrumbs=array(
	'Mabs Rankings'=>array('index'),
	$model->_id,
);

$this->menu=array(
	array('label'=>'List MAbsRanking', 'url'=>array('index')),
	array('label'=>'Create MAbsRanking', 'url'=>array('create')),
	array('label'=>'Update MAbsRanking', 'url'=>array('update', 'id'=>$model->_id)),
	array('label'=>'Delete MAbsRanking', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MAbsRanking', 'url'=>array('admin')),
);
?>

<h1>View MAbsRanking #<?php echo $model->_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'dt',
		'cat',
		'rank',
		'asin',
		'_id',
	),
)); ?>