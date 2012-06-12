<?php
?>

<h1>Manage Asins</h1>

<?php $this->widget('bootstrap.widgets.BootButton', array(
  'label'=>'New Asin',
  'icon'=>'plus-sign',
  'url'=>$this->createUrl('/asin1/create'),
));?>

<?php foreach($asins as $row):?>
<div class="row-fluid">
  <?php foreach($row as $asin=>$key):?>
    <div class="span2">
      <a href="<?php echo $this->createUrl('/site/main', array('AsinForm[asin]'=>$asin));?>"><?php echo $asin;?></a>
      <a href="<?php echo $this->createUrl('/asin1/delete', array('id'=>$asin));?>"><span class="icon-trash"></span></a>
    </div>
  <?php endforeach;?>
</div>
<?php endforeach;?>
