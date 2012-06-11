<?php
/**
 * FetchingListDataProvider class file.
 *
 *
 *
 *
 *
 *
 *
 */
class FetchingListDataProvider extends CDataProvider {
  public $keyField = 'id';
  public $aid;
  public $keyName;

  public function __construct($asin, $config=array()) {
    $asinId = Redis::client()->hget('asins', $asin);
    $this->aid = $asinId;
    $this->keyName = 'asin:'.$asinId.':fetch';
    foreach($config as $key=>$value)
      $this->$key=$value;
  }

  protected function fetchData() {
    if(($sort=$this->getSort())!==false && ($order=$sort->getOrderBy())!='')
      $this->sortData($this->getSortDirections($order));

    if(($pagination=$this->getPagination())!==false) {
      $pagination->setItemCount($this->getTotalItemCount());
      $list = Redis::client()->sort($this->keyName, array('sort'=>'desc', 'limit'=>array($pagination->getOffset(), $pagination->getLimit())));
      $return = array();
      foreach($list as $fid) {
        $fetch = array(
          'id'=>$fid,
          'time'=>Redis::client()->get('fetch:'.$fid.':time'),
          //'bs'=>Redis::client()->get('fetch:'.$fid.':seller'),
          //'bp'=>Redis::client()->get('fetch:'.$fid.':bp'),
        );
        $fs = Redis::client()->get('asin:'.$this->aid.':fs');
        $ranks = Redis::client()->lrange('fetch:'.$fid.':list', 0, $fs);
        foreach($ranks as $key=>$rank) {
          $fetch['r'.$key] = $rank;
        }
        $return[] = $fetch;
      }
      return $return;
    }
    else
      return null;
  }

  protected function fetchKeys() {
    $keys = array();
    foreach($this->getData() as $i=>$data)
      $keys[$i] = is_object($data) ? $data->{$this->keyField} : $data[$this->keyField];

    return $keys;
  }

  protected function calculateTotalItemCount() {
    return Redis::client()->llen($this->keyName);
  }

  protected function sortData($directions) {
    if(empty($directions))
      return;
  }

  protected function getSortDirections($order) {
    $segs = explode(',', $order);
    $directions = array();
    foreach($segs as $seg) {
      if(preg_match('/(.*?)(\s+(desc|asc))?$/', trim($seg), $matches))
        $directions[$matches[1]] = isset($matches[3]) && !strcasecmp($matches[3], 'desc');
      else
        $directions[trim($seg)] = false;
    }
    return $directions;
  }
}
