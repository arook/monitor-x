
<table style="width:900px;overflow:auto;display:block;">
<tr>
  <th>Seller</th>
  <th>FBA</th>
  <th>TYPE</th>
  <th>Rate</th>
</tr>
<tbody>
<?php foreach($buybox as $item):?> 
<tr>
  <td><?php echo $item['seller'] ?></td>
  <td><?php echo $item['if_fba'] ?></td>
  <td><?php echo $item['type'] ?></td>
  <td><?php echo 100 * $item['rate'] ?> %</td>
</tr>
<?php endforeach;?>
</tbody>
</table>
