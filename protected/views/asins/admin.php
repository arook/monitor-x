<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Asin','url'=>array('index')),
	array('label'=>'Create Asin','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
  $('#searchForm').submit(function(){
    $.fn.yiiGridView.update('asin-grid', {
      data: $(this).serialize()
    });
    return false;
  });

  $('#btn_now').click(function(){
    $('#searchForm #next').val('<' +  Math.round(new Date().getTime()/1000));
    $('#searchForm').submit();
  });

  $('#btn_next').click(function(){
    $('#searchForm #next').val('<' + (3600 + Math.round(new Date().getTime()/1000)));
    $('#searchForm').submit();
  });
");
?>

<h1>Manage Asins</h1>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'=>'searchForm',
  'type'=>'search',
  'htmlOptions'=>array('class'=>'well'),
))?>
<input type="hidden" name="MAsin[next]" id="next" />
<?php echo $form->textFieldRow($model,
  'asin',
  array(
    'class'=>'input-medium',
    'prepend'=>'<i class="icon-search"></i>'
  )
); ?>

<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
  'buttons'=>array(
    array('label'=>'当前运行中', 'url'=>'', 'htmlOptions'=>array('id'=>'btn_now')),
    array('label'=>'未来一小时', 'url'=>'', 'htmlOptions'=>array('id'=>'btn_next')),
    array('label'=>'上传ASIN', 'url'=>'', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#uploadModal')),
  ),
)) ?>

<?php $this->endWidget();?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
  'id'=>'asin-grid',
  'type'=>'striped bordered condensed',
  'dataProvider'=>new EMongoDocumentDataProvider($model->search()),
  //'filter'=>$model,
  'columns'=>array(
		'asin',
    array(
      'name'=>'Delay',
      'value'=>'time() - $data->next->sec',
    ),
    array(
      'name'=>'Last Time',
      'value'=>'$data->dt ? date("F j, g:i a", $data->dt->sec) : ""',
    ),
    array(
      'name'=>'Next Time',
      'value'=>'$data->next ? date("F j, g:i a", $data->next->sec) : ""',
    ),
		'level',
    'fs',
    array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>

<!-- upload -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'uploadModal')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>上传ASIN</h4>
</div>

<div class="modal-body">
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'=>'horizontalForm',
  'htmlOptions'=>array(
    'enctype' => 'multipart/form-data',
  ),
)); ?>
<?php echo $form->fileFieldRow($model, 'ASIN'); ?>
<a href="<?php echo $this->createUrl('samplefile')?>" target="_blank">*Sample File</a>
</div>

<div class="modal-footer">
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'buttonType'=>'submit',
  'type'=>'primary',
  'label'=>'Upload',
  'htmlOptions'=>array('name'=>'upload'),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'label'=>'Close',
  'url'=>'#',
  'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>
