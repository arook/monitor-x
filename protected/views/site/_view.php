<div class="view">

  <b><?php echo CHtml::encode($data->getAttributeLabel('asin')); ?>:</b>
  <?php echo CHtml::link(CHtml::encode($data->asin), array('view', 'id'=>$data->id)); ?>
<?php echo CHtml::encode($data->dt); ?>
  <br />

</div>
