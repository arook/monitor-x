
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'asin-form',
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

 <div class="row">
  <?php echo $form->labelEx($model,'date_from'); ?>
  <?php
  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'AsinForm[date_from]',
    'attribute'=>'date_from',
    'model'=>$model,
    'options' => array(
      'showAnim'=>'fold',
    )
  ));
  ?>
  <?php echo $form->error($model,'date_from'); ?>
	</div>
  
  <div class="row">
  <?php echo $form->labelEx($model,'date_to'); ?>
  <?php
  $this->widget('zii.widgets.jui.CJuiDatePicker', array(
    'name'=>'AsinForm[date_to]',
    'attribute'=>'date_to',
    'model'=>$model,
    'options' => array(
      'showAnim'=>'fold',
    )
  ));
  ?>
  <?php echo $form->error($model,'date_to'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
