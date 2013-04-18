<?php
$this->breadcrumbs=array(
	'Mabs Rankings'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List MAbsRanking', 'url'=>array('index')),
	array('label'=>'Manage MAbsRanking', 'url'=>array('admin')),
);
?>

<h1>Create MAbsRanking</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>