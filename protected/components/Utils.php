<?php

class Utils extends CComponent {
  public static function getAvatarBySidbak($sid) {
    $seller_id = Redis::client()->hget('sellers', $sid);
    if($avatar = Redis::client()->get('seller:'.$seller_id.':avatar'))
      return "<img src='$avatar' width='120' height='30' />";
    return Redis::client()->get('seller:'.$seller_id.':name');
  }

  public static function getAvatarBySid($sid) {
    $seller = MSeller::model()->getCollection()->getDbRef($sid);
    if($avatar = $seller['avatar'])
      return "<img src='$avatar' width='120' height='30' />";
    return $seller['name'];
  }

}
