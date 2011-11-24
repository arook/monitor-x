<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Asin', 'url'=>array('index')),
	array('label'=>'Create Asin', 'url'=>array('create')),
	array('label'=>'View Asin', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Asin', 'url'=>array('admin')),
);
?>

<h1>Update Asin <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>