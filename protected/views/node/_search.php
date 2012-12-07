<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'des'); ?>
		<?php echo $form->textField($model,'des'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'weight'); ?>
		<?php echo $form->textField($model,'weight'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'radio'); ?>
		<?php echo $form->textField($model,'radio'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'priority'); ?>
		<?php echo $form->textField($model,'priority'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'n_total'); ?>
		<?php echo $form->textField($model,'n_total'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'n_current'); ?>
		<?php echo $form->textField($model,'n_current'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'n_rate'); ?>
		<?php echo $form->textField($model,'n_rate'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'n_time'); ?>
		<?php echo $form->textField($model,'n_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'_id'); ?>
		<?php echo $form->textField($model,'_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->