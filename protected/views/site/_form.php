<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'asin-form',
  'method'=>'get',
  'action'=>$this->createUrl('site/default'),
  'enableClientValidation'=>true,
  'htmlOptions'=>array('class'=>'well'),
  'type'=>'search',
  'clientOptions'=>array(
    'validateOnSubmit'=>true,
  ),
)); ?>

<?php echo $form->errorSummary($model); ?>

  <?php
  $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'AsinForm[asin]',
    'attribute'=>'asin',
    'model'=>$model,
    'source'=>$this->createUrl('site/asinList'),
    'options' => array(
      'showAnim'=>'fold',
    ),
  ));
  ?>
  <?php echo $form->error($model,'asin'); ?>

		<?php if (isset($_GET['AsinForm']) && isset($_GET['AsinForm']['asin'])):?>
		<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
      'toggle'=>'radio',
      'buttons'=>array(
        array('label'=>'概要', 'url'=>'?r=asins/summary/id/'.$asin->_id, 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#statusModal')),
        array('label'=>'BUYBOX', 'url'=>'?r=asins', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#statusModal')),
        array('label'=>'PRICE', 'url'=>'', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#statusModal')),
        
        array('label'=>'负载分布', 'url'=>'', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#statusModal')),
        array('label'=>'上传ASIN', 'url'=>'', 'htmlOptions'=>array('data-toggle'=>'modal', 'data-target'=>'#uploadModal')),
      ),
    )) ?>
		<?php endif?>

<?php $this->endWidget(); ?>
