<?php
$this->breadcrumbs=array(
	'Mabs Rankings'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List MAbsRanking', 'url'=>array('index')),
	array('label'=>'Create MAbsRanking', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('mabs-ranking-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>Manage Mabs Rankings</h1>

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
	'id'=>'mabs-ranking-grid',
	'dataProvider'=>new EMongoDocumentDataProvider($model->search(), array(
		'sort'=>array(
			'attributes'=>array(
				'dt',
				'cat',
				'rank',
				'asin',
				'_id',
			),
		),
	)),
	'filter'=>$model,
	'columns'=>array(
		array(
			'name' => 'dt',
			'value' => 'date("Y-m-d", $data->dt->sec)',
		),
		array(
			'name' => 'cat',
			'value' => '(($tmp = MAbs::model()->getCollection()->getDbRef($data->cat))? $tmp["c2"] : "")',
		),
		// 'cat',
		'rank',
		'asin',
		array(
			'name' => 'name',
			'value' => '$data->name->bin',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>