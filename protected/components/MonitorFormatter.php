<?php

class MonitorFormatter extends CFormatter {

  public function formatRankbak($value) {
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

  public function formatRank($value) {
    $html = '';
    // $html .= '<a href="javascript:;" rel="tooltip" title="' . Utils::getAvatarBySid($value->s) . '">';
	$logo = '<span style="border:width:40px;heigth:10px;white-space:nowrap;overflower:hidden"><a href="javascript:;" rel="tooltip" title="' . Utils::getAvatarBySid($value->s) . '">' . Utils::getAvatarBySid($value->s, true) . "</a></span>";
	
	$line = "<hr style='margin:1px'>";
	
    if($value->f)
      $html = '<span style="color:#C60;border:1px solid;padding:2px 4px;">';
    else
      $html = '<span>';
	
    $html .= ($value->p/100 + $value->sp/100);
    $html .= '</div>';

		
    if($value->b)
      $html = '<b style="background:#FF0;">'.$html.'</b>';
    return $logo . $line . $html;
  }

}
