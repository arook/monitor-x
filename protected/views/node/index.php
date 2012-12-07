<?php
$this->breadcrumbs=array(
	'Mnodes',
);

$this->menu=array(
	array('label'=>'Create MNode', 'url'=>array('create')),
	array('label'=>'Manage MNode', 'url'=>array('admin')),
);
?>

<h1>Mnodes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>