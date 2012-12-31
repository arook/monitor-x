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
    $('#searchForm #next').val('now');
    $('#searchForm').submit();
  });

  $('#btn_next').click(function(){
    $('#searchForm #next').val('next');
    $('#searchForm').submit();
  });

  $('#btn_issues').click(function(){
    $('#searchForm #next').val('issues');
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
  'toggle'=>'radio',
  'buttons'=>array(
    array('label'=>'当前运行中', 'url'=>'', 'htmlOptions'=>array('id'=>'btn_now')),
    array('label'=>'未来一小时', 'url'=>'', 'htmlOptions'=>array('id'=>'btn_next')),
    array('label'=>'问题帖子', 'url'=>'', 'htmlOptions'=>array('id'=>'btn_issues')),
    array('label'=>'负载分布', 'url'=>'', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#statusModal')),
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
	'_r',
	array(
		'class'=>'bootstrap.widgets.TbButtonColumn',
		'template'=>'{reset} {view} {delete}',
		'buttons'=>array(
			'reset'=>array(
				'icon'=>'refresh',
				'url'=>'Yii::app()->controller->createUrl("asins/reset", array("id" => $data->_id))',
				'options'=>array(
	            	'target'=>'_blank',
	          	),
				'click'=><<<EOD
					function() {
						var th=this;
						$.fn.yiiGridView.update('asin-grid', {
							type: 'POST',
							url:$(this).attr('href'),// $csrf
							success:function(data) {
								$.fn.yiiGridView.update('asin-grid');
							},
							error:function(XHR) {
								return;
							}
						});
						return false;
					}
EOD
			),
			'view'=>array(
				'url'=>'Yii::app()->controller->createUrl("site/default", array("AsinForm[asin]" => $data->asin))',
				'options'=>array(
	            	'target'=>'_blank',
	          	),
	        ),
		),
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

<!-- status -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'statusModal'));?>
<div class="modal-header">
<a class="close" data-dismiss="modal">&times;</a>
<h4>负载分布图</h4>
</div>

<div class="modal-body">
<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
  'toggle'=>'radio',
  'buttons'=>array(
    array('label' => '1H', 'url'=>'javascript:($("#charts").attr("src", "' . $this->createUrl('spark', array('range'=>1)) . '"));'),
    array('label' => '3H', 'url'=>'javascript:($("#charts").attr("src", "' . $this->createUrl('spark', array('range'=>3)) . '"));'),
    array('label' => '6H', 'url'=>'javascript:($("#charts").attr("src", "' . $this->createUrl('spark', array('range'=>6)) . '"));'),
    array('label' => '12H', 'url'=>'javascript:($("#charts").attr("src", "' . $this->createUrl('spark', array('range'=>12)) . '"));'),
    array('label' => '24H', 'url'=>'javascript:($("#charts").attr("src", "' . $this->createUrl('spark', array('range'=>24)) . '"));'),
  ),
  'htmlOptions'=>array(
    'align'=>'center',
  ),
))?>
<p align="center"><img id='charts' src="<?php echo $this->createUrl('spark')?>" /></p>
</div>

<div class="modal-footer">
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'type'=>'primary',
  'label'=>'重新分配',
  'url'=>$this->createUrl('regen'),
  'htmlOptions'=>array('name'=>'regen'),
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
  'label'=>'Close',
  'url'=>'#',
  'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>
<?php $this->endWidget(); ?>
