
<table style="width:900px;overflow:auto;display:block;">
<tr>
  <th>Seller</th>
  <th>TYPE</th>
  <th>1 DAY</th>
  <th>3 DAYS</th>
  <th>7 DAYS</th>
</tr>
<tbody>
<?php foreach($buybox as $item):?> 
<tr>
  <td><?php echo $item['seller'] ?></td>
  <td><?php echo $item['if_fba'] ? 'FBA' : 'NONE' ?></td>
  <td><?php echo 100 * $item['rate1'] ?> %</td>
  <td><?php echo 100 * $item['rate2'] ?> %</td>
  <td><?php echo 100 * $item['rate3'] ?> %</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
