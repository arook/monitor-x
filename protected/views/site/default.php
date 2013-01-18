<?php $this->pageTitle=Yii::app()->name; ?>

<?php Yii::app()->clientScript->registerScript('ajaxmodal', <<<EOD
$("[data-toggle=modal]").click(function(event) {
	event.preventDefault();
	var url = $(this).attr("href");
	if (url.indexOf("#") == 0) {
		$(url).modal('open');
	} else {
		$.get(url, function(data) {
			$('<div class="modal hide fade">' + data + '</div>').modal().on('hidden', function () { $(this).remove(); });
			}).success(function() { $('input:text:visible:first').focus(); });
	}
});
EOD
)?>

<?php $this->renderPartial('_form', array('model'=>$form, 'asin'=>$asin));?>

<?php if (isset($_GET['AsinForm']) && isset($_GET['AsinForm']['asin'])):?>

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
    array(
      'name'=>'Node',
      'value'=>'$data["rc"]',
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
