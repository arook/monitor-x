<?php

class Utils extends CComponent {
  public static function getAvatarBySid($sid) {
    $seller_id = Redis::client()->hget('sellers', $sid);
    if($avatar = Redis::client()->get('seller:'.$seller_id.':avatar'))
      return "<img src='$avatar' width='120' height='30' />";
    return Redis::client()->get('seller:'.$seller_id.':name');
  }
}
