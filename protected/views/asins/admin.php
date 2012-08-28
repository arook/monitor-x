<?php
$this->breadcrumbs=array(
	'Asins'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Asin','url'=>array('index')),
	array('label'=>'Create Asin','url'=>array('create')),
);

?>

<h1>Manage Asins</h1>
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'label'=>'上传ASIN',
  'htmlOptions'=>array(
    'data-toggle'=>'modal',
    'data-target'=>'#uploadModal',
  ),
))?>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
  'id'=>'asin-grid',
  'dataProvider'=>new EMongoDocumentDataProvider($model->search()),
  'filter'=>$model,
  'columns'=>array(
		'asin',
    'fs',
		'level',
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
