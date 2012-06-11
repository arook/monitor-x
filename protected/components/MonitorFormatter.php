<?php

class MonitorFormatter extends CFormatter {

  public function formatRank($value) {
    $value = CJSON::decode($value);
    $html = '';
    $html .= '<a href="javascript:;" rel="tooltip" title="' . Utils::getAvatarBySid($value['sid']) . '">';

    if($value['if_fba'])
      $html .= '<span style="color:#C60;border:1px solid;padding:2px 4px;">';
    else
      $html .= '<span>';

    $html .= $value['sell_price'] + $value['shipping_price'];
    $html .= '</span></a>';

    if($value['if_buybox'])
      $html = '<b style="background:#FF0;">'.$html.'</b>';
    return $html;
  }
}
