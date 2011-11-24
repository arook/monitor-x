<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Asin', 'url'=>array('index')),
	array('label'=>'Manage Asin', 'url'=>array('admin')),
);
?>

<h1>Create Asin</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>