<?php
$this->breadcrumbs=array(
	'Status',
);?>
<h1>Monitor Status</h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
  'type'=>'striped bordered condensed',
  'dataProvider'=>$dataProvider,
  'columns'=>array(
    'asin',
    array(
      'name'=>'Delay',
      'value'=>'time() - $data->next->sec',
    ),
    array(
      'name'=>'Last Time',
      'value'=>'$data->dt ? date("F j, g:i a", $data->dt->sec) : ""',
    ),
    array(
      'name'=>'Next Time',
      'value'=>'$data->next ? date("F j, g:i a", $data->next->sec) : ""',
    ),
  ),
)); ?>
