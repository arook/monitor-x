<?php
$this->breadcrumbs=array(
	'Mabs Rankings'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('mabs-ranking-grid', {
		data: $(this).serialize()
	});
	return false;
});

$('[data-toggle=\"modal\"]').click(function(e) {
	var url = $(this).attr('href');
	$.get(url, function(data) {
		$('.modal-body').html(data);
	});
});

");

?>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'=>'searchForm',
  'type'=>'search',
  'htmlOptions'=>array('class'=>'well'),
))?>

<?php echo $form->dropDownListRow($model, 'cat', $cats, array('class'=>'input-larger', 'style'=>'width:400px')) ?>

<?php
$this->widget('zii.widgets.jui.CJuiDatePicker',array(
	'model'=>$model,
	'attribute'=>'dt',
	'options'=>array(
		'showAnim'=>'fold',
	),
	'htmlOptions'=>array(
		'class'=>'input-medium',
		'style'=>'height:20px;'
	),
));
?>

<button class="btn ">Filter</button>

<?php $this->endWidget(); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'mabs-ranking-grid',
	'type'=>'striped bordered condensed',
	'dataProvider'=>$ranking,
	'columns'=>array(
		// array(
		// 	'name' => 'dt',
		// 	'value' => 'date("Y-m-d", $data->dt->sec)',
		// ),
		'rank',
		array(
			'class' => 'CLinkColumn',
			'header' => 'asin',
			'labelExpression' => '$data->asin',
			'urlExpression' => 'Yii::app()->createUrl("abs/asin", array("asin" => $data->asin))',
			'linkHtmlOptions' => array('data-toggle' => "modal", 'data-target' => "#historyModal"),
		),
		array(
			'name' => 'name',
			'value' => '$data->name->bin',
		),
	),
)); ?>

<!-- history -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'historyModal'));?>
<div class="modal-header">
<a class="close" data-dismiss="modal">&times;</a>
<h4>History</h4>
</div>

<div class="modal-body">

</div>

<div class="modal-footer">

</div>
<?php $this->endWidget(); ?>