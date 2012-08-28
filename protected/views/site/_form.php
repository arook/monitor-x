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
    )
  ));
  ?>
  <?php echo $form->error($model,'asin'); ?>

		<?php echo CHtml::submitButton('Search'); ?>

<?php $this->endWidget(); ?>
