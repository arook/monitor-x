<?php
$this->breadcrumbs=array(
	'Mnodes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List MNode', 'url'=>array('index')),
	array('label'=>'Manage MNode', 'url'=>array('admin')),
);
?>

<h1>Create MNode</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>