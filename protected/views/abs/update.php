<?php
$this->breadcrumbs=array(
	'Mabs Rankings'=>array('index'),
	$model->_id=>array('view','id'=>$model->_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MAbsRanking', 'url'=>array('index')),
	array('label'=>'Create MAbsRanking', 'url'=>array('create')),
	array('label'=>'View MAbsRanking', 'url'=>array('view', 'id'=>$model->_id)),
	array('label'=>'Manage MAbsRanking', 'url'=>array('admin')),
);
?>

<h1>Update MAbsRanking <?php echo $model->_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>