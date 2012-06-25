<?php $this->pageTitle=Yii::app()->name; ?>


<?php $this->renderPartial('_form', array('model'=>$form));?>

<h2>BBR</h2>
<table>
<thead>
  <th>SELLER</th>
  <th>1</th>
  <th>3</th>
  <th>5</th>
</thead>
<?php foreach($bbr as $key=>$seller):?>
<tr>
  <td><?php echo Utils::getAvatarBySid($key);?></td>
  <td>
  <?php echo $sales[$key]['1']?>
<?php echo sprintf('%.2f%%', $seller['1'] * 100)?></td>
  <td><?php echo sprintf('%.2f%%', $seller['2'] * 100)?></td>
  <td><?php echo sprintf('%.2f%%', $seller['3'] * 100)?></td>
</tr>
<?php endforeach;?>
</table>

<?php $this->widget('bootstrap.widgets.BootGridView', array(
  'dataProvider'=>$dataProvider,
  'columns'=>array_merge(array(
//    'id',
    array(
      'name'=>'time',
      'value'=>'date("F j, g:i a", $data["time"])',
    ),
//    'bp',
//    'bs',
  ), $columns),
));
?>
