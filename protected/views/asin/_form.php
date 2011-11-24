<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'asin-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'asin'); ?>
		<?php echo $form->textField($model,'asin',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'asin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'retry'); ?>
		<?php echo $form->textField($model,'retry'); ?>
		<?php echo $form->error($model,'retry'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->