<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'dt'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name' => 'dt',
		)) ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'cat'); ?>
		<?php echo $form->textField($model,'cat'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rank'); ?>
		<?php echo $form->textField($model,'rank'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'asin'); ?>
		<?php echo $form->textField($model,'asin'); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->label($model,'_id'); ?>
		<?php echo $form->textField($model,'_id'); ?>
	</div> -->

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->