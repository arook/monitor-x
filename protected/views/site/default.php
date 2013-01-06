<?php $this->pageTitle=Yii::app()->name; ?>


<?php $this->renderPartial('_form', array('model'=>$form));?>

<?php if (isset($_GET['AsinForm']) && isset($_GET['AsinForm']['asin'])):?>
<table class="table table-striped">
<tbody>
<tr>
  <th>Last Fetch</th>
  <td><?php echo date('F j, g:i a', $asin->dt->sec)?></td>
  <th>Next Fetch</th>
  <td><?php echo date('F j, g:i a', $asin->next->sec)?></td>
</tr>
<?php if($asin->_e):?>
<tr>
  <th>Retry</th>
  <td><?php echo $asin->_r?></td>
  <th>Error</th>
  <td><?php echo $asin->_e?></td>
</tr>
<tr>
  <th>Listing</th>
  <td><a href="?r=site/content&type=listing&id=<?php echo $asin->_id?>" target="_blank">Listing Content</a></td>
  <th>Buybox</th>
  <td><a href="?r=site/content&type=buybox&id=<?php echo $asin->_id?>" target="_blank">Buybox Content</a></td>
</tr>
<?php endif;?>
</tbody>
</table>

<h2>BBR && Sales</h2>
<table class="table table-striped">
<thead>
  <th>SELLER</th>
  <th>12</th>
  <th>36</th>
  <th>84</th>
  <th>12</th>
  <th>36</th>
  <th>84</th>
</thead>
<?php foreach($bbr as $key=>$seller):?>
<tr>
  <td><?php echo Utils::getAvatarBySid($key);?></td>
  <td><?php echo sprintf('%.2f%%', $seller['1'] * 100)?></td>
  <td><?php echo sprintf('%.2f%%', $seller['2'] * 100)?></td>
  <td><?php echo sprintf('%.2f%%', $seller['3'] * 100)?></td>

  <td><?php echo sprintf('%.2f', $sales[$key]['1'])?></td>
  <td><?php echo sprintf('%.2f', $sales[$key]['2'])?></td>
  <td><?php echo sprintf('%.2f', $sales[$key]['3'])?></td>
</tr>
<?php endforeach;?>
</table>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
  'type'=>'striped bordered condensed',
  'dataProvider'=>new EMongoDocumentDataProvider($model->search(), array(
    'sort'=>array(
  //    'attributes'=>array(
  //      'time',
  //    ),
    ),
  )),
  'columns'=>array_merge(array(
//    'id',
    array(
      'name'=>'DT',
      'value'=>'date("m-d H:i:s", $data["t"]->sec)',
    ),
//    'bp',
//    'bs',
  ), $columns),
  'htmlOptions'=>array(
    //'class'=>'well',
  ),
));

?>
<?php endif;?>
