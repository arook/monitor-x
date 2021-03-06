<div id="chart_div" align="center" style="height:500px;width:900px"></div>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
<!--

  google.load("visualization", "1", {packages:["corechart", 'table', 'motionchart', 'annotatedtimeline']});
  google.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('datetime', 'Date');

    <?php
    foreach($keys as $seller=>$key) {
      echo "data.addColumn('number', '$seller');";
    }
    ?>

    data.addColumn('number', 'Buy Box');

    <?php
      foreach($data as $dt=>$detail) {
        unset($detail[-2]);
        echo sprintf("data.addRow([new Date('%s'), %s]);", $dt, implode(",", $detail));
      }
    ?>

    var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
    chart.draw(data, {
      width:900, 
      height:500, 
      'displayAnnotations': true,
      'displayExactValues': true,
      'displayRangeSelector' : true,
      'displayZoomButtons': true,
      'fill': 10,
      'legendPosition': 'newRow',
      'scaleType': 'maximized',
      'wmode': 'transparent',
//      'scaleColumns': [<?php echo implode(',', $keys)?>],
      'thickness': 0,
//      'zoomStartTime': new Date('<?php echo $model->date_from?>'),
      });
  }

-->
</script>
