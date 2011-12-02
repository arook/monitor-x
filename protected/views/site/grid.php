<?php $this->pageTitle=Yii::app()->name; ?>

<table border="1">
<tr>
<th>DT</th>
<?php
foreach($keys as $seller=>$key) {
echo "<th>", $seller, "</thd>";
}
?>
<th>BUYBOX</th>
</tr>
<tbody>
<?php foreach($data as $dt=>$item):?>
<tr>
<td><?php echo $dt?></td>
<?php foreach($item as $val){
  echo "<td>", $val, "</td>";
}
?>
</tr>
<?php endforeach;?>
</tbody>
</table>
