<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id),array('view','id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('asin')); ?>:</b>
	<?php echo CHtml::encode($data->asin); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('retry')); ?>:</b>
	<?php echo CHtml::encode($data->retry); ?>
	<br />


</div>