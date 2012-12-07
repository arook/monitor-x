<?php
$this->breadcrumbs=array(
	'Mnodes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List MNode', 'url'=>array('index')),
	array('label'=>'Create MNode', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('mnode-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>Manage Mnodes</h1>

<?php echo CHtml::linkButton('重新计算权重', array('href'=>array('count'))); ?>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'mnode-grid',
	'dataProvider'=>new EMongoDocumentDataProvider($model->search(), array(
		'sort'=>array(
			'attributes'=>array(
				'id',
				'des',
				'weight',
				
				'n_total',
				'n_current',
				'n_rate',
				'n_time',
				/*
				
				'radio',
				'priority',

				
				'_id',
				*/
			),
		),
	)),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'des',
		'weight',
		
		'n_total',
		'n_current',
		'n_rate',
		'n_time',
		/*
		'radio',
		'priority',
		
		
		'_id',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>