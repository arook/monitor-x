<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'mnode-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'des'); ?>
		<?php echo $form->textField($model,'des'); ?>
		<?php echo $form->error($model,'des'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'weight'); ?>
		<?php echo $form->textField($model,'weight'); ?>
		<?php echo $form->error($model,'weight'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'radio'); ?>
		<?php echo $form->textField($model,'radio'); ?>
		<?php echo $form->error($model,'radio'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'priority'); ?>
		<?php echo $form->textField($model,'priority'); ?>
		<?php echo $form->error($model,'priority'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'n_total'); ?>
		<?php echo $form->textField($model,'n_total'); ?>
		<?php echo $form->error($model,'n_total'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'n_current'); ?>
		<?php echo $form->textField($model,'n_current'); ?>
		<?php echo $form->error($model,'n_current'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'n_rate'); ?>
		<?php echo $form->textField($model,'n_rate'); ?>
		<?php echo $form->error($model,'n_rate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'n_time'); ?>
		<?php echo $form->textField($model,'n_time'); ?>
		<?php echo $form->error($model,'n_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->