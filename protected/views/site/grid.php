<table style="width:900px;overflow:auto;display:block;">
<tr>
<th>DT</th>
<?php
foreach($keys as $seller=>$key) {
echo "<th title='$seller'>", substr($seller, 0, 4), "</thd>";
}
?>
<th>BB</th>
</tr>
<tbody>
<?php krsort($data); ?>
<?php foreach($data as $dt=>$item):?>
<?php $bb = $item[-2];unset($item[-2]);?>
<tr>
<td><?php echo $dt?></td>
<?php foreach($item as $k=>$val){
  if ($k == $bb) {
    echo "<td bgcolor='blue'>", $val, "</td>";
  } else {
    if ($k == -1) {
      echo "<td>", CHtml::link($val, array('site/issue', 'dt'=>$dt, 'asin'=>$model->asin), array('target'=>'_blank')), "</td>";
    } else {
      echo "<td>", $val, "</td>";
    }
  }
}
?>
</tr>
<?php endforeach;?>
</tbody>
</table>
