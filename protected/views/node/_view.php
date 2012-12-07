<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->_id), array('view', 'id'=>$data->_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::encode($data->id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('des')); ?>:</b>
	<?php echo CHtml::encode($data->des); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('weight')); ?>:</b>
	<?php echo CHtml::encode($data->weight); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('radio')); ?>:</b>
	<?php echo CHtml::encode($data->radio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('priority')); ?>:</b>
	<?php echo CHtml::encode($data->priority); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('n_total')); ?>:</b>
	<?php echo CHtml::encode($data->n_total); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('n_current')); ?>:</b>
	<?php echo CHtml::encode($data->n_current); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('n_rate')); ?>:</b>
	<?php echo CHtml::encode($data->n_rate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('n_time')); ?>:</b>
	<?php echo CHtml::encode($data->n_time); ?>
	<br />

	*/ ?>

</div>