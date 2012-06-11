
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'asin-form',
  'method'=>'get',
  'action'=>$this->createUrl('site/main'),
  'enableClientValidation'=>true,
  'clientOptions'=>array(
    'validateOnSubmit'=>true,
  ),
)); ?>

<?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'asin'); ?>
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
	</div>

		<?php echo CHtml::submitButton('Search'); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->
