<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	$model->asin=>array('view','id'=>$model->_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Asin','url'=>array('index')),
	array('label'=>'Create Asin','url'=>array('create')),
	array('label'=>'View Asin','url'=>array('view','id'=>$model->_id)),
	array('label'=>'Manage Asin','url'=>array('admin')),
);
?>

<h1>Update Asin <?php echo $model->asin; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>
