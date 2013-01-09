<?php

class TCommand extends CConsoleCommand {
  public function run() {
    $node = MNode::model()->findByAttributes(array('id'=>"1"));
    $node->lock();
    
    // $node->unlock();
  }
}
