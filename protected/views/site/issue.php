<?php

if ($issue) {
  $this->widget('CTabView',
    array(
      'tabs'=>array(
       'tab1'=>array(
         'title'=>'LISTING',
         'content'=>$issue->content_listing,
       ),
       'tab0'=>array(
         'title'=>'BUY BOX',
         'content'=>$issue->content_buybox,
      ),
     ),
    )
  );
}
?>
