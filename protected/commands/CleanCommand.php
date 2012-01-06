<?php

class CleanCommand extends CConsoleCommand {

  public function run($args) {
    $result = Yii::app()->db->createCommand('
        SELECT * 
        FROM  `fetching` 
        WHERE  `dt` < DATE_SUB( NOW( ) , INTERVAL 14 DAY )
      ')->query();

    if ($result->getRowCount()) {
      while ($fetching = $result->read()) {
        echo $fetching['dt'], "\n";
        $this->deleteFetching($fetching['id']);
      }
    }

  }

  private function deleteFetching($id) {
    Yii::app()->db->createCommand("
      delete from `fetching_issue` where `fetching_id` = '$id'
      ")->query();
    Yii::app()->db->createCommand("
      delete from `fetching_detail` where `fetching_id` = '$id'
      ")->query();
    Fetching::model()->deleteByPk($id);
  }
}
?>
