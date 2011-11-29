<div class="view">

<table>
  <tr>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
    <td><?php echo CHtml::encode($data->dt); ?></td>
  </tr>
</table>
  <b></b>
  <br />
  <?php
    foreach($data->fetchingDetails as $detail) {
      echo "<dl>";
      echo "<dd>", $detail->seller, "</dd>";
      echo "<dd>", $detail->shipping_price, "</dd>";
/*        $detail->shipping_price, 
        $detail->sell_price,
        $detail->if_buybox,
        $detail->if_fba;
*/
      echo "</dl>";
    }
  ?>

</div>
