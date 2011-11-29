<div class="view">

  <b><?php echo CHtml::encode($data->dt); ?></b>
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
