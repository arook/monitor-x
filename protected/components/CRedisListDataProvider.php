<?php
/**
 * CRedisListDataProvider class file.
 *
 *
 *
 *
 *
 *
 *
 */
class CRedisListDataProvider extends CDataProvider {
  public $keyField = 'id';
  public $keyName;

  public function __construct($keyName, $config=array()) {
    $this->keyName = $keyName;
    foreach($config as $key=>$value)
      $this->$key=$value;
  }

  protected function fetchData() {
    if(($sort=$this->getSort())!==false && ($order=$sort->getOrderBy())!='')
      $this->sortData($this->getSortDirections($order));

    if(($pagination=$this->getPagination())!==false) {
      $pagination->setItemCount($this->getTotalItemCount());
      echo $pagination->getItemCount();
      return Redis::client()->lrange($this->keyName, $pagination->getOffset(), $pagination->getLimit());
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
