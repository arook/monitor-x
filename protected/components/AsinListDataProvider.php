<?php
/**
 * AsinListDataProvider class file.
 *
 *
 *
 *
 *
 *
 *
 */
class AsinListDataProvider extends CDataProvider {
  public $keyField = 'id';
  public $kw;

  public function __construct($kw, $config=array()) {
    $this->kw = $kw;
    foreach($config as $key=>$value)
      $this->$key=$value;
  }

  protected function fetchData() {
    if(($sort=$this->getSort())!==false && ($order=$sort->getOrderBy())!='')
      $this->sortData($this->getSortDirections($order));

    if(($pagination=$this->getPagination())!==false) {
      $list = Redis::client()->hgetall('asins');
      $return = array();
      foreach($list as $asin=>$key) {
        if(stristr($asin, $this->kw))
          $return[] = array('id'=>$key, 'asin'=>$asin, 'retry'=>1);
      }
      $pagination->setItemCount(count($return));
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
    return Redis::client()->hlen('asins');
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
