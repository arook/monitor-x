<?php
$this->breadcrumbs=array(
	'Mnodes'=>array('index'),
	"$model->_id"=>array('view','id'=>$model->_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MNode', 'url'=>array('index')),
	array('label'=>'Create MNode', 'url'=>array('create')),
	array('label'=>'View MNode', 'url'=>array('view', 'id'=>$model->_id)),
	array('label'=>'Manage MNode', 'url'=>array('admin')),
);
?>

<h1>Update MNode <?php echo $model->_id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>